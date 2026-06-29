<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PROYECTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_proyecto = $_POST['id_proyecto'];

    // Flag de eliminar el proyecto
    $eliminar_proyecto = true;

    // Borrado de valores de proyecto
    if ($eliminar_proyecto == true)
    {
        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_BORRA_VALORES_REALES_SIMULADOS_PROYECTO,
                "id_proyecto" => $id_proyecto
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si los valores están bloqueados (hay alguna operación de valores de proyectos en ejecución)
        $valores_proyectos_bloqueados = $resultado_funcion_externa["valores_proyectos_bloqueados"];
        if ($valores_proyectos_bloqueados == VALOR_SI)
        {
            $eliminar_proyecto = False;

            $res = "ERROR";
            $msg = $idiomas->_("Se están actualizando el avance y el estado de proyectos, inténtelo de nuevo en unos minutos");
        }
    }

    // Se elimina el proyecto
    if ($eliminar_proyecto == true)
    {
        // Se recupera la fila del proyecto
        $fila_proyecto = dame_fila_proyecto($id_proyecto);

        // Se elimina el proyecto
        $operacion_borrado = "
            DELETE
            FROM proyectos
            WHERE
                id = '".$bd_red->_($id_proyecto)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Acciones a realizar al eliminar un proyecto
            realiza_acciones_proyecto_eliminado($id_proyecto);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_proyecto($fila_proyecto);

            $res = "OK";
            $msg = $idiomas->_("Proyecto eliminado correctamente");
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


    // Realiza acciones al eliminar un proyecto
    function realiza_acciones_proyecto_eliminado($id_proyecto)
    {
        // Se eliminan los widgets correspondientes
        elimina_widgets_proyecto_eliminado($id_proyecto);

        // Se modifican los elementos de plantillas de informes que contengan este proyecto (se establece a ninguno)
        modifica_elementos_plantillas_informes_proyecto_eliminado($id_proyecto);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este proyecto seleccionado en algún parámetro
        modifica_informes_automaticos_plantillas_informes_proyecto_eliminado($id_proyecto);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_proyecto_eliminado($id_proyecto);
    }


    // Añade la acción de usuario de eliminación del proyecto
    function anyade_accion_usuario_eliminar_proyecto($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_PROYECTO;
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
