<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_HIJO_SENSOR, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_hijo_sensor = $_POST['id_hijo_sensor'];
    $id_sensor_padre = $_POST['id_sensor_padre'];
    $id_sensor_hijo = $_POST['id_sensor_hijo'];
    $tipo_sensor_padre = $_POST['tipo_sensor_padre'];

    // Se recupera la información del hijo de sensor
    $fila_hijo_sensor = dame_fila_hijo_sensor($id_hijo_sensor);

    // Se borra el hijo de sensor
	$operacion_borrado = "
        DELETE
        FROM hijos_sensores
        WHERE
            id = '".$bd_red->_($id_hijo_sensor)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se actualiza el orden del sensor padre (y sus padres recursivamente)
        // (sólo si el sensor hijo es del mismo tipo que el sensor padre)
        $tipo_sensor_hijo = dame_tipo_sensor($id_sensor_hijo);
        if ($tipo_sensor_hijo == $tipo_sensor_padre)
        {
            // Carga la información de los sensores padres e hijos
            $info_sensores_padres = NULL;
            $info_sensores_hijos = NULL;
            carga_informacion_sensores_padres_hijos(
                $tipo_sensor_padre,
                $_SESSION["id_red"],
                $info_sensores_padres,
                $info_sensores_hijos);

            // Se actualiza el orden del sensor padre (y sus padres recursivamente)
            $ordenes_sensores = NULL;
            carga_ordenes_sensores_padres_hijos(
                $tipo_sensor_padre,
                $_SESSION["id_red"],
                $ordenes_sensores);
            actualiza_orden_sensores_ascendientes(
                $info_sensores_padres,
                $info_sensores_hijos,
                $ordenes_sensores,
                $id_sensor_padre);
        }

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_hijo_sensor($fila_hijo_sensor);

        $res = "OK";
        $msg = $idiomas->_("Sensor hijo eliminado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del hijo de sensor
    function anyade_accion_usuario_eliminar_hijo_sensor($fila)
    {
        // Filas de los sensores padre e hijo
        $fila_sensor_padre = dame_fila_sensor($fila["sensor_padre"]);
        $fila_sensor_hijo = dame_fila_sensor($fila["sensor_hijo"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_HIJO_SENSOR;
        $objeto_accion_usuario = $fila_sensor_hijo["nombre"]." (".$fila_sensor_padre["nombre"].")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
