<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Lee el fichero de caudales diarios máximos mensuales
    function lee_fichero_caudales_diarios_maximos_mensuales_Espanya(
        $ruta_fichero_caudales_diarios_maximos,
        &$caudales_diarios_maximos_mensuales,
        &$mensaje_error)
    {
        $idiomas = new Idiomas();

        // Se lee el fichero de caudales diarios máximos
        // - Se lee la cabecera (se comprueba el número de columnas: 3 (mes y año y caudal diario máximo))
        // - Se lee cada una de las filas
        $fichero_caudales_diarios_maximos_correcto = true;
        $fichero_caudales_diarios_maximos = fopen($ruta_fichero_caudales_diarios_maximos, "r");
        $fila_cabecera_caudales_diarios_maximos = fgetcsv($fichero_caudales_diarios_maximos, NUMERO_MAXIMO_CARACTERES_FILA_FICHERO_CSV, SEPARADOR_COLUMNAS_FICHERO_CSV_POTENCIAS_MAXIMAS);
        if (count($fila_cabecera_caudales_diarios_maximos) != 3)
        {
            $fichero_caudales_diarios_maximos_correcto = false;
            $mensaje_error = $idiomas->_("El número de columnas del fichero de caudales diarios máximos es incorrecto");
        }
        else
        {
            $caudales_diarios_maximos_mensuales = array();
            $mes_anterior = NULL;
            $anyo_anterior = NULL;
            while (true)
            {
                // Se lee la siguiente fila
                $fila_caudales_diarios_maximos = fgetcsv($fichero_caudales_diarios_maximos, NUMERO_MAXIMO_CARACTERES_FILA_FICHERO_CSV, SEPARADOR_COLUMNAS_FICHERO_CSV_CAUDALES_DIARIOS_MAXIMOS);
                if ($fila_caudales_diarios_maximos == false)
                {
                    break;
                }

                // Si no hay elementos se pasa a la siguiente fila
                $primer_caudal_diario_maximo = array_values($fila_caudales_diarios_maximos)[0];
                if ((count($fila_caudales_diarios_maximos) == 1) && ($primer_caudal_diario_maximo === NULL))
                {
                    continue;
                }

                // Comprobación de número de elementos
                if (count($fila_caudales_diarios_maximos) != 3)
                {
                    $mensaje_error = $idiomas->_("El número de valores de las filas del fichero de caudales diarios máximos es incorrecto");
                    $fichero_caudales_diarios_maximos_correcto = false;
                    break;
                }

                // Si todos los valores de la fila están vacíos
                $numero_valores_no_vacios = count(array_filter($fila_caudales_diarios_maximos));
                if ($numero_valores_no_vacios == 0)
                {
                    continue;
                }

                // Mes y año
                $mes = $fila_caudales_diarios_maximos[0];
                $anyo = $fila_caudales_diarios_maximos[1];

                // Validación de mes y año
                // - http://stackoverflow.com/questions/2012187/how-to-check-that-a-string-is-an-int-but-not-a-double-etc
                if (($mes == NULL) || (ctype_digit($mes) == false) ||
                    ($anyo == NULL) || (ctype_digit($anyo) == false))
                {
                    $mensaje_error = $idiomas->_("Los valores del fichero de caudales diarios máximos deben ser numéricos");
                    $fichero_caudales_diarios_maximos_correcto = false;
                    break;
                }

                // Comprobación de fechas (mes entre 1 y 12, año mayor o igual que 1970)
                // - https://es.wikipedia.org/wiki/Tiempo_Unix
                if ((($mes < 1) || ($mes > 12)) || ($anyo < 1970))
                {
                    $mensaje_error = $idiomas->_("Las fechas del fichero de caudales diarios máximos son incorrectas");
                    $fichero_caudales_diarios_maximos_correcto = false;
                    break;
                }

                // Comprobación de fechas ascendentes
                if (($mes_anterior !== NULL) && ($anyo_anterior !== NULL))
                {
                    if (($anyo < $anyo_anterior) ||
                        (($anyo == $anyo_anterior) && ($mes <= $mes_anterior)))
                    {
                        $mensaje_error = $idiomas->_("Las fechas del fichero de caudales diarios máximos deben ser ascendentes");
                        $fichero_caudales_diarios_maximos_correcto = false;
                        break;
                    }
                }

                // Caudal diario máximo
                $caudal_diario_maximo = $fila_caudales_diarios_maximos[2];

                // Validación del caudal diario
                if (($caudal_diario_maximo == NULL) || (is_numeric($caudal_diario_maximo) == false))
                {
                    $mensaje_error = $idiomas->_("Los valores del fichero de caudales diarios máximos deben ser numéricos");
                    $fichero_caudales_diarios_maximos_correcto = false;
                    break;
                }
                else
                {
                    if ($caudal_diario_maximo < 0)
                    {
                        $mensaje_error = $idiomas->_("Los valores de caudales diarios máximos deben ser mayores o iguales que 0");
                        $fichero_caudales_diarios_maximos_correcto = false;
                        break;
                    }
                }

                // Redondeo del caudal diario máximo
                $caudal_diario_maximo_redondeado = round($caudal_diario_maximo);

                // Se añade el caudal diario máximo
                $caudal_diario_maximo_mes = array(
                    "mes" => $mes,
                    "anyo" => $anyo,
                    "caudal_diario_maximo" => $caudal_diario_maximo_redondeado
                );
                array_push($caudales_diarios_maximos_mensuales, $caudal_diario_maximo_mes);

                // Se guardan el mes y año anteriores
                $mes_anterior = $mes;
                $anyo_anterior = $anyo;
            }

            // Se cierra y se borra el fichero (es un fichero temporal)
            fclose($fichero_caudales_diarios_maximos);
            unlink($ruta_fichero_caudales_diarios_maximos);
        }

        // Se devuelve si se ha leído el fichero correctamente
        return ($fichero_caudales_diarios_maximos_correcto);
    }


    // Devuelve la tabla de caudal diario óptimo
    function dame_tabla_caudal_diario_optimo_Espanya($id_tabla_caudal_diario_optimo, $info_costes_caudales_diarios, &$caudal_diario_actual_optimo)
    {
        $idiomas = new Idiomas();
				$tipo_calculo_coste_termino_fijo = $info_costes_caudales_diarios["tipo_calculo_coste_termino_fijo"];
				$numero_columnas = NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_OPTIMO;

				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$numero_columnas =NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_OPTIMO_TARIFAS_2021;
				}

	       // Tabla de potencias óptimas
	      $params_tabla_caudal_diario_optimo = array(
	           "numero_columnas" => $numero_columnas,
	           "generar_valores_xml" => true
	       	);
	      $tabla_caudal_diario_optimo = new TablaDatos(
	           $id_tabla_caudal_diario_optimo,
	           $idiomas->_("Caudal diario óptimo"),
	           TIPO_TABLA_DATOS_LISTA,
	           $params_tabla_caudal_diario_optimo
	      	);

	      // Se calcula el ahorro
				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$coste_actual = $info_costes_caudales_diarios["coste_caudal_diario_contratado_actual"] + $info_costes_caudales_diarios["coste_caudal_diario_actual"];
					$coste_optimo = $info_costes_caudales_diarios["coste_caudal_diario_contratado_seleccionado"] + $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"];
				}
				else
				{
					$coste_actual = $info_costes_caudales_diarios["coste_caudal_diario_actual"];
		      $coste_optimo = $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"];
				}
	      $ahorro = $coste_actual - $coste_optimo;

	      // Si el ahorro es menor o igual que 0, el caudal diario actual es el óptimo
	      // (si el usuario es super administrador se muestran las potencias óptimas calculadas (aunque no sean mejor que la actual))
	      $caudal_diario_actual_optimo = ($ahorro <= 0) && ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR);
	      if ($caudal_diario_actual_optimo == true)
	      {
	        $info_costes_caudales_diarios["caudal_diario_seleccionado"] = $info_costes_caudales_diarios["caudal_diario_actual"];
	        $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"] = $info_costes_caudales_diarios["coste_caudal_diario_actual"];
	        $info_costes_caudales_diarios["coste_caudal_diario_contratado_seleccionado"] = $info_costes_caudales_diarios["coste_caudal_diario_contratado_actual"];
	      }

	      // Se añade la cabecera de la tabla (si el ahorro es menor que 0, es que el caudal diario óptimas calculado es peor que el actual
	      // y el usuario es superadministrador, se añade un asterisco a las columnas de caudal diario óptimo (para indicar que realmente no es el óptimo)
	      if ($ahorro < 0)
	      {
	        $sufijo_cabeceras_tabla_caudal_diario_optimo = " (*)";
	      }
				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
		      $cabecera_tabla_caudal_diario_optimo = array(
		        $idiomas->_("Caudal diario actual"),
		        $idiomas->_("Coste total actual"),
						$idiomas->_("Término fijo actual"),
						$idiomas->_("Capacidad demandada actual"),
		        $idiomas->_("Caudal diario óptimo").$sufijo_cabeceras_tabla_caudal_diario_optimo,
		        $idiomas->_("Coste óptimo").$sufijo_cabeceras_tabla_caudal_diario_optimo,
						$idiomas->_("Término fijo optimo").$sufijo_cabeceras_tabla_caudal_diario_optimo,
						$idiomas->_("Capacidad demandada optima").$sufijo_cabeceras_tabla_caudal_diario_optimo,
		        $idiomas->_("Diferencia de coste").$sufijo_cabeceras_tabla_caudal_diario_optimo
		      );
				}
				else
				{
					$cabecera_tabla_caudal_diario_optimo = array(
		        $idiomas->_("Caudal diario actual"),
		        $idiomas->_("Coste actual"),
		        $idiomas->_("Caudal diario óptimo").$sufijo_cabeceras_tabla_caudal_diario_optimo,
		        $idiomas->_("Coste óptimo").$sufijo_cabeceras_tabla_caudal_diario_optimo,
		        $idiomas->_("Diferencia de coste").$sufijo_cabeceras_tabla_caudal_diario_optimo
					);
				}
	      $tabla_caudal_diario_optimo->anyade_cabecera("", $cabecera_tabla_caudal_diario_optimo);

	      // Datos del caudal diario óptimo
	      $porcentaje_ahorro = dame_porcentaje_valor_referencia($coste_optimo, $coste_actual);
	      $datos_caudal_diario_optimo = array(
	        "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
	        "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
	        "coste_caudal_diario_contratado_actual" => $info_costes_caudales_diarios["coste_caudal_diario_contratado_actual"],
	        "caudal_diario_optimo" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
	        "coste_caudal_diario_optimo" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
	        "coste_caudal_diario_contratado_optimo" => $info_costes_caudales_diarios["coste_caudal_diario_contratado_seleccionado"],
	        "ahorro" => $ahorro,
	        "porcentaje_ahorro" => $porcentaje_ahorro);

      	// Se añaden dato a la fila de la tabla
	      if ($coste_actual == $coste_optimo)
	      {
	        $imagen_porcentaje = "<i class='icon-sort color-gris-claro'>".
	          	"<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
	          $signo_porcentaje = "";
	      }
	      else
	      {
	        if ($coste_actual < $coste_optimo)
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

	      // Unidad de medida de coste
	      $unidad_medida_coste = $_SESSION["moneda"];

	      // Se crean los datos para la fila de la tabla y se añade

				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$cadena_caudal_diario_actual = $datos_caudal_diario_optimo["caudal_diario_actual"]." ".$idiomas->_("kWh");
		      $cadena_coste_actual = formatea_numero($coste_actual, 2, false)." ".$unidad_medida_coste;
					$cadena_coste_termino_fijo_actual = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_contratado_actual"], 2, false)." ".$unidad_medida_coste;
					$cadena_coste_capacidad_demandada_actual = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_actual"], 2, false)." ".$unidad_medida_coste;
		      $cadena_caudal_diario_optimo = $datos_caudal_diario_optimo["caudal_diario_optimo"]." ".$idiomas->_("kWh");
					$cadena_coste_optimo = formatea_numero($coste_optimo, 2, false)." ".$unidad_medida_coste;
					$cadena_coste_termino_fijo_optimo = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_contratado_optimo"], 2, false)." ".$unidad_medida_coste;
					$cadena_coste_capacidad_demandada_optima = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_optimo"], 2, false)." ".$unidad_medida_coste;
					$cadena_diferencia_coste = $imagen_porcentaje." ".formatea_numero($datos_caudal_diario_optimo["ahorro"], 2, false)." ".$unidad_medida_coste.
		          " (".$signo_porcentaje.$datos_caudal_diario_optimo["porcentaje_ahorro"]." "."%".")";


					$datos_fila_caudal_diario_optimo = array(
		          $cadena_caudal_diario_actual,
		          $cadena_coste_actual,
							$cadena_coste_termino_fijo_actual,
							$cadena_coste_capacidad_demandada_actual,
		          $cadena_caudal_diario_optimo,
		          $cadena_coste_optimo,
							$cadena_coste_termino_fijo_optimo,
							$cadena_coste_capacidad_demandada_optima,
		          $cadena_diferencia_coste);
				}
				else {

					$cadena_caudal_diario_actual = $datos_caudal_diario_optimo["caudal_diario_actual"]." ".$idiomas->_("kWh");
		      $cadena_coste_actual = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_actual"], 2, false)." ".$unidad_medida_coste.
		          " (".formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_contratado_actual"], 2, false)." ".$unidad_medida_coste.")";
		      $cadena_caudal_diario_optimo = $datos_caudal_diario_optimo["caudal_diario_optimo"]." ".$idiomas->_("kWh");
		      $cadena_coste_optimo = formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_optimo"], 2, false)." ".$unidad_medida_coste.
		          " (".formatea_numero($datos_caudal_diario_optimo["coste_caudal_diario_contratado_optimo"], 2, false)." ".$unidad_medida_coste.")";
		      $cadena_diferencia_coste = $imagen_porcentaje." ".formatea_numero($datos_caudal_diario_optimo["ahorro"], 2, false)." ".$unidad_medida_coste.
		          " (".$signo_porcentaje.$datos_caudal_diario_optimo["porcentaje_ahorro"]." "."%".")";


					$datos_fila_caudal_diario_optimo = array(
							$cadena_caudal_diario_actual,
							$cadena_coste_actual,
							$cadena_caudal_diario_optimo,
							$cadena_coste_optimo,
							$cadena_diferencia_coste);
				}

	      $tabla_caudal_diario_optimo->anyade_fila("", $datos_fila_caudal_diario_optimo);

	      // Pie de tabla
	      if ($caudal_diario_actual_optimo == true)
	      {
	          $tabla_caudal_diario_optimo->anyade_pie($idiomas->_("El caudal diario actual es el caudal diario óptimo"));
	      }
	      else
	      {
	          $cadena_porcentaje_ahorro = formatea_numero($porcentaje_ahorro, 2);
	          $cadena_coste_actual = formatea_numero($coste_actual, 2, false);
	          $cadena_coste_optimo = formatea_numero($coste_optimo, 2, false);

	          $pie_tabla = $idiomas->_("Ahorro").": ".formatea_numero($ahorro, 2, false)." ".$unidad_medida_coste." (".$cadena_porcentaje_ahorro." "."%".")";
	          $pie_tabla .= " (".$idiomas->_("coste actual").": ".$cadena_coste_actual." ".$unidad_medida_coste.", ".
	              $idiomas->_("coste óptimo").": ".$cadena_coste_optimo." ".$unidad_medida_coste.")";
	          $tabla_caudal_diario_optimo->anyade_pie($pie_tabla);
	      }

        // Se devuelve la tabla
        return ($tabla_caudal_diario_optimo);
    }


    // Devuelve la tabla de caudal diario seleccionado
    function dame_tabla_caudal_diario_seleccionado_Espanya($id_tabla_caudal_diario_seleccionado, $info_costes_caudales_diarios)
    {
        $idiomas = new Idiomas();

				$tipo_calculo_coste_termino_fijo = $info_costes_caudales_diarios["tipo_calculo_coste_termino_fijo"];
				$numero_columnas = NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_SELECCIONADO;

				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$numero_columnas =NUMERO_COLUMNAS_TABLA_CAUDAL_DIARIO_SELECCIONADO_TARIFAS_2021;
				}
        // Tabla de caudal diario seleccionado
        $params_tabla_caudal_diario_seleccionado = array(
            "numero_columnas" => $numero_columnas,
            "generar_valores_xml" => true
        );
        $tabla_caudal_diario_seleccionado = new TablaDatos(
            $id_tabla_caudal_diario_seleccionado,
            $idiomas->_("Caudal diario seleccionado"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_caudal_diario_seleccionado
        );
				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
		      $cabecera_tabla_caudal_diario_seleccionado = array(
		        $idiomas->_("Caudal diario actual"),
		        $idiomas->_("Coste total actual"),
						$idiomas->_("Término fijo actual"),
						$idiomas->_("Capacidad demandada actual"),
		        $idiomas->_("Caudal diario seleccionado"),
		        $idiomas->_("Coste simulado"),
						$idiomas->_("Término fijo simulado"),
						$idiomas->_("Capacidad demandada simulada"),
		        $idiomas->_("Diferencia de coste")
		      );
				}
				else
				{
					$cabecera_tabla_caudal_diario_seleccionado = array(
	            $idiomas->_("Caudal diario actual"),
	            $idiomas->_("Coste actual"),
	            $idiomas->_("Caudal diario seleccionado"),
	            $idiomas->_("Coste simulado"),
	            $idiomas->_("Diferencia de coste")
	        );
				}
        $tabla_caudal_diario_seleccionado->anyade_cabecera("", $cabecera_tabla_caudal_diario_seleccionado);

        // Se calcula el ahorro
				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$coste_actual = $info_costes_caudales_diarios["coste_caudal_diario_contratado_actual"] + $info_costes_caudales_diarios["coste_caudal_diario_actual"];
					$coste_seleccionado = $info_costes_caudales_diarios["coste_caudal_diario_contratado_seleccionado"] + $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"];
				}
				else
				{
					$coste_actual = $info_costes_caudales_diarios["coste_caudal_diario_actual"];
		      $coste_seleccionado = $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"];
				}
        $ahorro = $coste_actual - $coste_seleccionado;

        // Datos del caudal diario seleccionado
        $porcentaje_ahorro = dame_porcentaje_valor_referencia($coste_seleccionado, $coste_actual);
        $datos_caudal_diario_seleccionado = array(
            "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
            "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
            "coste_caudal_diario_contratado_actual" => $info_costes_caudales_diarios["coste_caudal_diario_contratado_actual"],
            "caudal_diario_seleccionado" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
            "coste_caudal_diario_seleccionado" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
            "coste_caudal_diario_contratado_seleccionado" => $info_costes_caudales_diarios["coste_caudal_diario_contratado_seleccionado"],
            "ahorro" => $ahorro,
            "porcentaje_ahorro" => $porcentaje_ahorro);

        // Imagen y signo del porcentaje
        if ($coste_actual == $coste_seleccionado)
        {
            $imagen_porcentaje = "<i class='icon-sort color-gris-claro'>".
                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
            $signo_porcentaje = "";
        }
        else
        {
            if ($coste_actual < $coste_seleccionado)
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

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

				if ($tipo_calculo_coste_termino_fijo ==  TIPO_CALCULO_COSTE_TARIFAS_2021)
				{
					$cadena_caudal_diario_actual = $datos_caudal_diario_seleccionado["caudal_diario_actual"]." ".$idiomas->_("kWh");
		      $cadena_coste_actual = formatea_numero($coste_actual, 2, false)." ".$unidad_medida_coste;
					$cadena_coste_termino_fijo_actual = formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_contratado_actual"], 2, false)." ".$unidad_medida_coste;
					$cadena_coste_capacidad_demandada_actual = formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_actual"], 2, false)." ".$unidad_medida_coste;
		      $cadena_caudal_diario_seleccionado = $datos_caudal_diario_seleccionado["caudal_diario_seleccionado"]." ".$idiomas->_("kWh");
					$cadena_coste_seleccionado = formatea_numero($coste_seleccionado, 2, false)." ".$unidad_medida_coste;
					$cadena_coste_termino_fijo_seleccionado = formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_contratado_seleccionado"], 2, false)." ".$unidad_medida_coste;
					$cadena_coste_capacidad_demandada_seleccionada = formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_seleccionado"], 2, false)." ".$unidad_medida_coste;
					$cadena_diferencia_coste = $imagen_porcentaje." ".formatea_numero($datos_caudal_diario_seleccionado["ahorro"], 2, false)." ".$unidad_medida_coste.
		          " (".$signo_porcentaje.$datos_caudal_diario_seleccionado["porcentaje_ahorro"]." "."%".")";


					$datos_fila_caudal_diario_seleccionado = array(
		          $cadena_caudal_diario_actual,
		          $cadena_coste_actual,
							$cadena_coste_termino_fijo_actual,
							$cadena_coste_capacidad_demandada_actual,
		          $cadena_caudal_diario_seleccionado,
		          $cadena_coste_seleccionado,
							$cadena_coste_termino_fijo_seleccionado,
							$cadena_coste_capacidad_demandada_seleccionada,
		          $cadena_diferencia_coste);
				}
				else {
	        // Se crean los datos para la fila de la tabla y se añade
	        $datos_fila_caudal_diario_seleccionado = array(
	            $datos_caudal_diario_seleccionado["caudal_diario_actual"]." ".$idiomas->_("kWh"),
	            formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_actual"], 2, false)." ".$unidad_medida_coste.
	                " (".formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_contratado_actual"], 2, false)." ".$unidad_medida_coste.")",
	            $datos_caudal_diario_seleccionado["caudal_diario_seleccionado"]." ".$idiomas->_("kWh"),
	            formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_seleccionado"], 2, false)." ".$unidad_medida_coste.
	                " (".formatea_numero($datos_caudal_diario_seleccionado["coste_caudal_diario_contratado_seleccionado"], 2, false)." ".$unidad_medida_coste.")",
	            $imagen_porcentaje." ".formatea_numero($datos_caudal_diario_seleccionado["ahorro"], 2, false)." ".$unidad_medida_coste." (".$signo_porcentaje.$datos_caudal_diario_seleccionado["porcentaje_ahorro"]." "."%".")");
				}

				$tabla_caudal_diario_seleccionado->anyade_fila("", $datos_fila_caudal_diario_seleccionado);

        // Pie de tabla
        $cadena_porcentaje_ahorro = formatea_numero($porcentaje_ahorro, 2);
        $cadena_coste_actual = formatea_numero($coste_actual, 2, false);
        $cadena_coste_seleccionado = formatea_numero($coste_seleccionado, 2, false);

        $signo_porcentaje = "";
        if ($ahorro < 0)
        {
            $signo_porcentaje = "-";
        }
        $pie_tabla = $idiomas->_("Ahorro").": ".formatea_numero($ahorro, 2, false)." ".$unidad_medida_coste." (".$signo_porcentaje.$cadena_porcentaje_ahorro." "."%".")";
        $pie_tabla .= " (".$idiomas->_("coste actual").": ".$cadena_coste_actual." ".$unidad_medida_coste.", ".
            $idiomas->_("coste seleccionado").": ".$cadena_coste_seleccionado." ".$unidad_medida_coste.")";
        $tabla_caudal_diario_seleccionado->anyade_pie($pie_tabla);

        // Se devuelve la tabla
        return ($tabla_caudal_diario_seleccionado);
    }


    // Devuelve el porcentaje de rango de caudales diarios para los optimizadores y simuladores de caudal diario
    function dame_porcentaje_rango_caudales_diarios_optimizador_simulador_caudales_Espanya($rango_caudales_diarios)
    {
        switch ($rango_caudales_diarios)
        {
            case RANGO_CAUDALES_DIARIOS_MAXIMO:
            {
                $porcentaje_rango_caudales_diarios = PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MAXIMO;
                break;
            }
            case RANGO_CAUDALES_DIARIOS_MEDIO:
            {
                $porcentaje_rango_caudales_diarios = PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MEDIO;
                break;
            }
            case RANGO_CAUDALES_DIARIOS_MINIMO:
            {
                $porcentaje_rango_caudales_diarios = PORCENTAJE_RANGO_OPTIMIZADOR_CAUDALES_DIARIOS_MINIMO;
                break;
            }
        }
        return ($porcentaje_rango_caudales_diarios);
    }


    // Devuelve el paso máximo de los caudales diarios
    function dame_paso_maximo_caudales_diarios_Espanya($info_costes_caudales_diarios)
    {
        $paso_maximo_caudales_diarios = 1;
        $caudales_diario_costes = $info_costes_caudales_diarios["caudales_diarios_costes"];
        if (count($caudales_diario_costes) > 1)
        {
            $paso_caudales_diarios = $caudales_diario_costes[1][0] - $caudales_diario_costes[0][0];
            if ($paso_caudales_diarios > $paso_maximo_caudales_diarios)
            {
                $paso_maximo_caudales_diarios = $paso_caudales_diarios;
            }
        }
        return ($paso_maximo_caudales_diarios);
    }
?>
