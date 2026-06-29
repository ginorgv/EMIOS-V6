<?php
	session_start();


    // Si no existe el directorio
    if (!isset($_SESSION["directorio"]))
    {
        // Se guarda el directorio
        $_SESSION["directorio"] = getcwd();
    }

    // Se cargan los idiomas
    $cadenas_idiomas_comun = json_decode(file_get_contents($_SESSION["directorio"].'/comun/rsc/idiomas/idiomas.json'));
    $cadenas_idiomas_web = json_decode(file_get_contents($_SESSION["directorio"].'/rsc/idiomas/idiomas.json'));
    $_SESSION["cadenas_idiomas"] = (object) array_merge((array) $cadenas_idiomas_comun, (array) $cadenas_idiomas_web);
?>

<!DOCTYPE HTML>
<html>
<head>
  	<meta charset="utf-8">

    <link rel="shortcut icon" href="./comun/rsc/imagenes/favicon.ico" />

    <!-- Estilos -->
    <?php
        include_once($_SESSION["directorio"].'/comun/includes/estilos_web.php');
        include_once($_SESSION["directorio"].'/includes/estilos_web.php');
    ?>
</head>
<body>
	<!-- Contenedor pagina WEB -->
	<div id='contenedor' class ='container'>
		<div id='banner' class='elemento-no-seleccionable'>
            <span id='logo-web'>
                <?php
                    $ruta_logo = "./rsc/imagenes/logo_web.png";
                    $html_logo = "<img src='".$ruta_logo."'>";
                    print($html_logo);
                ?>
            </span>
        </div>

		<div align="center">
            <div class="floatnone">
                <?php
                    $ruta_imagen_mantenimiento = "./comun/rsc/imagenes/imagen_mantenimiento.png";
                    $html_imagen_mantenimiento = "<img src='".$ruta_imagen_mantenimiento."'>";
                    print($html_imagen_mantenimiento);
                ?>
            </div>
        </div>
        <blockquote style="background-color: lightgrey; border: solid thin grey;">
            <center>
                <p>El servidor está en mantenimiento, disculpe las molestias.</p>
            </center>
        </blockquote>

        <div id='pie-pagina' class='row-fluid elemento-no-seleccionable'>
            <p id='texto-pie-pagina'>
                    <?php
                        session_start();
                        include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_pie_pagina.php');

                        print(dame_texto_pie_pagina());
                    ?>
            </p>
        </div>
	</div>
	</div>
</body>
</html>
