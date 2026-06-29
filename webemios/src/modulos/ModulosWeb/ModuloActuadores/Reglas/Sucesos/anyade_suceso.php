<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_SUCESO_REGLA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $id_regla = $_POST['id_regla'];
    $causa = $_POST['causa'];
    $id_causa = $_POST['id_causa'];
    $origen = $_POST['origen'];
    $id_origen = $_POST['id_origen'];
    $modo_activacion = $_POST['modo_activacion'];
    $parametros_modo_activacion = $_POST['parametros_modo_activacion'];
    $numero_activaciones = $_POST['numero_activaciones'];

    // Se comprueba si existe un suceso con el mismo nombre en la misma regla
    $consulta_existe = "
        SELECT *
        FROM sucesos_reglas
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (regla = '".$bd_red->_($id_regla)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un suceso con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de añadir el suceso de la regla:
        // - Causas y orígenes válidos
        // - No puede existir un suceso con el mismo origen
        // - El número de activaciones debe ser 1 para una regla múltiple
        // - Comprobación de bucle en los sucesos de las reglas
        $anyadir_suceso = true;

        // Causas y orígenes válidos
        if ($anyadir_suceso == true)
        {
            switch ($causa)
            {
                case CAUSA_SUCESO_EVENTO:
                case CAUSA_SUCESO_REGLA:
                {
                    if (($id_causa) == ID_NINGUNO)
                    {
                        $anyadir_suceso = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("La causa no puede ser ninguna");
                    }
                    break;
                }
            }
        }
        if ($anyadir_suceso == true)
        {
            switch ($causa)
            {
                case CAUSA_SUCESO_EVENTO:
                case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
                {
                    if ($origen == ID_NINGUNO)
                    {
                        $anyadir_suceso = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("El tipo de origen no puede ser ninguno");
                    }
                    break;
                }
            }
        }
        if ($anyadir_suceso == true)
        {
            if (($origen != ID_NINGUNO) && ($id_origen == ID_NINGUNO))
            {
                $anyadir_suceso = false;

                $res = "ERROR";
                $msg = $idiomas->_("El origen no puede ser ninguno");
            }
        }

        // No puede existir un suceso con el mismo origen
        if ($anyadir_suceso == true)
        {
            $consulta_mismo_suceso = "
                SELECT *
                FROM sucesos_reglas
                WHERE
                    (regla = '".$bd_red->_($id_regla)."')
                    AND (causa = '".$bd_red->_($causa)."')
                    AND (id_causa = '".$bd_red->_($id_causa)."')
                    AND (origen = '".$bd_red->_($origen)."')
                    AND (id_origen = '".$bd_red->_($id_origen)."')";
            $res_mismo_suceso = $bd_red->ejecuta_consulta($consulta_mismo_suceso);
            if ($res_mismo_suceso == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_mismo_suceso."'");
            }
            if ($res_mismo_suceso->dame_numero_filas() > 0)
            {
                $anyadir_suceso = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe un suceso con el mismo origen");
            }
        }

        // El número de activaciones debe ser 1 para una regla múltiple
        if ($anyadir_suceso == true)
        {
            $fila_regla = dame_fila_regla($id_regla);
            $tipo_regla = $fila_regla["tipo"];
            if ($tipo_regla == TIPO_REGLA_MULTIPLE)
            {
                if ($numero_activaciones <> 1)
                {
                    $anyadir_suceso = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("El número de activaciones debe ser 1 para una regla múltiple");
                }
            }
        }

        // Comprobación de bucle en los sucesos de las reglas
        if ($anyadir_suceso == true)
        {
            // Comprobación de bucle en las reglas hijas
            if ($causa == CAUSA_SUCESO_REGLA)
            {
                $info_reglas_hijas = NULL;
                carga_informacion_reglas_hijas($info_reglas_hijas);
                anyade_regla_hija($info_reglas_hijas, $id_regla, $id_causa);

                $existe_bucle = existe_bucle_reglas_hijas($info_reglas_hijas);
                if ($existe_bucle == true)
                {
                    $anyadir_suceso = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay un bucle en los sucesos de las reglas");
                }
            }
        }

        // Se añade el suceso de la regla
        if ($anyadir_suceso == true)
        {
            // Se añade el suceso de la regla
            $operacion_insercion = "
                INSERT INTO sucesos_reglas (
                    nombre,
                    red,
                    regla,
                    causa,
                    id_causa,
                    origen,
                    id_origen,
                    modo_activacion,
                    parametros_modo_activacion,
                    numero_activaciones,
                    activaciones
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($causa)."',
                    '".$bd_red->_($id_causa)."',
                    '".$bd_red->_($origen)."',
                    '".$bd_red->_($id_origen)."',
                    '".$bd_red->_($modo_activacion)."',
                    '".$bd_red->_($parametros_modo_activacion)."',
                    '".$bd_red->_($numero_activaciones)."',
                    0
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila del suceso de regla añadido
                $id_suceso = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_suceso = dame_fila_suceso_regla($id_suceso);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_suceso_regla($fila_suceso);

                $res = "OK";
                $msg = $idiomas->_("Suceso añadido correctamente");
                $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del suceso
    function anyade_accion_usuario_anyadir_suceso_regla($fila)
    {
        // Nombre de la regla
        $nombre_regla = dame_nombre_regla($fila["regla"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_SUCESO_REGLA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_regla.")";

        // Nombres de causa y origen del suceso
        $nombre_causa = SucesoRegla::dame_nombre_causa_suceso_regla($fila["causa"], $fila["id_causa"]);
        $nombre_origen = SucesoRegla::dame_nombre_origen_suceso_regla($fila["origen"], $fila["id_origen"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CAUSA_SUCESO] = $fila["causa"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUSA_SUCESO] = $nombre_causa;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_SUCESO] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_ACTIVACIONES_SUCESO_REGLA] = $fila["numero_activaciones"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
