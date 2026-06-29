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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_REGLA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_regla = $_POST['id_regla'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $modo_activacion = $_POST['modo_activacion'];
    $numero_dias_caducidad_acciones = $_POST['numero_dias_caducidad_acciones'];

    // Se comprueba si existe otra regla con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM reglas
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_regla)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una regla con el mismo nombre");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_regla_anterior = dame_fila_regla($id_regla);

        // Se modifica la regla
        $operacion_modificacion = "
            UPDATE reglas
            SET
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
                tipo = '".$bd_red->_($tipo)."',
                modo_activacion = '".$bd_red->_($modo_activacion)."',
                numero_dias_caducidad_acciones = '".$bd_red->_($numero_dias_caducidad_acciones)."',
                activaciones = '0',
                habilitada = '".VALOR_SI."'
            WHERE
                id = '".$bd_red->_($id_regla)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se envía el mensaje MQTT
            notifica_operacion_administracion_regla(OPERACION_MODIFICACION, $id_regla);

            // Se recupera la fila actual
            $fila_regla_actual = dame_fila_regla($id_regla);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_regla(
                $fila_regla_actual,
                $fila_regla_anterior);

            $res = "OK";
            $msg = $idiomas->_("Regla modificada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la regla
    function anyade_accion_usuario_modificar_regla($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_REGLA;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_REGLA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_REGLA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["modo_activacion"] != $fila_anterior["modo_activacion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MODO_ACTIVACION_REGLA] = $fila_actual["modo_activacion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MODO_ACTIVACION_REGLA] = $fila_anterior["modo_activacion"];
        }
        if ($fila_actual["numero_dias_caducidad_acciones"] != $fila_anterior["numero_dias_caducidad_acciones"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_CADUCIDAD_ACCIONES] = $fila_actual["numero_dias_caducidad_acciones"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_CADUCIDAD_ACCIONES] = $fila_anterior["numero_dias_caducidad_acciones"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
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
