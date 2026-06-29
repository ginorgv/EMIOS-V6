<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Lee el fichero de potencias máximas mensuales
    function lee_fichero_potencias_maximas_mensuales_Espanya(
        $ruta_fichero_potencias_maximas,
        $numero_tramos,
        &$potencias_maximas_mensuales,
        &$mensaje_error)
    {
        $idiomas = new Idiomas();

        // Se lee el fichero de potencias máximas
        // - Se lee la cabecera (se comprueba el número de columnas: 2 (mes y año) + número de tramos)
        // - Se lee cada una de las filas
        $fichero_potencias_maximas_correcto = true;
        $fichero_potencias_maximas = fopen($ruta_fichero_potencias_maximas, "r");
        $fila_cabecera_potencias_maximas = fgetcsv($fichero_potencias_maximas, NUMERO_MAXIMO_CARACTERES_FILA_FICHERO_CSV, SEPARADOR_COLUMNAS_FICHERO_CSV_POTENCIAS_MAXIMAS);
        if (count($fila_cabecera_potencias_maximas) != (2 + $numero_tramos))
        {
            $fichero_potencias_maximas_correcto = false;
            $mensaje_error = $idiomas->_("El número de columnas del fichero de potencias máximas es incorrecto");
        }
        else
        {
            $potencias_maximas_mensuales = array();
            $mes_anterior = NULL;
            $anyo_anterior = NULL;
            while (true)
            {
                // Se lee la siguiente fila
                $fila_potencias_maximas = fgetcsv($fichero_potencias_maximas, NUMERO_MAXIMO_CARACTERES_FILA_FICHERO_CSV, SEPARADOR_COLUMNAS_FICHERO_CSV_POTENCIAS_MAXIMAS);
                if ($fila_potencias_maximas == false)
                {
                    break;
                }

                // Si no hay elementos se pasa a la siguiente fila
                $primera_potencia_maxima = array_values($fila_potencias_maximas)[0];
                if ((count($fila_potencias_maximas) == 1) && ($primera_potencia_maxima === NULL))
                {
                    continue;
                }

                // Comprobación de número de elementos
                if (count($fila_potencias_maximas) != (2 + $numero_tramos))
                {
                    $mensaje_error = $idiomas->_("El número de valores de las filas del fichero de potencias máximas es incorrecto");
                    $fichero_potencias_maximas_correcto = false;
                    break;
                }

                // Si todos los valores de la fila están vacíos
                $numero_valores_no_vacios = count(array_filter($fila_potencias_maximas));
                if ($numero_valores_no_vacios == 0)
                {
                    continue;
                }

                // Mes y año
                $mes = $fila_potencias_maximas[0];
                $anyo = $fila_potencias_maximas[1];

                // Validación de mes y año
                // - http://stackoverflow.com/questions/2012187/how-to-check-that-a-string-is-an-int-but-not-a-double-etc
                if (($mes == NULL) || (ctype_digit($mes) == false) ||
                    ($anyo == NULL) || (ctype_digit($anyo) == false))
                {
                    $mensaje_error = $idiomas->_("Los valores del fichero de potencias máximas deben ser numéricos");
                    $fichero_potencias_maximas_correcto = false;
                    break;
                }

                // Comprobación de fechas (mes entre 1 y 12, año mayor o igual que 1970)
                // - https://es.wikipedia.org/wiki/Tiempo_Unix
                if ((($mes < 1) || ($mes > 12)) || ($anyo < 1970))
                {
                    $mensaje_error = $idiomas->_("Las fechas del fichero de potencias máximas son incorrectas");
                    $fichero_potencias_maximas_correcto = false;
                    break;
                }

                // Comprobación de fechas ascendentes
                if (($mes_anterior !== NULL) && ($anyo_anterior !== NULL))
                {
                    if (($anyo < $anyo_anterior) ||
                        (($anyo == $anyo_anterior) && ($mes <= $mes_anterior)))
                    {
                        $mensaje_error = $idiomas->_("Las fechas del fichero de potencias máximas deben ser ascendentes");
                        $fichero_potencias_maximas_correcto = false;
                        break;
                    }
                }

                // Potencias máximas por tramo
                $potencias_maximas_tramos = array();
                for ($i = 0; $i < $numero_tramos; $i++)
                {
                    array_push($potencias_maximas_tramos, $fila_potencias_maximas[2 + $i]);
                }

                // Validación de las potencias
                foreach ($potencias_maximas_tramos as $potencia_maxima_tramo)
                {
                    if (($potencia_maxima_tramo == NULL) || (is_numeric($potencia_maxima_tramo) == false))
                    {
                        $mensaje_error = $idiomas->_("Los valores del fichero de potencias máximas deben ser numéricos");
                        $fichero_potencias_maximas_correcto = false;
                        break;
                    }
                    else
                    {
                        if ($potencia_maxima_tramo < 0)
                        {
                            $mensaje_error = $idiomas->_("Los valores de potencias máximas deben ser mayores o iguales que 0");
                            $fichero_potencias_maximas_correcto = false;
                            break;
                        }
                    }
                }
                if ($fichero_potencias_maximas_correcto == false)
                {
                    break;
                }

                // Redondeo de las potencias
                for ($i = 0; $i < $numero_tramos; $i++)
                {
                    $potencias_maximas_tramos[$i] = round($potencias_maximas_tramos[$i]);
                }

                // Se añaden las potencias máximas mensuales
                $potencias_maximas_mes = array(
                    "mes" => $mes,
                    "anyo" => $anyo,
                    "potencias_maximas_tramos" => $potencias_maximas_tramos
                );
                array_push($potencias_maximas_mensuales, $potencias_maximas_mes);

                // Se guardan el mes y año anteriores
                $mes_anterior = $mes;
                $anyo_anterior = $anyo;
            }

            // Se cierra y se borra el fichero (es un fichero temporal)
            fclose($fichero_potencias_maximas);
            unlink($ruta_fichero_potencias_maximas);
        }

        // Se devuelve si se ha leído el fichero correctamente
        return ($fichero_potencias_maximas_correcto);
    }


    // Devuelve la tabla de potencias óptimas por tramo
    function dame_tabla_potencias_optimas_tramos_Espanya(
        $id_tabla_potencias_optimas_tramos,
        $datos_potencias_tramos,
        &$potencias_actuales_optimas)
    {
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Tabla de potencias óptimas por tramo
        $params_tabla_potencias_optimas_tramos = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_POTENCIAS_OPTIMAS_TRAMOS,
            "generar_valores_xml" => true
        );
        $tabla_potencias_optimas_tramos = new TablaDatos(
            $id_tabla_potencias_optimas_tramos,
            $idiomas->_("Potencias óptimas por tramo"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_potencias_optimas_tramos
        );

        // Se calcula primero el ahorro total
        $coste_actual_total = 0.0;
        $coste_optimo_total = 0.0;
        foreach ($datos_potencias_tramos as $datos_potencias_tramo)
        {
            $coste_actual_total += $datos_potencias_tramo["coste_potencia_actual"];
            $coste_optimo_total += $datos_potencias_tramo["coste_potencia_seleccionada"];
        }
        $ahorro_total = $coste_actual_total - $coste_optimo_total;

        // Si el ahorro total es menor o igual que 0, las potencias actuales son las óptimas
        // (si el usuario es super administrador se muestran las potencias óptimas calculadas (aunque no sean mejor que la actual))
        // (Nota: Si las potencias actuales son las óptimas, el ahorro total se establece a 0)
        $potencias_actuales_optimas = ($ahorro_total <= 0) && ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR);
        if ($potencias_actuales_optimas == true)
        {
            $ahorro_total = 0.0;
        }

        // Se añade la cabecera de la tabla (si el ahorro total es menor que 0, es que las potencias óptimas calculadas son peores que las actuales
        // y el usuario es superadministrador, se añade un asterisco a las columnas de potencias óptimas (para indicar que realmente no son las óptimas)
        if ($ahorro_total < 0)
        {
            $sufijo_cabeceras_tabla_potencias_optimas = " (*)";
        }
        $cabecera_tabla_potencias_optimas_tramos = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Potencia actual"),
            $idiomas->_("Coste actual")." (".$idiomas->_("ATR").")",
            $idiomas->_("Potencia óptima").$sufijo_cabeceras_tabla_potencias_optimas,
            $idiomas->_("Coste óptimo")." (".$idiomas->_("ATR").")".$sufijo_cabeceras_tabla_potencias_optimas,
            $idiomas->_("Diferencia de coste").$sufijo_cabeceras_tabla_potencias_optimas
        );
        $tabla_potencias_optimas_tramos->anyade_cabecera("", $cabecera_tabla_potencias_optimas_tramos);

        // Se añade el ahorro en cada tramo y se ordena por tramo
        $datos_potencias_optimas_tramos = array();
        foreach ($datos_potencias_tramos as $datos_potencias_tramo)
        {
            // Si las potencias actuales son las óptimas
            if ($potencias_actuales_optimas == true)
            {
                $datos_potencias_tramo["potencia_seleccionada"] = $datos_potencias_tramo["potencia_actual"];
                $datos_potencias_tramo["coste_potencia_seleccionada"] = $datos_potencias_tramo["coste_potencia_actual"];
                $datos_potencias_tramo["coste_potencia_contratada_seleccionada"] = $datos_potencias_tramo["coste_potencia_contratada_actual"];
            }

            $ahorro_tramo = ($datos_potencias_tramo["coste_potencia_actual"] - $datos_potencias_tramo["coste_potencia_seleccionada"]) * -1;
            $porcentaje_ahorro_tramo = dame_porcentaje_valor_referencia($datos_potencias_tramo["coste_potencia_seleccionada"], $datos_potencias_tramo["coste_potencia_actual"]);

            $datos_potencias_optimas_tramos[$datos_potencias_tramo["tramo"]] = array(
                "tramo" => $datos_potencias_tramo["tramo"],
                "potencia_actual" => $datos_potencias_tramo["potencia_actual"],
                "coste_potencia_actual" => $datos_potencias_tramo["coste_potencia_actual"],
                "coste_potencia_contratada_actual" => $datos_potencias_tramo["coste_potencia_contratada_actual"],
                "potencia_optima" => $datos_potencias_tramo["potencia_seleccionada"],
                "coste_potencia_optima" => $datos_potencias_tramo["coste_potencia_seleccionada"],
                "coste_potencia_contratada_optima" => $datos_potencias_tramo["coste_potencia_contratada_seleccionada"],
                "ahorro" => $ahorro_tramo,
                "porcentaje_ahorro" => $porcentaje_ahorro_tramo);
        }
        ksort($datos_potencias_optimas_tramos);

        // Se añaden datos de tramos ordenados a las filas de la tabla
        foreach ($datos_potencias_optimas_tramos as $datos_potencias_optimas_tramo)
        {
            if ($datos_potencias_optimas_tramo["coste_potencia_actual"] == $datos_potencias_optimas_tramo["coste_potencia_optima"])
            {
                $imagen_porcentaje = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
                $signo_porcentaje = "";
            }
            else
            {
                if ($datos_potencias_optimas_tramo["coste_potencia_actual"] < $datos_potencias_optimas_tramo["coste_potencia_optima"])
                {
                    $imagen_porcentaje = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje = "+";
                }
                else
                {
                    $imagen_porcentaje = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje = "-";
                }
            }

            // Se crean los datos para la fila de la tabla y se añade
            $cadena_tramo = "P".$datos_potencias_optimas_tramo["tramo"];
            $cadena_potencia_actual = $datos_potencias_optimas_tramo["potencia_actual"]." ".$idiomas->_("kW");
            $cadena_coste_actual = formatea_numero($datos_potencias_optimas_tramo["coste_potencia_actual"], 2, false)." ".$unidad_medida_coste.
                " (".formatea_numero($datos_potencias_optimas_tramo["coste_potencia_contratada_actual"], 2, false)." ".$unidad_medida_coste.")";
            $cadena_potencia_optima = $datos_potencias_optimas_tramo["potencia_optima"]." ".$idiomas->_("kW");
            $cadena_coste_optimo = formatea_numero($datos_potencias_optimas_tramo["coste_potencia_optima"], 2, false)." ".$unidad_medida_coste.
                " (".formatea_numero($datos_potencias_optimas_tramo["coste_potencia_contratada_optima"], 2, false)." ".$unidad_medida_coste.")";
            $cadena_diferencia_coste = $imagen_porcentaje." ".formatea_numero($datos_potencias_optimas_tramo["ahorro"], 2, false)." ".$unidad_medida_coste.
                " (".$signo_porcentaje.$datos_potencias_optimas_tramo["porcentaje_ahorro"]." "."%".")";
            $datos_fila_potencias_optimas_tramo = array(
                $cadena_tramo,
                $cadena_potencia_actual,
                $cadena_coste_actual,
                $cadena_potencia_optima,
                $cadena_coste_optimo,
                $cadena_diferencia_coste);
            $tabla_potencias_optimas_tramos->anyade_fila("", $datos_fila_potencias_optimas_tramo);
        }

        // Pie de tabla
        if ($potencias_actuales_optimas == true)
        {
            $tabla_potencias_optimas_tramos->anyade_pie($idiomas->_("Las potencias actuales son las potencias óptimas"));
        }
        else
        {
            $porcentaje_ahorro_total = dame_porcentaje_valor_referencia($coste_optimo_total, $coste_actual_total);
            $cadena_porcentaje_ahorro_total = formatea_numero($porcentaje_ahorro_total, 2);
            $cadena_coste_actual_total = formatea_numero($coste_actual_total, 2, false);
            $cadena_coste_optimo_total = formatea_numero($coste_optimo_total, 2, false);

            $pie_tabla = $idiomas->_("Ahorro total").": ".formatea_numero($ahorro_total, 2)." ".$unidad_medida_coste." (".$cadena_porcentaje_ahorro_total." "."%".")";
            $pie_tabla .= " (".$idiomas->_("coste actual total").": ".$cadena_coste_actual_total." ".$unidad_medida_coste.", ".
                $idiomas->_("coste óptimo total").": ".$cadena_coste_optimo_total." ".$unidad_medida_coste.")";
            $tabla_potencias_optimas_tramos->anyade_pie($pie_tabla);
        }

        // Se devuelve la tabla
        return ($tabla_potencias_optimas_tramos);
    }


    // Devuelve la tabla de potencias seleccionadas por tramo
    function dame_tabla_potencias_seleccionadas_tramos_Espanya($id_tabla_potencias_seleccionadas_tramos, $datos_potencias_tramos)
    {
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Tabla de potencias seleccionadas por tramo
        $params_tabla_potencias_seleccionadas_tramos = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_POTENCIAS_SELECCIONADAS_TRAMOS,
            "generar_valores_xml" => true
        );
        $tabla_potencias_seleccionadas_tramos = new TablaDatos(
            $id_tabla_potencias_seleccionadas_tramos,
            $idiomas->_("Potencias seleccionadas por tramo"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_potencias_seleccionadas_tramos
        );
        $cabecera_tabla_potencias_seleccionadas_tramos = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Potencia actual"),
            $idiomas->_("Coste actual"),
            $idiomas->_("Potencia seleccionada"),
            $idiomas->_("Coste simulado"),
            $idiomas->_("Diferencia de coste")
        );
        $tabla_potencias_seleccionadas_tramos->anyade_cabecera("", $cabecera_tabla_potencias_seleccionadas_tramos);

        // Se calcula primero el ahorro total
        $coste_actual_total = 0.0;
        $coste_seleccionado_total = 0.0;
        foreach ($datos_potencias_tramos as $datos_potencias_tramo)
        {
            $coste_actual_total += $datos_potencias_tramo["coste_potencia_actual"];
            $coste_seleccionado_total += $datos_potencias_tramo["coste_potencia_seleccionada"];
        }
        $ahorro_total = $coste_actual_total - $coste_seleccionado_total;

        // Se ordena por tramo
        $datos_potencias_seleccionadas_tramos = array();
        foreach ($datos_potencias_tramos as $datos_potencias_tramo)
        {
            $ahorro_tramo = ($datos_potencias_tramo["coste_potencia_actual"] - $datos_potencias_tramo["coste_potencia_seleccionada"]) * -1;
            $porcentaje_ahorro_tramo = dame_porcentaje_valor_referencia($datos_potencias_tramo["coste_potencia_seleccionada"], $datos_potencias_tramo["coste_potencia_actual"]);

            $datos_potencias_seleccionadas_tramos[$datos_potencias_tramo["tramo"]] = array(
                "tramo" => $datos_potencias_tramo["tramo"],
                "potencia_actual" => $datos_potencias_tramo["potencia_actual"],
                "coste_potencia_actual" => $datos_potencias_tramo["coste_potencia_actual"],
                "coste_potencia_contratada_actual" => $datos_potencias_tramo["coste_potencia_contratada_actual"],
                "potencia_seleccionada" => $datos_potencias_tramo["potencia_seleccionada"],
                "coste_potencia_seleccionada" => $datos_potencias_tramo["coste_potencia_seleccionada"],
                "coste_potencia_contratada_seleccionada" => $datos_potencias_tramo["coste_potencia_contratada_seleccionada"],
                "ahorro" => $ahorro_tramo,
                "porcentaje_ahorro" => $porcentaje_ahorro_tramo);
        }
        ksort($datos_potencias_seleccionadas_tramos);

        // Se añaden datos de tramos ordenados a las filas de la tabla
        foreach ($datos_potencias_seleccionadas_tramos as $datos_potencias_seleccionadas_tramo)
        {
            if ($datos_potencias_seleccionadas_tramo["coste_potencia_actual"] == $datos_potencias_seleccionadas_tramo["coste_potencia_seleccionada"])
            {
                $imagen_porcentaje = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
                $signo_porcentaje = "";
            }
            else
            {
                if ($datos_potencias_seleccionadas_tramo["coste_potencia_actual"] < $datos_potencias_seleccionadas_tramo["coste_potencia_seleccionada"])
                {
                    $imagen_porcentaje = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje = "+";
                }
                else
                {
                    $imagen_porcentaje = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje = "-";
                }
            }

            // Se crean los datos para la fila de la tabla y se añade
            $datos_fila_potencias_seleccionadas_tramo = array(
                "P".$datos_potencias_seleccionadas_tramo["tramo"],
                $datos_potencias_seleccionadas_tramo["potencia_actual"]." ".$idiomas->_("kW"),
                formatea_numero($datos_potencias_seleccionadas_tramo["coste_potencia_actual"], 2, false)." ".$unidad_medida_coste.
                    " (".formatea_numero($datos_potencias_seleccionadas_tramo["coste_potencia_contratada_actual"], 2, false)." ".$unidad_medida_coste.")",
                $datos_potencias_seleccionadas_tramo["potencia_seleccionada"]." ".$idiomas->_("kW"),
                formatea_numero($datos_potencias_seleccionadas_tramo["coste_potencia_seleccionada"], 2, false)." ".$unidad_medida_coste.
                    " (".formatea_numero($datos_potencias_seleccionadas_tramo["coste_potencia_contratada_seleccionada"], 2, false)." ".$unidad_medida_coste.")",
                $imagen_porcentaje." ".formatea_numero($datos_potencias_seleccionadas_tramo["ahorro"], 2, false)." ".$unidad_medida_coste." (".$signo_porcentaje.$datos_potencias_seleccionadas_tramo["porcentaje_ahorro"]." "."%".")");
            $tabla_potencias_seleccionadas_tramos->anyade_fila("", $datos_fila_potencias_seleccionadas_tramo);
        }

        // Pie de tabla
        $porcentaje_ahorro_total = dame_porcentaje_valor_referencia($coste_seleccionado_total, $coste_actual_total);
        $cadena_porcentaje_ahorro_total = formatea_numero($porcentaje_ahorro_total, 2);
        $cadena_coste_actual_total = formatea_numero($coste_actual_total, 2, false);
        $cadena_coste_seleccionado_total = formatea_numero($coste_seleccionado_total, 2, false);

        $signo_porcentaje = "";
        if ($ahorro_total < 0)
        {
            $signo_porcentaje = "-";
        }
        $pie_tabla = $idiomas->_("Ahorro total").": ".formatea_numero($ahorro_total, 2)." ".$unidad_medida_coste." (".$signo_porcentaje.$cadena_porcentaje_ahorro_total." "."%".")";
        $pie_tabla .= " (".$idiomas->_("coste actual total").": ".$cadena_coste_actual_total." ".$unidad_medida_coste.", ".
            $idiomas->_("coste seleccionado total").": ".$cadena_coste_seleccionado_total." ".$unidad_medida_coste.")";
        $tabla_potencias_seleccionadas_tramos->anyade_pie($pie_tabla);

        // Se devuelve la tabla
        return ($tabla_potencias_seleccionadas_tramos);
    }


    // Devuelve el porcentaje de rango de potencias por tramo para los optimizadores y simuladores de potencias
    function dame_porcentaje_rango_potencias_tramo_optimizador_simulador_potencias_Espanya($rango_potencias)
    {
        switch ($rango_potencias)
        {
            case RANGO_POTENCIAS_MAXIMO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MAXIMO;
                break;
            }
            case RANGO_POTENCIAS_MEDIO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MEDIO;
                break;
            }
            case RANGO_POTENCIAS_MINIMO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_TRAMO_MINIMO;
                break;
            }
        }
        return ($porcentaje_rango_potencias);
    }


    // Devuelve el porcentaje de rango de potencias de la potencia óptima del tramo contiguo por tramo para los optimizadores de potencias
    function dame_porcentaje_rango_potencias_potencia_optima_tramo_contiguo_optimizador_potencias_Espanya($rango_potencias)
    {
        switch ($rango_potencias)
        {
            case RANGO_POTENCIAS_MAXIMO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MAXIMO;
                break;
            }
            case RANGO_POTENCIAS_MEDIO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MEDIO;
                break;
            }
            case RANGO_POTENCIAS_MINIMO:
            {
                $porcentaje_rango_potencias = PORCENTAJE_RANGO_OPTIMIZADOR_POTENCIAS_POTENCIA_OPTIMA_TRAMO_CONTIGUO_MINIMO;
                break;
            }
        }
        return ($porcentaje_rango_potencias);
    }


    // Devuelve el paso máximo de las potencias
    function dame_paso_maximo_potencias_tramos_Espanya($datos_potencias_tramos)
    {
        $paso_maximo_potencias_tramos = 1;
        foreach ($datos_potencias_tramos as $datos_potencias_tramo)
        {
            $potencias_costes_tramo = $datos_potencias_tramo["potencias_costes"];
            if (count($potencias_costes_tramo) > 1)
            {
                $paso_potencias_tramo = $potencias_costes_tramo[1][0] - $potencias_costes_tramo[0][0];
                if ($paso_potencias_tramo > $paso_maximo_potencias_tramos)
                {
                    $paso_maximo_potencias_tramos = $paso_potencias_tramo;
                }
            }
        }
        return ($paso_maximo_potencias_tramos);
    }
?>
