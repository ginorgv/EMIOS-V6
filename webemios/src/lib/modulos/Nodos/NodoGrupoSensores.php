<?php
	session_start();


    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


	class NodoGrupoSensores extends Nodo
	{
		static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            // Flag para mostrar la localización
            $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);

            // Se devuelve la cabecera
            if ($mostrar_localizacion == true)
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Localización"),
                    $idiomas->_("Clase"));
            }
            else
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Clase"));
            }
            return ($cabecera_tabla);
		}


		function dame_datos_tabla()
		{
            // Flag para mostrar la localización
            if (array_key_exists("mostrar_localizacion", $this->params) == true)
            {
                $mostrar_localizacion = $this->params["mostrar_localizacion"];
            }
            else
            {
                $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);
            }

            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $nombre_localizacion = $icono_dato_erroneo;
            $nombre_clase = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Localización
                if ($mostrar_localizacion == true)
                {
                    if (array_key_exists("nombre_localizacion", $this->params) == true)
                    {
                        $nombre_localizacion = $this->params["nombre_localizacion"];
                    }
                    else
                    {
                        $nombre_localizacion = dame_nombre_localizacion($this->params["localizacion"]);
                    }
                    $nombre_localizacion = htmlspecialchars($nombre_localizacion, ENT_QUOTES);
                }

                // Nombre de clase
                $nombre_clase = NodoSensor::dame_descripcion_clase_sensor($this->params['clase']);
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
            $datos_tabla = array();
            array_push($datos_tabla, $nombre);
            if ($mostrar_localizacion == true)
            {
                array_push($datos_tabla, $nombre_localizacion);
            }
            array_push($datos_tabla, $nombre_clase);
            return ($datos_tabla);
		}


        function dame_herramientas_detalles_tabla()
		{
			$herramientas = "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->tipo."__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_nodo'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

			return ($herramientas);
		}


		function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            $administracion_eventos = Evento::dame_administracion_eventos();

            // Se recupera la fila del grupo de sensores
            $fila_grupo = dame_fila_grupo_sensores($this->id);

            // Información para administradores:
            // - Identificador
            if ($administracion_sensores == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Descripción
            if ($fila_grupo['descripcion'] != "")
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".$fila_grupo['descripcion']."<br/>";
                $info .= "<br/>";
            }

            // Sensores del grupo
            $consulta_sensores = "
				SELECT nombre
				FROM sensores
				WHERE
					grupo = '".$bd_red->_($this->id)."'
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
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_sensores." ".$this->idiomas->_("sensor").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_sensores." ".$this->idiomas->_("sensores").":";
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
                $info .= $this->idiomas->_("Este grupo no tiene sensores")."<br/>";
            }
            $info .= "<br/>";

            // Se muestran los eventos configurados si es administrador de eventos
            if ($administracion_eventos == true)
            {
                // Se muestran los eventos configurados (si los hay)
                $eventos_configurados = $this->dame_nombres_eventos_configurados($fila_grupo["id"]);
                $numero_eventos_configurados = count($eventos_configurados);
                if ($numero_eventos_configurados > 0)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    if ($numero_eventos_configurados == 1)
                    {
                        $info .= $this->idiomas->_("Este grupo tiene el siguiente evento configurado").":";
                    }
                    else
                    {
                        $info .= $this->idiomas->_("Este grupo tiene los siguientes eventos configurados").":";
                    }
                    $nombres_eventos = "<ul>";
                    foreach ($eventos_configurados as $evento_configurado)
                    {
                        $nombres_eventos .= "<li>".$evento_configurado."</li>";
                    }
                    $nombres_eventos .= "</ul>";
                    $info .= $nombres_eventos;
                    $info .= "<br/>";
                }
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
		}


        //
        //  Funciones auxiliares
        //


        function dame_nombres_eventos_configurados($id_grupo_sensores)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $eventos_configurados = array();

            $consulta_eventos = "
                SELECT nombre
                FROM eventos
                WHERE
                    (origen = '".ORIGEN_EVENTO_GRUPO_SENSORES."')
                    AND (id_origen = ".$bd_red->_($id_grupo_sensores).")";
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }
            while ($fila_evento = $res_eventos->dame_siguiente_fila())
            {
                array_push($eventos_configurados, $fila_evento["nombre"]);
            }

            return ($eventos_configurados);
        }
	}
?>
