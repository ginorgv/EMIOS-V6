<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');

    // Clase que representa una tarifa eléctrica (España)
	class TarifaElectrica_Portugal extends Tarifa
	{

        // Funciones estáticas


        // TABLA DE TARIFAS
        // Dibuja la tabla de tarifas
        static function dame_tabla_tarifas_electricas($filtro, $tipo, $ciclo, $id_grupo, $estado)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
			$opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_tarifa_electrica = "<i id='anyade_modifica_tarifa_electrica' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_tarifa_electrica);
            }
            $boton_actualizar_tabla_tarifas_electricas = "<i id='actualiza_tarifas_electricas' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_tarifas_electricidad_Portugal boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_tarifas_electricas);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-tarifas-electricas",
                $idiomas->_("Tarifas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = TarifaElectrica_Portugal::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las tarifas a la tabla y el pie de tabla
            $consulta = TarifaElectrica_Portugal::dame_consulta_tarifas_electricas(
                $filtro,
                $tipo,
                $ciclo,
                $id_grupo,
                $estado);
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Identificadores de tarifas del usuario actual
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_tarifas_electricas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_ELECTRICIDAD);
            }

            // Se añaden las tarifas eléctricas
            $numero_tarifas_electricas = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $tarifa_electrica = new TarifaElectrica_Portugal($fila);

                $anyadir_tarifa_electrica = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($tarifa_electrica->id, $ids_tarifas_electricas_usuario) == false)
                    {
                        $anyadir_tarifa_electrica = false;
                    }
                }

                if ($anyadir_tarifa_electrica == true)
                {
                    // Si hay filtrado por estado y el estado no coincide, no se añade la tarifa eléctrica a la tabla
                    $info_tabla_tarifa_electrica = $tarifa_electrica->dame_info_tabla();
                    if (($estado != ESTADO_TARIFA_TODOS) && ($estado != $info_tabla_tarifa_electrica["estado"]))
                    {
                        continue;
                    }

                    $params_fila = array(
                        "opciones" => $tarifa_electrica->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosTarifaElectrica_Portugal__".$fila['id'],
                        $info_tabla_tarifa_electrica["datos"],
                        $params_fila
                    );
                    $numero_tarifas_electricas += 1;

                }
            }
			$tabla->anyade_pie($idiomas->_("Tarifas").": ".$numero_tarifas_electricas);

            return ($tabla->dame_tabla());
		}


		// Devuelve la cabecera para la tabla de tarifas
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Ciclo"),
                $idiomas->_("Grupo"),
                $idiomas->_("Fecha de expiración")
			));
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
                    "class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' nombre_tarifa_electrica='".$nombre."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_tarifa_electricidad_Portugal boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        // Devuelve la consulta para la tabla de tarifas eléctricas
        static function dame_consulta_tarifas_electricas($filtro, $tipo, $ciclo, $id_grupo)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
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
            if ($ciclo != CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS)
            {
                $consulta .= "
                    AND (ciclo = '".$bd_red->_($ciclo)."')";
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
                    FROM ".TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL."
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
                        $tabla_tarifas_electricas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
                        $hay_tarifa_electrica_grupo_mas_reciente = $this->dame_hay_tarifa_grupo_mas_reciente($tabla_tarifas_electricas);
                        if ($hay_tarifa_electrica_grupo_mas_reciente == false)
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
                TarifaElectrica_Portugal::dame_descripcion_tipo_tarifa_electrica($this->params["tipo"]),
                TarifaElectrica_Portugal::dame_descripcion_ciclo_tarifa_electrica($this->params["ciclo"]),
                $nombre_grupo,
                $cadena_fecha_expiracion_local_local);
            $info_tabla = array(
                "datos" => $datos_tabla,
                "estado" => $estado_tabla);
            return ($info_tabla);
		}




        //
        // Tipos de tarifa eléctrica
        //


        // Función que devuelve el array de los tipos de tarifas
        static function dame_tipos_tarifa_electrica()
        {
            $tipos_tarifa_electrica = array();

            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_PORTUGAL_MAT);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_PORTUGAL_AT);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_PORTUGAL_MT);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_PORTUGAL_BTE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_PORTUGAL_BTN);
            return ($tipos_tarifa_electrica);
        }


        // Devuelve el texto correspondiente a cada tipo de tarifa para representarlo en la plataforma
        static function dame_descripcion_tipo_tarifa_electrica($tipo_tarifa)
        {
            $idiomas = new Idiomas();

            $descripcion_tipo_tarifa = $idiomas->_("Portugal")." ";
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
                case TIPO_TARIFA_ELECTRICA_PORTUGAL_MAT:
                {
                    $descripcion_tipo_tarifa .= "MAT";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_PORTUGAL_AT:
                {
                    $descripcion_tipo_tarifa .= "AT";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_PORTUGAL_MT:
                {
                    $descripcion_tipo_tarifa .= "MT";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_PORTUGAL_BTE:
                {
                    $descripcion_tipo_tarifa .= "BTE";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_PORTUGAL_BTN:
                {
                    $descripcion_tipo_tarifa .= "BTN";
                    break;
                }
                default:
                {
                    $descripcion_tipo_tarifa = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($descripcion_tipo_tarifa);
        }

        // Función que devuelve el array de los tipos de tarifas
        static function dame_ciclos_tarifa_electrica()
        {
            $ciclos_tarifa_electrica = array();
            array_push($ciclos_tarifa_electrica, CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_COM_FERIADOS);
						array_push($ciclos_tarifa_electrica, CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_SEM_FERIADOS);
            array_push($ciclos_tarifa_electrica, CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_OPCIONAL);
            array_push($ciclos_tarifa_electrica, CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO);
            array_push($ciclos_tarifa_electrica, CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO_OPCIONAL);
            return ($ciclos_tarifa_electrica);
        }


        // Devuelve el texto correspondiente a cada ciclo de tarifa disponible para representarlo en la plataforma
        static function dame_descripcion_ciclo_tarifa_electrica($ciclo_tarifa)
        {
            $idiomas = new Idiomas();
            $descripcion_ciclo_tarifa = "";
            switch ($ciclo_tarifa)
            {
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_NINGUNO:
                {
                    $descripcion_ciclo_tarifa = $idiomas->_("Ninguno");
                    break;
                }
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS:
                {
                    $descripcion_ciclo_tarifa = $idiomas->_("Todos");
                    break;
                }
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_COM_FERIADOS:
                {
                    $descripcion_ciclo_tarifa = "Semanal com feriados";
                    break;
                }
								case CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_SEM_FERIADOS:
                {
                    $descripcion_ciclo_tarifa = "Semanal sem feriados";
                    break;
                }
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_SEMANAL_OPCIONAL:
                {
                    $descripcion_ciclo_tarifa = "Semanal opcional";
                    break;
                }
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO:
                {
                    $descripcion_ciclo_tarifa = "Diario";
                    break;
                }
                case CICLO_TARIFA_ELECTRICA_PORTUGAL_DIARIO_OPCIONAL:
                {
                    $descripcion_ciclo_tarifa = "Diario opcional";
                    break;
                }
                default:
                {
                    $descripcion_ciclo_tarifa = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($descripcion_ciclo_tarifa);
        }

        // Función que devuelve el array de las regiones tarifarias de Portugal
        static function dame_regiones_Portugal()
        {
            $regiones_Portugal = array();

            array_push($regiones_Portugal, REGION_PORTUGAL_CONTINENTAL);
            array_push($regiones_Portugal, REGION_PORTUGAL_AZORES);
            array_push($regiones_Portugal, REGION_PORTUGAL_MADEIRA);
            return ($regiones_Portugal);
        }


        // Devuelve el texto correspondiente a cada ciclo de tarifa disponible para representarlo en la plataforma
        static function dame_descripcion_regiones_Portugal($region_Portugal)
        {
            $idiomas = new Idiomas();
            $descripcion_region_Portugal = "";
            switch ($region_Portugal)
            {
                case OPCIONES_EXTRA_LISTA_REGIONES_PORTUGAL_NINGUNO:
                {
                    $descripcion_region_Portugal = $idiomas->_("Ninguna");
                    break;
                }
                case OPCIONES_EXTRA_LISTA_REGIONES_PORTUGAL_TODOS:
                {
                    $descripcion_region_Portugal = $idiomas->_("Todas");
                    break;
                }
                case REGION_PORTUGAL_CONTINENTAL:
                {
                    $descripcion_region_Portugal = "Portugal Continental";
                    break;
                }
                case REGION_PORTUGAL_AZORES:
                {
                    $descripcion_region_Portugal = "Açores";
                    break;
                }
                case REGION_PORTUGAL_MADEIRA:
                {
                    $descripcion_region_Portugal = "Madeira";
                    break;
                }
                default:
                {
                    $descripcion_region_Portugal = $idiomas->_("Desconocida");
                    break;
                }
            }
            return ($descripcion_region_Portugal);
        }


        // Devuelve las caractgerísticas de las tarifas eléctricas necesarias para hacer el cálculo de la tarifa.
        static function dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa)
        {
            $idiomas = new Idiomas();

						if ($tipo_tarifa == TIPO_TARIFA_ELECTRICA_PORTUGAL_BTN)
						{
							$caracteristicas_tipo_tarifa = array();
	            $caracteristicas_tipo_tarifa["numero_tramos"] = 3;
	            $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
	            $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
	            $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
	            $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
	            $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
						}
						else {
							$caracteristicas_tipo_tarifa = array();
							$caracteristicas_tipo_tarifa["numero_tramos"] = 4;
							$caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
							$caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
							$caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
							$caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
							$caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
						}

            // Se devuelven las características del tipo de tarifa
            return ($caracteristicas_tipo_tarifa);
        }


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Características de tipo de la tarifa eléctrica
            $tipo_tarifa_electrica = $this->params["tipo"];
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);

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

            // Aviso de expiración de tarifa eléctrica
            $tabla_tarifas_electricas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
            $aviso_expiracion .= $this->dame_aviso_expiracion($tabla_tarifas_electricas);
            if ($aviso_expiracion != "")
            {
                $info .= $aviso_expiracion;
                $info .= "<br/>";
            }

            // Tabla de tramos (de tarifa eléctrica)
            $info .= $this->dame_tabla_tramos();
            $info .= "<br/>";


            // Parámetros de factura eléctrica
            $impuesto_electrico = $this->params["impuesto_electrico"];
            $tipo_alquiler_contador = $this->params["tipo_alquiler_contador"];
            $alquiler_contador = $this->params["alquiler_contador"];
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Factura").": ";
            $info .= "<ul>";
            if ($impuesto_electrico > 0)
            {
                $info .= "<li>";
                $info .= $this->idiomas->_("Impuesto eléctrico").": ".formatea_numero($impuesto_electrico, 2)." %";
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
            $descripcion_iva = $this->idiomas->_("IVA");
            $info .= $descripcion_iva.": ".$iva." %"."<br/>";
            $info .= "</li>";

						$contribucion_audiovisual = formatea_numero($this->params["contribucion_audiovisual"], 2);
						$info .= "<li>";
						$descripcion_contribucion_audiovisual = $this->idiomas->_("Contribución audiovisual");
						$info .= $descripcion_contribucion_audiovisual.": ".$contribucion_audiovisual." €"."<br/>";
						$info .= "</li>";

						$iva_reducido = formatea_numero($this->params["iva_reducido"], 2);
						$info .= "<li>";
						$descripcion_iva_reducido = $this->idiomas->_("IVA Reducido");
						$info .= $descripcion_iva_reducido.": ".$iva_reducido." %"."<br/>";
						$info .= "</li>";

            $info .= "</ul>";
            $info .= "<br/>";

            // Sensores a los que está asignada esta tarifa eléctrica
            $consulta_sensores = "
				SELECT nombre
				FROM sensores
				WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_ENERGIA_ACTIVA."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_Portugal_ID_TARIFA_ELECTRICA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')
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
            $tabla_conceptos_adicionales_factura = dame_tabla_conceptos_adicionales_factura_tarifa(MEDICION_ELECTRICIDAD, $this->id);
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
        // Tipos de alquiler de contador
        //


        static function dame_tipos_alquiler_contador_tarifa_electrica()
        {
            $tipos_alquiler_contador_tarifa = array();
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_NINGUNO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_DIARIO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_FIJO);
            return ($tipos_alquiler_contador_tarifa);
        }


        static function dame_descripcion_tipo_alquiler_contador_tarifa_electrica($tipo_alquiler_contador_tarifa)
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


        function dame_tabla_tramos()
        {
            $id_elemento_tramos_tarifa_electrica = "tramos-tarifa-electrica".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_tramos = "<div id='".$id_elemento_tramos_tarifa_electrica."' class='contenedor-detalle-tabla-datos'>".
                dame_tabla_tramos_tarifa_electricidad_Portugal($this->id, NULL, false)."</div>";
            return ($tabla_tramos);
        }


    }
?>
