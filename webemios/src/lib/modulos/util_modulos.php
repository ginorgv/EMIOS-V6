<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Licencias/Licencia.php');


	//
    // Funciones de filtros
    //


    // Crea la selección de una fecha con un botón
    function dame_filtro_fecha_informe(
        $id_controles,
        $horas,
        $periodo,
        $opciones,
        $botones_extra)
    {
        $idiomas = new Idiomas();

        $control_fecha = dame_control_fecha(
            $id_controles,
            $idiomas->_("Fecha"),
            $horas,
            $periodo);
        $boton_ver_informe = dame_boton_formulario($id_controles."_ver_informe", $idiomas->_("Ver informe"));

        $controles = array($control_fecha);
        foreach ($opciones as $opcion)
        {
            array_push($controles, $opcion);
        }
        array_push($controles, $boton_ver_informe);
        foreach ($botones_extra as $boton_extra)
        {
            array_push($controles, $boton_extra);
        }
        return ($controles);
    }


    // Crea la selección de dos fechas (inicio y fin) con un botón
    function dame_filtro_fechas_informe(
        $id_controles,
        $hora_inicio,
        $hora_fin,
        $periodo,
        $opciones,
        $botones_extra)
    {
        $idiomas = new Idiomas();

        $control_fecha_inicio = dame_control_fecha_inicio(
            $id_controles,
            $idiomas->_("Inicio"),
            $hora_inicio,
            $periodo);
        $control_fecha_fin = dame_control_fecha_fin(
            $id_controles,
            $idiomas->_("Fin"),
            $hora_fin,
            $periodo);
        $boton_ver_informe = dame_boton_formulario($id_controles."_ver_informe", $idiomas->_("Ver informe"));

        $controles = array($control_fecha_inicio, $control_fecha_fin);
        foreach ($opciones as $opcion)
        {
            array_push($controles, $opcion);
        }
        array_push($controles, $boton_ver_informe);
        foreach ($botones_extra as $boton_extra)
        {
            array_push($controles, $boton_extra);
        }
        return ($controles);
    }


    // Crea una seleccion de periodos anterior y posterior con un botón
    function dame_filtro_periodos_informe(
        $id_controles,
        $periodo,
        $numero_dias_periodo,
        $opciones,
        $botones_extra)
    {
        $idiomas = new Idiomas();

        if ($periodo != "")
        {
            // http://stackoverflow.com/questions/2094797/the-first-day-of-the-current-month-in-php-using-date-modify-as-datetime-object
            switch ($periodo)
            {
                case PERIODO_DIA_INICIO_HOY:
                {
                    $dia_posterior = new DateTime('00:00:00');
                    $dia_anterior = clone $dia_posterior;
                    $dia_anterior->modify('-1 day');
                    $fecha_inicio_anterior = date($_SESSION["formato_fecha_local"], $dia_anterior->getTimestamp());
                    $fecha_inicio_posterior = date($_SESSION["formato_fecha_local"], $dia_posterior->getTimestamp());
                    break;
                }
                case PERIODO_DIA_INICIO_SEMANA:
                {
                    $dia_posterior = new DateTime('last Monday 00:00:00');
                    $dia_anterior = clone $dia_posterior;
                    $dia_anterior->modify('-1 week');
                    $fecha_inicio_anterior = date($_SESSION["formato_fecha_local"], $dia_anterior->getTimestamp());
                    $fecha_inicio_posterior = date($_SESSION["formato_fecha_local"], $dia_posterior->getTimestamp());
                    break;
                }
                case PERIODO_DIA_INICIO_MES:
                {
                    $dia_posterior = new DateTime('first day of this month 00:00:00');
                    $dia_anterior = clone $dia_posterior;
                    $dia_anterior->modify('-1 month');
                    $fecha_inicio_anterior = date($_SESSION["formato_fecha_local"], $dia_anterior->getTimestamp());
                    $fecha_inicio_posterior = date($_SESSION["formato_fecha_local"], $dia_posterior->getTimestamp());
                    break;
                }
                default:
                {
                    $fecha_inicio_anterior = date($_SESSION["formato_fecha_local"], strtotime('-2 '.$periodo));
                    $fecha_inicio_posterior = date($_SESSION["formato_fecha_local"], strtotime('-1 '.$periodo));
                    break;
                }
            }
        }

        $inicio_periodo_anterior = "<div id='etiqueta_fecha_inicio_anterior_".$id_controles."'>".$idiomas->_("Inicio del periodo anterior").": "."</div>";
        $inicio_periodo_anterior .= "<input size='10' type='text' id='fecha_inicio_anterior_".$id_controles."' class='datepicker selector-fecha'
            readonly='readonly' value='".$fecha_inicio_anterior."'".">";
        $inicio_periodo_posterior = "<div id='etiqueta_fecha_inicio_posterior_".$id_controles."'>".$idiomas->_("Inicio del periodo posterior").": "."</div>";
        $inicio_periodo_posterior .= "<input size='10' type='text' id='fecha_inicio_posterior_".$id_controles."' class='datepicker selector-fecha'
            readonly='readonly' value='".$fecha_inicio_posterior."'".">";
        $duracion_periodo = dame_entrada_numero(
            "dias_".$id_controles,
            $idiomas->_("Duración del periodo")." (".$idiomas->_("días").")",
            $numero_dias_periodo,
            TAMANYO_CONTROL_PEQUENYO);
        $boton_ver_informe = dame_boton_formulario($id_controles."_ver_informe", $idiomas->_("Ver informe"));

        $controles = array(
            $inicio_periodo_anterior,
            $inicio_periodo_posterior,
            $duracion_periodo);
        foreach ($opciones as $opcion)
        {
            array_push($controles, $opcion);
        }
        array_push($controles, $boton_ver_informe);
        foreach ($botones_extra as $boton_extra)
        {
            array_push($controles, $boton_extra);
        }
        return ($controles);
    }


    // Crea un campo de texto y una lista de selección de clase con un botón para filtrar
    function dame_filtro_texto_clase_nodo($id_controles, $nombre_texto, $tipo_nodo)
    {
        $idiomas = new Idiomas();

        $filtro = "<div id='etiqueta_filtro_".$id_controles."'>".$nombre_texto.": "."</div>";
        $filtro .= "<input type='text' class='filtro-texto' id='filtro_".$id_controles."'>";
        $control_lista_clases = dame_control_lista_clases_nodo(
            $id_controles,
            $tipo_nodo,
            OPCIONES_EXTRA_LISTA_CLASES_TODAS,
            $idiomas->_("Clase"));
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

        $controles = array(
            $filtro,
            $boton,
            $control_lista_clases
        );
        return ($controles);
    }


    // Crea un filtro de texto y selección de fechas de inicio y fin con un botón
    function dame_filtro_texto_fechas(
        $id_controles,
        $hora_inicio,
        $hora_fin,
        $nombre_texto,
        $periodo_anterior,
        $periodo_posterior)
    {
        $idiomas = new Idiomas();

        $filtro = "<div id='etiqueta_filtro_".$id_controles."'>".$nombre_texto.": "."</div>";
        $filtro .= "<input type='text' class='filtro-texto' id='filtro_".$id_controles."'>";
        $control_fecha_inicio = dame_control_fecha_inicio(
            $id_controles,
            $idiomas->_("Inicio"),
            $hora_inicio,
            $periodo_anterior);
        $control_fecha_fin = dame_control_fecha_fin(
            $id_controles,
            $idiomas->_("Fin"),
            $hora_fin,
            $periodo_posterior);
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

        $controles = array(
            $filtro,
            $control_fecha_inicio,
            $control_fecha_fin,
            $boton
        );
        return ($controles);
    }


    // Crea un campo de texto y unos controles extra (parámetros) con un botón para filtrar
    function dame_filtro_texto_controles_extra($id_controles, $nombre_texto, $controles_extra)
    {
        $idiomas = new Idiomas();

        $filtro = "<div id='etiqueta_filtro_".$id_controles."'>".$nombre_texto.": "."</div>";
        $filtro .= "<input type='text' class='filtro-texto' id='filtro_".$id_controles."'>";
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

        $controles = array($filtro);
        array_push($controles, $boton);
        foreach ($controles_extra as $control_extra)
        {
            array_push($controles, $control_extra);
        }
        return ($controles);
    }


    //
    // Funciones para devolver controles individuales
    //


    // Devuelve el control de una fecha
    function dame_control_fecha(
        $id_controles,
        $etiqueta,
        $horas,
        $periodo)
    {
        $timestamp_fecha_defecto = strtotime("now");
        if ($periodo != "")
        {
            // http://stackoverflow.com/questions/2094797/the-first-day-of-the-current-month-in-php-using-date-modify-as-datetime-object
            switch ($periodo)
            {
                case PERIODO_DIA_INICIO_HOY:
                {
                    $dia = new DateTime('00:00:00');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_MANYANA:
                {
                    $dia = new DateTime('00:00:00');
                    $dia->modify('+1 day');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_SEMANA:
                {
                    $dia = new DateTime('last Monday 00:00:00');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_MES:
                {
                    $dia = new DateTime('first day of this month 00:00:00');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                default:
                {
                    $timestamp_fecha_defecto = strtotime('-1 '.$periodo);
                    break;
                }
            }
        }
        $cadena_fecha_defecto_local_local = date($_SESSION["formato_fecha_local"], $timestamp_fecha_defecto);

        $control_fecha = "<div id='etiqueta_fecha_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_fecha .= "<input size='10' type='text' id='fecha_".$id_controles."' class='selector-fecha datepicker'
            readonly='readonly' value='".$cadena_fecha_defecto_local_local."'>";
        if ($horas == true)
        {
            $control_fecha .= "
                <span class='bootstrap-timepicker'>
                    <input type='text' id='hora_".$id_controles."' class='selector-hora timepicker'
                        readonly='readonly' value='00:00'>
                </span>";
        }
        return ($control_fecha);
    }


    // Devuelve el control de una fecha de inicio
    function dame_control_fecha_inicio(
        $id_controles,
        $etiqueta,
        $hora_inicio,
        $periodo)
    {
        $mostrar_hora = ($hora_inicio !== NULL);
        if ($hora_inicio === NULL)
        {
            $hora_inicio = "00:00";
        }
        $timestamp_fecha_defecto = strtotime("now");
        if ($periodo != "")
        {
            // http://stackoverflow.com/questions/2094797/the-first-day-of-the-current-month-in-php-using-date-modify-as-datetime-object
            switch ($periodo)
            {
                case PERIODO_DIA_INICIO_HOY:
                {
                    $dia = new DateTime("00:00:00");
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_MANYANA_DURACION_SEMANA:
                {
                    $dia = new DateTime('00:00:00');
                    $dia->modify('+1 day');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_SEMANA:
                {
                    $dia = new DateTime("last Monday 00:00:00");
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                case PERIODO_DIA_INICIO_MES:
                {
                    $dia = new DateTime("first day of this month 00:00:00");
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
                default:
                {
                    $timestamp_fecha_defecto = strtotime('-1 '.$periodo);
                    break;
                }
            }
        }
        $cadena_fecha_defecto_local_local = date($_SESSION["formato_fecha_local"], $timestamp_fecha_defecto);

        $control_fecha_inicio = "<div id='etiqueta_fecha_inicio_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_fecha_inicio .= "<input size='10' type='text' id='fecha_inicio_".$id_controles."' class='selector-fecha datepicker'
            readonly='readonly' value='".$cadena_fecha_defecto_local_local."'>";
        if ($mostrar_hora == true)
        {
            $control_fecha_inicio .= "
                <span class='bootstrap-timepicker'>
                    <input type='text' id='hora_inicio_".$id_controles."' class='selector-hora timepicker'
                        readonly='readonly' value='".$hora_inicio."'>
                </span>";
        }
        return ($control_fecha_inicio);
    }


    // Devuelve el control de una fecha de fin
    function dame_control_fecha_fin(
        $id_controles,
        $etiqueta,
        $hora_fin,
        $periodo)
    {
        $mostrar_hora = ($hora_fin !== NULL);
        if ($hora_fin === NULL)
        {
            $hora_fin = "23:59";
        }
        $timestamp_fecha_defecto = strtotime("now");
        if ($periodo != "")
        {
            switch ($periodo)
            {
                case PERIODO_DIA_INICIO_MANYANA_DURACION_SEMANA:
                {
                    $dia = new DateTime('00:00:00');
                    $dia->modify('+7 day');
                    $timestamp_fecha_defecto = $dia->getTimestamp();
                    break;
                }
            }
        }
        $cadena_fecha_defecto_local_local = date($_SESSION["formato_fecha_local"], $timestamp_fecha_defecto);

        // Nota: Si la hora de fin no es las 23:59, se añade 1 día a la fecha de fin para que se recuperen como mínimo de todo el día
        // de la fecha de fin (y un poco del día siguiente ... hasta la hora de fin especificada)
        if ($hora_fin != "23:59")
        {
            $zona_horaria = dame_zona_horaria_local();
            $fecha_defecto_local = convierte_cadena_a_fecha($cadena_fecha_defecto_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
            $fecha_defecto_local->modify('+1 day');
            $cadena_fecha_defecto_local_local = convierte_fecha_a_cadena($fecha_defecto_local, $_SESSION["formato_fecha_local"]);
        }

        $control_fecha_fin = "<div id='etiqueta_fecha_fin_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_fecha_fin .= "<input size='10' type='text' id='fecha_fin_".$id_controles."' class='selector-fecha datepicker'
            readonly='readonly' value='".$cadena_fecha_defecto_local_local."'>";
        if ($mostrar_hora == true)
        {
            $control_fecha_fin .= "
                <span class='bootstrap-timepicker'>
                    <input type='text' id='hora_fin_".$id_controles."' class='selector-hora timepicker'
                        readonly='readonly' value='".$hora_fin."'>
                </span>";
        }
        return ($control_fecha_fin);
    }


    // Devuelve el control de botón de un formulario
    function dame_boton_formulario($id_controles, $nombre_boton, $activado = true)
    {
        $boton = "<button id='boton_".$id_controles."' class='boton-formulario btn-mini btn btn-success'";
        if ($activado == false)
        {
            $boton .= " disabled='disabled'";
        }
        $boton .= ">".$nombre_boton."</button>";
        return ($boton);
    }


    // Devuelve el control de una lista de tipos de un tipo de nodo especificado
    function dame_control_lista_tipos_nodo(
        $id_controles,
        $tipo_nodo,
        $opciones_extra,
        $etiqueta)
    {
        $control_lista_clases = "";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $control_lista_clases = dame_control_lista_tipos_sensor($id_controles, $opciones_extra, true, $etiqueta);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $control_lista_clases = dame_control_lista_tipos_actuador($id_controles, $opciones_extra, true, $etiqueta);
                break;
            }
        }
        return ($control_lista_clases);
    }


    // Devuelve el control de una lista de clases de un tipo de nodo especificado
    function dame_control_lista_clases_nodo(
        $id_controles,
        $tipo_nodo,
        $opciones_extra,
        $etiqueta)
    {
        $control_lista_clases = "";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $control_lista_clases = dame_control_lista_clases_sensor(
                    $id_controles,
                    $opciones_extra,
                    true,
                    true,
                    $etiqueta);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $control_lista_clases = dame_control_lista_clases_actuador(
                    $id_controles,
                    $opciones_extra,
                    true,
                    $etiqueta);
                break;
            }
        }
        return ($control_lista_clases);
    }


    // Devuelve el control de una lista de grupos de un tipo de nodo especificado
    function dame_control_lista_grupos_nodos($id_controles, $tipo_nodo, $etiqueta)
    {
        $control_lista_grupos = "";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $control_lista_grupos = dame_control_lista_grupos_sensores($id_controles, $etiqueta);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $control_lista_grupos = dame_control_lista_grupos_actuadores($id_controles, $etiqueta);
                break;
            }
        }
        return ($control_lista_grupos);
    }


    // Devuelve el control de una lista de estados de un tipo de nodo especificado
    function dame_control_lista_estados_nodo($id_controles, $tipo_nodo, $etiqueta)
    {
        $control_lista_estados = "";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $control_lista_estados = dame_control_lista_estados_sensor($id_controles, $etiqueta);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $control_lista_estados = dame_control_lista_estados_actuador($id_controles, $etiqueta);
                break;
            }
        }
        return ($control_lista_estados);
    }


    // Devuelve el control de una casilla para seleccionar una opción
    function dame_casilla_opcion(
        $id_controles,
        $id_casilla,
        $nombre_casilla,
        $activada)
    {
        $casilla .= "<input type='checkbox' class='alineado-texto boton-formulario' id='".$id_casilla."_".$id_controles."'";
        if ($activada == true)
        {
            $casilla .= " checked";
        }
        $casilla .= "> ".$nombre_casilla;
        return ($casilla);
    }


    // Devuelve el control de una lista con la lista especificada
    function dame_control_lista(
        $id_controles,
        $id_lista,
        $nombre_lista,
        $lista,
        $clase)
    {
        $control_lista = "
            <div id='etiqueta_".$id_lista."_".$id_controles."'>".$nombre_lista.": "."</div>
                <select id='".$id_lista."_".$id_controles."' class='".$clase."'>";
        $control_lista .= $lista;
        $control_lista .= "
                </select>";
        return ($control_lista);
    }


    // Devuelve el control de una lista con los valores especificados
    function dame_control_lista_valores(
        $id_controles,
        $id_lista,
        $nombre_lista,
        $valores,
        $valor_seleccionado,
        $clase)
    {
        $control_lista = "
            <div id='control_".$id_lista."_".$id_controles."'>
                <div id='etiqueta_".$id_lista."_".$id_controles."'>".$nombre_lista.": "."</div>
                <select id='".$id_lista."_".$id_controles."' class='".$clase."'>";
        $control_lista .= dame_lista_valores($valores, array($valor_seleccionado));
        $control_lista .= "
                </select>
            </div>";
        return ($control_lista);
    }


    // Devuelve un control para introducir un número
    function dame_entrada_numero(
        $id_controles,
        $texto_control,
        $valor_inicial,
        $tamanyo_control)
    {
        $control = "
            <div id='control_numero_".$id_controles."'>
                <div id='etiqueta_numero_".$id_controles."'>".$texto_control.": "."</div>";
        switch ($tamanyo_control)
        {
            case TAMANYO_CONTROL_PEQUENYO:
            {
                $clase = 'input-texto-informes-pequenyo';
                break;
            }
            case TAMANYO_CONTROL_MEDIANO:
            {
                $clase = 'input-texto-informes-mediano';
                break;
            }
            default:
            {
                throw new Exception("Tamaño de control incorrecto: '".$tamanyo_control."'");
            }
        }
        $control .= "<input size='5' type='text' id='numero_".$id_controles."' class='".$clase."' value='".$valor_inicial."'".">
            </div>";
        return ($control);
    }


    // Devuelve un control para introducir una cadena
    function dame_entrada_cadena(
        $id_controles,
        $texto_control,
        $cadena_inicial,
        $tamanyo_control,
        $mostrar_boton_ayuda)
    {
        $control = "
            <div id='control_cadena_".$id_controles."'>
                <div id='etiqueta_cadena_".$id_controles."'>".$texto_control.": "."</div>";
        switch ($tamanyo_control)
        {
            case TAMANYO_CONTROL_PEQUENYO:
            {
                $clase = 'input-texto-informes-pequenyo';
                break;
            }
            case TAMANYO_CONTROL_MEDIANO:
            {
                $clase = 'input-texto-informes-mediano';
                break;
            }
            case TAMANYO_CONTROL_GRANDE:
            {
                $clase = 'input-texto-informes-grande';
                break;
            }
            case TAMANYO_CONTROL_MUY_GRANDE:
            {
                $clase = 'input-texto-informes-muy-grande';
                break;
            }
            default:
            {
                throw new Exception("Tamaño de control incorrecto: '".$tamanyo_control."'");
            }
        }
        $control .= "<input size='25' type='text' id='cadena_".$id_controles."' class='".$clase."' value='".$cadena_inicial."'".">";
        if ($mostrar_boton_ayuda == true)
        {
            $control .= "
                <span id='boton_ayuda_".$id_controles."' class='clickable boton-ayuda-texto-informe'>
                    <i class='icon-question-sign color-azul icono-ayuda'></i>
                </span>";
        }
        $control .= "
            </div>";
        return ($control);
    }


    // Devuelve el control de una lista de tipos de mapas de calor para los informes de información
    function dame_control_lista_tipos_mapa_calor_informacion($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_tipos_mapa_calor_informacion = dame_control_lista_valores(
            $id_controles,
            "tipo_mapa_calor",
            $idiomas->_("Tipo de mapa de calor"),
            array(
                array(TIPO_MAPA_CALOR_NINGUNO, $idiomas->_("Ninguno")),
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
            NULL,
            "filtro-desplegable");
        return ($control_lista_tipos_mapa_calor_informacion);
    }


    // Devuelve el control de la lista de comentarios para los informes de información
    function dame_control_lista_comentarios_informes($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_opciones_mostrar_comentarios = dame_control_lista_valores(
            $id_controles,
            "comentarios",
            $idiomas->_("Comentarios"),
            array(
                array(COMENTARIOS_NINGUNO, dame_descripcion_comentarios(COMENTARIOS_NINGUNO)),
                array(COMENTARIOS_GRAFICA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA)),
                array(COMENTARIOS_GRAFICA_TABLA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA_TABLA))),
            COMENTARIOS_GRAFICA_TABLA,
            "filtro-desplegable");
        return ($control_lista_opciones_mostrar_comentarios);
    }


    //
    // Funciones de parámetros 'complejos'
    //


    // Devuelve la cadena de horario semanal
    function dame_cadena_horario_semanal($horario_semanal)
    {
        if ($horario_semanal === NULL)
        {
            return ("");
        }

        // Formato de cadena (sin espacios):
        // P.e. Lunes de 00:00 a 01:00 y 02:00 a 03:00 y martes de 00:23:59 ->
        // "1|1|0|0|0|0|0, 00:00:00-00:59:59|02:00:00-02:59:59, 00:00:00-00:23:59,,,,,"
        $cadena_selecciones_dias_semana = implode(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES, $horario_semanal["selecciones_dias_semana"]);
        $cadenas_periodos_dias_semana = array();
        for ($i = 0; $i < 7; $i++)
        {
            $cadena_periodos_dia_semana = "";
            for ($j = 0; $j < count($horario_semanal["periodos_dias_semana"][$i]); $j++)
            {
                if ($j > 0)
                {
                    $cadena_periodos_dia_semana .= SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
                }
                $periodo_dia_semana = $horario_semanal["periodos_dias_semana"][$i][$j];
                $cadena_periodos_dia_semana .= $periodo_dia_semana[0].SEPARADOR_HORAS.$periodo_dia_semana[1];
            }
            array_push($cadenas_periodos_dias_semana, $cadena_periodos_dia_semana);
        }
        $cadena_horario_semanal = $cadena_selecciones_dias_semana.SEPARADOR_PARAMETROS_SIMPLES.
            implode(SEPARADOR_PARAMETROS_SIMPLES, $cadenas_periodos_dias_semana);
        return ($cadena_horario_semanal);
    }


    // Devuelve el horario semanal
    function dame_horario_semanal($cadena_horario_semanal)
    {
        if ($cadena_horario_semanal == "")
        {
            return (NULL);
        }

        if ($cadena_horario_semanal === NULL)
        {
            throw new Exception("La cadena de horario semanal es nula");
        }
        $cadenas_elementos_horario_semanal = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_horario_semanal);
        $cadena_selecciones_dias_semana = $cadenas_elementos_horario_semanal[0];
        $selecciones_dias_semana = explode(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES, $cadena_selecciones_dias_semana);
        $selecciones_dias_semana = array_map(
            function($value) { return ((int) $value); },
            $selecciones_dias_semana
        );
        $periodos_dias_semana = array();
        for ($i = 0; $i < 7; $i++)
        {
            $periodos_dia_semana = array();
            $cadena_periodos_dia_semana = $cadenas_elementos_horario_semanal[$i + 1];
            $cadenas_periodos_dia_semana = explode(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES, $cadena_periodos_dia_semana);
            for ($j = 0; $j < count($cadenas_periodos_dia_semana); $j++)
            {
                $elementos_periodo_dia_semana = explode(SEPARADOR_HORAS, $cadenas_periodos_dia_semana[$j]);
                array_push($periodos_dia_semana, $elementos_periodo_dia_semana);
            }
            array_push($periodos_dias_semana, $periodos_dia_semana);
        }
        $horario_semanal = array(
            "correcto" => true,
            "selecciones_dias_semana" => $selecciones_dias_semana,
            "periodos_dias_semana" => $periodos_dias_semana);
        return ($horario_semanal);
    }


    // Devuelve los controles para la selección de horario semanal
    function dame_controles_horario_semanal(
        $id_controles,
        $origen_controles,
        $mostrar_horas,
        $horario_semanal)
    {
        $controles_dias_semana = array();
        for ($i = 0; $i < 7; $i++)
        {
            if ($horario_semanal === NULL)
            {
                $seleccion_dia_semana = VALOR_SI;
                $cadena_periodos_dia_semana = "00:00 - 23:59";
            }
            else
            {
                $seleccion_dia_semana = $horario_semanal["selecciones_dias_semana"][$i];
                $periodos_dia_semana = $horario_semanal["periodos_dias_semana"][$i];
                $cadena_periodos_dia_semana = "";
                for ($j = 0; $j < count($periodos_dia_semana); $j++)
                {
                    if ($j > 0)
                    {
                        $cadena_periodos_dia_semana .= ", ";
                    }
                    $periodo_dia_semana = $periodos_dia_semana[$j];
                    // Nota: Se eliminan los segundos
                    $periodo_dia_semana[0] = substr($periodo_dia_semana[0], 0, -3);
                    $periodo_dia_semana[1] = substr($periodo_dia_semana[1], 0, -3);
                    $cadena_periodos_dia_semana .= $periodo_dia_semana[0]." - ".$periodo_dia_semana[1];
                }
            }
            $controles_dia_semana = dame_controles_dia_semana(
                $id_controles,
                $origen_controles,
                dame_dia_semana_horario_semanal($i),
                dame_nombre_dia_semana_horario_semanal($i),
                $mostrar_horas,
                $seleccion_dia_semana,
                $cadena_periodos_dia_semana);
            array_push($controles_dias_semana, $controles_dia_semana);
        }
        return ($controles_dias_semana);
    }


    // Devuelve el día de la semana para el horario semanal
    function dame_dia_semana_horario_semanal($numero_dia_semana)
    {
        switch ($numero_dia_semana)
        {
            case 0:
            {
                $dia_semana = "lunes";
                break;
            }
            case 1:
            {
                $dia_semana = "martes";
                break;
            }
            case 2:
            {
                $dia_semana = "miercoles";
                break;
            }
            case 3:
            {
                $dia_semana = "jueves";
                break;
            }
            case 4:
            {
                $dia_semana = "viernes";
                break;
            }
            case 5:
            {
                $dia_semana = "sabado";
                break;
            }
            case 6:
            {
                $dia_semana = "domingo";
                break;
            }
        }
        return ($dia_semana);
    }


    // Devuelve el nombre del día de la semana para el horario semanal
    function dame_nombre_dia_semana_horario_semanal($numero_dia_semana)
    {
        switch ($numero_dia_semana)
        {
            case 0:
            {
                $nombre_dia_semana = "Lunes";
                break;
            }
            case 1:
            {
                $nombre_dia_semana = "Martes";
                break;
            }
            case 2:
            {
                $nombre_dia_semana = "Miércoles";
                break;
            }
            case 3:
            {
                $nombre_dia_semana = "Jueves";
                break;
            }
            case 4:
            {
                $nombre_dia_semana = "Viernes";
                break;
            }
            case 5:
            {
                $nombre_dia_semana = "Sábado";
                break;
            }
            case 6:
            {
                $nombre_dia_semana = "Domingo";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($nombre_dia_semana));
    }


    // Devuelve los controles para la selección de un día de la semana
    function dame_controles_dia_semana(
        $id_controles,
        $origen_controles,
        $dia_semana,
        $nombre_dia_semana,
        $mostrar_horas,
        $seleccion_dia_semana,
        $cadena_periodos_dia_semana)
    {
        switch ($origen_controles)
        {
            case ORIGEN_CONTROLES_INFORMES:
            {
                $clase_nombres_periodos_dias_semana = "nombres-periodos-dias-semana-informes";
                $clase_horas_periodos_dias_semana = "horas-periodos-dias-semana-informes";
                break;
            }
            case ORIGEN_CONTROLES_VENTANA_MODAL:
            {
                $clase_nombres_periodos_dias_semana = "nombres-periodos-dias-semana-ventana-modal";
                $clase_horas_periodos_dias_semana = "horas-periodos-dias-semana-ventana-modal";
                break;
            }
        }

        $control_dia_semana.= "
            <div class='".$clase_nombres_periodos_dias_semana."'>
                <input type='checkbox' id='".$dia_semana."_".$id_controles."'
                    class='alineado-texto'";
        if ($seleccion_dia_semana == VALOR_SI)
        {
            $control_dia_semana .= " checked";
        }
        $control_dia_semana .= ">"." ".$nombre_dia_semana."
            </div>";
        $control_dia_semana .= "
            <div class='".$clase_horas_periodos_dias_semana."'";
        if ($mostrar_horas == false)
        {
            $control_dia_semana .= " hidden";
        }
        $control_dia_semana .= ">
                <input type='text' id='periodos_".$dia_semana."_".$id_controles."'
                    class='TLNT_input_mandatory TLNT_input_valid_characters texto-horas-periodos-dias-semana' value='".$cadena_periodos_dia_semana."'>
            </div>";

        $controles_dia_semana = array();
        array_push($controles_dia_semana, $control_dia_semana);
        return ($controles_dia_semana);
    }


    // Añade los controles de selección de horario semanal a la tabla del informe correspondiente
    function anyade_controles_horario_semanal_tabla_informe(
        $id_controles,
        $tabla,
        $titulo,
        $horario_semanal)
    {
        $ids_elementos_desplegables = array();
        for ($i = 0; $i < 7; $i++)
        {
            array_push($ids_elementos_desplegables, "horario_semanal_".$id_controles."-".$i);
        }
        $tabla->anyade_cabecera_elementos_desplegables(
            "cabecera_horario_semanal_".$id_controles,
            array($titulo),
            $ids_elementos_desplegables);
        $params_fila_dia_semana = array(
            "clase_dato" => "seleccion-horario-semanal anchura100",
            "sin_borde_inferior" => true,
            "oculta" => true
        );
        $params_fila_primer_dia_semana = array(
            "clase_dato" => "seleccion-horario-semanal anchura100 margen-superior-primer-dia-semana",
            "sin_borde_inferior" => true,
            "oculta" => true
        );
        $params_fila_ultimo_dia_semana = array(
            "clase_dato" => "seleccion-horario-semanal anchura100 margen-inferior-ultimo-dia-semana",
            "oculta" => true
        );
        for ($i = 0; $i < 7; $i++)
        {
            switch ($i)
            {
                case 0:
                {
                    $tabla->anyade_fila("horario_semanal_".$id_controles."-".$i, $horario_semanal[$i], $params_fila_primer_dia_semana);
                    break;
                }
                case 6:
                {
                    $tabla->anyade_fila("horario_semanal_".$id_controles."-".$i, $horario_semanal[$i], $params_fila_ultimo_dia_semana);
                    break;
                }
                default:
                {
                    $tabla->anyade_fila("horario_semanal_".$id_controles."-".$i, $horario_semanal[$i], $params_fila_dia_semana);
                    break;
                }
            }
        }
    }


    // Añade los controles de selección de horario semanal a la ventana modal correspondiente
    function anyade_controles_horario_semanal_ventana_modal(
        &$contenido,
        $prefijo_elemento,
        $horario_semanal,
        $mostrar_horario_semanal)
    {
        $idiomas = new Idiomas();
        $id_elemento = "control_horario_semanal_".$prefijo_elemento;
        $contenido .= "
            <div class='row-fluid' id='".$id_elemento."'";
        if ($mostrar_horario_semanal == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horario semanal").": "."</span><br/>";
        for ($i = 0; $i < 7; $i++)
        {
            $contenido .= "
                    <div class='row-fluid'>";
            $contenido .= $horario_semanal[$i][0];
            $contenido .= "
                    </div>";
        }
        $contenido .= "
                </div>
            </div>";
        if ($mostrar_horario_semanal == true)
        {
            $contenido .= "<div class='separacion-fin-horario-semanal-ventana-modal'></div>";
        }
    }


    // Devuelve la descripción del horario semanal
    function dame_descripcion_horario_semanal(
        $cadena_horario_semanal,
        $mostrar_horas,
        $tipo_descripcion)
    {
        $idiomas = new Idiomas();

        // Delimitadores de descripción de parámetros
        $cadena_inicio_lista_parametros = "";
        $cadena_fin_lista_parametros = "";
        $cadena_inicio_primer_parametro = "";
        $cadena_inicio_parametro = "";
        $cadena_fin_parametro = "";
        establece_delimitadores_descripcion_parametros(
            $tipo_descripcion,
            $cadena_inicio_lista_parametros,
            $cadena_fin_lista_parametros,
            $cadena_inicio_primer_parametro,
            $cadena_inicio_parametro,
            $cadena_fin_parametro);

        // Horario semanal
        $descripcion = "";
        $horario_semanal = dame_horario_semanal($cadena_horario_semanal);
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
                    $descripcion .= $cadena_inicio_lista_parametros;
                }
                $descripcion .= $cadena_inicio_parametro.$nombres_dias_semana[$i];
                if ($mostrar_horas == true)
                {
                    $descripcion .= " (";
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
                        $inicio_periodo_dia_semana = explode(":", $periodos_dia_semana[$j][0]);
                        $fin_periodo_dia_semana = explode(":", $periodos_dia_semana[$j][1]);
                        $cadena_inicio_periodo_dia_semana = $inicio_periodo_dia_semana[0].":".$inicio_periodo_dia_semana[1];
                        $cadena_fin_periodo_dia_semana = $fin_periodo_dia_semana[0].":".$fin_periodo_dia_semana[1];
                        $descripcion .= $cadena_inicio_periodo_dia_semana." - ".$cadena_fin_periodo_dia_semana;
                        if ($j < (count($periodos_dia_semana) - 1))
                        {
                            $descripcion .= ", ";
                        }
                    }
                    $descripcion .= ")";
                }
                $descripcion .= $cadena_fin_parametro;
            }
        }
        if ($algun_dia_semana_seleccionado == true)
        {
            $descripcion .= $cadena_fin_lista_parametros;
        }

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        if ($descripcion != "")
        {
            switch ($tipo_descripcion)
            {
                case TIPO_DESCRIPCION_TEXTO:
                {
                    $descripcion = substr($descripcion, 1);
                    break;
                }
            }
        }
        if (($numero_dias_seleccionados == 7) && ($horas_modificadas == false))
        {
            $descripcion = "";
        }
        return ($descripcion);
    }


    // Devuelve la cadena de fechas
    function dame_cadena_fechas($fechas)
    {
        if ($fechas === NULL)
        {
            return ("");
        }

        // Formato de cadena (sin espacios)
        // (formatos de fecha de base de datos - para poder cambiar el formato de fecha de una red):
        // P.e. 01/01/2016 y del 06/01/2016 al 10/01/2016 (seleccionado) ->
        // "1, 2016-01-01|2016-01-06_2016-01-10"
        $cadena_seleccion = $fechas["seleccion"];
        $cadena_periodos_fechas = "";
        for ($i = 0; $i < count($fechas["periodos_fechas"]); $i++)
        {
            if ($i > 0)
            {
                $cadena_periodos_fechas .= SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
            }
            $periodo_fechas = $fechas["periodos_fechas"][$i];
            $cadena_periodo_fechas = NULL;
            if ($periodo_fechas[0] == $periodo_fechas[1])
            {
                $cadena_periodo_fechas = $periodo_fechas[0];
            }
            else
            {
                $cadena_periodo_fechas = $periodo_fechas[0].SEPARADOR_FECHAS.$periodo_fechas[1];
            }
            $cadena_periodos_fechas .= $cadena_periodo_fechas;
        }
        $cadena_periodos_dias_anyo = "";
        for ($i = 0; $i < count($fechas["periodos_dias_anyo"]); $i++)
        {
            if ($i > 0)
            {
                $cadena_periodos_dias_anyo .= SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
            }
            $periodo_dias_anyo = $fechas["periodos_dias_anyo"][$i];
            $cadena_periodo_dias_anyo = NULL;
            if ($periodo_dias_anyo[0] == $periodo_dias_anyo[1])
            {
                $cadena_periodo_dias_anyo = $periodo_dias_anyo[0];
            }
            else
            {
                $cadena_periodo_dias_anyo = $periodo_dias_anyo[0].SEPARADOR_FECHAS.$periodo_dias_anyo[1];
            }
            $cadena_periodos_dias_anyo .= $cadena_periodo_dias_anyo;
        }
        $cadena_fechas = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
            $cadena_seleccion,
            $cadena_periodos_fechas,
            $cadena_periodos_dias_anyo));
        return ($cadena_fechas);
    }


    // Devuelve las fechas
    function dame_fechas($cadena_fechas)
    {
        if ($cadena_fechas == "")
        {
            return (NULL);
        }

        if ($cadena_fechas === NULL)
        {
            throw new Exception("La cadena de fechas es nula");
        }
        $cadenas_elementos_fechas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_fechas);
        if (count($cadenas_elementos_fechas) != 3)
        {
            throw new Exception("El número de elementos de fechas es incorrecto");
        }
        $cadena_seleccion = $cadenas_elementos_fechas[0];
        $cadena_periodos_fechas = $cadenas_elementos_fechas[1];
        $cadena_periodos_dias_anyo = $cadenas_elementos_fechas[2];
        $seleccion = (int) $cadena_seleccion;
        $periodos_fechas = array();
        if ($cadena_periodos_fechas != "")
        {
            $cadenas_periodos_fechas = explode(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES, $cadena_periodos_fechas);
            for ($i = 0; $i < count($cadenas_periodos_fechas); $i++)
            {
                $elementos_periodo_fechas = explode(SEPARADOR_FECHAS, $cadenas_periodos_fechas[$i]);
                if (count($elementos_periodo_fechas) == 1)
                {
                    array_push($elementos_periodo_fechas, $elementos_periodo_fechas[0]);
                }
                array_push($periodos_fechas, $elementos_periodo_fechas);
            }
        }
        $periodos_dias_anyo = array();
        if ($cadena_periodos_dias_anyo != "")
        {
            $cadenas_periodos_dias_anyo = explode(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES, $cadena_periodos_dias_anyo);
            for ($i = 0; $i < count($cadenas_periodos_dias_anyo); $i++)
            {
                $elementos_periodo_dias_anyo = explode(SEPARADOR_FECHAS, $cadenas_periodos_dias_anyo[$i]);
                if (count($elementos_periodo_dias_anyo) == 1)
                {
                    array_push($elementos_periodo_dias_anyo, $elementos_periodo_dias_anyo[0]);
                }
                array_push($periodos_dias_anyo, $elementos_periodo_dias_anyo);
            }
        }
        $fechas = array(
            "correcto" => true,
            "seleccion" => $seleccion,
            "periodos_fechas" => $periodos_fechas,
            "periodos_dias_anyo" => $periodos_dias_anyo);
        return ($fechas);
    }


    // Devuelve los controles para las fechas
    function dame_controles_fechas($id_controles, $origen_controles, $fechas)
    {
        $idiomas = new Idiomas();

        switch ($origen_controles)
        {
            case ORIGEN_CONTROLES_INFORMES:
            {
                $clase_nombre_periodos_fechas = "nombre-periodos-fechas-informes";
                $clase_fechas_periodos_fechas = "fechas-periodos-fechas-informes";
                break;
            }
            case ORIGEN_CONTROLES_VENTANA_MODAL:
            {
                $clase_nombre_periodos_fechas = "nombre-periodos-fechas-ventana-modal";
                $clase_fechas_periodos_fechas = "fechas-periodos-fechas-ventana-modal";
                break;
            }
        }

        if ($fechas === NULL)
        {
            $seleccion = VALOR_SI;
            $cadena_periodos = "";
        }
        else
        {
            $seleccion = $fechas["seleccion"];
            $periodos_fechas = $fechas["periodos_fechas"];
            $periodos_dias_anyo = $fechas["periodos_dias_anyo"];
            $cadena_periodos = "";
            for ($i = 0; $i < count($periodos_fechas); $i++)
            {
                if ($i > 0)
                {
                    $cadena_periodos .= ", ";
                }
                $periodo_fechas = $periodos_fechas[$i];
                $cadena_inicio_periodo_base_datos = $periodo_fechas[0];
                $cadena_fin_periodo_base_datos = $periodo_fechas[1];

                $cadenas_fecha_hora_inicio_periodo_base_datos = explode(" ", $cadena_inicio_periodo_base_datos);
                $cadena_fecha_inicio_periodo_base_datos = $cadenas_fecha_hora_inicio_periodo_base_datos[0];
                $cadena_inicio_periodo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                if (count($cadenas_fecha_hora_inicio_periodo_base_datos > 1))
                {
                    $cadena_hora_inicio_periodo_local = $cadenas_fecha_hora_inicio_periodo_base_datos[1];
                    $cadena_inicio_periodo_local .= " ".$cadena_hora_inicio_periodo_local;
                }
                $cadena_periodos .= $cadena_inicio_periodo_local;
                if ($cadena_inicio_periodo_base_datos != $cadena_fin_periodo_base_datos)
                {
                    $cadenas_fecha_hora_fin_periodo_base_datos = explode(" ", $cadena_fin_periodo_base_datos);
                    $cadena_fecha_fin_periodo_base_datos = $cadenas_fecha_hora_fin_periodo_base_datos[0];
                    $cadena_fin_periodo_local = convierte_formato_fecha($cadena_fecha_fin_periodo_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                    if (count($cadenas_fecha_hora_fin_periodo_base_datos > 1))
                    {
                        $cadena_hora_fin_periodo_local = $cadenas_fecha_hora_fin_periodo_base_datos[1];
                        $cadena_fin_periodo_local .= " ".$cadena_hora_fin_periodo_local;
                    }
                    $cadena_periodos .= " - ".$cadena_fin_periodo_local;
                }
            }
            for ($i = 0; $i < count($periodos_dias_anyo); $i++)
            {
                if (($i > 0) || (count($periodos_fechas) > 0))
                {
                    $cadena_periodos .= ", ";
                }
                $periodo_dias_anyo = $periodos_dias_anyo[$i];
                $cadena_inicio_periodo_base_datos = $periodo_dias_anyo[0];
                $cadena_fin_periodo_base_datos = $periodo_dias_anyo[1];

                $cadena_inicio_periodo_local = convierte_formato_dia_anyo($cadena_inicio_periodo_base_datos, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                $cadena_periodos .= $cadena_inicio_periodo_local;
                if ($cadena_inicio_periodo_base_datos != $cadena_fin_periodo_base_datos)
                {
                    $cadena_fin_periodo_local = convierte_formato_dia_anyo($cadena_fin_periodo_base_datos, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                    $cadena_periodos .= " - ".$cadena_fin_periodo_local;
                }
            }
        }
        $control_fechas = "
            <div class='".$clase_nombre_periodos_fechas."'>
                <input type='checkbox' id='".$id_controles."' class='alineado-texto'";
        if ($seleccion == VALOR_SI)
        {
            $control_fechas .= " checked";
        }
        $control_fechas .= ">"." ".$idiomas->_("Fechas")."
            </div>

            <div class='".$clase_fechas_periodos_fechas."'>
                <input type='text' id='periodos_".$id_controles."' class='TLNT_input_valid_characters texto-fechas' value='".$cadena_periodos."'>
                <span id='boton_ayuda_".$id_controles."' class='clickable'>
                    <i class='icon-question-sign color-azul icono-ayuda'></i>
                </span>
            </div>";

        $controles_fechas = array();
        array_push($controles_fechas, $control_fechas);
        return ($controles_fechas);
    }


    // Añade los controles de fechas a la tabla del informe correspondiente
    function anyade_controles_fechas_tabla_informe(
        $id_controles,
        $tabla,
        $titulo,
        $fechas)
    {
        $tabla->anyade_cabecera_elementos_desplegables(
            "cabecera_".$id_controles,
            array($titulo),
            array($id_controles));
        $params_contenido_fechas = array(
            "clase_dato" => "seleccion-fechas anchura100 margen-superior-fechas margen-inferior-fechas",
            "oculta" => true
        );
        $tabla->anyade_fila($id_controles, $fechas, $params_contenido_fechas);
    }


    // Añade los controles de de fechas a la ventana modal correspondiente
    function anyade_controles_fechas_ventana_modal(
        &$contenido,
        $prefijo_elemento,
        $titulo,
        $fechas)
    {
        $id_elemento = "control_".$prefijo_elemento;
        $contenido .= "
            <div class='row-fluid' id='".$id_elemento."'>
                <div class='span12'><span class='titulo-campo-administracion'>".$titulo.": "."</span><br/>";
        for ($i = 0; $i < count($fechas); $i++)
        {
            $contenido .= "
                    <div class='row-fluid'>";
            $contenido .= $fechas[$i];
            $contenido .= "
                    </div>";
        }
        $contenido .= "
                </div>
            </div>";
    }


    // Devuelve la descripción de las fechas
    function dame_descripcion_fechas($cadena_fechas)
    {
        $codigo_html = "";
        $fechas = dame_fechas($cadena_fechas);
        $seleccion = $fechas["seleccion"];
        if ($seleccion == VALOR_SI)
        {
            $periodos_fechas = $fechas["periodos_fechas"];
            if (count($periodos_fechas) > 0)
            {
                for ($i = 0; $i < count($periodos_fechas); $i++)
                {
                    if ($i > 0)
                    {
                        $codigo_html .= ", ";
                    }
                    $cadena_inicio_periodo_base_datos = $periodos_fechas[$i][0];
                    $cadena_fin_periodo_base_datos = $periodos_fechas[$i][1];

                    $cadenas_fecha_hora_inicio_periodo_base_datos = explode(" ", $cadena_inicio_periodo_base_datos);
                    $cadena_fecha_inicio_periodo_base_datos = $cadenas_fecha_hora_inicio_periodo_base_datos[0];
                    $cadena_inicio_periodo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                    if (count($cadenas_fecha_hora_inicio_periodo_base_datos > 1))
                    {
                        $cadena_hora_inicio_periodo_local = $cadenas_fecha_hora_inicio_periodo_base_datos[1];
                        $cadena_inicio_periodo_local .= " ".$cadena_hora_inicio_periodo_local;
                    }
                    $codigo_html .= $cadena_inicio_periodo_local;
                    if ($cadena_inicio_periodo_base_datos != $cadena_fin_periodo_base_datos)
                    {
                        $cadenas_fecha_hora_fin_periodo_base_datos = explode(" ", $cadena_fin_periodo_base_datos);
                        $cadena_fecha_fin_periodo_base_datos = $cadenas_fecha_hora_fin_periodo_base_datos[0];
                        $cadena_fin_periodo_local = convierte_formato_fecha($cadena_fecha_fin_periodo_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                        if (count($cadenas_fecha_hora_fin_periodo_base_datos > 1))
                        {
                            $cadena_hora_fin_periodo_local = $cadenas_fecha_hora_fin_periodo_base_datos[1];
                            $cadena_fin_periodo_local .= " ".$cadena_hora_fin_periodo_local;
                        }
                        $codigo_html .= " - ".$cadena_fin_periodo_local;
                    }
                }
            }
            $periodos_dias_anyo = $fechas["periodos_dias_anyo"];
            if (count($periodos_dias_anyo) > 0)
            {
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
            }
        }
        return ($codigo_html);
    }


    // Contenido de pestaña de horario semanal, exclusión e inclusión de fechas
    function anyade_controles_pestanya_horario_semanal_fechas($sufijo_controles, $parametros, &$contenido)
    {
        $idiomas = new Idiomas();

        if (array_key_exists("horario_semanal", $parametros) == true)
        {
            $horario_semanal = $parametros["horario_semanal"];
        }
        else
        {
            $horario_semanal = NULL;
        }
        if (array_key_exists("exclusion_fechas", $parametros) == true)
        {
            $exclusion_fechas = $parametros["exclusion_fechas"];
        }
        else
        {
            $exclusion_fechas = NULL;
        }
        if (array_key_exists("inclusion_fechas", $parametros) == true)
        {
            $inclusion_fechas = $parametros["inclusion_fechas"];
        }
        else
        {
            $inclusion_fechas = NULL;
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-horario-semanal-fechas'>";

        $controles_horario_semanal = dame_controles_horario_semanal(
            $sufijo_controles,
            ORIGEN_CONTROLES_VENTANA_MODAL,
            true,
            $horario_semanal);
        anyade_controles_horario_semanal_ventana_modal(
            $contenido,
            $sufijo_controles,
            $controles_horario_semanal,
            true);
        $controles_exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_VENTANA_MODAL, $exclusion_fechas);
        anyade_controles_fechas_ventana_modal(
            $contenido,
            "exclusion_fechas_".$sufijo_controles,
            $idiomas->_("Exclusión de fechas"),
            $controles_exclusion_fechas);
        $controles_inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_VENTANA_MODAL, $inclusion_fechas);
        anyade_controles_fechas_ventana_modal(
            $contenido,
            "inclusion_fechas_".$sufijo_controles,
            $idiomas->_("Inclusión de fechas"),
            $controles_inclusion_fechas);

        $contenido .= "
                    </div>";
    }


    // Devuelve la cadena de agrupaciones de días de la semana
    function dame_cadena_agrupaciones_dias_semana($agrupaciones_dias_semana)
    {
        // Formato de cadena (sin espacios)
        // "1-2-3-4-5,6-7"
        $cadena_agrupaciones_dias_semana = "";
        for ($i = 0; $i < count($agrupaciones_dias_semana["agrupaciones_dias"]); $i++)
        {
            if ($i > 0)
            {
                $cadena_agrupaciones_dias_semana .= SEPARADOR_PARAMETROS_SIMPLES;
            }
            $agrupacion_dias = $agrupaciones_dias_semana["agrupaciones_dias"][$i];
            for ($j = 0; $j < count($agrupacion_dias); $j++)
            {
                if ($j > 0)
                {
                    $cadena_agrupaciones_dias_semana .= SEPARADOR_DIAS_SEMANA;
                }
                $cadena_agrupaciones_dias_semana .= $agrupacion_dias[$j];
            }
        }
        return ($cadena_agrupaciones_dias_semana);
    }


    // Devuelve las agrupaciones de días de la semana
    function dame_agrupaciones_dias_semana($cadena_agrupaciones_dias_semana)
    {
        $agrupaciones_dias = array();
        if ($cadena_agrupaciones_dias_semana != "")
        {
            $cadenas_agrupaciones_dias = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_agrupaciones_dias_semana);
            for ($i = 0; $i < count($cadenas_agrupaciones_dias); $i++)
            {
                $dias = explode(SEPARADOR_DIAS_SEMANA, $cadenas_agrupaciones_dias[$i]);
                array_push($agrupaciones_dias, $dias);
            }
        }
        $agrupaciones_dias_semana = array(
            "correcto" => true,
            "agrupaciones_dias" => $agrupaciones_dias);
        return ($agrupaciones_dias_semana);
    }


    // Devuelve los controles de parámetros de pie de página
    function dame_controles_parametros_pie_pagina($id_controles)
    {
        $idiomas = new Idiomas();

        $mostrar_numeros_pagina = true;
        $numero_pagina_inicial = 1;
        $mostrar_numero_paginas_totales = VALOR_SI;
        $numero_paginas_totales_automatico = VALOR_SI;
        $numero_paginas_totales_manual = 0;
        $texto_titulo_izquierda_pie_pagina = "";
        $texto_titulo_derecha_pie_pagina = "";

        $control_lista_mostrar_numeros_pagina = dame_control_lista_valores(
            $id_controles,
            "mostrar_numeros_pagina",
            $idiomas->_("Mostrar números de página"),
            array(
                array(VALOR_SI, $idiomas->_("Sí")),
                array(VALOR_NO, $idiomas->_("No"))),
            $mostrar_numeros_pagina,
            "filtro-desplegable");
        $control_numero_pagina_inicial = dame_entrada_numero(
            "numero_pagina_inicial_".$id_controles,
            $idiomas->_("Número de página inicial"),
            $numero_pagina_inicial,
            TAMANYO_CONTROL_MEDIANO);
        $control_lista_mostrar_numero_paginas_totales = dame_control_lista_valores(
            $id_controles,
            "mostrar_numero_paginas_totales",
            $idiomas->_("Mostrar número de páginas totales"),
            array(
                array(VALOR_SI, $idiomas->_("Sí")),
                array(VALOR_NO, $idiomas->_("No"))),
            $mostrar_numero_paginas_totales,
            "filtro-desplegable");
        $control_lista_numero_paginas_totales_automatico = dame_control_lista_valores(
            $id_controles,
            "numero_paginas_totales_automatico",
            $idiomas->_("Número de páginas totales automático"),
            array(
                array(VALOR_SI, $idiomas->_("Sí")),
                array(VALOR_NO, $idiomas->_("No"))),
            $numero_paginas_totales_automatico,
            "filtro-desplegable");
        $control_numero_paginas_totales_manual = dame_entrada_numero(
            "numero_paginas_totales_manual_".$id_controles,
            $idiomas->_("Número de páginas totales manual"),
            $numero_paginas_totales_manual,
            TAMANYO_CONTROL_MEDIANO);
        $control_texto_titulo_izquierda_pie_pagina = dame_entrada_cadena(
            "texto_titulo_izquierda_pie_pagina_".$id_controles,
            $idiomas->_("Texto de título (izquierda)"),
            $texto_titulo_izquierda_pie_pagina,
            TAMANYO_CONTROL_MUY_GRANDE,
            false);
        $control_texto_titulo_derecha_pie_pagina = dame_entrada_cadena(
            "texto_titulo_derecha_pie_pagina_".$id_controles,
            $idiomas->_("Texto de título (derecha)"),
            $texto_titulo_derecha_pie_pagina,
            TAMANYO_CONTROL_MUY_GRANDE,
            false);

        $controles_parametros_pie_pagina = array();
        array_push($controles_parametros_pie_pagina, $control_lista_mostrar_numeros_pagina);
        array_push($controles_parametros_pie_pagina, $control_numero_pagina_inicial);
        array_push($controles_parametros_pie_pagina, $control_lista_mostrar_numero_paginas_totales);
        array_push($controles_parametros_pie_pagina, $control_lista_numero_paginas_totales_automatico);
        array_push($controles_parametros_pie_pagina, $control_numero_paginas_totales_manual);
        array_push($controles_parametros_pie_pagina, $control_texto_titulo_izquierda_pie_pagina);
        array_push($controles_parametros_pie_pagina, $control_texto_titulo_derecha_pie_pagina);
        return ($controles_parametros_pie_pagina);
    }


    // Añade los controles de parámetros de pie de página a la tabla del informe correspondiente
    function anyade_controles_parametros_pie_pagina_tabla_informe(
        $id_controles,
        $tabla,
        $titulo,
        $parametros_pie_pagina)
    {
        $ids_elementos_desplegables = array();
        array_push($ids_elementos_desplegables, "parametros_numeros_paginas_parametros_pie_pagina_".$id_controles);
        array_push($ids_elementos_desplegables, "textos_titulos_parametros_pie_pagina_".$id_controles);

        $tabla->anyade_cabecera_elementos_desplegables(
            "cabecera_parametros_pie_pagina_".$id_controles,
            array($titulo),
            $ids_elementos_desplegables);
        $params_contenido_parametros_pie_pagina_numeros_paginas = array(
            "clase_dato" => "parametros-pie-pagina-numeros-paginas",
            "sin_borde_inferior" => true,
            "oculta" => true
        );
        $tabla->anyade_fila(
            "parametros_numeros_paginas_parametros_pie_pagina_".$id_controles,
            array_slice($parametros_pie_pagina, 0, 5),
            $params_contenido_parametros_pie_pagina_numeros_paginas);
        $params_contenido_parametros_pie_pagina_texto_titulos = array(
            "clase_dato" => "parametros-pie-pagina-numeros-paginas-texto-titulos",
            "oculta" => true
        );
        $tabla->anyade_fila(
            "textos_titulos_parametros_pie_pagina_".$id_controles,
            array_slice($parametros_pie_pagina, 5, 2),
            $params_contenido_parametros_pie_pagina_texto_titulos);
    }


    //
    // Funciones auxiliares
    //


    // Crea la selección de una fecha con un botón
    function dame_seleccion_fecha($id_controles, $texto_boton)
    {
        $idiomas = new Idiomas();

        $fecha = "<div id='etiqueta_fecha_".$id_controles."'>".$idiomas->_("Fecha").": "."</div>";
        $fecha .= "<input size='10' type='text' id='fecha_".$id_controles."' class='selector-fecha datepicker'
            readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>";
        $boton = dame_boton_formulario($id_controles, $texto_boton);

        $controles = array(
            $fecha,
            $boton
        );
        return ($controles);
    }


    // Devuelve el nombre de un módulo
    function dame_nombre_modulo($modulo)
    {
        $idiomas = new Idiomas();

        switch ($modulo)
        {
            case MODULO_ADMINISTRACION:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_ADMINISTRACION);
                break;
            }
            case MODULO_MONITORIZACION:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_MONITORIZACION);
                break;
            }
            case MODULO_PERSONAL:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_PERSONAL);
                break;
            }
            case MODULO_RED:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_RED);
                break;
            }
            case MODULO_LOCALIZACIONES:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_LOCALIZACIONES);
                break;
            }
            case MODULO_SENSORES:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_SENSORES);
                break;
            }
            case MODULO_ACTUADORES:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_ACTUADORES);
                break;
            }
            case MODULO_SMARTMETER:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_SMARTMETER);
                break;
            }
            case MODULO_PROYECTOS:
            {
                $nombre_modulo = $idiomas->_(NOMBRE_MODULO_PROYECTOS);
                break;
            }
        }
        return ($nombre_modulo);
    }


    // Devuelve el número máximo de elementos de un módulo especificado en la licencia actual
    function dame_numero_maximo_elementos_modulo($modulo)
    {
        $modulos = Licencia::dame_modulos_licencias();
        if (in_array($modulo, $modulos) == false)
        {
            $numero_maximo_elementos = -1;
        }
        else
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_numero_maximo_elementos = "
                SELECT
                    licencias.numero_maximo_elementos
                FROM licencias
                WHERE
                    (licencias.modulo = '".$bd_red->_($modulo)."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_numero_maximo_elementos = $bd_red->ejecuta_consulta($consulta_numero_maximo_elementos);
            if (($res_numero_maximo_elementos == false) || ($res_numero_maximo_elementos->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_maximo_elementos."'");
            }

            $fila_numero_maximo_elementos = $res_numero_maximo_elementos->dame_siguiente_fila();
            $numero_maximo_elementos = $fila_numero_maximo_elementos['numero_maximo_elementos'];
        }
        return ($numero_maximo_elementos);
    }


    // Devuelve el tipo de un sensor
    function dame_tipo_sensor($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta = "
            SELECT
                tipo
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res = $bd_red->ejecuta_consulta($consulta);
        if (($res == false) || ($res->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
        }
        $fila = $res->dame_siguiente_fila();
        $tipo = $fila["tipo"];
        return ($tipo);
    }


    // Devuelve una lista con los valores especificados
    function dame_lista_valores($valores, $valores_seleccionados)
    {
        foreach ($valores as $valor)
        {
            $lista .= "<option value='".$valor[0]."'";
			if (in_array($valor[0], $valores_seleccionados) == true)
			{
				$lista .= " selected";
			}
			$lista .= ">".$valor[1]."</option>";
        }
        return ($lista);
    }


    // Devuelve la opción de una lista desplegable (simple)
    function dame_opcion_valor_lista_simple($texto_valor, $valor, $valor_seleccionado)
    {
        $opcion .= "<option value='".$valor."'";
        if ($valor == $valor_seleccionado)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".$texto_valor."</option>";
        return ($opcion);
    }


    // Devuelve la opción de una lista desplegable (simple)
    function dame_opcion_valor_lista_simple_valores_extra(
        $texto_valor,
        $valor,
        $valor_seleccionado,
        $valores_extra)
    {
        $opcion .= "<option value='".$valor."'";
        foreach ($valores_extra as $nombre_valor_extra => $valor_extra)
        {
            $opcion .= " ".$nombre_valor_extra."='".$valor_extra."'";
        }
        if ($valor == $valor_seleccionado)
        {
            $opcion .= " selected";
        }
        // Se pone control para que solo podamos crear sensores de datadis nosotros
        // pero que siga siendo visible para todo el mundo
        if (($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR) && ($valor == FORMATO_FICHERO_VALORES_DATADIS))
        {
            $opcion .= " disabled>".$texto_valor."</option>";
        } else
        {
            $opcion .= ">".$texto_valor."</option>";
        }
        return ($opcion);
    }


    // Devuelve la opción de una lista doble (múltiple)
    function dame_opcion_valor_lista_multiple($texto_valor, $valor, $valores_seleccionados)
    {
        $opcion .= "<option value='".$valor."'";
        if (in_array($valor, $valores_seleccionados) == true)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".$texto_valor."</option>";
        return ($opcion);
    }


    // Devuelve el número de elementos de una lista
    function dame_numero_elementos_lista($lista)
    {
        $numero_elementos = substr_count($lista, "<option value=");
        return ($numero_elementos);
    }


    // Devuelve una lista de valores sí/no
    function dame_lista_valores_si_no($valor_seleccionado)
    {
        $idiomas = new Idiomas();
        $lista = dame_lista_valores(
            array(
                array(VALOR_SI, $idiomas->_("Sí")),
                array(VALOR_NO, $idiomas->_("No"))),
            array($valor_seleccionado));
        return ($lista);
    }


    // Devuelve una lista de protocolos IP
    function dame_lista_protocolos_ip($protocolo_seleccionado)
    {
        $idiomas = new Idiomas();
        $lista = dame_lista_valores(
            array(
                array(PROTOCOLO_TCP, $idiomas->_("TCP")),
                array(PROTOCOLO_UDP, $idiomas->_("UDP"))),
            array($protocolo_seleccionado));
        return ($lista);
    }


    // Devuelve una lista de temas
    function dame_lista_temas($tema_seleccionado)
    {
        $lista = dame_lista_valores(
            array(
                array(TEMA_DEFECTO, dame_descripcion_tema(TEMA_DEFECTO)),
                array(TEMA_NARANJA, dame_descripcion_tema(TEMA_NARANJA)),
                array(TEMA_ROJO, dame_descripcion_tema(TEMA_ROJO)),
                array(TEMA_TURQUESA, dame_descripcion_tema(TEMA_TURQUESA)),
                array(TEMA_AZUL_CLARO, dame_descripcion_tema(TEMA_AZUL_CLARO)),
                array(TEMA_AZUL, dame_descripcion_tema(TEMA_AZUL)),
                array(TEMA_MORADO, dame_descripcion_tema(TEMA_MORADO)),
                array(TEMA_MAGENTA, dame_descripcion_tema(TEMA_MAGENTA)),
                array(TEMA_VERDE, dame_descripcion_tema(TEMA_VERDE)),
                array(TEMA_MARRON, dame_descripcion_tema(TEMA_MARRON)),
                array(TEMA_GRIS, dame_descripcion_tema(TEMA_GRIS)),
                array(TEMA_NEGRO, dame_descripcion_tema(TEMA_NEGRO))),
            array($tema_seleccionado));
        return ($lista);
    }


    // Devuelve una lista de paletas de colores de gráficas
    function dame_lista_paleta_colores_graficas($paleta_colores_graficas_seleccionada)
    {
        $lista = dame_lista_valores(
            array(
                array(PALETA_COLORES_GRAFICAS_DEFECTO, dame_descripcion_paleta_colores_graficas(PALETA_COLORES_GRAFICAS_DEFECTO)),
                array(PALETA_COLORES_GRAFICAS_ORIGINAL, dame_descripcion_paleta_colores_graficas(PALETA_COLORES_GRAFICAS_ORIGINAL)),
                array(PALETA_COLORES_GRAFICAS_ALTO_CONTRASTE, dame_descripcion_paleta_colores_graficas(PALETA_COLORES_GRAFICAS_ALTO_CONTRASTE)),
                array(PALETA_COLORES_GRAFICAS_EJENER, dame_descripcion_paleta_colores_graficas(PALETA_COLORES_GRAFICAS_EJENER))),
            array($paleta_colores_graficas_seleccionada));
        return ($lista);
    }


    // Devuelve la lista con los periodos de tiempo
    function dame_lista_periodos_tiempo($periodo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Día"), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Semana"), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Mes"), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Año"), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista de zonas horarias
    function dame_lista_zonas_horarias($zona_horaria_seleccionada)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_zonas_horarias = "
			SELECT
                id,
                nombre
			FROM zonas_horarias
			ORDER BY nombre ASC";
		$res_zonas_horarias = $bd_red->ejecuta_consulta($consulta_zonas_horarias);
        if ($res_zonas_horarias == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_zonas_horarias."'");
		}

        $lista = "";
		while ($fila_zona_horaria = $res_zonas_horarias->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_zona_horaria['id']."'";
			if ($fila_zona_horaria['id'] == $zona_horaria_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".$fila_zona_horaria['nombre']."</option>";
		}
        return ($lista);
    }


    //
    // Funciones para devolver botones específicos
    //


    // Devuelve el botón para generar PDF
    function dame_boton_generar_pdf($id_controles, $habilitado)
    {
        $idiomas = new Idiomas();
        $boton_generar_pdf = dame_boton_formulario($id_controles."_generar_pdf", $idiomas->_("Generar PDF"), $habilitado);
        return ($boton_generar_pdf);
    }


    // Devuelve el botón para añadir informe automático
    function dame_boton_anyadir_informe_automatico($id_controles, $habilitado)
    {
        $idiomas = new Idiomas();
        $boton_añadir_informe_automatico = dame_boton_formulario($id_controles."_anyadir_informe_automatico", $idiomas->_("Añadir informe automático"), $habilitado);
        return ($boton_añadir_informe_automatico);
    }


    // Recarga la configuración de un dispositivo
    function recarga_configuracion_dispositivo($id_dispositivo)
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta())
        {
            $mqtt->publica("MNG/DEV/".$id_dispositivo."/RELOAD", "", 0);
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT");
        }
    }


    //
    // Funciones de descripciones y formateado de cadenas
    //


    // Devuelve la descripción de valores si/no
    function dame_descripcion_valores_si_no($valor)
    {
        switch ($valor)
        {
            case VALOR_SI:
            {
                $descripcion = "Sí";
                break;
            }
            case VALOR_NO:
            {
                $descripcion = "No";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción del protocolo IP
    function dame_descripcion_protocolo_ip($protocolo)
    {
        switch ($protocolo)
        {
            case PROTOCOLO_TCP:
            {
                $descripcion = "TCP";
                break;
            }
            case PROTOCOLO_UDP:
            {
                $descripcion = "UDP";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción del tema
    function dame_descripcion_tema($tema)
    {
        switch ($tema)
        {
            case TEMA_DEFECTO:
            {
                $descripcion = "Defecto";
                break;
            }
            case TEMA_NARANJA:
            {
                $descripcion = "Naranja";
                break;
            }
            case TEMA_ROJO:
            {
                $descripcion = "Rojo";
                break;
            }
            case TEMA_TURQUESA:
            {
                $descripcion = "Turquesa";
                break;
            }
            case TEMA_AZUL_CLARO:
            {
                $descripcion = "Azul claro";
                break;
            }
            case TEMA_AZUL:
            {
                $descripcion = "Azul";
                break;
            }
            case TEMA_MORADO:
            {
                $descripcion = "Morado";
                break;
            }
            case TEMA_MAGENTA:
            {
                $descripcion = "Magenta";
                break;
            }
            case TEMA_VERDE:
            {
                $descripcion = "Verde";
                break;
            }
            case TEMA_MARRON:
            {
                $descripcion = "Marrón";
                break;
            }
            case TEMA_GRIS:
            {
                $descripcion = "Gris";
                break;
            }
            case TEMA_NEGRO:
            {
                $descripcion = "Negro";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción de la paleta de colores de las gráficas
    function dame_descripcion_paleta_colores_graficas($paleta_colores_graficas)
    {
        switch ($paleta_colores_graficas)
        {
            case PALETA_COLORES_GRAFICAS_DEFECTO:
            {
                $descripcion = "Defecto";
                break;
            }
            case PALETA_COLORES_GRAFICAS_ORIGINAL:
            {
                $descripcion = "Original";
                break;
            }
            case PALETA_COLORES_GRAFICAS_ALTO_CONTRASTE:
            {
                $descripcion = "Alto contraste";
                break;
            }
            case PALETA_COLORES_GRAFICAS_EJENER:
            {
                $descripcion = "Ejener";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción de error de evaluación de función con variables
    function dame_descripcion_error_funcion_variables($error)
    {
        switch ($error)
        {
            case ERROR_FUNCION_VARIABLES_ERROR_DESCONOCIDO:
            {
                $descripcion = "error desconocido";
                break;
            }
            case ERROR_FUNCION_VARIABLES_FUNCION_VACIA:
            {
                $descripcion = "función vacía";
                break;
            }
            case ERROR_FUNCION_VARIABLES_VARIABLE_NO_DEFINIDA:
            {
                $descripcion = "variables incorrectas";
                break;
            }
            case ERROR_FUNCION_VARIABLES_OPERADORES_INCORRECTOS:
            {
                $descripcion = "operadores incorrectos";
                break;
            }
            case ERROR_FUNCION_VARIABLES_FORMATO_INCORRECTO:
            {
                $descripcion = "formato incorrecto";
                break;
            }
            case ERROR_FUNCION_VARIABLES_NUMERO_VALORES_INCORRECTO:
            {
                $descripcion = "número de valores incorrecto";
                break;
            }
            case ERROR_FUNCION_VARIABLES_DIVISION_POR_CERO:
            {
                $descripcion = "división por cero";
                break;
            }
            case ERROR_FUNCION_VARIABLES_PARENTESIS:
            {
                $descripcion = "número de paréntesis no coincidentes";
                break;
            }
            case ERROR_FUNCION_VARIABLES_PRESENCIA_COMAS:
            {
                $descripcion = "presencia de comas, sustituir por puntos para los decimales";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción de periodo de tiempo
    function dame_descripcion_periodo_tiempo($periodo_tiempo)
    {
        switch ($periodo_tiempo)
        {
            case PERIODO_TIEMPO_FECHA_INICIO:
            {
                $descripcion = "Desde la fecha de inicio";
                break;
            }
            case PERIODO_TIEMPO_HORA:
            {
                $descripcion = "Hora";
                break;
            }
            case PERIODO_TIEMPO_DIA:
            {
                $descripcion = "Día";
                break;
            }
            case PERIODO_TIEMPO_SEMANA:
            {
                $descripcion = "Semana";
                break;
            }
            case PERIODO_TIEMPO_MES:
            {
                $descripcion = "Mes";
                break;
            }
            case PERIODO_TIEMPO_ANYO:
            {
                $descripcion = "Año";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve el nombre de una zona horaria
    function dame_nombre_zona_horaria($zona_horaria)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_zona_horaria = "
            SELECT nombre
            FROM zonas_horarias
            WHERE
                id = '".$bd_red->_($zona_horaria)."'";
        $res_zona_horaria = $bd_red->ejecuta_consulta($consulta_zona_horaria);
        if (($res_zona_horaria == false) || ($res_zona_horaria->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_zona_horaria."'");
        }
        $fila_zona_horaria = $res_zona_horaria->dame_siguiente_fila();
        $nombre_zona_horaria = $fila_zona_horaria["nombre"];
        return ($nombre_zona_horaria);
    }


    // Devuelve la calibración formateada par visualización
    function formatea_calibracion($calibracion)
    {
        $calibracion = str_replace(" ", "", $calibracion);
        $calibracion = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $calibracion);
        $calibracion = str_replace(SEPARADOR_OPERACIONES_CALIBRACION, SEPARADOR_OPERACIONES_CALIBRACION." ", $calibracion);
        $calibracion = str_replace(SEPARADOR_ELEMENTOS_OPERACIONES_CALIBRACION, SEPARADOR_ELEMENTOS_OPERACIONES_CALIBRACION." ", $calibracion);
        $calibracion = str_replace(SEPARADOR_PARAMETROS_OPERACIONES_CALIBRACION, " ".SEPARADOR_PARAMETROS_OPERACIONES_CALIBRACION." ", $calibracion);
        return ($calibracion);
    }


    //
    // Funciones de separadores numéricos
    //


    // Devuelve la descripción del separador de miles
    function dame_descripcion_id_separador_miles($id_separador_miles)
    {
        $idiomas = new Idiomas();

        switch ($id_separador_miles)
        {
            case ID_SEPARADOR_MILES_PUNTO:
            {
                $descripcion = "."." (".$idiomas->_("punto").")";
                break;
            }
            case ID_SEPARADOR_MILES_COMA:
            {
                $descripcion = ","." (".$idiomas->_("coma").")";
                break;
            }
            case ID_SEPARADOR_MILES_ESPACIO:
            {
                $descripcion = "(".$idiomas->_("espacio").")";
                break;
            }
            default:
            {
                $descripcion = $idiomas->_("Desconocido");
                break;
            }
        }
        return ($descripcion);
    }


    // Devuelve la descripción del punto decimal
    function dame_descripcion_id_punto_decimal($id_punto_decimal)
    {
        $idiomas = new Idiomas();

        switch ($id_punto_decimal)
        {
            case ID_PUNTO_DECIMAL_COMA:
            {
                $descripcion = ","." (".$idiomas->_("coma").")";
                break;
            }
            case ID_PUNTO_DECIMAL_PUNTO:
            {
                $descripcion = "."." (".$idiomas->_("punto").")";
                break;
            }
            default:
            {
                $descripcion = $idiomas->_("Desconocido");
                break;
            }
        }
        return ($descripcion);
    }


    // Devuelve el identificador de separador de miles
    function dame_id_separador_miles($separador_miles)
    {
        switch ($separador_miles)
        {
            case ".":
            {
                $id_separador_miles = ID_SEPARADOR_MILES_PUNTO;
                break;
            }
            case ",":
            {
                $id_separador_miles = ID_SEPARADOR_MILES_COMA;
                break;
            }
            case " ":
            {
                $id_separador_miles = ID_SEPARADOR_MILES_ESPACIO;
                break;
            }
            default:
            {
                throw new Exception("Separador de miles desconocido: '".$separador_miles."'");
            }
        }
        return ($id_separador_miles);
    }


    // Devuelve el identificador de punto decimal
    function dame_id_punto_decimal($punto_decimal)
    {
        switch ($punto_decimal)
        {
            case ",":
            {
                $id_punto_decimal = ID_PUNTO_DECIMAL_COMA;
                break;
            }
            case ".":
            {
                $id_punto_decimal = ID_PUNTO_DECIMAL_PUNTO;
                break;
            }
            default:
            {
                throw new Exception("Punto decimal desconocido: '".$punto_decimal."'");
            }
        }
        return ($id_punto_decimal);
    }


    //
    // Funciones de parámetros
    //


    // Devuelve los delimitadores para las descripciones de paráemtros (según el tipo de descripcion)
    function establece_delimitadores_descripcion_parametros(
        $tipo_descripcion,
        &$cadena_inicio_lista_parametros,
        &$cadena_fin_lista_parametros,
        &$cadena_inicio_primer_parametro,
        &$cadena_inicio_parametro,
        &$cadena_fin_parametro)
    {
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_HTML:
            {
                $cadena_inicio_lista_parametros = "<ul>";
                $cadena_fin_lista_parametros = "</ul>";
                $cadena_inicio_primer_parametro = "<li>";
                $cadena_inicio_parametro = "<li>";
                $cadena_fin_parametro = "</li>";
                break;
            }
            case TIPO_DESCRIPCION_TEXTO:
            {
                $cadena_inicio_lista_parametros = "";
                $cadena_fin_lista_parametros = "";
                $cadena_inicio_primer_parametro = "- ";
                $cadena_inicio_parametro = "\n- ";
                $cadena_fin_parametro = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de descripción desconocida: '".$tipo_descripcion."'");
            }
        }
    }
?>
