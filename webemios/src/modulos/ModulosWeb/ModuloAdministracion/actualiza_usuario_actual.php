<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ACTUALIZAR_USUARIO_ACTUAL, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $idioma = $_POST["idioma"];
    $tamanyo_letra = $_POST["tamanyo_letra"];
    $cadena_preferencias_modulos = $_POST["preferencias_modulos"];

    // Preferencias anteriores
    $idioma_anterior = $_SESSION["idioma"];
    $tamanyo_letra_anterior = $_SESSION["tamanyo_letra"];

    // Se establecen las preferencias del usuario en la sesión
    $_SESSION["idioma"] = $idioma;
    $_SESSION["tamanyo_letra"] = $tamanyo_letra;

    // Preferencias modificadas
    $idioma_modificado = ($idioma_anterior != $_SESSION["idioma"]);
    $tamanyo_letra_modificado = ($tamanyo_letra_anterior != $_SESSION["tamanyo_letra"]);

    // Preferencias de los módulos anteriores
    $modo_seleccion_localizacion_actual_anterior = $_SESSION["modo_seleccion_localizacion_actual"];

    // Se establecen las preferencias de los módulos del usuario en la sesión
    $preferencias_modulos = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_preferencias_modulos);
    $modo_seleccion_localizacion_actual = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_MODO_SELECCION_LOCALIZACION_ACTUAL];
    $_SESSION["modo_seleccion_localizacion_actual"] = $modo_seleccion_localizacion_actual;

    // Si el modo de selección ha cambiado, se establece la localización actual seleccionada a 'sin_localizaciones'
    if ($modo_seleccion_localizacion_actual_anterior != $modo_seleccion_localizacion_actual)
    {
        $_SESSION["id_localizacion"] = ID_DESACTIVADO;
        $_SESSION["ids_localizaciones_seleccionadas"] = array();
    }

    // Se actualiza la descripción del usuario
    $html_descripcion_usuario = dame_descripcion_usuario();

    $res = "OK";
    $msg = $idiomas->_("Usuario actual modificado correctamente");

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "idioma_modificado" => $idioma_modificado,
        "tamanyo_letra_modificado" => $tamanyo_letra_modificado,
        "html_descripcion_usuario" => $html_descripcion_usuario))
    );
?>
