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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_ACCION_REGLA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $id_regla = $_POST['id_regla'];
    $tipo = $_POST['tipo'];
    $causa = $_POST['causa'];
    $clase_actuador = $_POST['clase_actuador'];
    $destino = $_POST['destino'];
    $id_destino = $_POST['id_destino'];
    $id_accion_predefinida = $_POST['id_accion_predefinida'];
    $contenido_accion = $_POST['contenido_accion'];
    $valor_accion = $_POST['valor_accion'];

    // Se comprueba si existe una acción con el mismo nombre en la misma regla
    $consulta_existe = "
        SELECT *
        FROM acciones_reglas
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
        $msg = $idiomas->_("Ya existe una acción con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de añadir la acción de la regla:
        // - Comprobación de acción con el mismo destino (se permite si es de tipo mensaje)
        $anyadir_accion = true;

        // Comprobación de acción con el mismo destino (se permite si es de tipo mensaje)
        if ($anyadir_accion == true)
        {
            if ($clase_actuador != CLASE_ACTUADOR_MENSAJE)
            {
                $consulta_misma_accion = "
                    SELECT *
                    FROM acciones_reglas
                    WHERE
                        (regla = '".$bd_red->_($id_regla)."')
                        AND (tipo = '".$bd_red->_($tipo)."')
                        AND ((causa = '".CAUSA_EJECUCION_ACCION_TODAS."')
                            OR (causa = '".$bd_red->_($causa)."'))
                        AND (destino = '".$bd_red->_($destino)."')
                        AND (id_destino = '".$bd_red->_($id_destino)."')";
                $res_misma_accion = $bd_red->ejecuta_consulta($consulta_misma_accion);
                if ($res_misma_accion == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_misma_accion."'");
                }
                if ($res_misma_accion->dame_numero_filas() > 0)
                {
                    $anyadir_accion = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Ya existe una acción con el mismo destino");
                }
            }
        }

        // Se añade la acción de la regla
        if ($anyadir_accion == true)
        {
            // Si es una acción predefinida, se recuperan el contenido y el valor de la acción predefinida
            if ($id_accion_predefinida != ID_NINGUNO)
            {
                $fila_accion_predefinida = dame_fila_accion_predefinida($id_accion_predefinida);
                $contenido_accion = $fila_accion_predefinida['contenido'];
                $valor_accion = $fila_accion_predefinida['valor'];
            }

            // Se añade la acción de la regla
            $operacion_insercion = "
                INSERT INTO acciones_reglas (
                    nombre,
                    red,
                    regla,
                    tipo,
                    causa,
                    clase,
                    destino,
                    id_destino,
                    contenido_accion,
                    valor_accion
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($causa)."',
                    '".$bd_red->_($clase_actuador)."',
                    '".$bd_red->_($destino)."',
                    '".$bd_red->_($id_destino)."',
                    '".$bd_red->_($contenido_accion)."',
                    '".$bd_red->_($valor_accion)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la acción de regla añadida
                $id_accion = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_accion = dame_fila_accion_regla($id_accion);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_accion_regla($fila_accion);

                $res = "OK";
                $msg = $idiomas->_("Acción añadida correctamente");
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


    // Añade la acción de usuario de adición de la acción de regla
    function anyade_accion_usuario_anyadir_accion_regla($fila)
    {
        // Nombre de la regla
        $nombre_regla = dame_nombre_regla($fila["regla"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_ACCION_REGLA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_regla.")";

        // Nombre de destino de la acción
        $nombre_destino = AccionRegla::dame_nombre_destino_accion_regla($fila["destino"], $fila["id_destino"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ACCION_REGLA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUSA_ACCION_REGLA] = $fila["causa"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila["clase"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_DESTINO_ACCION] = $fila["destino"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_DESTINO] = $nombre_destino;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila["contenido_accion"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
