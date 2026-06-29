<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	// Sustitución de caracteres ascii no estándar
    function convierte_ascii_estandar($cadena)
    {
        // Replace 'single curly quotes'
        $search[]  = chr(226).chr(128).chr(152);
        $replace[] = "'";
        $search[]  = chr(226).chr(128).chr(153);
        $replace[] = "'";

        // Replace 'smart double curly quotes'
        $search[]  = chr(226).chr(128).chr(156);
        $replace[] = '"';
        $search[]  = chr(226).chr(128).chr(157);
        $replace[] = '"';

        // Replace 'en dash'
        $search[]  = chr(226).chr(128).chr(147);
        $replace[] = '--';

        // Replace 'em dash'
        $search[]  = chr(226).chr(128).chr(148);
        $replace[] = '---';

        // Replace 'bullet'
        $search[]  = chr(226).chr(128).chr(162);
        $replace[] = '*';

        // Replace 'middle dot'
        $search[]  = chr(194).chr(183);
        $replace[] = '*';

        // Replace 'ellipsis with three consecutive dots'
        $search[]  = chr(226).chr(128).chr(166);
        $replace[] = '...';

        // Euros
        $search[]  = "€";
        $replace[] = 'eur';

        // Dólares
        $search[]  = "$";
        $replace[] = 'usd';

        // Libra
        $search[]  = "£";
        $replace[] = 'gbp';

        // Grados ('º' por 'o')
        $search[]  = 'º';
        $replace[] = 'o';

        // Vocales con acento
        $search[]  = 'á';
        $replace[] = 'a';

        $search[]  = 'é';
        $replace[] = 'e';

        $search[]  = 'í';
        $replace[] = 'i';

        $search[]  = 'ó';
        $replace[] = 'o';

        $search[]  = 'ú';
        $replace[] = 'u';

        $search[]  = 'Á';
        $replace[] = 'A';

        $search[]  = 'É';
        $replace[] = 'E';

        $search[]  = 'Í';
        $replace[] = 'I';

        $search[]  = 'Ó';
        $replace[] = 'O';

        $search[]  = 'Ú';
        $replace[] = 'U';

        // 'Ñ' y 'Ç'
        $search[]  = 'ñ';
        $replace[] = 'n';

        $search[]  = 'Ñ';
        $replace[] = 'N';

        $search[]  = 'ç';
        $replace[] = 'c';

        $search[]  = 'Ç';
        $replace[] = 'C';

        // Aplicar reemplazos
        $cadena = str_replace($search, $replace, $cadena);

        // Eliminar caracteres no-ASCII
        $cadena = preg_replace("/[^\x01-\x7F]/", "", $cadena);

        // Se devuelve la cadena
        return ($cadena);
    }


    // Reemplaza los saltos de línea y los tabuladores por el caracter especificado
    function reemplaza_saltos_linea_tabuladores($cadena, $caracter)
    {
        $search[]  = chr(9);
        $replace[] = $caracter;

        $search[]  = chr(10);
        $replace[] = $caracter;

        $search[]  = chr(13);
        $replace[] = $caracter;

        // Aplicar reemplazos
        $cadena = str_replace($search, $replace, $cadena);

        // Se devuelve la cadena
        return ($cadena);
    }


    // http://stackoverflow.com/questions/2368539/php-replacing-multiple-spaces-with-a-single-space
    function reemplaza_multiples_espacios_espacio($cadena)
    {
        $cadena = preg_replace('/\s+/', ' ', $cadena);
        return ($cadena);
    }


    // Reemplaza los caracteres no alfanuméricos por el caracter especificado
    // - https://spanishsolution.wordpress.com/2012/10/18/php-reemplazar-todos-los-caracteres-no-alfanumericos/
    function reemplaza_caracteres_no_alfanumericos($cadena, $caracter)
    {
        $caracteres_alfanumericos = '0-9a-zA-Z'; // juego de caracteres a conservar
        $regex = sprintf('~[^%s]++~i', $caracteres_alfanumericos); // case insensitive
        $cadena = preg_replace($regex, $caracter, $cadena);

        // Se devuelve la cadena
        return ($cadena);
    }


    // Eliminación de comillas (simples y dobles)
    function elimina_comillas($cadena)
    {
        $search[]  = '\'';
        $replace[] = '';

        $search[]  = '"';
        $replace[] = '';

        // Aplicar reemplazos
        $cadena = str_replace($search, $replace, $cadena);

        // Se devuelve la cadena
        return ($cadena);
    }


    // Decodificación de json con caracteres especiales (saltos de línea, etc.)
    // - http://stackoverflow.com/questions/12911536/json-decode-with-special-chars
    function json_decode_caracteres_especiales($json)
    {
        $json = str_replace("\n", "\\n", $json);
        $json = str_replace("\r", "", $json);

        return (json_decode($json, true));
    }


    // Elimina una subcadena de una cadena comprendida entre las cadenas inicial y final especificadas
    // - http://stackoverflow.com/questions/9389553/regex-to-find-and-replace-string-with-starting-and-ending-text-in-php
    function elimina_subcadena_inicial_final($cadena, $cadena_inicial, $cadena_final)
    {
        while (true)
        {
            $inicio_subcadena = strpos($cadena, $cadena_inicial);
            $fin_subcadena = strpos($cadena, $cadena_final);
            if (($inicio_subcadena === false) || ($fin_subcadena === false))
            {
                break;
            }
            if ($fin_subcadena <= $inicio_subcadena)
            {
                break;
            }
            $subcadena = substr($cadena, $inicio_subcadena, ($fin_subcadena - $inicio_subcadena) + strlen($cadena_final));
            $cadena = str_replace($subcadena, "", $cadena);
        }
        return ($cadena);
    }


    // Sustituye una subcadena de una cadena comprendida entre las cadenas inicial y final especificadas por el valor de la etiqueta correspondiente
    // - http://stackoverflow.com/questions/9389553/regex-to-find-and-replace-string-with-starting-and-ending-text-in-php
    function sustituye_subcadena_inicial_final_valor_etiqueta($cadena, $cadena_inicial, $cadena_final, $etiqueta)
    {
        while (true)
        {
            $inicio_subcadena = strpos($cadena, $cadena_inicial);
            $fin_subcadena = strpos($cadena, $cadena_final);
            if (($inicio_subcadena === false) || ($fin_subcadena === false))
            {
                break;
            }
            if ($fin_subcadena <= $inicio_subcadena)
            {
                break;
            }
            $subcadena = substr($cadena, $inicio_subcadena, ($fin_subcadena - $inicio_subcadena) + strlen($cadena_final));

            // https://stackoverflow.com/questions/13458133/php-parse-html-tags
            $valor_etiqueta = "";
            $documento_dom = new DOMDocument();
            $res = $documento_dom->loadHTML('<?xml version="1.0" encoding="utf-8"?>'.$subcadena);
            if ($res == true)
            {
                $elementos = $documento_dom->getElementsByTagName($etiqueta);
                if (count($elementos) > 0)
                {
                    foreach ($elementos as $elemento)
                    {
                        $valor_etiqueta .= $elemento->nodeValue;
                        break;
                    }
                }
            }
            $cadena = str_replace($subcadena, $valor_etiqueta, $cadena);
        }
        return ($cadena);
    }


    // Convierte cadenas de escape HTML en cadenas de escape diferentes para XML
    function escapeHtmlXml($cadena)
    {
        $search[]  = "&amp;";
        $replace[] = "%amp;";

        $search[]  = "&lt;";
        $replace[] = "%lt;";

        $search[]  = "&gt;";
        $replace[] = "%gt;";

        $search[]  = "&quot;";
        $replace[] = "%quot;";

        $search[]  = "&#039;";
        $replace[] = "%#039;";

        // Aplicar reemplazos
        $cadena = str_replace($search, $replace, $cadena);

        // Se devuelve la cadena
        return ($cadena);
    }


    // Devuelve el número de caracteres (de una cadena en 'UTF-8')
    // (https://stackoverflow.com/questions/11034058/strlen-and-utf-8-encoding)
    function dame_numero_caracteres($cadena)
    {
        $numero_caracteres = mb_strlen($cadena, 'UTF-8');
        return ($numero_caracteres);
    }


    // https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
    function endsWith($haystack, $needle)
    {
        return (substr($haystack, -strlen($needle)) === $needle);
    }
?>
