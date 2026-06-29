<?php
	session_start();


    // Se recupera la zona horaria local
	function dame_zona_horaria_local()
	{
        $zona_horaria_local = $_SESSION["zona_horaria"];
        return ($zona_horaria_local);
    }


    // Convierte a fecha hora local
    function dame_fecha_hora_local($fecha_hora_utc)
    {
        $zona_horaria_local = dame_zona_horaria_local();
        $fecha_hora_local = clone $fecha_hora_utc;
        $fecha_hora_local->setTimezone(new DateTimeZone($zona_horaria_local));
        return ($fecha_hora_local);
    }


    // Devuelve los minutos de desfase de la hora local con respecto a UTC (en minutos)
    function dame_minutos_desfase_utc_zona_horaria_local()
    {
        $zona_horaria_local = dame_zona_horaria_local();
        $objeto_zona_horaria_local = new DateTimeZone($zona_horaria_local);
        $minutos_desfase_utc_zona_horaria_local = $objeto_zona_horaria_local->getOffset(new DateTime()) / 60;
        return ($minutos_desfase_utc_zona_horaria_local);
    }


    // Devuelve la fecha y hora actual en zona horaria local
    function dame_fecha_hora_actual_local()
    {
        $zona_horaria_local = dame_zona_horaria_local();
        $fecha_hora_actual_local = new DateTime();
        $fecha_hora_actual_local->setTimezone(new DateTimeZone($zona_horaria_local));
        return ($fecha_hora_actual_local);
    }
?>
