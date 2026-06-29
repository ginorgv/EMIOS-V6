<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/OperacionDatosSensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLAS_OPERACIONES_DATOS_SENSORES, $_POST);

    $modulo = $_POST["modulo"];
    $actualizacion_periodica_activada = $_POST["actualizacion_periodica_activada"];
    $html_tabla_operaciones_datos_sensores = OperacionDatosSensor::dame_tabla_operaciones_datos_sensores($modulo, $actualizacion_periodica_activada);
    $html_tabla_importaciones_valores_sensores_pendientes = ImportacionValoresSensorPendiente::dame_tabla_importaciones_pendientes($modulo);
    $html_tabla_recalculos_valores_clase_horarios = dame_tabla_recalculos_valores_clase($modulo, GRANULARIDAD_HORARIA);
    $html_tabla_recalculos_valores_clase_cuartohorarios = dame_tabla_recalculos_valores_clase($modulo, GRANULARIDAD_CUARTOHORARIA);
    $html_tabla_sensores_procesado_valores_antiguos_horarios = dame_tabla_sensores_procesado_valores_antiguos($modulo, GRANULARIDAD_HORARIA);
    $html_tabla_sensores_procesado_valores_antiguos_cuartohorarios = dame_tabla_sensores_procesado_valores_antiguos($modulo, GRANULARIDAD_CUARTOHORARIA);

	print(json_encode(array(
        "res" => "OK",
        "html_tabla_operaciones_datos_sensores" => $html_tabla_operaciones_datos_sensores,
        "html_tabla_importaciones_valores_sensores_pendientes" => $html_tabla_importaciones_valores_sensores_pendientes,
        "html_tabla_recalculos_valores_clase_horarios" => $html_tabla_recalculos_valores_clase_horarios,
        "html_tabla_recalculos_valores_clase_cuartohorarios" => $html_tabla_recalculos_valores_clase_cuartohorarios,
        "html_tabla_sensores_procesado_valores_antiguos_horarios" => $html_tabla_sensores_procesado_valores_antiguos_horarios,
        "html_tabla_sensores_procesado_valores_antiguos_cuartohorarios" => $html_tabla_sensores_procesado_valores_antiguos_cuartohorarios))
    );
?>
