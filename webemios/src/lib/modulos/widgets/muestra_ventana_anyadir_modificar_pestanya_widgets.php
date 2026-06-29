<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_administracion_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/CuadriculaWidgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PESTANYA_WIDGETS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_pestanya = $_POST["id_pestanya"];
    if ($id_pestanya === NULL)
    {
        $id_pestanya = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];
    $modulo = $_POST["modulo"];

    // Añadir o modificar pestaña de widgets
    $anyadir_pestanya = (($id_pestanya == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_pestanya == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_pestanya_widgets">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Flag de duplicar pestaña
    $duplicar_pestanya = (($anyadir_pestanya == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));

    // Título
    $titulo .= " ".$idiomas->_("pestaña de widgets");
    if ($duplicar_pestanya == true)
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_pestanya_widgets(
        $anyadir_pestanya,
        $duplicar_pestanya,
        $id_pestanya,
        $modulo,
        $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de añadir / modificar la cuadrícula de widgets
	//


	// Función que rellena el contenido de la ventana de añadir / modificar la pestaña de widgets
 	function rellena_contenido_ventana_anyadir_modificar_pestanya_widgets(
        $anyadir_pestanya,
        $duplicar_pestanya,
        $id_pestanya,
        $modulo,
        &$contenido)
	{
        $idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        // Si hay que modificar la pestaña de widgets (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_pestanya != ID_NINGUNO)
		{
            $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);

            $nombre = $fila_pestanya_widgets['nombre'];
            $posicion = $fila_pestanya_widgets['posicion'];
            $actualizacion_periodica_rotatoria = $fila_pestanya_widgets['actualizacion_periodica_rotatoria'];
            $numeros_columnas_filas_widgets = $fila_pestanya_widgets['numeros_columnas_filas_widgets'];
            $titulos_filas_widgets = $fila_pestanya_widgets['titulos_filas_widgets'];
            $ajustar_altura_widgets = $fila_pestanya_widgets['ajustar_altura_widgets'];

            $parametros_apariencia_pestanya = dame_nombres_valores_parametros_apariencia_pestanya_pestanya_widgets($fila_pestanya_widgets['parametros_apariencia_pestanya']);
            $parametros_apariencia_widgets = dame_nombres_valores_parametros_apariencia_widgets_pestanya_widgets($fila_pestanya_widgets['parametros_apariencia_widgets']);
            $parametros_opciones_pantalla_completa = dame_nombres_valores_parametros_opciones_pantalla_completa_pestanya_widgets($fila_pestanya_widgets['parametros_opciones_pantalla_completa']);
        }
        else
        {
            // Principal y filas de widgets
            $posicion = POSICION_PESTANYA_ULTIMA;
            $numeros_columnas_filas_widgets = NUMERO_COLUMNAS_FILAS_WIDGETS_DEFECTO;
            $ajustar_altura_widgets = VALOR_SI;
            $actualizacion_periodica_rotatoria = VALOR_NO;

            // Parámetros de apariencia de pestaña
            $parametros_apariencia_pestanya = array();
            $parametros_apariencia_pestanya["imagen_fondo"] = VALOR_NO;
            $parametros_apariencia_pestanya["nombre_imagen_fondo"] = "";
            $parametros_apariencia_pestanya["mostrar_cabecera"] = VALOR_NO;
            $parametros_apariencia_pestanya["mostrar_hora_cabecera"] = VALOR_NO;
            $parametros_apariencia_pestanya["color_hora_cabecera"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["mostrar_fecha_cabecera"] = VALOR_NO;
            $parametros_apariencia_pestanya["color_fecha_cabecera"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["mostrar_titulo_cabecera"] = VALOR_NO;
            $parametros_apariencia_pestanya["color_titulo_cabecera"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["prefijo_titulo_cabecera"] = "";
            $parametros_apariencia_pestanya["color_prefijo_titulo_cabecera"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["sufijo_titulo_cabecera"] = "";
            $parametros_apariencia_pestanya["color_sufijo_titulo_cabecera"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["numero_lineas_separacion_cabecera"] = 1;
            $parametros_apariencia_pestanya["modificar_color_titulo_filas_widgets"] = VALOR_NO;
            $parametros_apariencia_pestanya["color_titulo_filas_widgets"] = COLOR_NEGRO;
            $parametros_apariencia_pestanya["mostrar_pie"] = VALOR_NO;
            $parametros_apariencia_pestanya["numero_lineas_separacion_pie"] = 1;

            // Parámetros de apariencia de widgets
            $parametros_apariencia_widgets = array();
            $parametros_apariencia_widgets["mostrar_opciones"] = VALOR_SI;
            $parametros_apariencia_widgets["mostrar_fechas"] = VALOR_SI;
            $parametros_apariencia_widgets["mostrar_botones"] = VALOR_SI;
            $parametros_apariencia_widgets["estilo_fuente"] = ESTILO_FUENTE_NEGRITA;
            $parametros_apariencia_widgets["modificar_borde"] = VALOR_NO;
            $parametros_apariencia_widgets["mostrar_borde"] = VALOR_NO;
            $parametros_apariencia_widgets["color_borde"] = $_SESSION["colores"]["color_tema_oscuro"];
            $parametros_apariencia_widgets["modificar_colores_titulo"] = VALOR_NO;
            $parametros_apariencia_widgets["color_titulo"] = COLOR_BLANCO;
            $parametros_apariencia_widgets["color_fondo_titulo"] = $_SESSION["colores"]["color_tema_oscuro"];
            $parametros_apariencia_widgets["transparencia_fondo_titulo"] = 0;
            $parametros_apariencia_widgets["modificar_colores"] = VALOR_NO;
            $parametros_apariencia_widgets["color"] = COLOR_NEGRO;
            $parametros_apariencia_widgets["color_fondo"] = $_SESSION["colores"]["color_tema_fondo"];
            $parametros_apariencia_widgets["transparencia_fondo"] = 0;
            $parametros_apariencia_widgets["color_icono"] = COLOR_ICONO_WIDGET_NEGRO;
            $parametros_apariencia_widgets["transparencia_icono"] = 0.8;
            $parametros_apariencia_widgets["transparencia_fondo_graficas"] = 0.6;

            // Parámetros de opciones de pantalla completa
            $parametros_opciones_pantalla_completa = array();
            $parametros_opciones_pantalla_completa["modificar"] = VALOR_NO;
            $parametros_opciones_pantalla_completa["mostrar_opciones"] = VALOR_SI;
            $parametros_opciones_pantalla_completa["estilo_fuente_titulo"] = ESTILO_FUENTE_NEGRITA;
            $parametros_opciones_pantalla_completa["color"] = COLOR_BLANCO;
            $parametros_opciones_pantalla_completa["color_fondo"] = $_SESSION["colores"]["color_tema_oscuro"];
            $parametros_opciones_pantalla_completa["mostrar_pie_pagina"] = VALOR_NO;
        }

        // Se muestran las siguientes pestañas:
        // - Si se añade o modifica pestaña: Principal, filas de widgets, apariencia de pestaña, apariencia de widgets y opciones de pantalla completa
        // - Si se duplica pestaña también: Usuario
        $contenido = "
            <div id='tabs-administracion-pestanya-widgets' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='tabs-pestanyas-widgets'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-filas-widgets' id='titulo-tab-filas-widgets'>".$idiomas->_("Filas de widgets")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-apariencia-pestanya' id='titulo-tab-apariencia-pestanya'>".$idiomas->_("Apariencia de pestaña")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-apariencia-widgets' id='titulo-tab-apariencia-widgets'>".$idiomas->_("Apariencia de widgets")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-opciones-pantalla-completa' id='titulo-tab-opciones-pantalla-completa'>".$idiomas->_("Opciones de pantalla completa")."</a></li>";
        if (($duplicar_pestanya == true) && ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR))
        {
            $contenido .= "
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-usuario' id='titulo-tab-usuario'>".$idiomas->_("Usuario")."</a></li>";
        }
        $contenido .="
                </ul>
                <div id='tabs-content-administracion-pestanya-widgets' class='tab-content'>";

        // Contenido de la pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        $mostrar_identificador = false;
        if ($anyadir_pestanya == false)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    if ($_SESSION["utilizada_contrasenya_admin_superadmin"] == true)
                    {
                        $mostrar_identificador = true;
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $mostrar_identificador = true;
                    break;
                }
            }
        }
        if ($mostrar_identificador == true)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador").": "."</span><br/>
                        <input type='text' id='id_pestanya'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$id_pestanya."' disabled>
                    </div>
                </div>";
        }

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                    <input type='text' id='nombre_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($duplicar_pestanya == true)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Pestaña anterior").": "."</span><br/>
					<select id='posicion_pestanya_anterior_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_pestanyas_widgets_modulo_anteriores($modulo, $posicion);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Actualización periódica rotatoria").": "."</span><br/>
					<select id='actualizacion_periodica_rotatoria_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($actualizacion_periodica_rotatoria);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de la pestaña de filas de widgets
        $contenido .= "
                <div class='tab-pane' id='tab-filas-widgets'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Números de columnas de filas de widgets").": "."</span><br/>
					<input type='text' id='numeros_columnas_filas_widgets_pestanya_widgets'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$numeros_columnas_filas_widgets."'>
				</div>
			</div>";

        $numero_caracteres_actuales = dame_numero_caracteres($titulos_filas_widgets);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TITULO_FILA_WIDGETS;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Títulos de filas de widgets').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='titulos_filas_widgets_pestanya_widgets'
                        class='TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($titulos_filas_widgets, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Posiciones de los widgets").": "."</span><br/>
                    <div class='lista-posiciones'>
                        <select id='posicion_widgets' size='10' class='select-administracion' multiple='multiple'>";

        // Se recuperan los widgets de la pestaña y se añaden a la lista de posiciones de los widgets
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (pestanya = '".$bd_red->_($id_pestanya)."')
            ORDER BY posicion ASC";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if (($res_widgets == false))
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }
        while ($fila = $res_widgets->dame_siguiente_fila())
        {
            $id = $fila['id'];
            $nombre = $fila['nombre'];
            $posicion = $fila['posicion'];

            $contenido .= "<option id='".$id."' value='".$posicion."'>".htmlspecialchars($nombre, ENT_QUOTES)."</option>";
        }

        $contenido .= "
                        </select>
                    </div>";
        if ($anyadir_pestanya == false)
        {
            $contenido .= "
                    <div>
                        <p><button id='boton_subir_posicion_widget' class='btn-mini btn btn-success'><i class='icon-arrow-up color-blanco'></i></button></p>
                        <p><button id='boton_bajar_posicion_widget' class='btn-mini btn btn-success'><i class='icon-arrow-down color-blanco'></i></button></p>
                    </div>";
        }
        $contenido .= "
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ajustar altura de widgets").": "."</span><br/>
					<select id='ajustar_altura_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($ajustar_altura_widgets);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                </div>";

        // Contenido de la pestaña de apariencia de pestaña
        $contenido .= "
                <div class='tab-pane' id='tab-apariencia-pestanya'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Imagen de fondo").": "."</span><br/>
					<select id='imagen_fondo_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["imagen_fondo"]);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de imagen de fondo").": "."</span><br/>
					<input type='text' id='nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($parametros_apariencia_pestanya["nombre_imagen_fondo"], ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen de fondo").": "."</span><br/>
                    <input type='file' id='fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file'>
                    <input type='text' id='fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_pestanya_widgets_seleccionar_fichero_imagen_fondo_apariencia_pestanya' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_pestanya == false) && ($parametros_apariencia_pestanya["imagen_fondo"] == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO;
            $id_origen = $id_pestanya;
            $nombre_ventana = $nombre;
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar cabecera").": "."</span><br/>
					<select id='mostrar_cabecera_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["mostrar_cabecera"]);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar hora en cabecera").": "."</span><br/>
					<select id='mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["mostrar_hora_cabecera"]);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_color_hora_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de hora en cabecera").": "."</span><br/>
                    <input type='color' id='color_hora_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_hora_cabecera"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar fecha en cabecera").": "."</span><br/>
					<select id='mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["mostrar_fecha_cabecera"]);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_color_fecha_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de fecha en cabecera").": "."</span><br/>
                    <input type='color' id='color_fecha_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_fecha_cabecera"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar título en cabecera").": "."</span><br/>
					<select id='mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["mostrar_titulo_cabecera"]);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'id='control_color_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de título en cabecera").": "."</span><br/>
                    <input type='color' id='color_titulo_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_titulo_cabecera"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Prefijo de título en cabecera").": "."</span><br/>
					<input type='text' id='prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($parametros_apariencia_pestanya["prefijo_titulo_cabecera"], ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de prefijo de título en cabecera").": "."</span><br/>
                    <input type='color' id='color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_prefijo_titulo_cabecera"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sufijo de título en cabecera").": "."</span><br/>
					<input type='text' id='sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($parametros_apariencia_pestanya["sufijo_titulo_cabecera"], ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de sufijo de título en cabecera").": "."</span><br/>
                    <input type='color' id='color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_sufijo_titulo_cabecera"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_numero_lineas_separacion_cabecera_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de líneas de separación de cabecera").": "."</span><br/>
					<input type='text' id='numero_lineas_separacion_cabecera_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".htmlspecialchars($parametros_apariencia_pestanya["numero_lineas_separacion_cabecera"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar color de título de filas de widgets").": "."</span><br/>
                    <select id='modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["modificar_color_titulo_filas_widgets"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='control_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de título de filas de widgets").": "."</span><br/>
                    <input type='color' id='color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_pestanya["color_titulo_filas_widgets"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_pie_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar pie").": "."</span><br/>
					<select id='mostrar_pie_apariencia_pestanya_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_pestanya["mostrar_pie"]);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_numero_lineas_separacion_pie_apariencia_pestanya_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de líneas de separación de pie").": "."</span><br/>
					<input type='text' id='numero_lineas_separacion_pie_apariencia_pestanya_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".htmlspecialchars($parametros_apariencia_pestanya["numero_lineas_separacion_pie"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
                </div>";

        // Contenido de la pestaña de apariencia de widgets
        $contenido .= "
                <div class='tab-pane' id='tab-apariencia-widgets'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar opciones").": "."</span><br/>
                    <select id='mostrar_opciones_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["mostrar_opciones"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar fechas").": "."</span><br/>
                    <select id='mostrar_fechas_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["mostrar_fechas"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar botones").": "."</span><br/>
                    <select id='mostrar_botones_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["mostrar_botones"]);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Estilo de fuente").": "."</span><br/>
					<select id='estilo_fuente_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(ESTILO_FUENTE_NORMAL, $idiomas->_("Normal")),
                array(ESTILO_FUENTE_NEGRITA, $idiomas->_("Negrita"))),
            array($parametros_apariencia_widgets["estilo_fuente"]));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar borde").": "."</span><br/>
                    <select id='modificar_borde_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["modificar_borde"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='control_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar borde").": "."</span><br/>
                    <select id='mostrar_borde_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["mostrar_borde"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='control_color_borde_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de borde").": "."</span><br/>
                    <input type='color' id='color_borde_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_widgets["color_borde"]."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar colores de título").": "."</span><br/>
                    <select id='modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["modificar_colores_titulo"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='control_color_titulo_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de título").": "."</span><br/>
                    <input type='color' id='color_titulo_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_widgets["color_titulo"]."'>
				</div>
			</div>

            <div class='row-fluid' id='control_color_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de fondo de título").": "."</span><br/>
                    <input type='color' id='color_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_widgets["color_fondo_titulo"]."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Transparencia de fondo de título").": "."</span><br/>
					<input type='text' id='transparencia_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".htmlspecialchars($parametros_apariencia_widgets["transparencia_fondo_titulo"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar colores").": "."</span><br/>
                    <select id='modificar_colores_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_apariencia_widgets["modificar_colores"]);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='control_color_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color").": "."</span><br/>
                    <input type='color' id='color_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_widgets["color"]."'>
				</div>
			</div>

            <div class='row-fluid' id='control_color_fondo_widgets_apariencia_widgets_pestanya_widgets'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de fondo").": "."</span><br/>
                    <input type='color' id='color_fondo_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_apariencia_widgets["color_fondo"]."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Transparencia de fondo").": "."</span><br/>
					<input type='text' id='transparencia_fondo_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".htmlspecialchars($parametros_apariencia_widgets["transparencia_fondo"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de icono").": "."</span><br/>
					<select id='color_icono_widgets_apariencia_widgets_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(COLOR_ICONO_WIDGET_BLANCO, $idiomas->_("Blanco")),
                array(COLOR_ICONO_WIDGET_NEGRO, $idiomas->_("Negro"))),
            array($parametros_apariencia_widgets["color_icono"]));
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Transparencia de icono").": "."</span><br/>
					<input type='text' id='transparencia_icono_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".htmlspecialchars($parametros_apariencia_widgets["transparencia_icono"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Transparencia de fondo de gráficas").": "."</span><br/>
					<input type='text' id='transparencia_fondo_graficas_widgets_apariencia_widgets_pestanya_widgets'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".htmlspecialchars($parametros_apariencia_widgets["transparencia_fondo_graficas"], ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
                </div>";

        // Contenido de la pestaña de opciones de pantalla completa (mismas opciones para todas las pestañas)
        $contenido .= "
                <div class='tab-pane' id='tab-opciones-pantalla-completa'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar").": "."</span><br/>
                    <select id='modificar_opciones_pantalla_completa_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_opciones_pantalla_completa["modificar"]);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_opciones_opciones_pantalla_completa_pestanya_widgets'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar opciones").": "."</span><br/>
                    <select id='mostrar_opciones_opciones_pantalla_completa_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_opciones_pantalla_completa["mostrar_opciones"]);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_estilo_fuente_titulo_opciones_pantalla_completa_pestanya_widgets'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Estilo de fuente de título").": "."</span><br/>
                    <select id='estilo_fuente_titulo_opciones_pantalla_completa_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(ESTILO_FUENTE_NORMAL, $idiomas->_("Normal")),
                array(ESTILO_FUENTE_NEGRITA, $idiomas->_("Negrita"))),
            array($parametros_opciones_pantalla_completa["estilo_fuente_titulo"]));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_color_opciones_pantalla_completa_pestanya_widgets'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color").": "."</span><br/>
                    <input type='color' id='color_opciones_pantalla_completa_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_opciones_pantalla_completa["color"]."'>
                </div>
            </div>

            <div class='row-fluid' id='control_color_fondo_opciones_pantalla_completa_pestanya_widgets'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Color de fondo").": "."</span><br/>
                    <input type='color' id='color_fondo_opciones_pantalla_completa_pestanya_widgets'
                        class='TLNT_input_hex_color input-administracion selector-color-administracion' value='".$parametros_opciones_pantalla_completa["color_fondo"]."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_mostrar_pie_pagina_opciones_pantalla_completa_pestanya_widgets'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar pie de página").": "."</span><br/>
                    <select id='mostrar_pie_pagina_opciones_pantalla_completa_pestanya_widgets' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($parametros_opciones_pantalla_completa["mostrar_pie_pagina"]);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                </div>";

         // Contenido de la pestaña de usuario (sólo si es un duplicado)
        if (($duplicar_pestanya == true) && ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR))
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-usuario'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Usuario").": "."</span><br/>
                        <select id='usuario_pestanya_widgets' class='select-administracion'>";
            $contenido .= dame_lista_usuarios(PERFIL_USUARIO_ESTANDAR, OPCIONES_EXTRA_LISTA_USUARIOS_ACTUAL);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_pestanya_widgets"
                anyadir_pestanya="'.$anyadir_pestanya.'"
                id_pestanya="'.$id_pestanya.'"
                perfil_usuario_actual="'.$_SESSION["perfil"].'"
                imagen_fondo_apariencia_pestanya="'.$parametros_apariencia_pestanya["imagen_fondo"].'"
                hidden>
            </div>';

        return ("OK");
	}
?>