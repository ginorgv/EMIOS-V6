<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Se recupera el texto del pie de página
    function dame_texto_pie_pagina()
    {
        // Si hay título de web se muestra el título configurado
        $texto_pie_pagina = "<a class='texto-fijo-pie-pagina elemento-no-seleccionable'>";
        if ($_SESSION["titulo_web"] != "")
        {
            $titulo_web = $_SESSION["titulo_web"];
        }
        else
        {
            $titulo_web = TITULO_WEB;
        }
        $texto_pie_pagina .= $titulo_web." v".VERSION_WEB." ".strToLower(NUMERO_LIBERACION_WEB)."</a>";
        return ($texto_pie_pagina);
    }
?>
