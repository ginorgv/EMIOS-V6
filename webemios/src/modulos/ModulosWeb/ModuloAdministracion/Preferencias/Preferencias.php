<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


	// Clase que representa preferencias (de visualización) (asociadas a una URL)
	class Preferencias
	{
		// Funciones estáticas de preferencias


		// Devuelve la cabecera para la tabla de preferencias
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("URL"),
                $idiomas->_("Logo"),
                $idiomas->_("Título de Web"),
                $idiomas->_("Tema"),
                $idiomas->_("Colores de gráficas")
			));
		}


        // Devuelve la consulta para la tabla de preferencias
        static function dame_consulta_preferencias()
        {
            $consulta = "
				SELECT *
				FROM preferencias
				ORDER BY url ASC";
			return ($consulta);
        }


        // Devuelve la tabla de preferencias
        static function dame_tabla_preferencias()
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $boton_actualizar_tabla_preferencias = "<i id='actualiza_preferencias' class='icon-refresh color-blanco boton_administracion_actualizar_tabla_preferencias boton-tabla-datos'></i>";
            $boton_anyadir_preferencias = "<i id='anyade_modifica_preferencias' class='icon-plus color-blanco boton_administracion_mostrar_ventana_anyadir_modificar_preferencias boton-tabla-datos'></i>";
			$opciones = array($boton_anyadir_preferencias, $boton_actualizar_tabla_preferencias);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_PREFERENCIAS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_PREFERENCIAS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_NORMAL,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-administracion-preferencias",
                $idiomas->_("Preferencias"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Preferencias::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las preferencias a la tabla y el pie de tabla
            $consulta = Preferencias::dame_consulta_preferencias();
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_preferencias = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $preferencias = new Preferencias($fila);
                $params_fila = array(
                    "opciones" => $preferencias->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosPreferencias__".$fila['id']."__".$fila['url'],
                    $preferencias->dame_datos_tabla(),
                    $params_fila
                );
            }
			$tabla->anyade_pie($idiomas->_("Preferencias").": ".$numero_preferencias);

            return ($tabla->dame_tabla());
        }


		// Miembros de preferencias


		public $idiomas;

		public $id;
		public $params;


		// Funciones de preferencias


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

            $this->id = $params["id"];
			$this->params = $params;
		}


		// Datos para la tabla
		function dame_datos_tabla()
        {
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $url = $icono_dato_erroneo;
            $descripcion_logo = $icono_dato_erroneo;
            $titulo_web = $icono_dato_erroneo;
            $descripcion_tema = $icono_dato_erroneo;
            $descripcion_paleta_colores_graficas = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $url_correcta = false;
            try
            {
                // URL
                $url = htmlspecialchars($this->params["url"], ENT_QUOTES);
                $url_correcta = true;

                // Descripción del logo
                $logo_personalizado = $this->params["logo_personalizado"];
                switch ($logo_personalizado)
                {
                    case VALOR_NO:
                    {
                        $descripcion_logo = $this->idiomas->_("Defecto");
                        break;
                    }
                    case VALOR_SI:
                    {
                        $descripcion_logo = $this->params["nombre_logo"];
                        $url_logo = $this->params["url_logo"];
                        if ($url_logo != "")
                        {
                            $descripcion_logo .= " (".$url_logo.")";
                        }
                        break;
                    }
                }
                $descripcion_logo = htmlspecialchars($descripcion_logo, ENT_QUOTES);

                // Título de Web
                $titulo_web = $this->params["titulo_web"];
                if ($titulo_web == "")
                {
                    $titulo_web = $this->idiomas->_("Defecto");
                }
                $titulo_web = htmlspecialchars($titulo_web, ENT_QUOTES);

                // Descripciones de tema y de paleta de colores de las gráficas
                $descripcion_tema = dame_descripcion_tema($this->params["tema"]);
                $descripcion_paleta_colores_graficas = dame_descripcion_paleta_colores_graficas($this->params["paleta_colores_graficas"]);
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en la URL
                if ($url_correcta == true)
                {
                    $url = "[".$icono_fila_con_errores."] ".$url;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
                $url,
                $descripcion_logo,
                $titulo_web,
                $descripcion_tema,
                $descripcion_paleta_colores_graficas
            ));
        }


        // Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            $url = htmlspecialchars($this->params["url"], ENT_QUOTES);

            $editar = "<i id='anyade_modifica__".$this->id."' class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_preferencias boton-tabla-datos'></i>";
            $borrar = "<i id='elimina__".$this->id."' url_preferencias='".$url."' class='icon-remove color-gris boton_administracion_eliminar_preferencias boton-tabla-datos'></i>";
            $opciones = array($borrar, $editar);

			return ($opciones);
		}
	}
?>
