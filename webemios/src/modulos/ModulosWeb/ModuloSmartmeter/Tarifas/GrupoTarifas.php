<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');


    // Clase que representa un grupo de tarifas
    // (diferentes tarifas asignadas a los mismos sensores en periodos de tiempo excluyentes)
	class GrupoTarifas
	{
        // Funciones estáticas de grupo de tarifas


        // Devuelve la cabecera para la tabla de grupos de tarifas
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre"),
				$idiomas->_("Fecha de expiración")
			));
		}


        // Devuelve la consulta para la tabla de grupos de tarifas
        static function dame_consulta_grupos_tarifas($tabla_grupos_tarifas, $filtro)
		{
            $consulta = "
				SELECT *
				FROM ".$tabla_grupos_tarifas."
				WHERE
					(red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
			$consulta .= "
                ORDER BY nombre ASC";
			return ($consulta);
		}


        // Devuelve la tabla de grupos de tarifas
        static function dame_tabla_grupos_tarifas($medicion, $filtro, $estado)
		{
            $bd_red = BaseDatosRed::dame_base_datos();
            $idiomas = new Idiomas();

            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
			$opciones = array();
            if ($administracion_tarifas == true)
            {
                $boton_anyadir_grupo_tarifas = "<i id='anyade_modifica_grupo_tarifas' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_grupo_tarifas boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_grupo_tarifas);
            }
            $boton_actualizar_tabla_grupos_tarifas = "<i id='actualiza_grupos_tarifas' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_grupos_tarifas boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_grupos_tarifas);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_GRUPOS_TARIFAS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_GRUPOS_TARIFAS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-grupos-tarifas",
                $idiomas->_("Grupos de tarifas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = GrupoTarifas::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los grupos de tarifas a la tabla y el pie de tabla
            $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
            $consulta = GrupoTarifas::dame_consulta_grupos_tarifas($tabla_grupos_tarifas, $filtro);
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Identificadores de grupos de tarifas del usuario actual
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_grupos_tarifas_usuario = GrupoTarifas::dame_ids_grupos_tarifas_usuario_actual($medicion);
            }

            // Se añaden los grupos de tarifas
            $numero_grupos_tarifas = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $grupo_tarifas = new GrupoTarifas($fila);

                $anyadir_grupo_tarifas = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($grupo_tarifas->id, $ids_grupos_tarifas_usuario) == false)
                    {
                        $anyadir_grupo_tarifas = false;
                    }
                }

                if ($anyadir_grupo_tarifas == true)
                {
                    // Si hay filtrado por estado y el estado no coincide, no se añade el grupo de tarifas a la tabla
                    $info_tabla_tarifa = $grupo_tarifas->dame_info_tabla($medicion);
                    if (($estado != ESTADO_TARIFA_TODOS) && ($estado != $info_tabla_tarifa["estado"]))
                    {
                        continue;
                    }

                    $params_fila = array(
                        "opciones" => $grupo_tarifas->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosGrupoTarifas__".$medicion."__".$fila['id'],
                        $info_tabla_tarifa["datos"],
                        $params_fila
                    );
                    $numero_grupos_tarifas += 1;
                }
            }
			$tabla->anyade_pie($idiomas->_("Grupos de tarifas").": ".$numero_grupos_tarifas);

            return ($tabla->dame_tabla());
		}


        // Miembros de grupo de tarifas


		public $idiomas;

		public $id;
        public $params;


		// Funciones de grupo de tarifas


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

            $this->id = $params["id"];
            $this->params = $params;
		}


		// Información para la tabla (datos y estado)
		function dame_info_tabla($medicion)
		{
            // Estado de tabla e información de expiración de la 'última' tarifa (aquella con fecha de expiración mayor)
            $estado_tabla = ESTADO_TARIFA_OK;
            $info_expiracion = $this->dame_info_expiracion_ultima_tarifa($medicion);
            if ($info_expiracion !== NULL)
            {
                $cadena_fecha_expiracion_local_local = convierte_formato_fecha($info_expiracion["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                $numero_dias_restantes_expiracion = dame_numero_dias_restantes_expiracion_tarifa($info_expiracion["fecha_expiracion"]);
                if ($numero_dias_restantes_expiracion <= 0)
                {
                    $cadena_fecha_expiracion .= " <iconos-dato class='iconos-dato'>["."<i class='icon-bell-alt color-rojo'></i>"."]</iconos-dato>";
                    $estado_tabla = ESTADO_TARIFA_EXPIRADA;
                }
                else
                {
                    if ($numero_dias_restantes_expiracion <= $info_expiracion["numero_dias_preaviso_expiracion"])
                    {
                        $cadena_fecha_expiracion .= " <iconos-dato class='iconos-dato'>["."<i class='icon-warning-sign color-rojo'></i>"."]</iconos-dato>";
                        $estado_tabla = ESTADO_TARIFA_AVISO_EXPIRACION;
                    }
                }
            }
            else
            {
                $cadena_fecha_expiracion_local_local = $this->idiomas->_("ND");
            }

            $datos_tabla = array(
				htmlspecialchars($this->params["nombre"], ENT_QUOTES),
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
                $editar = "<i id='anyade_modifica__".$this->id."' ".
                    "class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_grupo_tarifas boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' nombre_grupo_tarifas='".$nombre."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_grupo_tarifas boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }

			return ($opciones);
		}


        // Devuelve las herramientas de los detalles de la tabla
        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de tarifa
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_smartmeter_refrescar_tabla_grupo_tarifas'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Asignación de grupo de tarifas a sensores
            if ($administracion_tarifas == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_asignar_grupo__".$this->id."' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_asignacion_grupo_tarifas_sensores'>".
                            $this->idiomas->_("Asignar grupo de tarifas")."
                        </button>
                    </span>";
            }
			return ($herramientas);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla($medicion)
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

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
			}
            $info .= "<br/>";

            // Aviso de expiración de última tarifa
            $aviso_expiracion .= $this->dame_aviso_expiracion_ultima_tarifa($medicion);
            $info .= $aviso_expiracion;
            if ($aviso_expiracion != "")
            {
                $info .= "<br/>";
            }

            // Número de tarifas del grupo
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $consulta_tarifas = "
				SELECT
					nombre,
                    fecha_expiracion
				FROM ".$tabla_tarifas."
				WHERE
					grupo = '".$bd_red->_($this->id)."'
                ORDER BY fecha_expiracion DESC";
			$res_tarifas = $bd_red->ejecuta_consulta($consulta_tarifas);
            if ($res_tarifas == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_tarifas."'");
            }
            $numero_tarifas = $res_tarifas->dame_numero_filas();
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_tarifas > 0)
            {
                if ($numero_tarifas == 1)
                {
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_tarifas." ".$this->idiomas->_("tarifa").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_tarifas." ".$this->idiomas->_("tarifas").":";
                }

                $info_tarifas = "<ul>";
                while ($fila_tarifa = $res_tarifas->dame_siguiente_fila())
                {
                    $cadena_fecha_expiracion_local_local = convierte_formato_fecha($fila_tarifa["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                    $info_tarifas .= "<li>".
                        htmlspecialchars($fila_tarifa['nombre'], ENT_QUOTES).
                        " (".$this->idiomas->_("fecha de expiración").": ".$cadena_fecha_expiracion_local_local.")".
                        "</li>";
                }
                $info_tarifas .= "</ul>";

                $info .= $info_tarifas;
            }
            else
            {
                $info .= $this->idiomas->_("Este grupo no tiene tarifas");
                $info .= "<br/>";
            }

            // Sensores a los que está asignado este grupo de tarifas
            $nombres_sensores = $this->dame_nombres_sensores_grupo_tarifas_asignado($medicion);
            $numero_sensores = count($nombres_sensores);

            $info .= "<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_sensores > 0)
            {
                if ($numero_sensores == 1)
                {
                    $info .= $this->idiomas->_("Este grupo de tarifas está asignado a")." ".$numero_sensores." ".$this->idiomas->_("sensor").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Este grupo de tarifas está asignado a")." ".$numero_sensores." ".$this->idiomas->_("sensores").":";
                }

                $lista_nombres_sensores = "<ul>";
                foreach ($nombres_sensores as $nombre_sensor)
                {
                    $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
                }
                $lista_nombres_sensores .= "</ul>";

                $info .= $lista_nombres_sensores;
            }
            else
            {
                $info .= $this->idiomas->_("Este grupo de tarifas no está asignado a ningún sensor");
                $info .= "<br/>";
            }

            return ($info);
        }


        //
        // Funciones auxiliares
        //


        function dame_info_expiracion_ultima_tarifa($medicion)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $consulta_tarifa = "
				SELECT
					expiracion,
                    fecha_expiracion,
                    numero_dias_preaviso_expiracion
				FROM ".$tabla_tarifas."
				WHERE
					grupo = '".$bd_red->_($this->id)."'
				ORDER BY fecha_expiracion DESC
                LIMIT 1";
            $res_tarifa = $bd_red->ejecuta_consulta($consulta_tarifa);
            if ($res_tarifa == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifa."'");
            }
            if ($res_tarifa->dame_numero_filas() > 0)
            {
                $info_expiracion = $res_tarifa->dame_siguiente_fila();
            }
            else
            {
                $info_expiracion = NULL;
            }
            return ($info_expiracion);
        }


        function dame_aviso_expiracion_ultima_tarifa($medicion)
        {
            $aviso_expiracion = "";

            $info_expiracion = $this->dame_info_expiracion_ultima_tarifa($medicion);
            if ($info_expiracion !== NULL)
            {
                $numero_dias_restantes_expiracion = dame_numero_dias_restantes_expiracion_tarifa($info_expiracion["fecha_expiracion"]);
                if (($numero_dias_restantes_expiracion <= $info_expiracion["numero_dias_preaviso_expiracion"]) || ($numero_dias_restantes_expiracion <= 0))
                {
                    $aviso_expiracion .= "<i class='icon-warning-sign color-rojo'></i> ";
                    if ($numero_dias_restantes_expiracion > 0)
                    {
                        $aviso_expiracion .= $this->idiomas->_("La última tarifa expirará dentro de")." ".$numero_dias_restantes_expiracion." ";
                        if ($numero_dias_restantes_expiracion == 1)
                        {
                            $aviso_expiracion .= $this->idiomas->_("día");
                        }
                        else
                        {
                            $aviso_expiracion .= $this->idiomas->_("días");
                        }
                    }
                    else
                    {
                        if ($numero_dias_restantes_expiracion == 0)
                        {
                            $aviso_expiracion .= $this->idiomas->_("La última tarifa expira hoy");
                        }
                        else
                        {
                            $numero_dias_tarifa_expirada = -$numero_dias_restantes_expiracion;
                            $aviso_expiracion .= $this->idiomas->_("La última tarifa expiró hace")." ".$numero_dias_tarifa_expirada." ";
                            if ($numero_dias_tarifa_expirada == 1)
                            {
                                $aviso_expiracion .= $this->idiomas->_("día");
                            }
                            else
                            {
                                $aviso_expiracion .= $this->idiomas->_("días");
                            }
                        }
                    }
                    $aviso_expiracion .= "<br/>";
                }
            }
            return ($aviso_expiracion);
        }


        function dame_nombres_sensores_grupo_tarifas_asignado($medicion)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Parámetros dependientes de la medición
            $clase_sensor = dame_clase_sensor_medicion($medicion);
            $indice_parametros_clase_sensor_id_grupo_tarifas = dame_indice_parametro_clase_sensor_grupo_tarifas($medicion);

            // Sensores a los que está asignado este grupo de tarifas
            $consulta_sensores = "
				SELECT
					nombre
				FROM sensores
				WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".$clase_sensor."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')
                ORDER BY nombre ASC";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
            }

            $nombres_sensores = array();
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                array_push($nombres_sensores, $fila_sensor["nombre"]);
            }
            return ($nombres_sensores);
        }


        static function dame_ids_grupos_tarifas_usuario_actual($medicion, $ids_sensores_usuario = NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Tablas de tarifas
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);

            // Se muestran los grupos de tarifas con tarifas visibles por el usuario actual
            $ids_grupos_tarifas = array();
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual($medicion, $ids_sensores_usuario);
            $cadena_ids_tarifas_consulta = dame_cadena_ids_consulta($ids_tarifas_usuario);
            $consulta_grupos_tarifas = "
                SELECT ".
                    $tabla_grupos_tarifas.".id AS id
                FROM ".
                    $tabla_tarifas.", ".
                    $tabla_grupos_tarifas."
                WHERE
                    (".$tabla_tarifas.".id IN (".$cadena_ids_tarifas_consulta."))
                    AND (".$tabla_tarifas.".grupo = ".$tabla_grupos_tarifas.".id)";
            $res_grupos_tarifas = $bd_red->ejecuta_consulta($consulta_grupos_tarifas);
            if ($res_grupos_tarifas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_grupos_tarifas."'");
            }
            while ($fila_grupo_tarifas = $res_grupos_tarifas->dame_siguiente_fila())
            {
                if (in_array($fila_grupo_tarifas["id"], $ids_grupos_tarifas) == false)
                {
                    array_push($ids_grupos_tarifas, $fila_grupo_tarifas["id"]);
                }
            }

            // Se muestran los grupos de tarifas 'vacíos' (sin tarifas asignadas)
            $consulta_grupos_tarifas_vacios = "
                SELECT id
                FROM ".$tabla_grupos_tarifas."
                WHERE id NOT IN (
                    SELECT DISTINCT(grupo)
                    FROM ".$tabla_tarifas."
                )";
            $res_grupos_tarifas_vacios = $bd_red->ejecuta_consulta($consulta_grupos_tarifas_vacios);
            if ($res_grupos_tarifas_vacios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_grupos_tarifas_vacios."'");
            }
            while ($fila_grupo_tarifas_vacio = $res_grupos_tarifas_vacios->dame_siguiente_fila())
            {
                array_push($ids_grupos_tarifas, $fila_grupo_tarifas_vacio["id"]);
            }

            return ($ids_grupos_tarifas);
        }
	}
?>
