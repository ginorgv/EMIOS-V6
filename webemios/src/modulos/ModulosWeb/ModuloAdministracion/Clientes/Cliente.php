<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');


	// Clase que representa a un cliente final ("propietario" de las redes)
	class Cliente
	{
		// Funciones estáticas de cliente


		// Devuelve la cabecera para la tabla de clientes
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre")
			));
		}


        // Devuelve la consulta para la tabla de clientes
        static function dame_consulta_clientes()
        {
            $consulta = "
				SELECT *
				FROM clientes
				ORDER BY nombre ASC";
			return ($consulta);
        }


        // Devuelve la tabla de clientes
        static function dame_tabla_clientes()
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $boton_actualizar_tabla_clientes = "<i id='actualiza_clientes' class='icon-refresh color-blanco boton_administracion_actualizar_tabla_clientes boton-tabla-datos'></i>";
            $boton_anyadir_cliente = "<i id='anyade_modifica_cliente' class='icon-plus color-blanco boton_administracion_mostrar_ventana_anyadir_modificar_cliente boton-tabla-datos'></i>";
			$opciones = array($boton_anyadir_cliente, $boton_actualizar_tabla_clientes);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_CLIENTES,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-administracion-clientes",
                $idiomas->_("Clientes"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Cliente::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los clientes a la tabla y el pie de tabla
            $consulta = Cliente::dame_consulta_clientes();
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_clientes = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $cliente = new Cliente($fila);
                $params_fila = array(
                    "opciones" => $cliente->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosCliente__".$fila['id'],
                    $cliente->dame_datos_tabla(),
                    $params_fila
                );
            }
			$tabla->anyade_pie($idiomas->_("Clientes").": ".$numero_clientes);

            return ($tabla->dame_tabla());
        }


		// Miembros de cliente


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


		// Datos para la tabla
		function dame_datos_tabla()
        {
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre
                if ($nombre_correcto == true)
                {
                    $nombre = "[".$icono_fila_con_errores."] ".$nombre;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
                $nombre
            ));
        }


        // Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            $editar = "<i id='anyade_modifica_cliente__".$this->id."' ".
                "class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_cliente boton-tabla-datos'></i>";
            $borrar = "<i id='elimina_cliente__".$this->id."' nombre_cliente='".$this->params["nombre"]."' ".
                "class='icon-remove color-gris boton_administracion_eliminar_cliente boton-tabla-datos'></i>";
            $opciones = array($borrar, $editar);

			return ($opciones);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

          	// Se recuperan las redes del cliente
			$consulta_redes = "
				SELECT nombre
				FROM redes
				WHERE
                    cliente = '".$bd_red->_($this->id)."'";
			$res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            if ($res_redes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_redes."'");
            }
            $numero_redes = $res_redes->dame_numero_filas();
            $nombres_redes = "<ul>";
            while ($fila_red = $res_redes->dame_siguiente_fila())
            {
                $nombres_redes .= "<li>".htmlspecialchars($fila_red["nombre"], ENT_QUOTES)."</li>";
            }
            $nombres_redes .= "</ul>";

            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_redes > 0)
            {
                $texto_redes = $numero_redes;
                if ($numero_redes == 1)
                {
                    $texto_redes .= " ".$this->idiomas->_("red");
                }
                else
                {
                    $texto_redes .= " ".$this->idiomas->_("redes");
                }
                $texto_redes .= ": ".$nombres_redes;
                $info .= $this->idiomas->_("Este cliente tiene")." ".$texto_redes;
            }
            else
            {
                $info .= $this->idiomas->_("Este cliente no tiene redes");
            }

            return ($info);
        }
	}
?>
