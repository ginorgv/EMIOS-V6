<?php
    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');


    if (FICHEROS_WEB_CONCATENADOS == true)
    {
        ?>
        <script type="text/javascript" src="./js/comun_src_v5.4.0.0_R2.js"></script>
        <?php
    }
    else
    {
        ?>
        <script type="text/javascript" src="./comun/src/lib/constantes/constantes_v5.4.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/globales/globales_v5.2.0.0.js"></script>

        <script type="text/javascript" src="./comun/src/lib/herramientas/util_cadenas_v5.4.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_criptografia_v4.0.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_html_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_matematicas_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_pantalla_v4.0.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_pie_pagina_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_sistema_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_tabla_datos_v5.4.0.0.js"></script>
        <script type="text/javascript" src="./comun/src/lib/herramientas/util_tiempos_v5.2.0.0.js"></script>

        <script type="text/javascript" src="./comun/TLNT/TLNT_v5.2.0.0_R2.js"></script>
        <script type="text/javascript" src="./comun/src/login/login_v5.0.0.0.js"></script>
        <?php
    }
?>
