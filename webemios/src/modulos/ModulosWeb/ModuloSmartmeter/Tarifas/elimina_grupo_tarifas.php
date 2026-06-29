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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_GRUPO_TARIFAS, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $medicion = $_POST['medicion'];
    $id_grupo_tarifas = $_POST['id_grupo_tarifas'];

    // Tablas de tarifas
    $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
    $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);

    // Comprobaciones antes de eliminar el grupo de tarifas:
    // - Se comprueba si existen tarifas en el grupo
    // - Se comprueba si se está utilizando el grupo de tarifas eléctricas en algún sensor
    $eliminar_grupo = true;

    // Se comprueba si existen tarifas en el grupo
    if ($eliminar_grupo == true)
    {
        $consulta_tarifas = "
            SELECT nombre
            FROM ".$tabla_tarifas."
            WHERE
                grupo = '".$bd_red->_($id_grupo_tarifas)."'
            ORDER BY nombre ASC";
        $res_tarifas = $bd_red->ejecuta_consulta($consulta_tarifas);
        if ($res_tarifas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas."'");
        }
        if ($res_tarifas->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_tarifa = $res_tarifas->dame_siguiente_fila();
            $nombre_tarifa = $fila_tarifa["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene tarifas asignadas")."\n(".
                $nombre_tarifa.")";
        }
    }

    // Se comprueba si se está utilizando el grupo de tarifas eléctricas en algún sensor
    if ($eliminar_grupo == true)
    {
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $indice_parametros_clase_sensor_id_grupo_tarifas = dame_indice_parametro_clase_sensor_grupo_tarifas($medicion);
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (clase = '".$clase_sensor."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)  = '".$bd_red->_($id_grupo_tarifas)."')
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        if ($res_sensores->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_sensor = $res_sensores->dame_siguiente_fila();
            $nombre_sensor = $fila_sensor["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque está asignado a algún sensor")."\n(".
                $nombre_sensor.")";
        }
    }

    // Se elimina el grupo de tarifas
    if ($eliminar_grupo == true)
    {
        // Se recupera la fila del grupo de tarifas
        $fila_grupo_tarifas = dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);

        // Se elimina el grupo de tarifas
        $operacion_borrado = "
            DELETE
            FROM ".$tabla_grupos_tarifas."
            WHERE
                id = '".$bd_red->_($id_grupo_tarifas)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_grupo_tarifas($medicion, $fila_grupo_tarifas);

            $res = "OK";
            $msg = $idiomas->_("Grupo eliminado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del periodo
    function anyade_accion_usuario_eliminar_grupo_tarifas($medicion, $fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_GRUPO_TARIFAS;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
