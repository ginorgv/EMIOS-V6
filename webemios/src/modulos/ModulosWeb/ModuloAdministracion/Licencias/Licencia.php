<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


	// Clase que representa la licencia de un usuario de una red
	class Licencia
	{
		// Funciones estáticas de licencia


		// Devuelve la cabecera para la tabla de licencias
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Módulo"),
                $idiomas->_("Activada"),
                $idiomas->_("Número máximo de elementos")
			));
		}


        // Devuelve la consulta para la tabla de licencias
        static function dame_consulta_licencias()
        {
            $consulta = "
				SELECT *
				FROM licencias
                WHERE
                    red = '".$_SESSION["id_red"]."'
				ORDER BY
                    modulo ASC";
			return ($consulta);
        }


        // Devuelve la tabla de licencias
        static function dame_tabla_licencias()
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
            {
                $boton_anyadir_licencia = "<i id='anyade_modifica_licencia' class='icon-plus color-blanco boton_administracion_mostrar_ventana_anyadir_modificar_licencia boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_licencia);
            }
            $boton_actualizar_tabla_licencias = "<i id='actualiza_licencias' class='icon-refresh color-blanco boton_administracion_actualizar_tabla_licencias boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_licencias);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_LICENCIAS,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-administracion-licencias",
                $idiomas->_("Licencias"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Licencia::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las licencias a la tabla y el pie de tabla
            $consulta = Licencia::dame_consulta_licencias();
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_licencias = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $licencia = new Licencia($fila);
                $params_fila = array(
                    "opciones" => $licencia->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosLicencia__".$fila['id'],
                    $licencia->dame_datos_tabla(),
                    $params_fila
                );
            }
			$tabla->anyade_pie($idiomas->_("Licencias").": ".$numero_licencias);

            return ($tabla->dame_tabla());
        }


        // Devuelve los módulos de las licencias
        static function dame_modulos_licencias()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = Licencia::dame_consulta_licencias();
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $modulos = array();
            while ($fila = $res->dame_siguiente_fila())
            {
                $modulo = $fila["modulo"];
                array_push($modulos, $modulo);
            }
			return ($modulos);
        }


		// Miembros de licencia


		public $idiomas;

		public $id;
        public $params;


		// Funciones de cliente


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

            $this->id = $params["id"];
            $this->params = $params;
		}


		// Datos para la tabla del cliente
		function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre_modulo = $icono_dato_erroneo;
            $activada = $icono_dato_erroneo;
            $numero_maximo_elementos = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_modulo_correcto = false;
            try
            {
                // Nombre
                $nombre_modulo = dame_nombre_modulo($this->params["modulo"]);
                $nombre_modulo_correcto = true;

                // Activada y número máximo de elementos
                $activada = dame_descripcion_valores_si_no($this->params["activada"]);
                $numero_maximo_elementos = $this->idiomas->_("ND");
                switch ($this->params["modulo"])
                {
                    case MODULO_RED:
                    case MODULO_SENSORES:
                    case MODULO_ACTUADORES:
                    case MODULO_PROYECTOS:
                    {
                        $numero_maximo_elementos = $this->params["numero_maximo_elementos"];
                        if ($numero_maximo_elementos <= 0)
                        {
                            $numero_maximo_elementos = $this->idiomas->_("Ilimitado");
                        }
                        break;
                    }
                }
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre del módulo
                if ($nombre_modulo_correcto == true)
                {
                    $nombre_modulo = "[".$icono_fila_con_errores."] ".$nombre_modulo;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
				$nombre_modulo,
                $activada,
                $numero_maximo_elementos
			));
		}


		// Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            $opciones = array();
            if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
            {
                $editar = "<i id='anyade_modifica__".$this->id."' class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_licencia boton-tabla-datos'></i>";
                $borrar = "<i id='elimina__".$this->id."' class='icon-remove color-gris boton_administracion_eliminar_licencia boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }

			return ($opciones);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

          	// Se recuperan los usuarios de esta licencia
			$consulta_usuarios = "
				SELECT
                    usuarios.nombre AS nombre
				FROM usuarios, licencias_usuarios
				WHERE
                    (licencias_usuarios.licencia = '".$bd_red->_($this->id)."')
                    AND (usuarios.id = licencias_usuarios.usuario)
                ORDER BY usuarios.nombre ASC";
			$res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
            if ($res_usuarios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
            }
            $numero_usuarios = $res_usuarios->dame_numero_filas();
            $nombres_usuarios = "<ul>";
            while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
            {
                $nombres_usuarios .= "<li>".htmlspecialchars($fila_usuario["nombre"], ENT_QUOTES)."</li>";
            }
            $nombres_usuarios .= "</ul>";

            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_usuarios > 0)
            {
                $texto_usuarios = $numero_usuarios;
                if ($numero_usuarios == 1)
                {
                    $texto_usuarios .= " ".$this->idiomas->_("usuario");
                }
                else
                {
                    $texto_usuarios .= " ".$this->idiomas->_("usuarios");
                }
                $texto_usuarios .= ": ".$nombres_usuarios;
                $info .= $this->idiomas->_("Esta licencia está asociada a")." ".$texto_usuarios;
            }
            else
            {
                $info .= $this->idiomas->_("Esta licencia no está asociada a ningún usuario");
            }

            return ($info);
        }
	}
?>
