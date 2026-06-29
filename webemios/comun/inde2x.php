<?php
    include_once('comprueba_tipo_peticion_http.php');
	session_start();


    // Se guarda el directorio actual (compatible Windows/Linux)
    $_SESSION["directorio"] = dirname(__DIR__);

    // Si no se ha hecho login se muestra la ventana de login
    if (!isset($_SESSION["id_usuario"]))
    {
		header('Location: login.php');
        exit();
	}

    // Si no hay id de sesión se muestra la ventana de login
    if (!isset($_GET["sesion"]))
	{
		header('Location: login.php');
        exit();
	}

    // Se cargan los idiomas
    $cadenas_idiomas_comun = json_decode(file_get_contents($_SESSION["directorio"].'/comun/rsc/idiomas/idiomas.json'));
    $cadenas_idiomas_web = json_decode(file_get_contents($_SESSION["directorio"].'/rsc/idiomas/idiomas.json'));
    $_SESSION["cadenas_idiomas"] = (object) array_merge((array) $cadenas_idiomas_comun, (array) $cadenas_idiomas_web);

    // Se realizan las acciones de inicialización específicas antes de la carga de la página inicial
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_excepciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');

    try
    {
        if (function_exists('realiza_acciones_extra_antes_carga_pagina_inicial'))
        {
            realiza_acciones_extra_antes_carga_pagina_inicial();
        }
    }
    catch (Exception $e)
    {
        // Se añade información de la excepción en el log
        $log = dame_log();
        $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

        $mensaje_error = dame_mensaje_error_excepcion($e);
        $mensaje_error = convierte_ascii_estandar($mensaje_error);
        print($mensaje_error);
        exit();
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Monitorización y telecontrol con análisis visual de datos y motor de reglas en tiempo real">
    <base href="/">

    <link rel="shortcut icon" href="./comun/rsc/imagenes/favicon.ico" />

    <!-- Estilos -->
    <?php
        include_once($_SESSION["directorio"].'/comun/includes/estilos_librerias.php');
        include_once($_SESSION["directorio"].'/comun/includes/estilos_web.php');
        include_once($_SESSION["directorio"].'/includes/estilos_librerias.php');
        include_once($_SESSION["directorio"].'/includes/estilos_web.php');
    ?>
</head>
<body>
    <noscript>¡Tu navegador no soporta JavaScript!</noscript>

    <!-- Contenedor pagina WEB -->
	<div id='contenedor' class='container'>
		<div id='banner' class='elemento-no-seleccionable'>
            <span id='logo-web'>
                <?php
                    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');
                    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

                    if (isset($_SESSION["ruta_logo"]))
                    {
                        $ruta_logo = $_SESSION["ruta_logo"];
                        $url_logo = $_SESSION["url_logo"];
                    }
                    else
                    {
                        $ruta_logo = "./rsc/imagenes/logo_web.png";
                        $url_logo = WEB_ENERGY_MINUS;
                    }
                    $html_logo = $html_logo = '<img src="'.$ruta_logo.'">';
                    if ($url_logo != "")
                    {
                        $url_logo_http = dame_url_http($url_logo);
                        $html_logo = "<a href='".$url_logo_http."'>".$html_logo."</a>";
                    }
                    print($html_logo);
                ?>
            </span>
            <span id='descripcion-usuario'>
                <?php
                    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');

                    print(dame_descripcion_usuario());
                ?>
            </span>
        </div>

		<div id='menu-modulos' class='row-fluid elemento-no-seleccionable' hidden>
			<?php
                include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');

                $html_menu_modulos = dame_menu_modulos();
                if ($html_menu_modulos !== NULL)
                {
                    print($html_menu_modulos);
                }
                else
                {
                    print("<div id='error-menu-modulos' hidden></div>");
                }
			?>
		</div>

		<div id='contenido' hidden>
            <div id='contenedor-menu-secciones' class='elemento-no-seleccionable'>
                <div id='menu-secciones' hidden>
                </div>
            </div>
			<div id='contenedor-contenido-seccion'>
                <div id='contenido-seccion'></div>
			</div>
		</div>

        <div id='pie-pagina' class='row-fluid elemento-no-seleccionable' hidden>
            <div class='span2'>
                <p id='descripcion-perfil'>
                </p>
            </div>
            <div class='span8'>
                <p id="texto-pie-pagina">
                    <?php
                        include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_pie_pagina.php');

                        print(dame_texto_pie_pagina());
                    ?>
                </p>
            </div>
            <div class='span1'>
            </div>
            <div class='span1'>
                <img src='./comun/rsc/imagenes/pantalla_completa.png' id='boton-pantalla-completa' class='boton-pantalla-completa elemento-no-seleccionable'>
            </div>
        </div>
	</div>

	<!-- Ventanas_modales -->
	<div id='ventana_modal' data-backdrop='static' class='modal hide fade elemento-no-seleccionable' role='dialog' aria-labelledby='titulo_ventana_modal' aria-hidden='true'>
		<div class='modal-header'>
			<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
            <div id="boton_ayuda_ventana_modal" class="ayuda-ventana-modal icon-question-sign color-blanco"></div>
			<h3 id='titulo_ventana_modal'></h3>
		</div>
		<div id='contenido_modal' class='modal-body'></div>
		<div class='modal-footer'></div>
	</div>

    <!-- Tooltip general -->
    <div id='tooltip_general' class='tooltip elemento-no-seleccionable'></div>

	<!-- Fuentes -->
    <?php
        include_once($_SESSION["directorio"].'/comun/includes/fuentes_librerias.php');
        include_once($_SESSION["directorio"].'/comun/includes/fuentes_web.php');
        include_once($_SESSION["directorio"].'/includes/fuentes_librerias.php');
        include_once($_SESSION["directorio"].'/includes/fuentes_web.php');

        // La carga de contenidos sólo se puede hacer con usuario en sesión
        include_once($_SESSION["directorio"].'/includes/eventos_navegacion.php');
    ?>
</body>
</html>
