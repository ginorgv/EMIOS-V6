<?php
	session_start();


    // Devuelve el código HTML de una cadena de texto con la clase CSS especificada
    function dame_html_cadena_clase_css($cadena, $clase_css)
    {
        if ($clase_css === NULL)
        {
            $html_cadena = $cadena;
        }
        else
        {
            $html_cadena = "<span class='".$clase_css."'>".$cadena."</span>";
        }
        return ($html_cadena);
    }
?>
