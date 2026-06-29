<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');


    //
    // Funciones de días de la semana
    //


    // Devuelve el nombre del día de la semana (1: lunes - 7: domingo, -1: todos)
	function dame_nombre_dia_semana($dia)
	{
		switch ($dia)
		{
            case -1:
            {
                $nombre_dia_semana = "Todos";
                break;
            }
			case 1:
            {
                $nombre_dia_semana = "Lunes";
                break;
            }
            case 2:
            {
                $nombre_dia_semana = "Martes";
                break;
            }
            case 3:
            {
                $nombre_dia_semana = "Miércoles";
                break;
            }
            case 4:
            {
                $nombre_dia_semana = "Jueves";
                break;
            }
            case 5:
            {
                $nombre_dia_semana = "Viernes";
                break;
            }
            case 6:
            {
                $nombre_dia_semana = "Sábado";
                break;
            }
            case 7:
            {
                $nombre_dia_semana = "Domingo";
                break;
            }
            default:
            {
                $nombre_dia_semana = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($nombre_dia_semana));
	}

    function dame_nombre_tipo_periodo($tipo_periodo)
    {
		switch ($tipo_periodo)
		{
			case "DIARIO":
            {
                $nombre_tipo_periodo = "Diario";
                break;
            }
            case "SEMANAL":
            {
                $nombre_tipo_periodo = "Semanal";
                break;
            }
            case "MENSUAL":
            {
                $nombre_tipo_periodo = "Mensual";
                break;
            }
            default:
            {
                $nombre_tipo_periodo = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($nombre_tipo_periodo));
	}

    // Devuelve la abreviatura del nombre del día de la semana (1: lunes - 7: domingo, -1: todos)
	function dame_abreviatura_nombre_dia_semana($dia)
	{
		switch ($dia)
		{
            case -1:
            {
                $nombre_dia_semana = "todos";
                break;
            }
			case 1:
            {
                $nombre_dia_semana = "lu.";
                break;
            }
            case 2:
            {
                $nombre_dia_semana = "ma.";
                break;
            }
            case 3:
            {
                $nombre_dia_semana = "mi.";
                break;
            }
            case 4:
            {
                $nombre_dia_semana = "ju.";
                break;
            }
            case 5:
            {
                $nombre_dia_semana = "vi.";
                break;
            }
            case 6:
            {
                $nombre_dia_semana = "sa.";
                break;
            }
            case 7:
            {
                $nombre_dia_semana = "do.";
                break;
            }
            default:
            {
                $nombre_dia_semana = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($nombre_dia_semana));
	}


    // Devuelve los nombres de los días de la semana (1: lunes - 7: domingo, -1: todos)
	function dame_abreviaturas_nombres_dias_semana($dias)
	{
        $nombres_dias_semana = "";
        foreach ($dias as $dia)
        {
            $nombre_dia_semana = dame_abreviatura_nombre_dia_semana($dia);
            if ($nombres_dias_semana != "")
            {
                $nombres_dias_semana .= ", ";
            }
            $nombres_dias_semana .= $nombre_dia_semana;
        }
        $nombres_dias_semana = ucwords($nombres_dias_semana);
        return ($nombres_dias_semana);
	}


    //
    // Funciones de tiempos
    //


    function dame_texto_periodo($segundos)
	{
		$idiomas = new Idiomas();

        // http://www.forosdelweb.com/f18/calcular-tiempo-con-segundos-567075/
        $resto_segundos = $segundos % 60;
        $minutos = (int) (($segundos - $resto_segundos) / 60);
        $resto_minutos = $minutos % 60;
        $horas = (int) (($minutos - $resto_minutos) / 60);
        $resto_horas = $horas % 24;
        $dias = (int) (($horas - $resto_horas) / 24);

        $texto = "";
        if ($dias > 0)
        {
            if ($dias == 1)
            {
                $texto .= $dias." ".$idiomas->_("día");
            }
            else
            {
                $texto .= $dias." ".$idiomas->_("días");
            }
        }
        if ($resto_horas > 0)
        {
            if ($texto != "")
            {
                if (($resto_minutos == 0) && ($resto_segundos == 0))
                {
                    $texto .= " ".$idiomas->_("y")." ";
                }
                else
                {
                    $texto .= ", ";
                }
            }
            if ($resto_horas == 1)
            {
                $texto .= $resto_horas." ".$idiomas->_("hora");
            }
            else
            {
                $texto .= $resto_horas." ".$idiomas->_("horas");
            }
        }
        if ($resto_minutos > 0)
        {
            if ($texto != "")
            {
                if ($resto_segundos == 0)
                {
                    $texto .= " ".$idiomas->_("y")." ";
                }
                else
                {
                    $texto .= ", ";
                }
            }
            if ($resto_minutos == 1)
            {
                $texto .= $resto_minutos." ".$idiomas->_("minuto");
            }
            else
            {
                $texto .= $resto_minutos." ".$idiomas->_("minutos");
            }
        }
        if ($resto_segundos > 0)
        {
            if ($texto != "")
            {
                $texto .= " ".$idiomas->_("y")." ";
            }
            if ($resto_segundos == 1)
            {
                $texto .= $resto_segundos." ".$idiomas->_("segundo");
            }
            else
            {
                $texto .= $resto_segundos." ".$idiomas->_("segundos");
            }
        }
        if ($segundos == 0)
        {
            $texto .= $segundos." ".$idiomas->_("segundos");
        }
        return ($texto);
	}


    function convierte_formato_fecha($cadena_fecha, $formato_origen, $formato_destino)
	{
        if ($formato_origen == $formato_destino)
        {
            return ($cadena_fecha);
        }

        // Se convierte de cadena a fecha y después a cadena de nuevo con el nuevo formato
        $fecha = convierte_cadena_a_fecha($cadena_fecha, $formato_origen, ZONA_HORARIA_UTC);
        $cadena_fecha_convertida = convierte_fecha_a_cadena($fecha, $formato_destino);
        return ($cadena_fecha_convertida);
	}


    function convierte_formato_dia_anyo($cadena_dia_anyo, $formato_origen, $formato_destino)
	{
        if ($formato_origen == $formato_destino)
        {
            return ($cadena_dia_anyo);
        }
		$fecha = DateTime::createFromFormat($formato_origen, $cadena_dia_anyo, new DateTimeZone(ZONA_HORARIA_UTC));
        $cadena_convertida = $fecha->format($formato_destino);
        return ($cadena_convertida);
	}


    function convierte_cadena_a_fecha($cadena_fecha, $formato_fecha, $zona_horaria)
    {
        $fecha = DateTime::createFromFormat($formato_fecha, $cadena_fecha, new DateTimeZone($zona_horaria));

        // Nota: Se añaden los segundos por si se habían eliminado en una conversión de fecha a cadena anterior (si eran 0)
        if ($fecha == false)
        {
            if ($formato_fecha == $_SESSION["formato_fecha_hora_local"])
            {
                $cadena_fecha .= ":00";
                $fecha = DateTime::createFromFormat($formato_fecha, $cadena_fecha, new DateTimeZone($zona_horaria));
            }
        }
        if ($fecha == false)
        {
            throw new Exception("Error al convertir la cadena a fecha: '".$cadena_fecha."' ".
                "(formato de fecha: '".$formato_fecha."', zona horaria: '".$zona_horaria."')");
        }
        return ($fecha);
    }


    function convierte_fecha_a_cadena($fecha, $formato_fecha)
    {
        // Nota: Se eliminan los segundos si el formato de fecha es local (con segundos) y si son 0
        if (($formato_fecha == $_SESSION["formato_fecha_hora_local"]) && ($fecha->format('s') == 0))
        {
            $formato_fecha = $_SESSION["formato_fecha_hora_local_sin_segundos"];
        }
        $cadena_fecha = $fecha->format($formato_fecha);
        return ($cadena_fecha);
    }


    function dame_segundos_intervalo_tiempo($intervalo)
    {
        $numero_dias = dame_numero_dias_intervalo_tiempo($intervalo);
        $segundos = ($numero_dias * 24 * 60 * 60) +
            ($intervalo->h * 60 * 60) +
            ($intervalo->i * 60) +
            $intervalo->s;
        return ($segundos);
    }


    function dame_horas_intervalo_tiempo($intervalo)
    {
        $numero_horas = dame_segundos_intervalo_tiempo($intervalo) / 3600;
        return ($numero_horas);
    }


    function dame_numero_dias_intervalo_tiempo($intervalo)
    {
        $numero_dias = $intervalo->days;
        if (($numero_dias === -99999) || ($numero_dias === false))
        {
            $numero_dias = ($intervalo->y * 365) + ($intervalo->m * 30) + $intervalo->d;
        }
        return ($numero_dias);
    }


    function dame_intervalo_tiempo_segundos($segundos)
    {
        $intervalo_segundos = new DateInterval("PT".$segundos."S");
        $fecha_inicial = new DateTime("00:00");
        $fecha_inicial_aux = clone $fecha_inicial;
        $fecha_inicial->add($intervalo_segundos);
        $intervalo = $fecha_inicial_aux->diff($fecha_inicial);
        return ($intervalo);
    }


    function convierte_formato_hora_python_a_formato_fecha_hora($formato_fecha_hora_python)
    {
        $formato_fecha_hora = $formato_fecha_hora_python;
        $formato_fecha_hora = str_replace("%d", "d", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%m", "m", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%y", "y", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%Y", "Y", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%H", "H", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%M", "M", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%S", "S", $formato_fecha_hora);
        return ($formato_fecha_hora);
    }


    function convierte_formato_fecha_hora_a_formato_hora_python($formato_fecha_hora)
    {
        $formato_fecha_hora_python = $formato_fecha_hora;
        $formato_fecha_hora_python = str_replace("d", "%d", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("m", "%m", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("y", "%y", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("Y", "%Y", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("H", "%H", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("M", "%M", $formato_fecha_hora_python);
        $formato_fecha_hora_python = str_replace("S", "%S", $formato_fecha_hora_python);
        return ($formato_fecha_hora_python);
    }


    function dame_timestamp_fecha_milisegundos($fecha)
	{
        // Nota: getTimestamp() devolvía valores incorrectos y podía modificar la fecha
        // (al menos en la fecha 29/2/2020 y posteriores ... puede ser un error con años bisiestos?)
        $timestamp = $fecha->format("U") * 1000;
        return ($timestamp);
	}


    function dame_timestamp_cadena_fecha_milisegundos($cadena_fecha, $formato, $zona_horaria)
	{
        $fecha = convierte_cadena_a_fecha($cadena_fecha, $formato, $zona_horaria);
        $timestamp = dame_timestamp_fecha_milisegundos($fecha);
        return ($timestamp);
	}


    function dame_timestamp_ahora_milisegundos_utc()
    {
        $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
        $microsegundos = microtime();
        $milisegundos_hora_actual = (int) (explode(" ", $microsegundos)[0] * 1000.0);
        $timestamp_utc = dame_timestamp_fecha_milisegundos($fecha_hora_actual_utc);
        $timestamp_utc += $milisegundos_hora_actual;
        return ($timestamp_utc);
    }


    function dame_fecha_hora_actual_utc()
    {
        $fecha_hora_actual_utc = new DateTime();
        $fecha_hora_actual_utc->setTimezone(new DateTimeZone(ZONA_HORARIA_UTC));
        return ($fecha_hora_actual_utc);
    }


    function cambia_zona_horaria_fecha_hora($fecha_hora, $zona_horaria)
	{
        $fecha_hora_modificada = clone $fecha_hora;
        $fecha_hora_modificada->setTimezone(new DateTimeZone($zona_horaria));
        return ($fecha_hora_modificada);
	}


    function cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora, $formato, $zona_horaria_origen, $zona_horaria_destino)
	{
        $fecha_hora_destino = convierte_cadena_a_fecha($cadena_fecha_hora, $formato, $zona_horaria_origen);
        $fecha_hora_destino->setTimezone(new DateTimeZone($zona_horaria_destino));
        $cadena_fecha_hora_destino = convierte_fecha_a_cadena($fecha_hora_destino, $formato);
        return ($cadena_fecha_hora_destino);
	}


    function dame_horario_verano_fecha_hora_utc($fecha_hora_utc, $zona_horaria)
    {
        // (http://php.net/manual/es/datetimezone.gettransitions.php)
        $segundos_fecha_hora_utc = $fecha_hora_utc->format("U");
        $objeto_zona_horaria = new DateTimeZone($zona_horaria);
        $transicion_horario_verano = $objeto_zona_horaria->getTransitions($segundos_fecha_hora_utc, $segundos_fecha_hora_utc)[0];
        $horario_verano = $transicion_horario_verano["isdst"];
        return ($horario_verano);
    }


    function dame_horario_verano_cadena_fecha_hora_utc($cadena_fecha_hora_utc, $formato, $zona_horaria)
    {
        $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_utc, $formato, ZONA_HORARIA_UTC);
        $horario_verano = dame_horario_verano_fecha_hora_utc($fecha_hora_utc, $zona_horaria);
        return ($horario_verano);
    }


    function dame_fecha_hora_utc($fecha_hora_local)
    {
        $fecha_hora_utc = clone $fecha_hora_local;
        $fecha_hora_utc->setTimezone(new DateTimeZone(ZONA_HORARIA_UTC));
        return ($fecha_hora_utc);
    }
?>
