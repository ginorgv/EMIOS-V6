<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_GRUPO_ACTUADORES, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_grupo_actuadores = $_POST['id_grupo_actuadores'];

    // Comprobaciones antes de eliminar el grupo de actuadores:
    // - Se comprueba si existen actuadores en el grupo
    // - Se comprueba si existe alguna acción asignada a este grupo de actuadores
    $eliminar_grupo = true;

    // Se comprueba si existen actuadores en el grupo
    if ($eliminar_grupo == true)
    {
        $consulta_actuadores = "
            SELECT nombre
            FROM actuadores
            WHERE
                grupo = '".$bd_red->_($id_grupo_actuadores)."'
            ORDER BY nombre ASC";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }
        if ($res_actuadores->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_actuador = $res_actuadores->dame_siguiente_fila();
            $nombre_actuador = $fila_actuador["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene actuadores asignados")."\n(".
                $nombre_actuador.")";
        }
    }

    // Se comprueba si existe alguna acción asignada a este grupo de actuadores
    if ($eliminar_grupo == true)
    {
        $consulta_acciones = "
            SELECT *
            FROM acciones_reglas
            WHERE
                (destino = '".DESTINO_ACCION_GRUPO_ACTUADORES."')
                AND (id_destino = '".$bd_red->_($id_grupo_actuadores)."')
            ORDER BY nombre ASC";
        $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }
        if ($res_acciones->dame_numero_filas() > 0)
        {
            $eliminar_grupo = false;

            $fila_accion = $res_acciones->dame_siguiente_fila();
            $nombre_accion = $fila_accion["nombre"];
            $id_regla_accion = $fila_accion["regla"];
            $nombre_regla_accion = dame_nombre_regla($id_regla_accion);

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el grupo porque tiene acciones asignadas")."\n(".
                $idiomas->_("acción").": ".$nombre_accion.", ".
                $idiomas->_("regla").": ".$nombre_regla_accion.")";
        }
    }

    // Se elimina el grupo de actuadores
    if ($eliminar_grupo == true)
    {
        // Se recupera la información del grupo de actuadores
        $fila_grupo_actuadores = dame_fila_grupo_actuadores($id_grupo_actuadores);

        // Se elimina el grupo de actuadores
        $operacion_borrado = "
            DELETE
            FROM grupos_actuadores
            WHERE
                id = '".$bd_red->_($id_grupo_actuadores)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan las acciones enviadas del grupo de actuadores
            $operacion_borrado_acciones_enviadas = "
                DELETE
                FROM acciones_grupos_actuadores
                WHERE
                    (grupo_actuadores = '".$bd_datos->_($nombre_grupo)."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_borrado_acciones_enviadas = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_enviadas);
            if ($res_borrado_acciones_enviadas == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_acciones_enviadas."'");
            }

            // Acciones a realizar al eliminar un grupo de actuadores
            realiza_acciones_grupo_actuadores_eliminado($id_grupo_actuadores);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_grupo_actuadores($fila_grupo_actuadores);

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


    // Realiza acciones al eliminar un grupo de actuadores
    function realiza_acciones_grupo_actuadores_eliminado($id_grupo_actuadores)
    {
        // Se eliminan los widgets correspondientes
        elimina_widgets_grupo_actuadores_eliminado($id_grupo_actuadores);

        // Se modifican los elementos de plantillas de informes que contengan este grupo de actuadores (se establece a ninguno)
        modifica_elementos_plantillas_informes_grupo_actuadores_eliminado($id_grupo_actuadores);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este grupo de actuadores seleccionado en algún parámetro
        modifica_informes_automaticos_plantillas_informes_grupo_actuadores_eliminado($id_grupo_actuadores);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_grupo_actuadores_eliminado($id_grupo_actuadores);

        // Se elimina el grupo de actuadores de los parámetros del módulo Actuadores de los usuarios (si es necesario)
        elimina_actuador_grupo_parametros_modulo_actuadores_usuarios(TIPO_NODO_GRUPO_ACTUADORES, $id_grupo_actuadores);
    }


    // Añade la acción de usuario de eliminación del grupo de actuadores
    function anyade_accion_usuario_eliminar_grupo_actuadores($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_GRUPO_ACTUADORES;
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
