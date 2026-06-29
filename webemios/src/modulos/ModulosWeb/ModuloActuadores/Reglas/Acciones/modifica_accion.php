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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_ACCION_REGLA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_accion = $_POST['id_accion'];
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

    // Se comprueba si existe otra acción con el mismo nombre en la misma regla
    $consulta_existe = "
        SELECT *
        FROM acciones_reglas
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (regla = '".$bd_red->_($id_regla)."')
            AND (id <> '".$bd_red->_($id_accion)."')";
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
        // Comprobaciones antes de modificar la acción
        // - Comprobación de acción con el mismo destino (se permite si es de tipo mensaje)
        $modificar_accion = true;

        // Comprobación de acción con el mismo destino (se permite si es de tipo mensaje)
        if ($modificar_accion == true)
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
                        AND (id_destino = '".$bd_red->_($id_destino)."')
                        AND (id <> '".$bd_red->_($id_accion)."')";
                $res_misma_accion = $bd_red->ejecuta_consulta($consulta_misma_accion);
                if ($res_misma_accion == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_misma_accion."'");
                }
                if ($res_misma_accion->dame_numero_filas() > 0)
                {
                    $modificar_accion = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Ya existe una acción con el mismo destino");
                }
            }
        }

        // Se modifica la acción
        if ($modificar_accion == true)
        {
            // Si es una acción predefinida, se recuperan el contenido y valor de la acción predefinida
            if ($id_accion_predefinida != ID_NINGUNO)
            {
                $fila_accion_predefinida = dame_fila_accion_predefinida($id_accion_predefinida);
                $contenido_accion = $fila_accion_predefinida['contenido'];
                $valor_accion = $fila_accion_predefinida['valor'];
            }

            // Se recupera la fila anterior (antes de la modificación)
            $fila_accion_anterior = dame_fila_accion_regla($id_accion);

            // Se modifica la acción de la regla
            $operacion_modificacion = "
                UPDATE acciones_reglas
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    regla = '".$bd_red->_($id_regla)."',
                    tipo = '".$bd_red->_($tipo)."',
                    causa = '".$bd_red->_($causa)."',
                    clase = '".$bd_red->_($clase_actuador)."',
                    destino = '".$bd_red->_($destino)."',
                    id_destino = '".$bd_red->_($id_destino)."',
                    contenido_accion = '".$bd_red->_($contenido_accion)."',
                    valor_accion = '".$bd_red->_($valor_accion)."'
                WHERE
                    id = '".$bd_red->_($id_accion)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_accion_actual = dame_fila_accion_regla($id_accion);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_accion_regla(
                    $fila_accion_actual,
                    $fila_accion_anterior);

                $res = "OK";
                $msg = $idiomas->_("Acción modificada correctamente");
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


    // Añade la acción de usuario de modificación de la acción
    function anyade_accion_usuario_modificar_accion_regla($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_ACCION_REGLA;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ACCION_REGLA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_ACCION_REGLA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["causa"] != $fila_anterior["causa"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUSA_ACCION_REGLA] = $fila_actual["causa"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAUSA_ACCION_REGLA] = $fila_anterior["causa"];
        }
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_anterior["clase"];
        }
        if ($fila_actual["destino"] != $fila_anterior["destino"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_DESTINO_ACCION] = $fila_actual["destino"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_DESTINO_ACCION] = $fila_anterior["destino"];
        }
        if (($fila_actual["destino"] != $fila_anterior["destino"]) || ($fila_actual["id_destino"] != $fila_anterior["id_destino"]))
        {
            $nombre_destino = AccionRegla::dame_nombre_destino_accion_regla($fila_actual["destino"], $fila_actual["id_destino"]);
            $nombre_destino_anterior = AccionRegla::dame_nombre_destino_accion_regla($fila_anterior["destino"], $fila_anterior["id_destino"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESTINO_ACTUADOR] = $nombre_destino;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESTINO_ACTUADOR] = $nombre_destino_anterior;
        }
        if ($fila_actual["contenido_accion"] != $fila_anterior["contenido_accion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila_actual["contenido_accion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila_anterior["contenido_accion"];
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
