<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	function dame_porcentaje_valor_referencia($valor, $referencia)
	{
		if ($referencia <> 0.0)
		{
            $porcentaje = abs(1 - ($valor / $referencia)) * 100;
            $porcentaje_redondeado = round($porcentaje, 2);
            return ($porcentaje_redondeado);
		}
		else
		{
			return (0.0);
		}
	}


    function calcula_resultado_ecuacion($ecuacion, $valores)
    {
        $numero_valor = 1;
        foreach ($valores as $valor)
        {
            $ecuacion_evaluable = str_replace("var_".$numero_valor, "{$valor}", $ecuacion);
        }

        $ecuacion_evaluable = preg_replace('/\s+/', '', $ecuacion_evaluable);
        eval('$resultado = '.$ecuacion_evaluable.';');
        return ($resultado);
    }


    function dame_ecuacion_funcion_correlacion($funcion, $coeficientes)
    {
        switch ($funcion)
        {
            case FUNCION_CORRELACION_LINEAL:
            {
                $ecuacion = "{$coeficientes[0]} + {$coeficientes[1]} * var_1";
                break;
            }
            case FUNCION_CORRELACION_POLINOMIO_GRADO_2:
            {
                $ecuacion = "{$coeficientes[0]} + {$coeficientes[1]} * var_1 + {$coeficientes[2]} * pow(var_1, 2)";
                break;
            }
            case FUNCION_CORRELACION_LOGARITMICA:
            {
                $ecuacion = "{$coeficientes[0]} + {$coeficientes[1]} * log(var_1)";
                break;
            }
            case FUNCION_CORRELACION_RAIZ_CUADRADA:
            {
                $ecuacion = "{$coeficientes[0]} + {$coeficientes[1]} * sqrt(var_1)";
                break;
            }
        }

        return ($ecuacion);
    }


    function dame_cadena_funcion_correlacion($funcion, $coeficientes)
    {
        $cadena = "";
        switch ($funcion)
        {
            case FUNCION_CORRELACION_LINEAL:
            {
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[0], "");
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[1], "x");
                break;
            }
            case FUNCION_CORRELACION_POLINOMIO_GRADO_2:
            {
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[0], "");
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[1], "x");
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[2], "(x ** 2)");
                break;
            }
            case FUNCION_CORRELACION_LOGARITMICA:
            {
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[0], "");
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[1], "log(x)");
                break;
            }
            case FUNCION_CORRELACION_RAIZ_CUADRADA:
            {
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[0], "");
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[1], "(x ** 0.5)");
                break;
            }
            case FUNCION_CORRELACION_MULTIVARIABLE_LINEAL:
            {
                $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[0], "");
                for ($i = 1; $i <= count($coeficientes) - 1; $i++)
                {
                    $cadena .= dame_cadena_factor_funcion_correlacion($cadena, $coeficientes[$i], "x".$i);
                }
                break;
            }
        }
        if ($cadena == "")
        {
            $cadena = "0";
        }
        $cadena = "y = ".$cadena;

        return ($cadena);
    }


    function dame_cadena_factor_funcion_correlacion($cadena_funcion, $coeficiente, $texto_coeficiente)
    {
        // https://www.php.net/manual/es/function.round.php
        $cadena_factor = "";
        $coeficiente_redondeado = sprintf(
            "%.".NUMERO_DECIMALES_COEFICIENTES_FUNCION_CORRELACION."f",
            round($coeficiente, NUMERO_DECIMALES_COEFICIENTES_FUNCION_CORRELACION));
        if ($coeficiente_redondeado != 0.0)
        {
            if ($cadena_funcion != "")
            {
                if ($coeficiente_redondeado < 0.0)
                {
                    $cadena_factor .= " - ";
                    $coeficiente_redondeado = sprintf(
                        "%.".NUMERO_DECIMALES_COEFICIENTES_FUNCION_CORRELACION."f",
                        abs($coeficiente_redondeado));
                }
                else
                {
                    $cadena_factor .= " + ";
                }
            }
            if ($texto_coeficiente == "")
            {
                $cadena_factor .= $coeficiente_redondeado;
            }
            else
            {
                if ($coeficiente_redondeado != 1.0)
                {
                    $cadena_factor .= $coeficiente_redondeado." * ";
                }
                $cadena_factor .= $texto_coeficiente;
            }
        }
        return ($cadena_factor);
    }
?>
