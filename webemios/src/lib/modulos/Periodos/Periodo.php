<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


    // Clase que representa un periodo de tiempo (día inicio - fin, hora inicio - fin)
	class Periodo
	{
        // Funciones estáticas de periodo


        // Devuelve la cabecera para la tabla de periodos
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Día de inicio"),
                $idiomas->_("Día de fin"),
                $idiomas->_("Hora de inicio"),
                $idiomas->_("Hora de fin")
			));
        }


        // Devuelve la consulta para la tabla de periodos
        static function dame_consulta_periodos($origen, $id_origen)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT
                    id,
                    dia_inicio,
                    dia_fin,
                    TIME_FORMAT(hora_inicio, '%H:%i') AS hora_inicio,
                    TIME_FORMAT(hora_fin, '%H:%i') AS hora_fin
                FROM periodos
                WHERE
                    (origen = '".$bd_red->_($origen)."')
                    AND (id_origen = ".$bd_red->_($id_origen).")
                ORDER BY
                    dia_inicio,
                    hora_inicio";
            return ($consulta);
        }


        // Devuelve el número de periodos
        static function dame_numero_periodos($origen, $id_origen)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = Periodo::dame_consulta_periodos($origen, $id_origen);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_periodos = $res->dame_numero_filas();
            return ($numero_periodos);
        }


        // Devuelve la tabla de periodos
        static function dame_tabla_periodos($origen, $id_origen)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            switch ($origen)
            {
                case ORIGEN_PERIODOS_EVENTO:
                {
                    $administracion_periodos = Evento::dame_administracion_eventos();
                    break;
                }
                case ORIGEN_PERIODOS_REGLA:
                {
                    $administracion_periodos = Regla::dame_administracion_reglas();
                    break;
                }
            }
            if ($administracion_periodos == true)
            {
                $boton_anyadir_periodo = "<i id='anyade_modifica_periodo__".$origen."__".$id_origen."' class='icon-plus color-blanco boton_mostrar_ventana_anyadir_modificar_periodo boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_periodo);
            }
            $boton_actualizar_tabla_periodos = "<i id='actualiza_tabla_periodos__".$origen."__".$id_origen."' class='icon-refresh color-blanco boton_actualizar_tabla_periodos boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_periodos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_PERIODOS,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-periodos",
                $idiomas->_("Periodos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Periodo::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los periodos a la tabla y el pie de tabla
            $consulta = Periodo::dame_consulta_periodos($origen, $id_origen);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_periodos = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $datos = array(
                    dame_nombre_dia_semana($fila['dia_inicio']),
                    dame_nombre_dia_semana($fila['dia_fin']),
                    $fila['hora_inicio'],
                    $fila['hora_fin']
                );

                $opciones = array();
                if ($administracion_periodos == true)
                {
                    $editar = "<i id='anyade_modifica_periodo__".$origen."__".$id_origen."__".$fila['id']."' class='icon-pencil color-gris boton_mostrar_ventana_anyadir_modificar_periodo boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_periodo__".$origen."__".$id_origen."__".$fila['id']."' class='icon-remove color-gris boton_eliminar_periodo boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosPeriodo__".$origen."__".$id_origen."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Periodos").": ".$numero_periodos);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de periodo


		public $idiomas;

		public $id;

        public $dia_inicio;
        public $dia_fin;
        public $hora_inicio;
        public $hora_fin;


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

			$this->id = $params["id"];
            $this->dia_inicio = $params["dia_inicio"];
            $this->dia_fin = $params["dia_fin"];
			$this->hora_inicio = $params["hora_inicio"];
            $this->hora_fin = $params["hora_fin"];
		}


        function dame_conf()
		{
            $conf = array();

            $conf["DIA_INICIO"] = $this->dia_inicio;
            $conf["DIA_FIN"] = $this->dia_fin;
            $conf["HORA_INICIO"] = $this->hora_inicio;
            $conf["HORA_FIN"] = $this->hora_fin;

			return ($conf);
		}


        //
        // Funciones auxiliares
        //


        // Devuelve la descripción del origen del periodo
        static function dame_descripcion_origen_periodo($origen_periodo)
        {
            $idiomas = new Idiomas();

            switch ($origen_periodo)
            {
                case ORIGEN_PERIODOS_EVENTO:
                {
                    $descripcion = "Evento";
                    break;
                }
                case ORIGEN_PERIODOS_REGLA:
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


        // Devuelve el nombre del origen del periodo
        static function dame_nombre_id_origen_periodo($origen_periodo, $id_origen_periodo)
        {
            $idiomas = new Idiomas();

            switch ($origen_periodo)
            {
                case ORIGEN_PERIODOS_EVENTO:
                {
                    $nombre_id_origen = dame_nombre_evento($id_origen_periodo);
                    break;
                }
                case ORIGEN_PERIODOS_REGLA:
                {
                    $nombre_id_origen = dame_nombre_regla($id_origen_periodo);
                    break;
                }
                default:
                {
                    $nombre_id_origen = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($nombre_id_origen);
        }
    }
?>
