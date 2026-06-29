<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');



    // Clase que representa una tarifa eléctrica (España)
	class TarifaElectrica_Espanya extends Tarifa
	{
        // Funciones estáticas


		// Devuelve la cabecera para la tabla de tarifas
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Contrato"),
                $idiomas->_("Grupo"),
                $idiomas->_("Fecha de expiración")
			));
		}


        // Devuelve la consulta para la tabla de tarifas eléctricas
        static function dame_consulta_tarifas_electricas($filtro, $tipo, $contrato, $id_grupo)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT *
				FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
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
                //EMG: las tarifas 3.0TD pueden ser max o no, así que en vez de = ponemos like en la query para que abarque todas las posibilidades.
                $consulta .= "
                    AND (tipo like '".$bd_red->_($tipo)."%')";
            }
            if ($contrato != CONTRATO_TARIFA_ELECTRICA_TODOS)
            {
                $consulta .= "
                    AND (contrato = '".$bd_red->_($contrato)."')";
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


        // Devuelve la tabla de tarifas eléctricas
        static function dame_tabla_tarifas_electricas($filtro, $tipo, $contrato, $id_grupo, $estado)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
			$opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_tarifa_electrica = "<i id='anyade_modifica_tarifa_electrica' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_tarifa_electrica);
            }
            $boton_actualizar_tabla_tarifas_electricas = "<i id='actualiza_tarifas_electricas' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_tarifas_electricidad_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_tarifas_electricas);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-tarifas-electricas",
                $idiomas->_("Tarifas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = TarifaElectrica_Espanya::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las tarifas a la tabla y el pie de tabla
            $consulta = TarifaElectrica_Espanya::dame_consulta_tarifas_electricas(
                $filtro,
                $tipo,
                $contrato,
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
                $tarifa_electrica = new TarifaElectrica_Espanya($fila);

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
                        "datosTarifaElectrica_Espanya__".$fila['id'],
                        $info_tabla_tarifa_electrica["datos"],
                        $params_fila
                    );
                    $numero_tarifas_electricas += 1;
                }
            }
			$tabla->anyade_pie($idiomas->_("Tarifas").": ".$numero_tarifas_electricas);

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
                    FROM ".TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA."
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
                TarifaElectrica_Espanya::dame_descripcion_tipo_tarifa_electrica($this->params["tipo"]),
                TarifaElectrica_Espanya::dame_descripcion_contrato_tarifa_electrica($this->params["contrato"]),
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
                    "class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' nombre_tarifa_electrica='".$nombre."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
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

            // Características de tipo de la tarifa eléctrica
            $tipo_tarifa_electrica = $this->params["tipo"];
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
            $tipo_calculo_coste_potencias = $caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
            $parametros_medida_datos_facturacion = $caracteristicas_tipo_tarifa_electrica["parametros_medida_datos_facturacion"];
            $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_electrica["tipo_tarifa_canarias"];

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

            // Parámetros específicos del contrato
            $mostrar_tabla_periodos_calculo_costes_pass_pool = false;
            $mostrar_tabla_conceptos_coste_pass_through = false;
            $mostrar_tabla_conceptos_coste_cierre = false;
            switch ($this->params["contrato"])
            {
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("OMIE").": ".TarifaElectrica_Espanya::dame_descripcion_id_indicador_omie_pass_pool_tarifa_electrica($this->params["id_indicador_omie_pass_pool"], ENT_QUOTES)."<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Tipo de cálculo de coste").": ".TarifaElectrica_Espanya::dame_descripcion_tipo_calculo_coste_pass_pool_tarifa_electrica($this->params["tipo_calculo_coste_pass_pool"], ENT_QUOTES);
                    switch ($this->params["tipo_calculo_coste_pass_pool"])
                    {
                        case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO:
                        {
                            $info .= " (".$this->idiomas->_("día de cálculo de coste automático").": ".$this->params["dia_calculo_coste_automatico_pass_pool"].")"."<br/>";
                        break;
                        }
                        case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_MANUAL:
                        {
                            $mostrar_tabla_periodos_calculo_costes_pass_pool = true;
                        break;
                        }
                    }
                    $info .= "<br/>";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Fórmula de precio de consumo").": ".htmlspecialchars($this->params["formula_precio_consumo_pass_through"], ENT_QUOTES).
                        " (".$unidad_medida_coste."/".$this->idiomas->_("MWh").")"."<br/>";
                    $mostrar_tabla_conceptos_coste_pass_through = true;
                    $info .= "<br/>";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Fórmula de precio de consumo").": ".htmlspecialchars($this->params["formula_precio_consumo_pass_through"], ENT_QUOTES).
                        " (".$unidad_medida_coste."/".$this->idiomas->_("MWh").")"."<br/>";
                    $mostrar_tabla_conceptos_coste_cierre = true;
                    $info .= "<br/>";
                    break;
                }
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

            // Bonificación de 85 %
            switch ($tipo_calculo_coste_potencias)
            {
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $bonificacion_85 = $this->params["bonificacion_85"];
                    $info .= $this->idiomas->_("Bonificación 85 %").": ".TarifaElectrica_Espanya::dame_descripcion_bonificacion_85_tarifa_electrica($bonificacion_85)."<br/>";
                    $info .= "<br/>";
                    break;
                }
            }

            // Parámetros de medida de datos de facturación
            if ($parametros_medida_datos_facturacion == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $tipo_medida = $this->params["tipo_medida"];
                $info .= $this->idiomas->_("Tipo de medida").": ".TarifaElectrica_Espanya::dame_descripcion_tipo_medida_tarifa_electrica($tipo_medida)."<br/>";
                if ($tipo_medida == TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION)
                {
                    $info .= "<ul>";
                    $info .= "<li>".$this->idiomas->_("Potencia nominal del transformador").": ".
                        formatea_numero($this->params["potencia_nominal_transformador"], 2)." ".$this->idiomas->_("kVA")."</li>";
                    $info .= "</ul>";
                }
                $info .= "<br/>";
            }

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
            if ($tipo_tarifa_canarias == false)
            {
                $iva = formatea_numero($this->params["iva"], 2);
                $info .= "<li>";
                $descripcion_iva = $this->idiomas->_("IVA");
                $info .= $descripcion_iva.": ".$iva." %"."<br/>";
                $info .= "</li>";
            }
            else
            {
                $igic_reducido = formatea_numero($this->params["igic_reducido"], 2);
                $igic_normal = formatea_numero($this->params["igic_normal"], 2);
                $info .= "<li>";
                $descripcion_igic_reducido = $this->idiomas->_("IGIC")." (".$this->idiomas->_("reducido").")";
                $info .= $descripcion_igic_reducido.": ".$igic_reducido." %"."<br/>";
                $info .= "</li>";
                $info .= "<li>";
                $descripcion_igic_normal = $this->idiomas->_("IGIC")." (".$this->idiomas->_("normal").")";
                $info .= $descripcion_igic_normal.": ".$igic_normal." %"."<br/>";
                $info .= "</li>";
            }
            $info .= "</ul>";
            $info .= "<br/>";

            // Sensores a los que está asignada esta tarifa eléctrica
            $consulta_sensores = "
				SELECT nombre
				FROM sensores
				WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_ENERGIA_ACTIVA."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')
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

            // Periodos de cálculo de costes 'pass-pool'
            if ($mostrar_tabla_periodos_calculo_costes_pass_pool == true)
            {
                // Se muestra la tabla de los periodos de cálculos de costes
                $id_elemento_periodos_calculo_costes = 'periodos_calculo_costes_pass_pool'.$this->id;
                $info .= "<div id='".$id_elemento_periodos_calculo_costes."' class='contenedor-detalle-tabla-datos'>".
                    $this->dame_tabla_periodos_calculo_costes_pass_pool()."</div>";
                $info .= "<br/>";
            }

            // Conceptos de coste 'pass-through'
            if ($mostrar_tabla_conceptos_coste_pass_through == true)
            {
                // Se muestra la tabla de los conceptos de coste
                $id_elemento_conceptos_coste = 'conceptos_coste_pass_through'.$this->id;
                $info .= "<div id='".$id_elemento_conceptos_coste."' class='contenedor-detalle-tabla-datos'>".
                    $this->dame_tabla_conceptos_coste_pass_through()."</div>";
                $info .= "<br/>";
            }

            // Conceptos de coste 'cierre'
            if ($mostrar_tabla_conceptos_coste_cierre == true)
            {
                // Se muestra la tabla de los conceptos de coste
                $id_elemento_conceptos_coste = 'conceptos_coste_cierre'.$this->id;
                $info .= "<div id='".$id_elemento_conceptos_coste."' class='contenedor-detalle-tabla-datos'>".
                    $this->dame_tabla_conceptos_coste_cierre()."</div>";
                $info .= "<br/>";
            }

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
        // Tipos de tarifa eléctrica
        //


        static function dame_tipos_tarifa_electrica()
        {
            $tipos_tarifa_electrica = array();

            // Nota: Se añaden primero las tarifas actuales para facilitar la administración a los clientes
            // (cuando las nuevas tarifas ya estén asentadas, se pondrán primero las tarifas nuevas)
            // Tipos de tarifas eléctricas (vigentes a partir de 2026)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026);

            // Tipos de tarifas eléctricas (vigentes a partir de abril 2025)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL);

			// Tipos de tarifas eléctricas (vigentes a partir de enero 2025)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025);
            
			// Tipos de tarifas eléctricas (vigentes a partir de enero 2024)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024);

			// Tipos de tarifas eléctricas (vigentes a partir de enero 2023)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023);

			// Tipos de tarifas eléctricas (vigentes a partir de enero 2022)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022);

            // Tipos de tarifas eléctricas (vigentes a partir de 2020)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME);

            // Tipos de tarifas eléctricas (obsoletas a partir de 2020)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME_MAXIMETRO);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_31A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_31A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_31A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_31A_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_61A_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_62A_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_63A_ME);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64A_P);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64A_B);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64A_CE);
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_ESPANYA_64A_ME);

            //Tipo de tarifas para venta de energia (ELI)
            array_push($tipos_tarifa_electrica, TIPO_TARIFA_ELECTRICA_VENTA_ENERGIA);
            return ($tipos_tarifa_electrica);
        }


        static function dame_descripcion_tipo_tarifa_electrica($tipo_tarifa)
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
                


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2026)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2026)";
                    break;
                }


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (Abril 2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (Abril 2025)";
                    break;
                }


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2025)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2025)";
                    break;
                }

                
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2024)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2024)";
                    break;
                }


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2023)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2023)";
                    break;
                }


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2022)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2022)";
                    break;
                }


                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Baleares) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Canarias) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Ceuta) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME:
                {
                    $descripcion_tipo_tarifa .= "2.0TD (Melilla) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Península) (Tipo 4) (2021)" ;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Baleares) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Canarias) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Ceuta) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0TD (Melilla) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Península) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (2021)";
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Baleares) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (2021)";
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Canarias) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (2021)";
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Ceuta) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (2021)";
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX:
                {
                    $descripcion_tipo_tarifa .= "6.1TD (Melilla) (Tipo 4) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Baleares) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Canarias) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Ceuta) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME:
                {
                    $descripcion_tipo_tarifa .= "6.2TD (Melilla) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Baleares) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Canarias) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Ceuta) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME:
                {
                    $descripcion_tipo_tarifa .= "6.3TD (Melilla) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Península) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Baleares) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Canarias) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Ceuta) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME:
                {
                    $descripcion_tipo_tarifa .= "6.4TD (Melilla) (2021)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Península) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Baleares) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Canarias) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Ceuta) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.0DHA (Melilla) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Península) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Baleares) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Canarias) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Ceuta) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME_MAXIMETRO:
                {
                    $descripcion_tipo_tarifa .= "2.1DHA (Melilla) (maxímetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Península) (Maximetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Baleares) (Maximetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Canarias) (Maximetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Ceuta) (Maximetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME_MAX:
                {
                    $descripcion_tipo_tarifa .= "3.0A (Melilla) (Maximetro)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_P:
                {
                    $descripcion_tipo_tarifa .= "3.1A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_B:
                {
                    $descripcion_tipo_tarifa .= "3.1A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C:
                {
                    $descripcion_tipo_tarifa .= "3.1A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_CE:
                {
                    $descripcion_tipo_tarifa .= "3.1A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_ME:
                {
                    $descripcion_tipo_tarifa .= "3.1A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_P:
                {
                    $descripcion_tipo_tarifa .= "6.1A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_B:
                {
                    $descripcion_tipo_tarifa .= "6.1A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C:
                {
                    $descripcion_tipo_tarifa .= "6.1A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_CE:
                {
                    $descripcion_tipo_tarifa .= "6.1A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_ME:
                {
                    $descripcion_tipo_tarifa .= "6.1A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_P:
                {
                    $descripcion_tipo_tarifa .= "6.2A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_B:
                {
                    $descripcion_tipo_tarifa .= "6.2A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C:
                {
                    $descripcion_tipo_tarifa .= "6.2A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_CE:
                {
                    $descripcion_tipo_tarifa .= "6.2A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_ME:
                {
                    $descripcion_tipo_tarifa .= "6.2A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_P:
                {
                    $descripcion_tipo_tarifa .= "6.3A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_B:
                {
                    $descripcion_tipo_tarifa .= "6.3A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C:
                {
                    $descripcion_tipo_tarifa .= "6.3A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_CE:
                {
                    $descripcion_tipo_tarifa .= "6.3A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_ME:
                {
                    $descripcion_tipo_tarifa .= "6.3A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_P:
                {
                    $descripcion_tipo_tarifa .= "6.4A (Península)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_B:
                {
                    $descripcion_tipo_tarifa .= "6.4A (Baleares)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C:
                {
                    $descripcion_tipo_tarifa .= "6.4A (Canarias)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_CE:
                {
                    $descripcion_tipo_tarifa .= "6.4A (Ceuta)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_ME:
                {
                    $descripcion_tipo_tarifa .= "6.4A (Melilla)";
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_VENTA_ENERGIA:
                {
                    $descripcion_tipo_tarifa .= "Venta energia";
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


        static function dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa)
        {
            $idiomas = new Idiomas();

            $caracteristicas_tipo_tarifa = array();
            switch ($tipo_tarifa)
            {
                // Tipos de tarifas eléctricas (vigentes a partir de 2020)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 3;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = array(
                        $idiomas->_("Punta") => array(1, 2),
                        $idiomas->_("Valle") => array(3));
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = true;
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
				{
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 3;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = array(
                        $idiomas->_("Punta") => array(1, 2),
                        $idiomas->_("Valle") => array(3));
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = true;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
				{
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
				{
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = true;
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX:
				{
					$caracteristicas_tipo_tarifa["numero_tramos"] = 6;
					$caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
					$caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
					$caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
					$caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5, 6);
					$caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
				{
					$caracteristicas_tipo_tarifa["numero_tramos"] = 6;
					$caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
					$caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
					$caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
					$caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5, 6);
					$caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = true;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5, 6);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
				{
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5, 6);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = true;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }

                // Tipos de tarifas eléctricas (obsoletas a partir de 2020)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 2;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME_MAXIMETRO:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 2;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 3;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 3;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_ME:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_ME:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 6;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = array(1, 2, 3, 4, 5);
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_VENTA_ENERGIA:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 1;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                // Ninguno y todos
                case TIPO_TARIFA_NINGUNO:
                case TIPO_TARIFA_TODOS:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 0;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
                // Nota: Por defecto se devuelve igual que ninguno y todos para que al menos se pueda cargar la lista de tarifas
                // con tipos de tarifa desconocidos
                default:
                {
                    $caracteristicas_tipo_tarifa["numero_tramos"] = 0;
                    $caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = NULL;
                    $caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
                    $caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
                    $caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = NULL;
                    $caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
					$caracteristicas_tipo_tarifa["prorrateo_potencias"] = false;
					$caracteristicas_tipo_tarifa["precio_exceso_potencia_dia"] = false;
                    break;
                }
            }

            // Tipo de tarifa de canarias
            switch ($tipo_tarifa)
            {
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C:
                {
                    $tipo_tarifa_canarias = true;
                    break;
                }
                default:
                {
                    $tipo_tarifa_canarias = false;
                    break;
                }
            }
            $caracteristicas_tipo_tarifa["tipo_tarifa_canarias"] = $tipo_tarifa_canarias;

            // Se devuelven las características del tipo de tarifa
            return ($caracteristicas_tipo_tarifa);
        }


		static function dame_precio_penalizacion_sobrepotencia_Espanya($tipo_tarifa)
		{
			$precio_penalizacion_sobrepotencia_tarifa = 0;
            switch ($tipo_tarifa)
            {
                // 2022
				// Tipos de tarifas eléctricas (vigentes a partir de enero 2022)
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 0.078858;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.468725;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 0.081164;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.500611;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 0.118186;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.511007;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.268489;
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.244925;
					break;
				}


                //2023
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 0.104128;                 
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.424853;                                    
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX: 
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 0.112598;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
                {   
                    $precio_penalizacion_sobrepotencia_tarifa = 3.665629;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 0.120514;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 3.371776;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.080419;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.944120;
					break;
				}


                //2024
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.013070;                 
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.395810;                                    
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX: 
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 0.111643;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
                {   
                    $precio_penalizacion_sobrepotencia_tarifa = 3.566788;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 0.117264;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 3.312680;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.019048;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.915852;
					break;
				}

                
                // 2025
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 2.953979;                 
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.361213;                                    
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX: 
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 0.110506;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
                {   
                    $precio_penalizacion_sobrepotencia_tarifa = 3.332942;
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 0.109576;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                {
					$precio_penalizacion_sobrepotencia_tarifa = 3.292963;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = 3.099043;
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
				{
					$precio_penalizacion_sobrepotencia_tarifa = 2.732620;
					break;
				}


                // 2025_ABRIL
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                {
                    // Se duplican los dos últimos valores para que aparezca correctamente el coeficiente del tramo valle
                    $precio_penalizacion_sobrepotencia_tarifa = [0.275041, 0.005297, 0.005297];                 
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = [3.361213, 1.776545, 0.563477, 0.430844, 0.121880, 0.121880];                                    
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX: 
                {                    
                    $precio_penalizacion_sobrepotencia_tarifa = [0.168944, 0.089294, 0.028322, 0.021656, 0.006126, 0.006126];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
                {   
                    $precio_penalizacion_sobrepotencia_tarifa = [3.332942, 1.762138, 0.661311, 0.465989, 0.009852, 0.008771];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
                {                    
					$precio_penalizacion_sobrepotencia_tarifa = [0.272540, 0.144093, 0.054076, 0.038105, 0.000806, 0.000717];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
                {
					$precio_penalizacion_sobrepotencia_tarifa = [3.292963, 1.867567, 0.491658, 0.299575, 0.011745, 0.010432];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = [3.099043, 1.867297, 0.608334, 0.396461, 0.013018, 0.011460];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
				{
					$precio_penalizacion_sobrepotencia_tarifa = [2.732620, 1.633705, 0.396742, 0.275775, 0.008201, 0.005465];
					break;
				}
                
                
                // 2026_ABRIL
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
                {
                    // Se duplican los dos últimos valores para que aparezca correctamente el coeficiente del tramo valle
                    $precio_penalizacion_sobrepotencia_tarifa = [0.279426, 0.005316, 0.005316];                 
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = [3.325715, 1.757877, 0.557353, 0.424794, 0.119179, 0.119179];                                    
                    break;                 
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX: 
                {                    
                    $precio_penalizacion_sobrepotencia_tarifa = [0.171373, 0.090584, 0.028721, 0.021891, 0.006142, 0.006142];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
                {   
                    $precio_penalizacion_sobrepotencia_tarifa = [3.431797, 1.818277, 0.680379, 0.478581, 0.010172, 0.008984];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
                {                    
					$precio_penalizacion_sobrepotencia_tarifa = [0.275735, 0.146094, 0.054668, 0.038455, 0.000817, 0.000722];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
                {
					$precio_penalizacion_sobrepotencia_tarifa = [3.243495, 1.826897, 0.483612, 0.294055, 0.011467, 0.010143];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
                {
                    $precio_penalizacion_sobrepotencia_tarifa = [3.063808, 1.844204, 0.617738, 0.402624, 0.013069, 0.011406];
					break;
				}
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
				{
					$precio_penalizacion_sobrepotencia_tarifa = [2.736629, 1.630338, 0.409096, 0.284221, 0.008441, 0.005787];
					break;
				}
                

				default:
                {
                    //Este valor es el que se aplica hasta el 31/12/2021
                    $precio_penalizacion_sobrepotencia_tarifa = CONSTANTE_CALCULO_PENALIZACION_SOBREPOTENCIA_EXCESOS_CUARTOHORARIOS;
                    break;
                }
            }

            return ($precio_penalizacion_sobrepotencia_tarifa);
		}


        static function dame_penalizacion_potencias_Espanya($tipo_tarifa)
        {
            $idiomas = new Idiomas();

            $penalizacion_potencia_tarifa = array();
            switch ($tipo_tarifa)
            {   //Kp eliminado a partir de 2025
                // Tipos de tarifas eléctricas (vigentes a partir de 2026)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }

                // Tipos de tarifas eléctricas (vigentes a partir de Abril 2025)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                {
                    //Se duplica el primer valor para que haya 3 tramos
                    $penalizacion_potencia_tarifa = [1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
                {
                    $penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
                    break;
                }
       
                // Tipos de tarifas eléctricas (vigentes a partir de enero 2025)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                {
                    //Se duplica el primer valor para que haya 3 tramos
                    $penalizacion_potencia_tarifa = [1, 1, 0.019259];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
                {
                    $penalizacion_potencia_tarifa = [1, 0.528543, 0.167641, 0.128181, 0.036261, 0.036261];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
                {
                    $penalizacion_potencia_tarifa = [1, 0.528704, 0.198416, 0.139813, 0.002956, 0.002632];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                {
                    $penalizacion_potencia_tarifa = [1, 0.567139, 0.149306, 0.090974, 0.003567, 0.003168];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                {
                    $penalizacion_potencia_tarifa = [1, 0.602540, 0.196297, 0.127930, 0.004201, 0.003698];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                {
                    $penalizacion_potencia_tarifa = [1, 0.597853, 0.145188, 0.100919, 0.003001, 0.002000];
                    break;
                }

                
                // Tipos de tarifas eléctricas (vigentes a partir de enero 2024)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                {
                    //Se duplica el primer valor para que haya 3 tramos
                    $penalizacion_potencia_tarifa = [1, 1, 0.034665];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
                {
                    $penalizacion_potencia_tarifa = [1, 0.640766, 0.275670, 0.232691, 0.077884, 0.077884];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
                {
                    $penalizacion_potencia_tarifa = [1, 0.620828, 0.482845, 0.381770, 0.015816, 0.015816];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
                {
                    $penalizacion_potencia_tarifa = [1, 0.666078, 0.427424, 0.355531, 0.018151, 0.018151];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
                {
                    $penalizacion_potencia_tarifa = [1, 0.621562, 0.500437, 0.395142, 0.032600, 0.032600];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
                {
                    $penalizacion_potencia_tarifa = [1, 0.563080, 0.432501, 0.393593, 0.026604, 0.026604];
                    break;
                }
                    
                    
                // Tipos de tarifas eléctricas (vigentes a partir de enero 2023)
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
				{
                    //Se duplica el primer valor para que haya 3 tramos
					$penalizacion_potencia_tarifa = [1, 1, 0.051374];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
				{
					$penalizacion_potencia_tarifa = [1, 0.977847, 0.258225, 0.224324, 0.134596, 0.134596];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
				{
					$penalizacion_potencia_tarifa = [1, 0.937332, 0.467076, 0.374609, 0.026491, 0.026491];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
				{
					$penalizacion_potencia_tarifa = [1, 0.997427, 0.399716, 0.301945, 0.027593, 0.027593];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
				{
					$penalizacion_potencia_tarifa = [1, 0.958607, 0.485508, 0.363556, 0.049296, 0.049296];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
				{
					$penalizacion_potencia_tarifa = [1, 0.862139, 0.425286, 0.325868, 0.041422, 0.041422];
					break;
				}


				// Tipos de tarifas eléctricas (vigentes a partir de enero 2022)
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,1,1];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,0.872171,0.351490,0.267082,0.106998,0.106998];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,1,0.545204,0.412967,0.027431,0.027431];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,1,0.489150,0.444995,0.030784,0.030784];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,1,0.553151,0.323415,0.063681,0.063681];
					break;
				}
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
				{
					$penalizacion_potencia_tarifa = [1,0.765346,0.368150,0.271009,0.051202,0.051202];
					break;
				}

				// Tipos de tarifas eléctricas (vigentes a partir de 2020)
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,0.0410];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,0.873773,0.352340,0.267883,0.107572,0.107572];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,1,0.542746,0.410260,0.026371,0.026371];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,1,0.490071,0.437187,0.030054,0.030054];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,1,0.547301,0.319935,0.061337,0.061337];
                    break;
                }
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME:
                {
                    $penalizacion_potencia_tarifa = [1,0.766444,0.368643,0.279621,0.052149,0.052149];
                    break;
                }

				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
				case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
                case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
				{
					$penalizacion_potencia_tarifa = [1, 1, 1, 1, 1, 1];
					break;
				}

                default:
                {
                    $penalizacion_potencia_tarifa = [1.0, 0.5, 0.37, 0.37, 0.37, 0.17];
                    break;
                }
            }

            return ($penalizacion_potencia_tarifa);
        }

				static function dame_tramos_mes_tipo_tarifa($tipo_tarifa, $mes)
        {
					switch($tipo_tarifa)
					{
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
						{
							switch ($mes)
							{
								case "01":
								case "02":
								case "07":
								case "12":
								{
									$tramos = array(1,2,6);
									break;
								}
								case "03":
								case "11":
								{
									$tramos = array(2,3,6);
									break;
								}
								case "06":
								case "08":
								case "09":
								{
									$tramos = array(3,4,6);
									break;
								}
								case "04":
								case "05":
								case "10":
								{
									$tramos = array(4,5,6);
									break;
								}
							}
							break;
						}
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
						{
							switch ($mes)
							{
								case "01":
								case "02":
								case "07":
								case "12":
								{
									$tramos = array(1,2,6);
									break;
								}
								case "03":
								case "11":
								{
									$tramos = array(2,3,6);
									break;
								}
								case "06":
								case "08":
								case "09":
								{
									$tramos = array(3,4,6);
									break;
								}
								case "04":
								case "05":
								case "10":
								{
									$tramos = array(4,5,6);
									break;
								}
							}
							break;
						}
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
						{
							switch ($mes)
							{
								case "01":
								case "02":
								case "07":
								case "12":
								{
									$tramos = array(1,2,6);
									break;
								}
								case "03":
								case "11":
								{
									$tramos = array(2,3,6);
									break;
								}
								case "06":
								case "08":
								case "09":
								{
									$tramos = array(3,4,6);
									break;
								}
								case "04":
								case "05":
								case "10":
								{
									$tramos = array(4,5,6);
									break;
								}
							}
							break;
						}
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
						{
							switch ($mes)
							{
								case "01":
								case "02":
								case "07":
								case "12":
								{
									$tramos = array(1,2,6);
									break;
								}
								case "03":
								case "11":
								{
									$tramos = array(2,3,6);
									break;
								}
								case "06":
								case "08":
								case "09":
								{
									$tramos = array(3,4,6);
									break;
								}
								case "04":
								case "05":
								case "10":
								{
									$tramos = array(4,5,6);
									break;
								}
							}
							break;
						}
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
						case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:  
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
                        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:  
						{
							switch ($mes)
							{
								case "01":
								case "02":
								case "07":
								case "12":
								{
									$tramos = array(1,2,6);
									break;
								}
								case "03":
								case "11":
								{
									$tramos = array(2,3,6);
									break;
								}
								case "06":
								case "08":
								case "09":
								{
									$tramos = array(3,4,6);
									break;
								}
								case "04":
								case "05":
								case "10":
								{
									$tramos = array(4,5,6);
									break;
								}
							}
							break;
						}
					}
            return ($tramos);
        }


        //
        // Contratos de tarifa eléctrica
        //


        static function dame_contratos_tarifa_electrica()
        {
            $contratos_tarifa = array();
            array_push($contratos_tarifa, CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO);
            array_push($contratos_tarifa, CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL);
            array_push($contratos_tarifa, CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH);
            array_push($contratos_tarifa, CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE);
            return ($contratos_tarifa);
        }


        static function dame_descripcion_contrato_tarifa_electrica($contrato_tarifa)
        {
            switch ($contrato_tarifa)
            {
                case CONTRATO_TARIFA_ELECTRICA_NINGUNO:
                {
                    $descripcion_contrato_tarifa = "Ninguno";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_TODOS:
                {
                    $descripcion_contrato_tarifa = "Todos";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO:
                {
                    $descripcion_contrato_tarifa = "Fijo";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
                {
                    $descripcion_contrato_tarifa = "Pass-pool";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
                {
                    $descripcion_contrato_tarifa = "Pass-through";
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
                {
                    $descripcion_contrato_tarifa = "Cierre";
                    break;
                }
                default:
                {
                    $descripcion_contrato_tarifa = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_contrato_tarifa));
        }


        //
        // Parámetros de tipo de tarifa eléctrica
        //


        static function dame_bonificaciones_85_tarifa_electrica()
        {
            $bonificaciones_85_tarifa_electrica = array();
            array_push($bonificaciones_85_tarifa_electrica, BONIFICACION_85_TARIFA_ELECTRICA_SI);
            array_push($bonificaciones_85_tarifa_electrica, BONIFICACION_85_TARIFA_ELECTRICA_NO);
            array_push($bonificaciones_85_tarifa_electrica, BONIFICACION_85_TARIFA_ELECTRICA_MINIMO_100);
            array_push($bonificaciones_85_tarifa_electrica, BONIFICACION_85_TARIFA_ELECTRICA_REAL);
            return ($bonificaciones_85_tarifa_electrica);
        }


        static function dame_descripcion_bonificacion_85_tarifa_electrica($bonificacion_85_tarifa)
        {
            switch ($bonificacion_85_tarifa)
            {
                case BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA:
                {
                    $descripcion_bonificacion_85_tarifa = "Ninguna";
                    break;
                }
                case BONIFICACION_85_TARIFA_ELECTRICA_SI:
                {
                    $descripcion_bonificacion_85_tarifa = "Sí";
                    break;
                }
                case BONIFICACION_85_TARIFA_ELECTRICA_NO:
                {
                    $descripcion_bonificacion_85_tarifa = "No";
                    break;
                }
                case BONIFICACION_85_TARIFA_ELECTRICA_MINIMO_100:
                {
                    $descripcion_bonificacion_85_tarifa = "Mínimo 100 %";
                    break;
                }
                case BONIFICACION_85_TARIFA_ELECTRICA_REAL:
                {
                    $descripcion_bonificacion_85_tarifa = "Potencia medida";
                    break;
                }
                default:
                {
                    $descripcion_bonificacion_85_tarifa = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_bonificacion_85_tarifa));
        }


        static function dame_tipos_medida_tarifa_electrica()
        {
            $tipos_medida_tarifa_electrica = array();
            array_push($tipos_medida_tarifa_electrica, TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION);
            array_push($tipos_medida_tarifa_electrica, TIPO_MEDIDA_TARIFA_ELECTRICA_ALTA_TENSION);
            return ($tipos_medida_tarifa_electrica);
        }


        static function dame_descripcion_tipo_medida_tarifa_electrica($tipo_medida_tarifa)
        {
            switch ($tipo_medida_tarifa)
            {
                case TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA:
                {
                    $descripcion_tipo_medida_tarifa = "Ninguna";
                    break;
                }
                case TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION:
                {
                    $descripcion_tipo_medida_tarifa = "Baja tensión";
                    break;
                }
                case TIPO_MEDIDA_TARIFA_ELECTRICA_ALTA_TENSION:
                {
                    $descripcion_tipo_medida_tarifa = "Alta tensión";
                    break;
                }
                default:
                {
                    $descripcion_tipo_medida_tarifa = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_medida_tarifa));
        }


        //
        // Parámetro de contrato 'pass-pool'
        //


        static function dame_ids_indicadores_omie_coste_pass_pool_tarifa_electrica()
        {
            $ids_indicadores_omie_coste_pass_pool_tarifa = array();
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_PENINSULA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_GRAN_CANARIA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LANZAROTE_FUERTEVENTURA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_TENERIFE);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_PALMA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_GOMERA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_EL_HIERRO);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_CEUTA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_MELILLA);
            array_push($ids_indicadores_omie_coste_pass_pool_tarifa, ID_INDICADOR_OMIE_TARIFA_ELECTRICA_BALEARES);
            return ($ids_indicadores_omie_coste_pass_pool_tarifa);
        }


        static function dame_descripcion_id_indicador_omie_pass_pool_tarifa_electrica($id_indicador_omie_pass_pool_tarifa)
        {
            $traducir_descripcion = false;
            switch ($id_indicador_omie_pass_pool_tarifa)
            {
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Ninguno";
                    $traducir_descripcion = true;
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_PENINSULA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Península";
                    $traducir_descripcion = true;
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_GRAN_CANARIA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Gran Canaria";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LANZAROTE_FUERTEVENTURA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Lanzarote - FuerteVentura";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_TENERIFE:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Tenerife";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_PALMA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "La Palma";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_LA_GOMERA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "La Gomera";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_EL_HIERRO:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "El Hierro";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_CEUTA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Ceuta";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_MELILLA:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Melilla";
                    break;
                }
                case ID_INDICADOR_OMIE_TARIFA_ELECTRICA_BALEARES:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Baleares";
                    break;
                }
                default:
                {
                    $descripcion_id_indicador_omie_pass_pool_tarifa = "Desconocido";
                    $traducir_descripcion = true;
                    break;
                }
            }
            if ($traducir_descripcion == true)
            {
                $idiomas = new Idiomas();
                $descripcion_id_indicador_omie_pass_pool_tarifa = $idiomas->_($descripcion_id_indicador_omie_pass_pool_tarifa);
            }
            return ($descripcion_id_indicador_omie_pass_pool_tarifa);
        }


        static function dame_tipos_calculo_coste_pass_pool_tarifa_electrica()
        {
            $tipos_calculo_coste_pass_pool_tarifa = array();
            array_push($tipos_calculo_coste_pass_pool_tarifa, TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO);
            array_push($tipos_calculo_coste_pass_pool_tarifa, TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO);
            array_push($tipos_calculo_coste_pass_pool_tarifa, TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_MANUAL);
            return ($tipos_calculo_coste_pass_pool_tarifa);
        }


        static function dame_descripcion_tipo_calculo_coste_pass_pool_tarifa_electrica($tipo_calculo_coste_pass_pool_tarifa)
        {
            switch ($tipo_calculo_coste_pass_pool_tarifa)
            {
                case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO:
                {
                    $descripcion_tipo_calculo_coste_pass_pool_tarifa = "Ninguno";
                    break;
                }
                case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO:
                {
                    $descripcion_tipo_calculo_coste_pass_pool_tarifa = "Automático";
                    break;
                }
                case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_MANUAL:
                {
                    $descripcion_tipo_calculo_coste_pass_pool_tarifa = "Manual";
                    break;
                }
                default:
                {
                    $descripcion_tipo_calculo_coste_pass_pool_tarifa = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_calculo_coste_pass_pool_tarifa));
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


        //
        // Funciones auxiliares
        //


        function dame_tabla_periodos_calculo_costes_pass_pool()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            $opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_periodo_calculo_costes_pass_pool = "<i id='anyade_modifica_periodo_calculo_costes_pass_pool__".$this->id."' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_periodo_calculo_costes_pass_pool);
            }
            $boton_actualizar_tabla_periodos_calculos_costes_pass_pool = "<i id='actualiza_tabla_periodos_calculos_costes_pass_pool__".$this->id."' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_periodos_calculos_costes_pass_pool);

            // Se crea la tabla
            $numero_columnas = NUMERO_COLUMNAS_TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL;
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => $numero_columnas
            );
            $tabla = new TablaDatos(
                "tabla-periodos-calculo-costes-pass-pool",
                $this->idiomas->_("Periodos de cálculo de costes"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Fecha de inicio"),
                $this->idiomas->_("Fecha de fin"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los periodos de cálculo de costes 'pass-pool' a la tabla y el pie de tabla
            $consulta = "
                SELECT
                    id,
                    fecha_inicio,
                    fecha_fin
                FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($this->id)."'
                ORDER BY
                    fecha_inicio DESC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_periodos_calculo_costes_pass_pool = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $cadena_fecha_inicio = convierte_formato_fecha($fila["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                $cadena_fecha_fin = convierte_formato_fecha($fila["fecha_fin"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                $datos = array(
                    $cadena_fecha_inicio,
                    $cadena_fecha_fin
                );

                $opciones = array();
                if ($administracion_tarifas == true)
                {
                    $editar = "<i id='anyade_modifica_periodo_calculo_costes_pass_pool__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_periodo_calculo_costes_pass_pool__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_smartmeter_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosPeriodoCalculoCostesPassPool__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Periodos de cálculo de costes").": ".$numero_periodos_calculo_costes_pass_pool);

            return ($tabla->dame_tabla(false));
		}


        function dame_tabla_conceptos_coste_pass_through()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            $opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_concepto_coste_pass_through = "<i id='anyade_modifica_concepto_coste_pass_through__".$this->id."' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_concepto_coste_pass_through);
            }
            $boton_actualizar_tabla_conceptos_coste_pass_through = "<i id='actualiza_tabla_conceptos_coste_pass_through__".$this->id."' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_conceptos_coste_pass_through);

            // Se crea la tabla
            $numero_columnas = NUMERO_COLUMNAS_TABLA_CONCEPTOS_COSTE_PASS_THROUGH;
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => $numero_columnas
            );
            $tabla = new TablaDatos(
                "tabla-conceptos-coste-pass-through",
                $this->idiomas->_("Conceptos de coste"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Fórmula de precio de consumo")." (€/MWh)");
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los periodos de cálculo de costes 'pass-pool' a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($this->id)."'
                ORDER BY
                    nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_conceptos_coste_pass_through = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $nombre = $fila["nombre"];
                $formula_precio_consumo = $fila["formula_precio_consumo"];
                $datos = array(
                    htmlspecialchars($nombre, ENT_QUOTES),
                    htmlspecialchars($formula_precio_consumo, ENT_QUOTES)
                );

                $opciones = array();
                if ($administracion_tarifas == true)
                {
                    $editar = "<i id='anyade_modifica_concepto_coste_pass_through__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_concepto_coste_pass_through__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_smartmeter_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosConceptoCostePassThrough__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Conceptos de coste").": ".$numero_conceptos_coste_pass_through);

            return ($tabla->dame_tabla(false));
		}

        
        function dame_tabla_conceptos_coste_cierre()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            $opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_concepto_coste_pass_through = "<i id='anyade_modifica_concepto_coste_pass_through__".$this->id."' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_concepto_coste_pass_through);
            }
            $boton_actualizar_tabla_conceptos_coste_pass_through = "<i id='actualiza_tabla_conceptos_coste_pass_through__".$this->id."' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_conceptos_coste_pass_through);

            // Se crea la tabla
            $numero_columnas = NUMERO_COLUMNAS_TABLA_CONCEPTOS_COSTE_PASS_THROUGH;
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => $numero_columnas
            );
            $tabla = new TablaDatos(
                "tabla-conceptos-coste-pass-through",
                $this->idiomas->_("Conceptos de coste"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Fórmula de precio de consumo")." (€/MWh)");
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los periodos de cálculo de costes 'pass-pool' a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($this->id)."'
                ORDER BY
                    nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_conceptos_coste_pass_through = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $nombre = $fila["nombre"];
                $formula_precio_consumo = $fila["formula_precio_consumo"];
                $datos = array(
                    htmlspecialchars($nombre, ENT_QUOTES),
                    htmlspecialchars($formula_precio_consumo, ENT_QUOTES)
                );

                $opciones = array();
                if ($administracion_tarifas == true)
                {
                    $editar = "<i id='anyade_modifica_concepto_coste_pass_through__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_concepto_coste_pass_through__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_smartmeter_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosConceptoCostePassThrough__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Conceptos de coste").": ".$numero_conceptos_coste_pass_through);

            return ($tabla->dame_tabla(false));
        }


        function dame_tabla_tramos()
        {
            $id_elemento_tramos_tarifa_electrica = "tramos-tarifa-electrica".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_tramos = "<div id='".$id_elemento_tramos_tarifa_electrica."' class='contenedor-detalle-tabla-datos'>".
                dame_tabla_tramos_tarifa_electricidad_Espanya($this->id, NULL, false)."</div>";
            return ($tabla_tramos);
        }
	}
?>
