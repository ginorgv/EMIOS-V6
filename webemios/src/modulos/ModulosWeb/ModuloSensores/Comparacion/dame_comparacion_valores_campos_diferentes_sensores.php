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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_informes_comparacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COMPARACION_CAMPOS_DIFERENTES_SENSORES, $_POST);

    // Se recuperan los datos de comparación de campos diferentes
    $parametros = $_POST;
    $resultado = dame_comparacion_valores_campos_diferentes_sensores($parametros);
    print(json_encode($resultado));
?>