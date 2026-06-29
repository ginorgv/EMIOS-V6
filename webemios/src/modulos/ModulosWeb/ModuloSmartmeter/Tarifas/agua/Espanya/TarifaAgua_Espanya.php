<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');


    // Clase que representa una tarifa de agua (España)
	class TarifaAgua_Espanya extends Tarifa
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


        // Devuelve la consulta para la tabla de tarifas de agua
        static function dame_consulta_tarifas_agua($filtro, $tipo, $id_grupo)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT *
				FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
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


        // Devuelve la tabla de tarifas de agua
        static function dame_tabla_tarifas_agua($filtro, $tipo, $id_grupo, $estado)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
			$opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_tarifa_agua = "<i id='anyade_modifica_tarifa_agua' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_tarifa_agua);
            }
            $boton_actualizar_tabla_tarifas_agua = "<i id='actualiza_tarifas_agua' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_tarifas_agua_Espanya boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_tarifas_agua);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-tarifas-agua",
                $idiomas->_("Tarifas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = TarifaAgua_Espanya::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las tarifas a la tabla y el pie de tabla
            $consulta = TarifaAgua_Espanya::dame_consulta_tarifas_agua(
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
                $ids_tarifas_agua_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_AGUA);
            }

            // Se añaden las tarifas de agua
            $numero_tarifas_agua = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $tarifa_agua = new TarifaAgua_Espanya($fila);

                $anyadir_tarifa_agua = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($tarifa_agua->id, $ids_tarifas_agua_usuario) == false)
                    {
                        $anyadir_tarifa_agua = false;
                    }
                }

                if ($anyadir_tarifa_agua == true)
                {
                    // Si hay filtrado por estado y el estado no coincide, no se añade la tarifa de agua a la tabla
                    $info_tabla_tarifa_agua = $tarifa_agua->dame_info_tabla();
                    if (($estado != ESTADO_TARIFA_TODOS) && ($estado != $info_tabla_tarifa_agua["estado"]))
                    {
                        continue;
                    }

                    $params_fila = array(
                        "opciones" => $tarifa_agua->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosTarifaAgua_Espanya__".$fila['id'],
                        $info_tabla_tarifa_agua["datos"],
                        $params_fila
                    );
                    $numero_tarifas_agua += 1;
                }
            }
			$tabla->anyade_pie($idiomas->_("Tarifas").": ".$numero_tarifas_agua);

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
                    FROM ".TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA."
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
                        $tabla_tarifas_agua = dame_nombre_tabla_tarifas(MEDICION_AGUA);
                        $hay_tarifa_agua_grupo_mas_reciente = $this->dame_hay_tarifa_grupo_mas_reciente($tabla_tarifas_agua);
                        if ($hay_tarifa_agua_grupo_mas_reciente == false)
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
                TarifaAgua_Espanya::dame_descripcion_tipo_tarifa_agua($this->params["tipo"]),
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
                    "class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' nombre_tarifa_agua='".$nombre."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_tarifa_agua_Espanya boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            // Características de tipo de la tarifa de agua
            $tipo_tarifa_agua = $this->params["tipo"];
            $caracteristicas_tipo_tarifa_agua = TarifaAgua_Espanya::dame_caracteristicas_tipo_tarifa_agua($tipo_tarifa_agua);
            $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_agua["tipo_tarifa_canarias"];

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

            // Aviso de expiración de tarifa de agua
            $tabla_tarifas_agua = dame_nombre_tabla_tarifas(MEDICION_AGUA);
            $aviso_expiracion .= $this->dame_aviso_expiracion($tabla_tarifas_agua);
            if ($aviso_expiracion != "")
            {
                $info .= $aviso_expiracion;
                $info .= "<br/>";
            }

            // Tabla de tramos (de tarifa de agua)
            $info .= $this->dame_tabla_tramos();
            $info .= "<br/>";

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Parámetros de factura de agua
            $tipo_alquiler_contador = $this->params["tipo_alquiler_contador"];
            $alquiler_contador = $this->params["alquiler_contador"];
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Factura").": ";
            $info .= "<ul>";
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
                $iva_consumo = formatea_numero($this->params["iva_consumo"], 2);
                $iva_alquiler_contador = formatea_numero($this->params["iva_alquiler_contador"], 2);
                $info .= "<li>";
                $info .= $this->idiomas->_("IVA de consumo").": ".$iva_consumo." %"."<br/>";
                $info .= "</li>";
                $info .= "<li>";
                $info .= $this->idiomas->_("IVA de alquiler de contador").": ".$iva_alquiler_contador." %"."<br/>";
                $info .= "</li>";
            }
            else
            {
                $igic_consumo = formatea_numero($this->params["igic_consumo"], 2);
                $igic_alquiler_contador = formatea_numero($this->params["igic_alquiler_contador"], 2);
                $info .= "<li>";
                $info .= $this->idiomas->_("IGIC de consumo").": ".$igic_consumo." %"."<br/>";
                $info .= "</li>";
                $info .= "<li>";
                $info .= $this->idiomas->_("IGIC de alquiler de contador").": ".$igic_alquiler_contador." %"."<br/>";
                $info .= "</li>";
            }
            $info .= "</ul>";
            $info .= "<br/>";

            // Sensores a los que está asignada esta tarifa de agua
            $consulta_sensores = "
				SELECT nombre
				FROM sensores
				WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_AGUA."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')
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
            $tabla_conceptos_adicionales_factura = dame_tabla_conceptos_adicionales_factura_tarifa(MEDICION_AGUA, $this->id);
            if ($tabla_conceptos_adicionales_factura !== NULL)
            {
                // Se muestra la tabla de los conceptos adicionales de factura
                $id_elemento_conceptos_adicionales_factura = 'conceptos-adicionales-factura-tarifa'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<div id='".$id_elemento_conceptos_adicionales_factura."' class='contenedor-detalle-tabla-datos'>".
                    $tabla_conceptos_adicionales_factura."</div>";
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
        }


        //
        // Tipos de tarifa de agua
        //


        static function dame_tipos_tarifa_agua()
        {
            $tipos_tarifa_agua = array();
            array_push($tipos_tarifa_agua, TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_P_B_CE_ME);
            array_push($tipos_tarifa_agua, TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_C);
            return ($tipos_tarifa_agua);
        }


        static function dame_descripcion_tipo_tarifa_agua($tipo_tarifa)
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
                case TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_P_B_CE_ME:
                {
                    $descripcion_tipo_tarifa .= $idiomas->_("estándar")." (excepto Canarias)";
                    break;
                }
                case TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_C:
                {
                    $descripcion_tipo_tarifa .= $idiomas->_("estándar")." (Canarias)";
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


        static function dame_caracteristicas_tipo_tarifa_agua($tipo_tarifa)
        {
            $caracteristicas_tipo_tarifa = array();

            // Tipo de tarifa de canarias
            switch ($tipo_tarifa)
            {
                case TIPO_TARIFA_AGUA_ESPANYA_ESTANDAR_C:
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


        //
        // Tipos de límite de consumo de tarifa de agua
        //


        static function dame_tipos_limites_consumo_tramos_tarifa_agua()
        {
            $tipos_limites_consumo_tramos_tarifa = array();
            array_push($tipos_limites_consumo_tramos_tarifa, TIPO_LIMITES_CONSUMO_TRAMOS_ABSOLUTO);
            array_push($tipos_limites_consumo_tramos_tarifa, TIPO_LIMITES_CONSUMO_TRAMOS_DIARIO);
            return ($tipos_limites_consumo_tramos_tarifa);
        }


        static function dame_descripcion_tipo_limites_consumo_tramos_tarifa_agua($tipo_limites_consumo_tramos_tarifa)
        {
            switch ($tipo_limites_consumo_tramos_tarifa)
            {
                case TIPO_LIMITES_CONSUMO_TRAMOS_NINGUNO:
                {
                    $descripcion_tipo_limites_consumo_tramos_tarifa = "Ninguno";
                    break;
                }
                case TIPO_LIMITES_CONSUMO_TRAMOS_ABSOLUTO:
                {
                    $descripcion_tipo_limites_consumo_tramos_tarifa = "Absoluto";
                    break;
                }
                case TIPO_LIMITES_CONSUMO_TRAMOS_DIARIO:
                {
                    $descripcion_tipo_limites_consumo_tramos_tarifa = "Diario";
                    break;
                }
                default:
                {
                    $descripcion_tipo_limites_consumo_tramos_tarifa = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_limites_consumo_tramos_tarifa));
        }


        //
        // Tipos de alquiler de contador
        //


        static function dame_tipos_alquiler_contador_tarifa_agua()
        {
            $tipos_alquiler_contador_tarifa = array();
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_NINGUNO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_DIARIO);
            array_push($tipos_alquiler_contador_tarifa, TIPO_ALQUILER_CONTADOR_FIJO);
            return ($tipos_alquiler_contador_tarifa);
        }


        static function dame_descripcion_tipo_alquiler_contador_tarifa_agua($tipo_alquiler_contador_tarifa)
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


        function dame_tabla_tramos()
        {
            $id_elemento_tramos_tarifa_agua = "tramos-tarifa-agua".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_tramos = "<div id='".$id_elemento_tramos_tarifa_agua."' class='contenedor-detalle-tabla-datos'>".
                dame_tabla_tramos_tarifa_agua_Espanya($this->id, NULL, false)."</div>";
            return ($tabla_tramos);
        }
	}
?>
