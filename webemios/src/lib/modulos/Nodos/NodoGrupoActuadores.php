<?php
	session_start();


    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


	class NodoGrupoActuadores extends NodoActuador
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
                    $idiomas->_("Clase"),
                    $idiomas->_("Programación"),
                    $idiomas->_("Última acción"));
            }
            else
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Clase"),
                    $idiomas->_("Programación"),
                    $idiomas->_("Última acción"));
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
            $nombre_programacion = $icono_dato_erroneo;
            $ultima_accion = $icono_dato_erroneo;

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
                $nombre_clase = NodoActuador::dame_descripcion_clase_actuador($this->params['clase']);

                // Programación
                if ($this->params["programacion"] != ID_NINGUNO)
                {
                    $nombre_programacion = dame_nombre_programacion($this->params["programacion"]);
                }
                else
                {
                    $nombre_programacion = $this->idiomas->_("Ninguna");
                }
                $nombre_programacion = htmlspecialchars($nombre_programacion, ENT_QUOTES);

                // Última acción
                $contenido_ultima_accion = $this->params["contenido_ultima_accion"];
                $hora_ultima_accion = $this->params["hora_ultima_accion"];
                if ($contenido_ultima_accion === NULL)
                {
                    $ultima_accion = $this->idiomas->_("Sin última acción");
                }
                else
                {
                    $ultima_accion = NodoActuador::dame_imagen_estado_actual_clase($this->params["clase"], $contenido_ultima_accion, $hora_ultima_accion);
                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_ultima_accion_utc = convierte_formato_fecha($this->params["hora_ultima_accion"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_ultima_accion_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultima_accion_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                    $ultima_accion .= " (".$cadena_hora_ultima_accion_local.")";
                }
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
            array_push($datos_tabla, $nombre_programacion);
            array_push($datos_tabla, $ultima_accion);
            return ($datos_tabla);
		}


        function dame_herramientas_detalles_tabla()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT *
				FROM grupos_actuadores
				WHERE
					id = '".$bd_red->_($this->id)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();

            // Herramientas de detalles de grupo de actuadores
            $administracion_actuadores = NodoActuador::dame_administracion_actuadores();
            $herramientas = "";

            // Recargar la fila del grupo de actuadores
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->tipo."__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_nodo'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Adición de comentario al grupo de actuadores
            if ($administracion_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button objeto='".$fila["nombre"]."' origen_comentario='".ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES."' ".
                            "class='btn-mini btn btn-success boton_mostrar_ventana_anyadir_modificar_comentario'>".
                            $this->idiomas->_("Añadir comentario")."
                        </button>
                    </span>";
            }

            // Acciones del grupo de actuadores
            $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
            if ($envio_acciones_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id_grupo_actuadores='".$this->id."' origen_envio_accion='".ORIGEN_ENVIO_ACCION_DETALLES_TABLA_GRUPOS_ACTUADORES."'
                            class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores'>".
                            $this->idiomas->_("Enviar acción")."
                        </button>
                    </span>";
            }

            // Borrado de acciones enviadas del grupo de actuadores
            if ($administracion_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_borrar_acciones_enviadas__".$this->id."__".$fila['clase']."' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_grupo_actuadores'>".
                            $this->idiomas->_("Borrar acciones enviadas")."
                        </button>
                    </span>";
            }

			return ($herramientas);
		}


		function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";
            $administracion_actuadores = NodoActuador::dame_administracion_actuadores();

            // Se recupera la fila del grupo de actuadores
            $fila_grupo = dame_fila_grupo_actuadores($this->id);

            // Información para administradores:
            // - Identificador
            if ($administracion_actuadores == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Descripción
            if ($fila_grupo['descripcion'] != "")
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ". htmlspecialchars($fila_grupo['descripcion'], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
            }

            // Comentarios anterior y siguiente de actuador
            $filas_comentarios = Comentario::dame_filas_comentarios_anterior_posterior_objeto(
                ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES,
                $fila_grupo["nombre"]);
            $fila_comentario_anterior = $filas_comentarios["anterior"];
            $fila_comentario_posterior = $filas_comentarios["posterior"];
            if (($fila_comentario_anterior !== NULL) || ($fila_comentario_posterior !== NULL))
            {
                // Comentario anterior
                if ($fila_comentario_anterior !== NULL)
                {
                    $cadenas_comentario_anterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_anterior, false);
                    $cadena_hora_comentario_anterior_local_local = $cadenas_comentario_anterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_anterior = $cadenas_comentario_anterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_anterior = $cadenas_comentario_anterior["descripcion_comentario"];
                    $comentario_anterior = $descripcion_tipo_comentario_anterior." (".$cadena_hora_comentario_anterior_local_local.") ".
                        "[".$descripcion_comentario_anterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario anterior").": ".$comentario_anterior."<br/>";
                }

                // Comentario posterior
                if ($fila_comentario_posterior !== NULL)
                {
                    $cadenas_comentario_posterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_posterior, false);
                    $cadena_hora_comentario_posterior_local_local = $cadenas_comentario_posterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_posterior = $cadenas_comentario_posterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_posterior = $cadenas_comentario_posterior["descripcion_comentario"];
                    $comentario_posterior = $descripcion_tipo_comentario_posterior." (".$cadena_hora_comentario_posterior_local_local.") ".
                        "[".$descripcion_comentario_posterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario siguiente").": ".$comentario_posterior."<br/>";
                }

                // Salto de línea
                $info .= "<br/>";
            }

            // Actuadores del grupo
            $consulta_actuadores = "
				SELECT nombre
				FROM actuadores
				WHERE
					grupo = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
			$res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuadores."'");
            }
            $numero_actuadores = $res_actuadores->dame_numero_filas();
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            if ($numero_actuadores > 0)
            {
                if ($numero_actuadores == 1)
                {
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_actuadores." ".$this->idiomas->_("actuador").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Este grupo tiene")." ".$numero_actuadores." ".$this->idiomas->_("actuadores").":";
                }
                $nombres_actuadores = "<ul>";
                while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
                {
                    $nombres_actuadores .= "<li>".htmlspecialchars($fila_actuador['nombre'], ENT_QUOTES)."</li>";
                }
                $nombres_actuadores .= "</ul>";
                $info .= $nombres_actuadores;
            }
            else
            {
                $info .= $this->idiomas->_("Este grupo no tiene actuadores")."<br/>";
            }

            return ($info);
		}


        function dame_duplicacion_tabla()
        {
            return (false);
        }


        //
        //  Funciones auxiliares
        //


        function dame_administracion_nodo($ids_nodos_administrables)
        {
            return (true);
        }
	}
?>
