<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');


    // Clase que representa una tarifa de gas (España)
	class TarifaGas_Espanya extends Tarifa
	{
        // Funciones estáticas


		// Devuelve la cabecera para la tabla de tarifas
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Grupo"),
                $idiomas->_("Fecha de expiración")
			));
		}


        // Devuelve la consulta para la tabla de tarifas de gas
        static function dame_consulta_tarifas_gas($filtro, $tipo, $id_grupo)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT *
				FROM ".TABLA_TARIFAS_GAS_ESPANYA."
				WHERE
					(red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($tipo != TIPO_TARIFA_TODOS)
            {
                $consulta .= "
                    AND (tipo = '".$bd_red->_($tipo)."')";
            }
            if ($id_grupo != ID_TODOS)
            {
                $consulta .= "
                    AND (grupo = '".$bd_red->_($id_grupo)."')";
            }
            switch ($id_grupo)
            {
                case ID_TODOS:
                case ID_NINGUNO:
                {
                    $consulta .= "
                        ORDER BY nombre ASC";
                    break;
                }
                default:
                {
                    $consulta .= "
                        ORDER BY fecha_expiracion DESC";
                    break;
                }
            }
			return ($consulta);
		}


        // Devuelve la tabla de tarifas de gas
        static function dame_tabla_tarifas_gas($filtro, $tipo, $id_grupo, $estado)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
			$opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_tarifa_gas = "<i id='anyade_modifica_tarifa_gas' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_tarifa_gas);
            }
            $boton_actualizar_tabla_tarifas_gas = "<i id='actualiza_tarifas_gas' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_tarifas_gas_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_tarifas_gas);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-tarifas-gas",
                $idiomas->_("Tarifas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = TarifaGas_Espanya::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las tarifas a la tabla y el pie de tabla
            $consulta = TarifaGas_Espanya::dame_consulta_tarifas_gas(
                $filtro,
                $tipo,
                $id_grupo);
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Identificadores de tarifas del usuario actual
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_tarifas_gas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_GAS);
            }

            // Se añaden las tarifas de gas
            $numero_tarifas_gas = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $tarifa_gas = new TarifaGas_Espanya($fila);

                $anyadir_tarifa_gas = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($tarifa_gas->id, $ids_tarifas_gas_usuario) == false)
                    {
                        $anyadir_tarifa_gas = false;
                    }
                }

                if ($anyadir_tarifa_gas == true)
                {
                    // Si hay filtrado por estado y el estado no coincide, no se añade la tarifa de gas a la tabla
                    $info_tabla_tarifa_gas = $tarifa_gas->dame_info_tabla();
                    if (($estado != ESTADO_TARIFA_TODOS) && ($estado != $info_tabla_tarifa_gas["estado"]))
                    {
                        continue;
                    }

                    $params_fila = array(
                        "opciones" => $tarifa_gas->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosTarifaGas_Espanya__".$fila['id'],
                        $info_tabla_tarifa_gas["datos"],
                        $params_fila
                    );
                    $numero_tarifas_gas += 1;
                }
            }
			$tabla->anyade_pie($idiomas->_("Tarifas").": ".$numero_tarifas_gas);

            return ($tabla->dame_tabla());
		}


		// Funciones


		// Información para la tabla (datos y estado)
		function dame_info_tabla()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Nombre del grupo
            if ($this->params["grupo"] != ID_NINGUNO)
            {
                $consulta_grupo = "
                    SELECT nombre
                    FROM ".TABLA_GRUPOS_TARIFAS_GAS_ESPANYA."
                    WHERE
                        id = '".$bd_red->_($this->params["grupo"])."'";
                $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                }
                $fila_grupo = $res_grupo->dame_siguiente_fila();
                $nombre_grupo = $fila_grupo["nombre"];
            }
            else
            {
                $nombre_grupo = $this->idiomas->_("Ninguno");
            }

            // Estado de tabla e información de expiración
            $estado_tabla = ESTADO_TARIFA_OK;
            $expiracion = $this->params["expiracion"];
            switch ($expiracion)
            {
                case EXPIRACION_TARIFA_NO:
                {
                    $cadena_fecha_expiracion_local_local = $this->idiomas->_("ND");
                    break;
                }
                case EXPIRACION_TARIFA_SI:
                {
                    $cadena_fecha_expiracion_local_local = convierte_formato_fecha($this->params["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                    $numero_dias_restantes_expiracion = dame_numero_dias_restantes_expiracion_tarifa($this->params["fecha_expiracion"]);
                    if (($numero_dias_restantes_expiracion <= $this->params["numero_dias_preaviso_expiracion"]) || ($numero_dias_restantes_expiracion <= 0))
                    {
                        // Nota: Diferentes iconos en la fecha de expiración dependiendo de si hay tarifa en el grupo (si existe) más reciente
                        $tabla_tarifas_gas = dame_nombre_tabla_tarifas(MEDICION_GAS);
                        $hay_tarifa_gas_grupo_mas_reciente = $this->dame_hay_tarifa_grupo_mas_reciente($tabla_tarifas_gas);
                        if ($hay_tarifa_gas_grupo_mas_reciente == false)
                        {
                            if ($numero_dias_restantes_expiracion <= 0)
                            {
                                $clases_icono_fecha_expiracion = "icon-bell-alt color-rojo";
                                $estado_tabla = ESTADO_TARIFA_EXPIRADA;
                            }
                            else
                            {
                                $clases_icono_fecha_expiracion = "icon-warning-sign color-rojo";
                                $estado_tabla = ESTADO_TARIFA_AVISO_EXPIRACION;
                            }
                        }
                        else
                        {
                            $clases_icono_fecha_expiracion = "icon-remove color-gris-claro";
                            $estado_tabla = ESTADO_TARIFA_EXPIRADA;
                        }

                        $cadena_fecha_expiracion_local_local .= " <iconos-dato class='iconos-dato'>["."<i class='".$clases_icono_fecha_expiracion."'></i>"."]</iconos-dato>";
                    }
                    break;
                }
            }

            $datos_tabla = array(
				htmlspecialchars($this->params["nombre"], ENT_QUOTES),
                TarifaGas_Espanya::dame_descripcion_tipo_tarifa_gas($this->params["tipo"]),
                $nombre_grupo,
                $cadena_fecha_expiracion_local_local);
            $info_tabla = array(
                "datos" => $datos_tabla,
                "estado" => $estado_tabla);
            return ($info_tabla);
		}


		// Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            if ($administracion_tarifas == true)
            {
                $editar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' nombre_tarifa_gas='".$nombre."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_tarifa_gas_Espanya boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Identificador y descripción
            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }
			if ($this->params["descripcion"] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($this->params["descripcion"], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Aviso de expiración de tarifa de gas
            $tabla_tarifas_gas = dame_nombre_tabla_tarifas(MEDICION_GAS);
            $aviso_expiracion .= $this->dame_aviso_expiracion($tabla_tarifas_gas);
            if ($aviso_expiracion != "")
            {
                $info .= $aviso_expiracion;
                $info .= "<br/>";
            }

            // Tabla de parámetros (de tarifa de gas)
            $info .= $this->dame_tabla_parametros();
            $info .= "<br/>";

            // Parámetros de factura de gas
            $impuesto_gas = $this->params["impuesto_gas"];
            $tipo_alquiler_contador = $this->params["tipo_alquiler_contador"];
            $alquiler_contador = $this->params["alquiler_contador"];
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Factura").": ";
            $info .= "<ul>";
            if ($impuesto_gas > 0)
            {
                $info .= "<li>";
                $info .= $this->idiomas->_("Impuesto de gas").": ".formatea_numero($impuesto_gas, 4)." ".$this->idiomas->_("€")."/".$this->idiomas->_("kWh");
                $info .= "</li>";
            }
            if ($alquiler_contador > 0)
            {
                $info .= "<li>";
                $info .= $this->idiomas->_("Alquiler de contador").": ".formatea_numero($alquiler_contador, 2)." ".$unidad_medida_coste;
                if ($tipo_alquiler_contador == TIPO_ALQUILER_CONTADOR_DIARIO)
                {
                    $info .= "/".$this->idiomas->_("día");
                }
                $info .= "</li>";
            }
            $iva = formatea_numero($this->params["iva"], 2);
            $info .= "<li>";
            $info .= $this->idiomas->_("IVA").": ".$iva." %"."<br/>";
            $info .= "</li>";
            $info .= "</ul>";
            $info .= "<br/>";

            // Sensores a los que está asignada esta tarifa de gas
            $consulta_sensores = "
				SELECT nombre
				FROM sensores
				WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_GAS."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')
                ORDER BY nombre ASC";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
            }
            $numero_sensores = $res_sensores->dame_numero_filas();
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_sensores > 0)
            {
                if ($numero_sensores == 1)
                {
                    $info .= $this->idiomas->_("Esta tarifa está asignada a")." ".$numero_sensores." ".$this->idiomas->_("sensor").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Esta tarifa está asignada a")." ".$numero_sensores." ".$this->idiomas->_("sensores").":";
                }
                $nombres_sensores = "<ul>";
                while ($fila_sensor = $res_sensores->dame_siguiente_fila())
                {
                    $nombres_sensores .= "<li>".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</li>";
                }
                $nombres_sensores .= "</ul>";
                $info .= $nombres_sensores;
            }
            else
            {
                $info .= $this->idiomas->_("Esta tarifa no está asignada a ningún sensor")."<br/>";
            }
            $info .= "<br/>";

            // Tabla de conceptos adicionales de factura
            $tabla_conceptos_adicionales_factura = dame_tabla_conceptos_adicionales_factura_tarifa(MEDICION_GAS, $this->id);
            if ($tabla_conceptos_adicionales_factura !== NULL)
            {
                // Se muestra la tabla de los conceptos adicionales de factura
                $id_elemento_conceptos_adicionales_factura = 'conceptos-adicionales-factura-tarifa'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<div id='".$id_elemento_conceptos_adicionales_factura."' class='contenedor-detalle-tabla-datos'>".
                    $tabla_conceptos_adicionales_factura."</div>";
                $info .= "<br/>";
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
        }


        //
        // Tipos de tarifa de gas
        //


        static function dame_tipos_tarifa_gas()
        {
            $tipos_tarifa_gas = array();
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL1);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL1_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL2);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL2_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL3);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL3_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL4);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL4_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL5);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL5_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL6);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL6_C);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL7);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL8);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL9);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL10);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_RL11);
			array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_1X);
            array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_2X);
            array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_3X);
            array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_35);
            array_push($tipos_tarifa_gas, TIPO_TARIFA_GAS_ESPANYA_4X);
            return ($tipos_tarifa_gas);
        }


        static function dame_descripcion_tipo_tarifa_gas($tipo_tarifa)
        {
            $idiomas = new Idiomas();

            $descripcion_tipo_tarifa = $idiomas->_("España")." ";
            switch ($tipo_tarifa)
            {
                case TIPO_TARIFA_NINGUNO:
                {
                    $descripcion_tipo_tarifa = $idiomas->_("Ninguno");
                    break;
                }
                case TIPO_TARIFA_TODOS:
                {
                    $descripcion_tipo_tarifa = $idiomas->_("Todos");
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_1X:
                {
                    $descripcion_tipo_tarifa .= "1.X";
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_2X:
                {
                    $descripcion_tipo_tarifa .= "2.X";
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_3X:
                {
                    $descripcion_tipo_tarifa .= "3.X (".$idiomas->_("excepto")." 3.5)";
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_35:
                {
                    $descripcion_tipo_tarifa .= "3.5";
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_4X:
                {
                    $descripcion_tipo_tarifa .= "4.X";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL1:
                {
                    $descripcion_tipo_tarifa .= "RL.1 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL1_C:
                {
                    $descripcion_tipo_tarifa .= "RL.1 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL2:
                {
                    $descripcion_tipo_tarifa .= "RL.2 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL2_C:
                {
                    $descripcion_tipo_tarifa .= "RL.2 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL3:
                {
                    $descripcion_tipo_tarifa .= "RL.3 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL3_C:
                {
                    $descripcion_tipo_tarifa .= "RL.3 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL4:
                {
                    $descripcion_tipo_tarifa .= "RL.4 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL4_C:
                {
                    $descripcion_tipo_tarifa .= "RL.4 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL5:
                {
                    $descripcion_tipo_tarifa .= "RL.5 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL5_C:
                {
                    $descripcion_tipo_tarifa .= "RL.5 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL6:
                {
                    $descripcion_tipo_tarifa .= "RL.6 Por Capacidad";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL6_C:
                {
                    $descripcion_tipo_tarifa .= "RL.6 Por Cliente";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL7:
                {
                    $descripcion_tipo_tarifa .= "RL.7";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL8:
                {
                    $descripcion_tipo_tarifa .= "RL.8";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL9:
                {
                    $descripcion_tipo_tarifa .= "RL.9";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL10:
                {
                    $descripcion_tipo_tarifa .= "RL.10";
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL11:
                {
                    $descripcion_tipo_tarifa .= "RL.11";
                    break;
                }
                default:
                {
                    $descripcion_tipo_tarifa = $idiomas->_("Desconocida");
                    break;
                }
            }
            return ($descripcion_tipo_tarifa);
        }


        static function dame_caracteristicas_tipo_tarifa_gas($tipo_tarifa)
        {
            $caracteristicas_tipo_tarifa = array();
            switch ($tipo_tarifa)
            {
                case TIPO_TARIFA_GAS_ESPANYA_1X:
                case TIPO_TARIFA_GAS_ESPANYA_2X:
                case TIPO_TARIFA_GAS_ESPANYA_35:
                case TIPO_TARIFA_GAS_ESPANYA_4X:
                {
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES;
                    break;
                }
                case TIPO_TARIFA_GAS_ESPANYA_3X:
                {
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS;
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL1:
				case TIPO_TARIFA_GAS_ESPANYA_RL2:
				case TIPO_TARIFA_GAS_ESPANYA_RL3:
				case TIPO_TARIFA_GAS_ESPANYA_RL4:
				case TIPO_TARIFA_GAS_ESPANYA_RL5:
				case TIPO_TARIFA_GAS_ESPANYA_RL6:
				case TIPO_TARIFA_GAS_ESPANYA_RL7:
				case TIPO_TARIFA_GAS_ESPANYA_RL8:
				case TIPO_TARIFA_GAS_ESPANYA_RL9:
				case TIPO_TARIFA_GAS_ESPANYA_RL10:
				case TIPO_TARIFA_GAS_ESPANYA_RL11:
                {
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_TARIFAS_2021;
                    break;
                }
				case TIPO_TARIFA_GAS_ESPANYA_RL1_C:
				case TIPO_TARIFA_GAS_ESPANYA_RL2_C:
				case TIPO_TARIFA_GAS_ESPANYA_RL3_C:
				case TIPO_TARIFA_GAS_ESPANYA_RL4_C:
				case TIPO_TARIFA_GAS_ESPANYA_RL5_C:
				case TIPO_TARIFA_GAS_ESPANYA_RL6_C:
				{
					$caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_POR_CLIENTE;
					break;
				}
                // Ninguno y todos
                case TIPO_TARIFA_NINGUNO:
                case TIPO_TARIFA_TODOS:
                {
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_TERMINO_FIJO_NINGUNO;
                    break;
                }
                // Nota: Por defecto se devuelve igual que ninguno y todos para que al menos se pueda cargar la lista de tarifas
                // con tipos de tarifa desconocidos
                default:
                {
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_termino_fijo"] = TIPO_CALCULO_COSTE_TERMINO_FIJO_NINGUNO;
                    break;
                }
            }
            return ($caracteristicas_tipo_tarifa);
        }


        //
        // Tipos de alquiler de contador
        //


        static function dame_tipos_alquiler_contador_tarifa_gas()
        {
            $tipos_alquiler_contador_tarifa = array();
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_NINGUNO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_DIARIO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_FIJO);
            return ($tipos_alquiler_contador_tarifa);
        }


        static function dame_descripcion_tipo_alquiler_contador_tarifa_gas($tipo_alquiler_contador_tarifa)
        {
            switch ($tipo_alquiler_contador_tarifa)
            {
                case TIPO_ALQUILER_CONTADOR_NINGUNO:
                {
                    $descripcion_tipo_alquiler_contador_tarifa = "Ninguno";
                    break;
                }
                case TIPO_ALQUILER_CONTADOR_DIARIO:
                {
                    $descripcion_tipo_alquiler_contador_tarifa = "Diario";
                    break;
                }
                case TIPO_ALQUILER_CONTADOR_FIJO:
                {
                    $descripcion_tipo_alquiler_contador_tarifa = "Fijo";
                    break;
                }
                default:
                {
                    $descripcion_tipo_alquiler_contador_tarifa = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_alquiler_contador_tarifa));
        }


        //
        // Funciones auxiliares
        //


        function dame_tabla_parametros()
		{
            $id_elemento_parametros_tarifa_gas = "parametros-tarifa-gas".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_parametros = "<div id='".$id_elemento_parametros_tarifa_gas."' class='contenedor-detalle-tabla-datos'>".
                dame_tabla_parametros_tarifa_gas_Espanya($this->id, NULL, false)."</div>";
            return ($tabla_parametros);
		}
	}
?>
