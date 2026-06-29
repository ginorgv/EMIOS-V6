<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    //
    // Funciones de parámetros de los informes
    //


    // Devuelve el HTML del parámetro horario semanal para un informe
    function dame_html_parametro_horario_semanal_informe_fichero($titulo, $cadena_horario_semanal)
    {
        $idiomas = new Idiomas();

        $codigo_html = "";
        $horario_semanal = dame_horario_semanal($cadena_horario_semanal);
        if ($horario_semanal !== NULL)
        {
            $dias_semana_seleccionados = $horario_semanal["selecciones_dias_semana"];
            $periodos_dias_semana = $horario_semanal["periodos_dias_semana"];
            $dias_semana = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo");
            $nombres_dias_semana = array(
                $idiomas->_("Lunes"),
                $idiomas->_("Martes"),
                $idiomas->_("Miércoles"),
                $idiomas->_("Jueves"),
                $idiomas->_("Viernes"),
                $idiomas->_("Sábado"),
                $idiomas->_("Domingo"));
            $numero_dias_seleccionados = 0;
            $horas_modificadas = false;
            $algun_dia_semana_seleccionado = false;
            for ($i = 0; $i < count($dias_semana); $i++)
            {
                if ($dias_semana_seleccionados[$i] == true)
                {
                    $numero_dias_seleccionados += 1;
                    if ($algun_dia_semana_seleccionado == false)
                    {
                        $algun_dia_semana_seleccionado = true;
                        $texto_horario_semanal = $titulo;
                    }
                    else
                    {
                        $texto_horario_semanal = "";
                    }
                    $codigo_html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_horario_semanal."</b></td><td class='contenido-parametro-informe-fichero'>• ";
                    $codigo_html .= $nombres_dias_semana[$i]." (";
                    $periodos_dia_semana = $periodos_dias_semana[$i];
                    if ((count($periodos_dia_semana) == 0) ||
                        (($periodos_dia_semana[0][0] == "00:00:00") && ($periodos_dia_semana[0][1] == "23:59:59")))
                    {
                    }
                    else
                    {
                        $horas_modificadas = true;
                    }
                    for ($j = 0; $j < count($periodos_dia_semana); $j++)
                    {
                        if ($j > 0)
                        {
                            $codigo_html .= ", ";
                        }
                        $inicio_periodo_dia_semana = explode(":", $periodos_dia_semana[$j][0]);
                        $fin_periodo_dia_semana = explode(":", $periodos_dia_semana[$j][1]);
                        $cadena_inicio_periodo_dia_semana = $inicio_periodo_dia_semana[0].":".$inicio_periodo_dia_semana[1];
                        $cadena_fin_periodo_dia_semana = $fin_periodo_dia_semana[0].":".$fin_periodo_dia_semana[1];
                        $codigo_html .= $cadena_inicio_periodo_dia_semana." - ".$cadena_fin_periodo_dia_semana;
                    }
                    $codigo_html .= ")"."</td></tr>";
                }
            }
            if (($numero_dias_seleccionados == 7) && ($horas_modificadas == false))
            {
                $codigo_html = "";
            }
        }
        return ($codigo_html);
    }


    // Devuelve el HTML de fechas de un informe
    function dame_html_parametro_fechas_informe_fichero($titulo, $cadena_fechas)
    {
        $codigo_html = "";
        $fechas = dame_fechas($cadena_fechas);
        if ($fechas !== NULL)
        {
            $seleccion = $fechas["seleccion"];
            if ($seleccion == VALOR_SI)
            {
                $periodos_fechas = $fechas["periodos_fechas"];
                $periodos_dias_anyo = $fechas["periodos_dias_anyo"];
                if ((count($periodos_fechas) > 0) || (count($periodos_dias_anyo) > 0))
                {
                    $codigo_html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$titulo.":</b></td><td class='contenido-parametro-informe-fichero'>";
                    for ($i = 0; $i < count($periodos_fechas); $i++)
                    {
                        if ($i > 0)
                        {
                            $codigo_html .= ", ";
                        }
                        $cadena_inicio_periodo_base_datos = $periodos_fechas[$i][0];
                        $cadena_fin_periodo_base_datos = $periodos_fechas[$i][1];
                        if (count(explode(" ", $cadena_inicio_periodo_base_datos)) == 1)
                        {
                            $formato_inicio_fin_periodo_base_datos = FORMATO_FECHA_BASE_DATOS;
                            $formato_inicio_fin_periodo_local = $_SESSION["formato_fecha_local"];
                        }
                        else
                        {
                            $formato_inicio_fin_periodo_base_datos = FORMATO_FECHA_BASE_DATOS." ".FORMATO_HORA_SIN_SEGUNDOS;
                            $formato_inicio_fin_periodo_local = $_SESSION["formato_fecha_local"]." ".FORMATO_HORA_SIN_SEGUNDOS;
                        }
                        if ($cadena_inicio_periodo_base_datos == $cadena_fin_periodo_base_datos)
                        {
                            $cadena_inicio_periodo_local = convierte_formato_fecha($cadena_inicio_periodo_base_datos, $formato_inicio_fin_periodo_base_datos, $formato_inicio_fin_periodo_local);
                            $codigo_html .= $cadena_inicio_periodo_local;
                        }
                        else
                        {
                            $cadena_inicio_periodo_local = convierte_formato_fecha($cadena_inicio_periodo_base_datos, $formato_inicio_fin_periodo_base_datos, $formato_inicio_fin_periodo_local);
                            $cadena_fin_periodo_local = convierte_formato_fecha($cadena_fin_periodo_base_datos, $formato_inicio_fin_periodo_base_datos, $formato_inicio_fin_periodo_local);
                            $codigo_html .= $cadena_inicio_periodo_local." - ".$cadena_fin_periodo_local;
                        }
                    }
                    for ($i = 0; $i < count($periodos_dias_anyo); $i++)
                    {
                        if (($i > 0) || (count($periodos_fechas) > 0))
                        {
                            $codigo_html .= ", ";
                        }
                        $cadena_inicio_periodo_base_datos = $periodos_dias_anyo[$i][0];
                        $cadena_fin_periodo_base_datos = $periodos_dias_anyo[$i][1];
                        if ($cadena_inicio_periodo_base_datos == $cadena_fin_periodo_base_datos)
                        {
                            $cadena_inicio_periodo_local = convierte_formato_dia_anyo($cadena_inicio_periodo_base_datos, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                            $codigo_html .= $cadena_inicio_periodo_local;
                        }
                        else
                        {
                            $cadena_inicio_periodo_local = convierte_formato_dia_anyo($cadena_inicio_periodo_base_datos, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                            $cadena_fin_periodo_local = convierte_formato_dia_anyo($cadena_fin_periodo_base_datos, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                            $codigo_html .= $cadena_inicio_periodo_local." - ".$cadena_fin_periodo_local;
                        }
                    }
                    $codigo_html .= "</td></tr>";
                }
            }
        }
        return ($codigo_html);
    }


    // Devuelve el HTML de agrupaciones de días de la semana de un informe
    function dame_html_parametro_agrupaciones_dias_semana_informe_fichero($titulo, $cadena_agrupaciones_dias_semana)
    {
        $codigo_html = "";
        if ($cadena_agrupaciones_dias_semana != "")
        {
            $cadena_agrupaciones_dias_semana = str_replace(" ", "", $cadena_agrupaciones_dias_semana);
            $cadena_agrupaciones_dias_semana = str_replace(SEPARADOR_PARAMETROS_SIMPLES, SEPARADOR_PARAMETROS_SIMPLES." ", $cadena_agrupaciones_dias_semana);
            $codigo_html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$titulo.":</b></td><td class='contenido-parametro-informe-fichero'>".
                $cadena_agrupaciones_dias_semana."</td></tr>";
        }
        return ($codigo_html);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero($titulo_informe, $traducir_titulo_informe)
    {
        $idiomas = new Idiomas();

        if (isset($_SESSION["ruta_logo_pdf"]))
        {
            $ruta_logo_pdf = $_SESSION["ruta_logo_pdf"];
        }
        else
        {
            if (isset($_SESSION["ruta_logo"]))
            {
                $ruta_logo_pdf = $_SESSION["ruta_logo"];
            }
            else
            {
                $ruta_logo_pdf = "./rsc/imagenes/logo_web_pdf.png";
            }
        }
        if (isset($_SESSION["titulo_informe"]))
        {
            $titulo_informe = $_SESSION["titulo_informe"];
        }
        else
        {
            if ($traducir_titulo_informe == true)
            {
                $titulo_informe = $idiomas->_($titulo_informe);
            }
        }
        $html = "
            <div class='cabecera-informe-fichero'>
                <table class='tabla-cabecera-informe-fichero'>
                    <tr>
                        <td class='logo-cabecera-informe-fichero' align='left'><img src='".$ruta_logo_pdf."' class='imagen-logo-cabecera-informe-fichero' /></td>
                        <td class='titulo-cabecera-informe-fichero' align='right'><span class='texto-titulo-cabecera-informe-fichero'>".$titulo_informe."</span></td>
                    </tr>
                </table>
            </div>";
        return ($html);
    }
?>