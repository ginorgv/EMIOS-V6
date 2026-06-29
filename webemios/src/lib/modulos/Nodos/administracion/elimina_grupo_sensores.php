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
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_GRUPO_SENSORES, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_grupo_sensores = $_POST['id_grupo_sensores'];

    // Comprobaciones antes de eliminar el grupo de sensores:
    // - Se comprueba si existen sensores en el grupo
    // - Se comprueba si existe algun evento asignado a este grupo
    // - Se comprueba si existe algun suceso asignado a este grupo de sensores (de timeout de envío)
    $eliminar_grupo = true;

    // Se comprueba si existen sensores en el grupo
    if ($eliminar_grupo == true)
    {
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                grupo = '".$bd_red->_($id_grupo_sensores)."'
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
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene sensores asignados")."\n(".
                $nombre_sensor.")";
        }
    }

    // Se comprueba si existe algun evento asignado a este grupo
    if ($eliminar_grupo == true)
    {
        $consulta_eventos = "
            SELECT nombre
            FROM eventos
            WHERE
                (origen = '".ORIGEN_EVENTO_GRUPO_SENSORES."')
                AND (id_origen = '".$bd_red->_($id_grupo_sensores)."')
            ORDER BY nombre ASC";
        $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
        if ($res_eventos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_eventos."'");
        }
        if ($res_eventos->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_evento = $res_eventos->dame_siguiente_fila();
            $nombre_evento = $fila_evento["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene eventos asignados")."\n(".
                $nombre_evento.")";
        }
    }

    // Se comprueba si existe algun suceso asignado a este grupo de sensores (de timeout de envío)
    if ($eliminar_grupo == true)
    {
        $consulta_sucesos = "
            SELECT *
            FROM sucesos_reglas
            WHERE
                (causa = '".CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR."')
                AND (origen = '".ORIGEN_SUCESO_GRUPO_SENSORES."')
                AND (id_origen = '".$bd_red->_($id_grupo_sensores)."')
            ORDER BY nombre ASC";
        $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
        if ($res_sucesos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
        }
        if ($res_sucesos->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_suceso = $res_sucesos->dame_siguiente_fila();
            $nombre_suceso = $fila_suceso["nombre"];
            $id_regla_suceso = $fila_suceso["regla"];
            $nombre_regla_suceso = dame_nombre_regla($id_regla_suceso);

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene sucesos de timeout de envío asignados")."\n(".
                $idiomas->_("suceso").": ".$nombre_suceso.", ".
                $idiomas->_("regla").": ".$nombre_regla_suceso.")";
        }
    }

    // Se elimina el grupo de sensores
    if ($eliminar_grupo == true)
    {
        // Se recupera la información del grupo de sensores
        $fila_grupo_sensores = dame_fila_grupo_sensores($id_grupo_sensores);

        // Se elimina el grupo de sensores
        $operacion_borrado = "
            DELETE
            FROM grupos_sensores
            WHERE
                id = '".$bd_red->_($id_grupo_sensores)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Acciones a realizar al eliminar un grupo de sensores
            realiza_acciones_grupo_sensores_eliminado($id_grupo_sensores);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_grupo_sensores($fila_grupo_sensores);

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


    // Realiza acciones al eliminar un grupo de sensores
    function realiza_acciones_grupo_sensores_eliminado($id_grupo_sensores)
    {
        // Se modifican los elementos de plantillas de informes que contengan este grupo de sensores (se establece a ninguno)
        modifica_elementos_plantillas_informes_grupo_sensores_eliminado($id_grupo_sensores);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este grupo de sensores seleccionado en algún parámetro
        modifica_informes_automaticos_plantillas_informes_grupo_sensores_eliminado($id_grupo_sensores);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_grupo_sensores_eliminado($id_grupo_sensores);

        // Se elimina el grupo de sensores de los parámetros del módulo Sensores de los usuarios (si es necesario)
        elimina_sensor_grupo_parametros_modulo_sensores_usuarios(TIPO_NODO_GRUPO_SENSORES, $id_grupo_sensores);
    }


    // Añade la acción de usuario de eliminación del grupo de sensores
    function anyade_accion_usuario_eliminar_grupo_sensores($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_GRUPO_SENSORES;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
