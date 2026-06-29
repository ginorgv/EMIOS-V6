<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


    // Clase que representa un periodo de tiempo (día año inicio, dia año fin)
	class RangoDias
	{
        // Funciones estáticas de periodo


        // Devuelve la cabecera para la tabla de rangos de días
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Día de inicio"),
                $idiomas->_("Día de fin")
			));
        }


        // Devuelve la consulta para la tabla de rangos de días
        static function dame_consulta_rangos_dias($origen, $id_origen)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM rangos_dias
                WHERE
                    (origen = '".$bd_red->_($origen)."')
                    AND (id_origen = ".$bd_red->_($id_origen).")
                ORDER BY
                    dia_anyo_inicio";
            return ($consulta);
        }


        // Devuelve el número de rangos de días
        static function dame_numero_rangos_dias($origen, $id_origen)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = RangoDias::dame_consulta_rangos_dias($origen, $id_origen);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_rangos_dias = $res->dame_numero_filas();
            return ($numero_rangos_dias);
        }


        // Devuelve la tabla de rangos de días
        static function dame_tabla_rangos_dias($origen, $id_origen)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            switch ($origen)
            {
                case ORIGEN_RANGOS_DIAS_EVENTO:
                {
                    $administracion_rangos_dias = Evento::dame_administracion_eventos();
                    break;
                }
                case ORIGEN_RANGOS_DIAS_REGLA:
                {
                    $administracion_rangos_dias = Regla::dame_administracion_reglas();
                    break;
                }
            }
            if ($administracion_rangos_dias == true)
            {
                $boton_anyadir_rango_dias = "<i id='anyade_modifica_rango_dias__".$origen."__".$id_origen."' class='icon-plus color-blanco boton_mostrar_ventana_anyadir_modificar_rango_dias boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_rango_dias);
            }
            $boton_actualizar_tabla_rangos_dias = "<i id='actualiza_tabla_rangos_dias__".$origen."__".$id_origen."' class='icon-refresh color-blanco boton_actualizar_tabla_rangos_dias boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_rangos_dias);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_RANGOS_DIAS,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-rangos-dias",
                $idiomas->_("Rangos de días"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = RangoDias::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los rangos de días a la tabla y el pie de tabla
            $consulta = RangoDias::dame_consulta_rangos_dias($origen, $id_origen);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_rangos_dias = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $dia_anyo_inicio = convierte_formato_dia_anyo($fila['dia_anyo_inicio'], FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                $dia_anyo_fin = convierte_formato_dia_anyo($fila['dia_anyo_fin'], FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                $datos = array(
                    $dia_anyo_inicio,
                    $dia_anyo_fin
                );

                $opciones = array();
                if ($administracion_rangos_dias == true)
                {
                    $editar = "<i id='anyade_modifica_rango_dias__".$origen."__".$id_origen."__".$fila['id']."' class='icon-pencil color-gris boton_mostrar_ventana_anyadir_modificar_rango_dias boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_rango_dias__".$origen."__".$id_origen."__".$fila['id']."' class='icon-remove color-gris boton_eliminar_rango_dias boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosRangoDias__".$origen."__".$id_origen."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Rangos de días").": ".$numero_rangos_dias);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de periodo


		public $idiomas;

		public $id;

        public $dia_anyo_inicio;
        public $dia_anyo_fin;


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

			$this->id = $params["id"];
            $this->dia_anyo_inicio = $params["dia_anyo_inicio"];
            $this->dia_anyo_fin = $params["dia_anyo_fin"];
		}


        function dame_conf()
		{
            $conf = array();

            $conf["DIA_ANYO_INICIO"] = $this->dia_anyo_inicio;
            $conf["DIA_ANYO_FIN"] = $this->dia_anyo_fin;

			return ($conf);
		}


        //
        // Funciones auxiliares
        //


        // Devuelve la descripción del origen del rango de días
        static function dame_descripcion_origen_rango_dias($origen_rango_dias)
        {
            $idiomas = new Idiomas();

            switch ($origen_rango_dias)
            {
                case ORIGEN_RANGOS_DIAS_EVENTO:
                {
                    $descripcion = "Evento";
                    break;
                }
                case ORIGEN_RANGOS_DIAS_REGLA:
                {
                    $descripcion = "Regla";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocido";
                    break;
                }
            }
            return ($idiomas->_($descripcion));
        }


        // Devuelve el nombre del origen del rango de días
        static function dame_nombre_id_origen_rango_dias($origen_rango_dias, $id_origen_rango_dias)
        {
            $idiomas = new Idiomas();

            switch ($origen_rango_dias)
            {
                case ORIGEN_RANGOS_DIAS_EVENTO:
                {
                    $nombre_origen = dame_nombre_evento($id_origen_rango_dias);
                    break;
                }
                case ORIGEN_RANGOS_DIAS_REGLA:
                {
                    $nombre_origen = dame_nombre_regla($id_origen_rango_dias);
                    break;
                }
                default:
                {
                    $nombre_origen = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($nombre_origen);
        }
    }
?>
