<?php
	class VectorDatos
	{
		public $datos;
        public $tuplas;


		function __construct()
		{
            $this->datos = array();
            $this->tuplas = array();
		}


		function anyade_dato($dato)
		{
            $numero_tuplas = count($this->tuplas);
            if ($numero_tuplas == 0)
            {
                array_push($this->datos, $dato);
            }
            else
            {
                array_push($this->tuplas[$numero_tuplas - 1], $dato);
            }
		}


		function anyade_etiqueta($etiqueta)
		{
            $this->anyade_dato(htmlspecialchars($etiqueta, ENT_QUOTES));
		}


		function inicio_tupla()
		{
            $tupla = array();
            array_push($this->tuplas, $tupla);
		}


		function fin_tupla()
		{
            if (count($this->tuplas) == 0)
            {
                throw new Exception("No hay tupla activa");
            }
            $tupla = array_pop($this->tuplas);
            $this->anyade_dato($tupla);
		}


        function anyade_tupla_dato($dato)
		{
			$this->inicio_tupla();
			$this->anyade_dato($dato);
			$this->fin_tupla();
		}


		function anyade_tupla_etiqueta_dato($etiqueta, $dato)
		{
			$this->inicio_tupla();
			$this->anyade_etiqueta($etiqueta);
			$this->anyade_dato($dato);
			$this->fin_tupla();
		}


		function anyade_tupla_pareja_datos($dato_1, $dato_2)
		{
			$this->inicio_tupla();
			$this->anyade_dato($dato_1);
			$this->anyade_dato($dato_2);
			$this->fin_tupla();
		}


        function anyade_tupla_pareja_etiquetas($etiqueta_1, $etiqueta_2)
		{
			$this->inicio_tupla();
			$this->anyade_etiqueta($etiqueta_1);
			$this->anyade_etiqueta($etiqueta_2);
			$this->fin_tupla();
		}


        function anyade_tupla_pareja_datos_etiqueta($dato_1, $dato_2, $etiqueta)
		{
			$this->inicio_tupla();
			$this->anyade_dato($dato_1);
			$this->anyade_dato($dato_2);
            $this->anyade_etiqueta($etiqueta);
			$this->fin_tupla();
		}


        function anyade_tupla_etiqueta_dato_etiqueta($etiqueta_1, $dato, $etiqueta_2)
		{
			$this->inicio_tupla();
			$this->anyade_etiqueta($etiqueta_1);
			$this->anyade_dato($dato);
            $this->anyade_etiqueta($etiqueta_2);
			$this->fin_tupla();
		}


		function dame_numero_datos()
		{
            $numero_datos = count($this->datos);
			return ($numero_datos);
		}


        function dame_datos()
		{
            return ($this->datos);
		}


        function dame_datos_media($numero_maximo_datos)
		{
            $numero_datos = count($this->datos);
            if ($numero_datos < $numero_maximo_datos)
            {
                $cadena_datos = $this->dame_datos();
                return ($cadena_datos);
            }

            $numero_valores_agrupados_dato = ceil($numero_datos / $numero_maximo_datos);
            $datos_agrupados = new VectorDatos();
            $suma_timestamps_agrupados = NULL;
            $suma_valores_agrupados = NULL;
            $numero_valores_agrupados = 0;
            for ($i = 0; $i < $numero_datos; $i++)
            {
                $dato = $this->datos[$i];
                $timestamp = $dato[0];
                $valor = $dato[1];

                // Si es un valor nulo o es el primer o último valor
                if (($valor === NULL) || ($i == 0) || ($i == ($numero_datos - 1)))
                {
                    if ($numero_valores_agrupados == 0)
                    {
                        continue;
                    }
                    $timestamp_agrupado = ($suma_timestamps_agrupados / $numero_valores_agrupados);
                    $valor_agrupado = ($suma_valores_agrupados / $numero_valores_agrupados);
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp_agrupado, $valor_agrupado);
                    $suma_timestamps_agrupados = NULL;
                    $suma_valores_agrupados = NULL;
                    $numero_valores_agrupados = 0;
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp, $valor);
                    continue;
                }

                if ($numero_valores_agrupados == 0)
                {
                    $suma_timestamps_agrupados = $timestamp;
                    $suma_valores_agrupados = $valor;
                }
                else
                {
                    $suma_timestamps_agrupados += $timestamp;
                    $suma_valores_agrupados += $valor;
                }
                $numero_valores_agrupados += 1;
                if ($numero_valores_agrupados == $numero_valores_agrupados_dato)
                {
                    $timestamp_agrupado = ($suma_timestamps_agrupados / $numero_valores_agrupados);
                    $valor_agrupado = ($suma_valores_agrupados / $numero_valores_agrupados);
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp_agrupado, $valor_agrupado);
                    $suma_timestamps_agrupados = NULL;
                    $suma_valores_agrupados = NULL;
                    $numero_valores_agrupados = 0;
                }
            }

            $cadena_datos = $datos_agrupados->dame_datos();
            return ($cadena_datos);
        }


        function dame_datos_salteados($numero_maximo_datos)
		{
            $numero_datos = count($this->datos);
            if ($numero_datos < $numero_maximo_datos)
            {
                $cadena_datos = $this->dame_datos();
                return ($cadena_datos);
            }

            $numero_valores_agrupados_dato = ceil($numero_datos / $numero_maximo_datos);
            $datos_agrupados = new VectorDatos();
            $timestamp_agrupado = NULL;
            $valor_agrupado = NULL;
            $numero_valores_agrupados = 0;
            for ($i = 0; $i < $numero_datos; $i++)
            {
                $dato = $this->datos[$i];
                $timestamp = $dato[0];
                $valor = $dato[1];

                // Si es un valor nulo o es el primer o último valor
                if (($valor === NULL) || ($i == 0) || ($i == ($numero_datos - 1)))
                {
                    if ($numero_valores_agrupados == 0)
                    {
                        continue;
                    }
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp_agrupado, $valor_agrupado);
                    $timestamp_agrupado = NULL;
                    $valor_agrupado = NULL;
                    $numero_valores_agrupados = 0;
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp, $valor);
                    continue;
                }

                $timestamp_agrupado = $timestamp;
                $valor_agrupado = $valor;
                $numero_valores_agrupados += 1;
                if ($numero_valores_agrupados == $numero_valores_agrupados_dato)
                {
                    $datos_agrupados->anyade_tupla_pareja_datos($timestamp_agrupado, $valor_agrupado);
                    $timestamp_agrupado = NULL;
                    $valor_agrupado = NULL;
                    $numero_valores_agrupados = 0;
                }
            }

            $cadena_datos = $datos_agrupados->dame_datos();
            return ($cadena_datos);
        }
	}
?>
