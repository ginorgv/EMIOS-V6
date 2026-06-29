<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_SUCESO_REGLA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_suceso = $_POST['id_suceso'];
    $nombre = $_POST['nombre'];
    $id_regla = $_POST['id_regla'];
    $causa = $_POST['causa'];
    $id_causa = $_POST['id_causa'];
    $origen = $_POST['origen'];
    $id_origen = $_POST['id_origen'];
    $modo_activacion = $_POST['modo_activacion'];
    $parametros_modo_activacion = $_POST['parametros_modo_activacion'];
    $numero_activaciones = $_POST['numero_activaciones'];
    $causa_anterior = $_POST['causa_anterior'];
    $id_causa_anterior = $_POST['id_causa_anterior'];

    // Se comprueba si existe otro suceso con el mismo nombre en la misma regla
    $consulta_existe = "
        SELECT *
        FROM sucesos_reglas
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (regla = '".$bd_red->_($id_regla)."')
            AND (id <> '".$bd_red->_($id_suceso)."')";
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
        // Comprobaciones antes de modificar el suceso de la regla:
        // - Causas y orígenes válidos
        // - No puede existir un suceso con el mismo origen
        // - El número de activaciones debe ser 1 para una regla múltiple
        // - Comprobación de bucle en los sucesos de las reglas
        $modificar_suceso = true;

        // Causas y orígenes válidos
        if ($modificar_suceso == true)
        {
            switch ($causa)
            {
                case CAUSA_SUCESO_EVENTO:
                case CAUSA_SUCESO_REGLA:
                {
                    if (($id_causa) == ID_NINGUNO)
                    {
                        $modificar_suceso = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("La causa no puede ser ninguna");
                    }
                    break;
                }
            }
        }
        if ($modificar_suceso == true)
        {
            switch ($causa)
            {
                case CAUSA_SUCESO_EVENTO:
                case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
                {
                    if ($origen == ID_NINGUNO)
                    {
                        $modificar_suceso = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("El tipo de origen no puede ser ninguno");
                    }
                    break;
                }
            }
        }
        if ($modificar_suceso == true)
        {
            if (($origen != ID_NINGUNO) AND ($id_origen == ID_NINGUNO))
            {
                $modificar_suceso = false;

                $res = "ERROR";
                $msg = $idiomas->_("El origen no puede ser ninguno");
            }
        }

        // No puede existir un suceso con el mismo origen
        if ($modificar_suceso == true)
        {
            $consulta_mismo_suceso = "
                SELECT *
                FROM sucesos_reglas
                WHERE
                    (regla = '".$bd_red->_($id_regla)."')
                    AND (causa = '".$bd_red->_($causa)."')
                    AND (id_causa = '".$bd_red->_($id_causa)."')
                    AND (origen = '".$bd_red->_($origen)."')
                    AND (id_origen = '".$bd_red->_($id_origen)."')
                    AND (id <> '".$bd_red->_($id_suceso)."')";
            $res_mismo_suceso = $bd_red->ejecuta_consulta($consulta_mismo_suceso);
            if ($res_mismo_suceso == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_mismo_suceso."'");
            }
            if ($res_mismo_suceso->dame_numero_filas() > 0)
            {
                $modificar_suceso = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe un suceso con el mismo origen");
            }
        }

        // El número de activaciones debe ser 1 para una regla múltiple
        if ($modificar_suceso == true)
        {
            $fila_regla = dame_fila_regla($id_regla);
            $tipo_regla = $fila_regla['tipo'];
            if ($tipo_regla == TIPO_REGLA_MULTIPLE)
            {
                if ($numero_activaciones <> 1)
                {
                    $modificar_suceso = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("El número de activaciones debe ser 1 para una regla múltiple");
                }
            }
        }

        // Comprobación de bucle en los sucesos de las reglas
        if ($modificar_suceso == true)
        {
            // Comprobación de bucle en las reglas hijas
            if ($causa == CAUSA_SUCESO_REGLA)
            {
                $info_reglas_hijas = NULL;
                carga_informacion_reglas_hijas($info_reglas_hijas);
                if ($causa_anterior == CAUSA_SUCESO_REGLA)
                {
                    elimina_regla_hija($info_reglas_hijas, $id_regla, $id_causa_anterior);
                }
                anyade_regla_hija($info_reglas_hijas, $id_regla, $id_causa);

                $existe_bucle = existe_bucle_reglas_hijas($info_reglas_hijas);
                if ($existe_bucle == true)
                {
                    $modificar_suceso = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay un bucle en los sucesos de las reglas");
                }
            }
        }

        // Se modifica el suceso
        if ($modificar_suceso == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_suceso_anterior = dame_fila_suceso_regla($id_suceso);

            // Se modifica el suceso de la regla
            $operacion_modificacion = "
                UPDATE sucesos_reglas
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    regla = '".$bd_red->_($id_regla)."',
                    causa = '".$bd_red->_($causa)."',
                    id_causa = '".$bd_red->_($id_causa)."',
                    origen = '".$bd_red->_($origen)."',
                    id_origen = '".$bd_red->_($id_origen)."',
                    modo_activacion = '".$bd_red->_($modo_activacion)."',
                    parametros_modo_activacion = '".$bd_red->_($parametros_modo_activacion)."',
                    numero_activaciones = '".$bd_red->_($numero_activaciones)."'
                WHERE
                    id = '".$bd_red->_($id_suceso)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_suceso_actual = dame_fila_suceso_regla($id_suceso);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_suceso(
                    $fila_suceso_actual,
                    $fila_suceso_anterior);

                $res = "OK";
                $msg = $idiomas->_("Suceso modificado correctamente");
                $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
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


    // Añade la acción de usuario de modificación del suceso
    function anyade_accion_usuario_modificar_suceso($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_SUCESO_REGLA;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["causa"] != $fila_anterior["causa"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CAUSA_SUCESO] = SucesoRegla::dame_descripcion_causa_suceso($fila_actual["causa"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_CAUSA_SUCESO] = SucesoRegla::dame_descripcion_causa_suceso($fila_anterior["causa"]);
        }
        if (($fila_actual["causa"] != $fila_anterior["causa"]) || ($fila_actual["id_causa"] != $fila_anterior["id_causa"]))
        {
            $nombre_causa = SucesoRegla::dame_nombre_causa_suceso_regla($fila_actual["causa"], $fila_actual["id_causa"]);
            $nombre_causa_anterior = SucesoRegla::dame_nombre_causa_suceso_regla($fila_anterior["causa"], $fila_anterior["id_causa"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUSA_SUCESO] = $nombre_causa;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAUSA_SUCESO] = $nombre_causa_anterior;
        }
        if ($fila_actual["origen"] != $fila_anterior["origen"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_SUCESO] = SucesoRegla::dame_descripcion_origen_suceso($fila_actual["origen"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ORIGEN_SUCESO] = SucesoRegla::dame_descripcion_origen_suceso($fila_anterior["origen"]);
        }
        if (($fila_actual["origen"] != $fila_anterior["origen"]) || ($fila_actual["id_origen"] != $fila_anterior["id_origen"]))
        {
            $nombre_origen = SucesoRegla::dame_nombre_origen_suceso_regla($fila_actual["origen"], $fila_actual["id_origen"]);
            $nombre_origen_anterior = SucesoRegla::dame_nombre_origen_suceso_regla($fila_anterior["origen"], $fila_anterior["id_origen"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen_anterior;
        }
        if ($fila_actual["numero_activaciones"] != $fila_anterior["numero_activaciones"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_ACTIVACIONES_SUCESO_REGLA] = $fila_actual["numero_activaciones"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NUMERO_ACTIVACIONES_SUCESO_REGLA] = $fila_anterior["numero_activaciones"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Nombre de la regla
        $nombre_regla = dame_nombre_regla($fila_actual["regla"]);

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"]." (".$nombre_regla.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"]." (".$nombre_regla.")",
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
