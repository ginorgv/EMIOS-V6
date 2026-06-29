<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_CALCULAR_AVANCE_ESTADO_PROYECTO, $_POST);

    $idiomas = new Idiomas();

    // Parámetros
    $id_proyecto = $_POST["id_proyecto"];

    // Parámetros de la función a llamar
    $parametros_funcion_externa =
        array(
            "llamante" => "web_emios",
            "nombre" => NOMBRE_FUNCION_CALCULA_AVANCE_ESTADO_PROYECTO,
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
    else
    {
        $res = "OK";
        $msg = $idiomas->_("El proyecto se ha actualizado correctamente");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
