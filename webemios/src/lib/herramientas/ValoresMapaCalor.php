<?php
    session_start();

	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Constantes de mapa de calor

	// Tipos de mapa de calor
    define("TIPO_MAPA_CALOR_DIARIO", "DIARIO");
    define("TIPO_MAPA_CALOR_SEMANAL", "SEMANAL");
    define("TIPO_MAPA_CALOR_PERSONALIZADO", "PERSONALIZADO");


    // Nota: Para añadir un mapa de calor: http://bl.ocks.org/tjdecke/5558084
	class ValoresMapaCalor
	{
		public $tipo;
        public $dias;
        public $valores_horas;
        public $periodos;
        public $subperiodos;
        public $valores_periodos_subperiodos;


		function __construct($tipo)
		{
            switch ($tipo)
            {
                case TIPO_MAPA_CALOR_NINGUNO:
                case TIPO_MAPA_CALOR_DIARIO:
                case TIPO_MAPA_CALOR_SEMANAL:
                case TIPO_MAPA_CALOR_PERSONALIZADO:
                {
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de mapa de calor desconocido: '".$tipo."'");
                }
            }
            $this->tipo = $tipo;
            $this->dias = array();
            $this->valores_horas = array();
            $this->periodos = array();
            $this->subperiodos = array();
            $this->valores_periodos_subperiodos = array();
		}


		function anyade_valor_fecha_hora($fecha_hora_local, $valor)
		{
			switch ($this->tipo)
            {
                case TIPO_MAPA_CALOR_DIARIO:
                {
                    $cadena_fecha_hora_local_local = convierte_fecha_a_cadena($fecha_hora_local, $_SESSION["formato_fecha_local"]);
                    if (in_array($cadena_fecha_hora_local_local, $this->dias) == false)
                    {
                        array_push($this->dias, $cadena_fecha_hora_local_local);
                    }
                    $numero_dia = count($this->dias);
                    $hora_dia = $fecha_hora_local->format('H');
                    $numero_hora = ($numero_dia - 1) * 24 + $hora_dia;
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL:
                {
                    $numero_dia = $fecha_hora_local->format('w');
                    if ($numero_dia == 0)
                    {
                        $numero_dia = 7;
                    }
                    $hora_dia = $fecha_hora_local->format('H');
                    $numero_hora = ($numero_dia - 1) * 24 + $hora_dia;
                    break;
                }
            }
            if (array_key_exists($numero_hora, $this->valores_horas) == False)
            {
                $ocurrencias = 1;
                $valores_hora = array($valor);
                $suma_valores_hora = $valor;
            }
            else
            {
                $ocurrencias = $this->valores_horas[$numero_hora]["ocurrencias"] + 1;
                array_push($this->valores_horas[$numero_hora]["valores_hora"], $valor);
                $valores_hora = $this->valores_horas[$numero_hora]["valores_hora"];
                $suma_valores_hora = $this->valores_horas[$numero_hora]["suma_valores_hora"] + $valor;
            }
            $this->valores_horas[$numero_hora] = array(
                "numero_dia" => $numero_dia,
                "hora_dia" => $hora_dia,
                "ocurrencias" => $ocurrencias,
                "valores_hora" => $valores_hora,
                "suma_valores_hora" => $suma_valores_hora);
        }


        function dame_dias()
		{
            switch ($this->tipo)
            {
                case TIPO_MAPA_CALOR_SEMANAL:
                {
                    $numeros_dias_semana_con_valores = array();
                    foreach ($this->valores_horas as $valores_hora)
                    {
                        $numero_dia = $valores_hora["numero_dia"];
                        if (in_array($numero_dia, $numeros_dias_semana_con_valores) == false)
                        {
                            array_push($numeros_dias_semana_con_valores, $numero_dia);
                        }
                    }
                    sort($numeros_dias_semana_con_valores);
                    foreach ($numeros_dias_semana_con_valores as $numero_dia_semana)
                    {
                        $nombre_dia_semana = dame_nombre_dia_semana($numero_dia_semana);
                        array_push($this->dias, $nombre_dia_semana);
                    }
                    break;
                }
            }
            return ($this->dias);
		}


        function dame_valores_horas()
		{
			return ($this->valores_horas);
		}


        function pon_valores_horas($valores_horas)
		{
			$this->valores_horas = $valores_horas;
		}


		function dame_datos()
        {
            $datos = array();
            switch ($this->tipo)
            {
                case TIPO_MAPA_CALOR_DIARIO:
                {
                    foreach ($this->valores_horas as $valores_hora)
                    {
                        $valor_medio_hora = round($valores_hora["suma_valores_hora"] / $valores_hora["ocurrencias"], 2);
                        $dato_hora = array(
                            "period" => $valores_hora["numero_dia"],
                            "subperiod" => $valores_hora["hora_dia"],
                            "value" => $valor_medio_hora);
                        array_push($datos, $dato_hora);
                    }
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL:
                {
                    $numeros_dias_semana_con_valores = array();
                    foreach ($this->valores_horas as $valores_hora)
                    {
                        $numero_dia = $valores_hora["numero_dia"];
                        if (in_array($numero_dia, $numeros_dias_semana_con_valores) == false)
                        {
                            array_push($numeros_dias_semana_con_valores, $numero_dia);
                        }
                    }
                    sort($numeros_dias_semana_con_valores);
                    foreach ($this->valores_horas as $valores_hora)
                    {
                        $numero_periodo = $valores_hora["numero_dia"];
                        $numero_periodo_datos = $valores_hora["numero_dia"];
                        for ($numero_periodo_anterior = 1; $numero_periodo_anterior < $numero_periodo; $numero_periodo_anterior++)
                        {
                            if (in_array($numero_periodo_anterior, $numeros_dias_semana_con_valores) == false)
                            {
                                $numero_periodo_datos -= 1;
                            }
                        }
                        $valor_medio_hora = round($valores_hora["suma_valores_hora"] / $valores_hora["ocurrencias"], 2);
                        $dato_hora = array(
                            "period" => $numero_periodo_datos,
                            "subperiod" => $valores_hora["hora_dia"],
                            "value" => $valor_medio_hora);
                        array_push($datos, $dato_hora);
                    }
                    break;
                }
            }
            return ($datos);
		}


        function anyade_valor_periodo_subperiodo($periodo, $subperiodo, $valor)
		{
			$clave_periodo_subperiodo = $periodo."-".$subperiodo;
            if (array_key_exists($clave_periodo_subperiodo, $this->valores_periodos_subperiodos) == False)
            {
                $ocurrencias = 1;
                $valores_periodo_subperiodo = array($valor);
                $suma_valores_periodo_subperiodo = $valor;
            }
            else
            {
                $ocurrencias = $this->valores_periodos_subperiodos[$clave_periodo_subperiodo]["ocurrencias"] + 1;
                array_push($valores_periodo_subperiodo, $valor);
                $suma_valores_periodo_subperiodo = $this->valores_periodos_subperiodos[$clave_periodo_subperiodo]["suma_valores_periodo_subperiodo"] + $valor;
            }
            $this->valores_periodos_subperiodos[$clave_periodo_subperiodo] = array(
                "periodo" => $periodo,
                "subperiodo" => $subperiodo,
                "ocurrencias" => $ocurrencias,
                "valores_periodo_subperiodo" => $valores_periodo_subperiodo,
                "suma_valores_periodo_subperiodo" => $suma_valores_periodo_subperiodo);
        }


        function anyade_periodo($periodo)
		{
            if (in_array($periodo, $this->periodos) == false)
            {
                array_push($this->periodos, $periodo);
            }
		}


        function pon_periodos($periodos)
		{
            $this->periodos = $periodos;
		}


		function dame_periodos()
		{
			return ($this->periodos);
		}


        function pon_subperiodos($subperiodos)
		{
            $this->subperiodos = $subperiodos;
		}


		function dame_subperiodos()
        {
			return ($this->subperiodos);
		}


        function dame_valores_periodos_subperiodos()
		{
			return ($this->valores_periodos_subperiodos);
		}


        function pon_valores_periodos_subperiodos($valores_periodos_subperiodos)
		{
			$this->valores_periodos_subperiodos = $valores_periodos_subperiodos;
		}


        function dame_datos_periodos_subperiodos()
        {
            $datos = array();
			foreach ($this->valores_periodos_subperiodos as $valores_periodo_subperiodo)
            {
                $valor_medio_periodo_subperiodo = round($valores_periodo_subperiodo["suma_valores_periodo_subperiodo"] / $valores_periodo_subperiodo["ocurrencias"], 2);
                $dato_periodo_subperiodo = array(
                    "period" => $valores_periodo_subperiodo["periodo"],
                    "subperiod" => $valores_periodo_subperiodo["subperiodo"],
                    "value" => $valor_medio_periodo_subperiodo);
                array_push($datos, $dato_periodo_subperiodo);
            }
            return ($datos);
		}
	}
?>
