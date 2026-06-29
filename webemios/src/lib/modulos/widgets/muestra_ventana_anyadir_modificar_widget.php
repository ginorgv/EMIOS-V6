<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/CuadriculaWidgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_administracion_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_WIDGET, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_widget = $_POST["id_widget"];
    if ($id_widget === NULL)
    {
        $id_widget = ID_NINGUNO;
    }
    $id_pestanya = $_POST["id_pestanya"];
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];
    $modulo = $_POST["modulo"];

    // Añadir o modificar widget
    $anyadir_widget = (($id_widget == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_widget == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_widget">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("widget");
    if (($anyadir_widget == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_widget($anyadir_widget, $id_widget, $id_pestanya, $modulo, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar widget
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar widget
	function rellena_contenido_ventana_anyadir_modificar_widget($anyadir_widget, $id_widget, $id_pestanya, $modulo, &$contenido)
	{
        $idiomas = new Idiomas();
        $GLOBALS["reutilizar_consultas_bases_datos"] = true;

        // Si hay que modificar el widget (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_widget != ID_NINGUNO)
		{
            $fila_widget = dame_fila_widget($id_widget);

			$nombre = $fila_widget["nombre"];
            $id_pestanya = $fila_widget["pestanya"];
            $numero_columnas = $fila_widget["numero_columnas"];
            $tipo = $fila_widget["tipo"];
            $cadena_parametros_tipo = $fila_widget["parametros_tipo"];

            // Se recuperan los parámetros de tipo de widget
            $parametros_tipo = dame_nombres_valores_parametros_tipo_widget($tipo, $cadena_parametros_tipo);
		}
        else
        {
            $nombre = "";
            $numero_columnas = 1;
            $tipo = TIPO_NINGUNO;
            $parametros_tipo = NULL;
        }

        // Se muestran las pestañas de la ventana
        $contenido = "
            <div id='tabs-administracion-widget' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='tabs-pestanyas-widgets'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-imagen' id='titulo-tab-tipo-imagen' style='display: none;'>".$idiomas->_("Imagen")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-ratio' id='titulo-tab-tipo-valor-ratio' style='display: none;'>".$idiomas->_("Valor de ratio")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-digital-sensor' id='titulo-tab-tipo-valor-digital-sensor' style='display: none;'>".$idiomas->_("Valor digital")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-digital-medio-acumulado-sensor' id='titulo-tab-tipo-valor-digital-medio-acumulado-sensor' style='display: none;'>".$idiomas->_("Valor digital medio / acumulado")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-analogico-sensor' id='titulo-tab-tipo-valor-analogico-sensor' style='display: none;'>".$idiomas->_("Valor analógico")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-analogico-medio-acumulado-sensor' id='titulo-tab-tipo-valor-analogico-medio-acumulado-sensor' style='display: none;'>".$idiomas->_("Valor analógico medio / acumulado")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-sensor' id='titulo-tab-tipo-grafica-valores-sensor' style='display: none;'>".$idiomas->_("Gráfica de valores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-mapa-calor-sensor' id='titulo-tab-tipo-mapa-calor-sensor' style='display: none;'>".$idiomas->_("Mapa de calor")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-periodos-sensor' id='titulo-tab-tipo-grafica-comparacion-periodos-sensor' style='display: none;'>".$idiomas->_("Gráfica de comparación de periodos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-evolucion-valores-comparacion-periodos-sensor' id='titulo-tab-tipo-evolucion-valores-comparacion-periodos-sensor' style='display: none;'>".$idiomas->_("Evolución de valores de comparación de periodos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-iguales-sensores-principal' id='titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-principal' style='display: none;'>".$idiomas->_("Gráfica de comparación de campos iguales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores' id='titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal' style='display: none;'>".$idiomas->_("Gráfica de comparación de campos diferentes")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-1' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-1' style='display: none;'>".$idiomas->_("Sensor")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-2' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-2' style='display: none;'>".$idiomas->_("Sensor")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-3' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-3' style='display: none;'>".$idiomas->_("Sensor")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-4' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-4' style='display: none;'>".$idiomas->_("Sensor")." 4"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-5' id='titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-5' style='display: none;'>".$idiomas->_("Sensor")." 5"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-generales-sensores-principal' id='titulo-tab-tipo-grafica-valores-generales-sensores-principal' style='display: none;'>".$idiomas->_("Gráfica de valores generales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-generales-sensores-campo-1' id='titulo-tab-tipo-grafica-valores-generales-sensores-campo-1' style='display: none;'>".$idiomas->_("Campo")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-generales-sensores-campo-2' id='titulo-tab-tipo-grafica-valores-generales-sensores-campo-2' style='display: none;'>".$idiomas->_("Campo")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-generales-sensores-campo-3' id='titulo-tab-tipo-grafica-valores-generales-sensores-campo-3' style='display: none;'>".$idiomas->_("Campo")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-valores-generales-sensores-sensores' id='titulo-tab-tipo-grafica-valores-generales-sensores-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-agregado-valores-generales-sensores-principal' id='titulo-tab-tipo-valor-agregado-valores-generales-sensores-principal' style='display: none;'>".$idiomas->_("Valor agregado de valores generales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-agregado-valores-generales-sensores-campo-1' id='titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-1' style='display: none;'>".$idiomas->_("Campo")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-agregado-valores-generales-sensores-campo-2' id='titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-2' style='display: none;'>".$idiomas->_("Campo")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-agregado-valores-generales-sensores-campo-3' id='titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-3' style='display: none;'>".$idiomas->_("Campo")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-valor-agregado-valores-generales-sensores-sensores' id='titulo-tab-tipo-valor-agregado-valores-generales-sensores-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-incrementos-totales-sensores-principal' id='titulo-tab-tipo-grafica-incrementos-totales-sensores-principal' style='display: none;'>".$idiomas->_("Gráfica de incrementos totales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-incrementos-totales-sensores-campo-1' id='titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-1' style='display: none;'>".$idiomas->_("Campo")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-incrementos-totales-sensores-campo-2' id='titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-2' style='display: none;'>".$idiomas->_("Campo")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-incrementos-totales-sensores-campo-3' id='titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-3' style='display: none;'>".$idiomas->_("Campo")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-incrementos-totales-sensores-sensores' id='titulo-tab-tipo-grafica-incrementos-totales-sensores-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-informacion-actuador' id='titulo-tab-tipo-informacion-actuador' style='display: none;'>".$idiomas->_("Información de actuador")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-informacion-grupo-actuadores' id='titulo-tab-tipo-informacion-grupo-actuadores' style='display: none;'>".$idiomas->_("Información de grupo de actuadores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grafica-consumos-costes-tramos-sensor' id='titulo-tab-tipo-grafica-consumos-costes-tramos-sensor' style='display: none;'>".$idiomas->_("Consumos y costes por tramo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-coste-factura-sensor' id='titulo-tab-tipo-coste-factura-sensor' style='display: none;'>".$idiomas->_("Factura")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-simulador-linea-base' id='titulo-tab-tipo-simulador-linea-base' style='display: none;'>".$idiomas->_("Simulación de línea base")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-informacion-proyecto' id='titulo-tab-tipo-informacion-proyecto' style='display: none;'>".$idiomas->_("Información de proyecto")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-horario-semanal-fechas' id='titulo-tab-horario-semanal-fechas' style='display: none;'>".$idiomas->_("Horario semanal y fechas")."</a></li>
                </ul>
                <div id='tabs-content-administracion-widget' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Contenido de pestaña principal
        $mostrar_identificador = false;
        if ($anyadir_widget == false)
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
                        <input type='text' id='id_widget'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$id_widget."' disabled>
                    </div>
                </div>";
        }

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                    <input type='text' id='nombre_widget'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
                </div>
            </div>";

        // No se permite cambiar la pestaña del widget si se está añadiendo
        $contenido .="
             <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Pestaña").": "."</span><br/>
                    <select id='pestanya_widget' class='select-administracion'";
        if (($anyadir_widget == true) && ($id_widget == ID_NINGUNO))
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_pestanyas_widgets_modulo($modulo, $id_pestanya);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de columnas").": "."</span><br/>
                    <input type='text' id='numero_columnas_widget'
                        class='TLNT_input_mandatory input-administracion' value='".$numero_columnas."'>
                </div>
            </div>";

        $contenido.="
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_widget' class='chosen-select-administracion'>";
        $tipos_widget_disponibles = dame_tipos_widget_disponibles($modulo);
        $contenido .= dame_lista_valores($tipos_widget_disponibles, array($tipo));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Se añaden los controles de las pestañas de cada uno de los tipos de elementos de plantillas de informes

        // Se recupera si se muestran los controles de localización
        $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();

        // Widgets "generales" (sin módulo asociado)
        anyade_controles_pestanyas_tipo_imagen(
            $anyadir_widget,
            $id_pestanya,
            $id_widget,
            $nombre,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de localizaciones (Ratios)
        anyade_controles_pestanyas_tipo_valor_ratio(
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de sensores (Información)
        anyade_controles_pestanyas_tipo_valor_digital_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_valor_digital_medio_acumulado_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_valor_analogico_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_valor_analogico_medio_acumulado_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_grafica_valores_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_mapa_calor_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de sensores (Comparación)
        anyade_controles_pestanyas_tipo_grafica_comparacion_periodos_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_evolucion_valores_comparacion_periodos_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_grafica_comparacion_campos_iguales_sensores(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_grafica_comparacion_campos_diferentes_sensores(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_grafica_valores_generales_sensores(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_valor_agregado_valores_generales_sensores(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_grafica_incrementos_totales_sensores(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de actuadores (Información)
        anyade_controles_pestanyas_tipo_informacion_actuador(
            $tipo,
            $parametros_tipo,
            $contenido);

        anyade_controles_pestanyas_tipo_informacion_grupo_actuadores(
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de SmartMeter (Consumos y costes)
        anyade_controles_pestanyas_tipo_grafica_consumos_costes_tramos_sensor(
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de SmartMeter (Facturas)
        anyade_controles_pestanyas_tipo_coste_factura_sensor(
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de proyectos (Líneas base)
        anyade_controles_pestanyas_tipo_simulador_linea_base(
            $tipo,
            $parametros_tipo,
            $contenido);

        // Widgets de proyectos (Información)
        anyade_controles_pestanyas_tipo_informacion_proyecto(
            $tipo,
            $parametros_tipo,
            $contenido);

        // Contenido de pestaña de horario semanal, exclusión e inclusión de fechas (utilizada por varios tipos de widgets)
        anyade_controles_pestanya_horario_semanal_fechas("widget", $parametros_tipo, $contenido);

        // Cierre de 'divs'
        $contenido .= "
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_widget"
                anyadir_widget="'.$anyadir_widget.'"
                id_widget="'.$id_widget.'"
                nombre="'.$nombre.'"
                id_pestanya="'.$id_pestanya.'"
                numero_columnas="'.$numero_columnas.'"
                hidden>
            </div>';

        return ("OK");
	}


    //
    // Funciones de controles de tipos de widgets
    //


    //
    // Widgets generales (sin módulo asociado)
    //


    function anyade_controles_pestanyas_tipo_imagen(
        $anyadir_widget,
        $id_pestanya,
        $id_widget,
        $nombre,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_IMAGEN)
        {
        }
        else
        {
            $altura_maxima = $parametros_tipo["altura_maxima"];
        }

        // Contenido de pestaña de tipo 'Imagen'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-imagen'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen").": "."</span><br/>
                    <input type='file' id='fichero_imagen_widget_imagen_file'>
                    <input type='text' id='fichero_imagen_widget_imagen_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_widget_imagen_seleccionar_fichero_imagen' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if ($anyadir_widget == false)
        {
            $origen = ORIGEN_IMAGEN_WIDGET_IMAGEN;
            $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $id_pestanya,
                $id_widget));
            $nombre_ventana = $nombre;
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Altura máxima")." (".$idiomas->_("píxeles").")".": "."</span><br/>
                    <input type='text' id='altura_maxima_widget_imagen'
                        class='TLNT_input_integer input-administracion' value='".$altura_maxima."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de localizaciones (Ratios)
    //


    function anyade_controles_pestanyas_tipo_valor_ratio(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_RATIO)
        {
            $id_ratio = ID_NINGUNO;
            $id_localizacion = ID_NINGUNO;
            $periodo_tiempo = PERIODO_TIEMPO_DIA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $id_localizacion = $parametros_tipo["id_localizacion"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Valor de un ratio'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-ratio'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
                    <select id='id_localizacion_widget_valor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_periodo_tiempo_widget_valor_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_valor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_valor_ratio($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_valor_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_valor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_valor_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_valor_ratio' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de sensores (Información)
    //


    function anyade_controles_pestanyas_tipo_valor_digital_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_DIGITAL_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $granularidad_sensor = GRANULARIDAD_TIEMPO_REAL;
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $granularidad_sensor = $parametros_tipo["granularidad_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Valor digital de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-digital-sensor'>";

        $id_control_id_ratio_widget_valor_digital_sensor = "control_id_ratio_widget_valor_digital_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_valor_digital_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_valor_digital_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_digital_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_valor_digital_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_VALOR_DIGITAL_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_valor_digital_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad").": "."</span><br/>
                    <select id='granularidad_sensor_widget_valor_digital_sensor' class='select-administracion'>";
        $contenido .= dame_lista_granularidades_sensor_widget($clase_sensor, $granularidad_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_valor_digital_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_valor_digital_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_VALOR_DIGITAL_SENSOR,
            $clase_sensor,
            $granularidad_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_widget_valor_digital_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_valor_digital_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_valor_digital_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_valor_digital_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_valor_digital_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_valor_digital_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_digital_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_valor_digital_medio_acumulado_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $periodo_tiempo = PERIODO_TIEMPO_DIA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Valor digital medio / acumulado de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-digital-medio-acumulado-sensor'>";

        $id_control_id_ratio_widget_valor_digital_medio_acumulado_sensor = "control_id_ratio_widget_valor_digital_medio_acumulado_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_valor_digital_medio_acumulado_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_valor_digital_medio_acumulado_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_valor_digital_medio_acumulado_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_valor_digital_medio_acumulado_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_widget_valor_digital_medio_acumulado_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_valor_digital_medio_acumulado_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_valor_digital_medio_acumulado_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_valor_digital_medio_acumulado_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_valores_medios_acumulados_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_valor_digital_medio_acumulado_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_valor_digital_medio_acumulado_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_digital_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_valor_analogico_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_ANALOGICO_SENSOR)
        {
            $tipo_grafico = TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ;
            $clase_sensor = CLASE_NINGUNA;
            $granularidad_sensor = GRANULARIDAD_TIEMPO_REAL;
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $tipo_grafico = $parametros_tipo["tipo_grafico"];
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $granularidad_sensor = $parametros_tipo["granularidad_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $valor_minimo_indicador = $parametros_tipo["valor_minimo_indicador"];
            $valor_maximo_indicador = $parametros_tipo["valor_maximo_indicador"];
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $valor_digital = $parametros_tipo["valor_digital"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Valor analógico de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-analogico-sensor'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de gráfico").": "."</span><br/>
                    <select id='tipo_grafico_widget_valor_analogico_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ, $idiomas->_("Reloj")),
                array(TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR, $idiomas->_("Circular"))),
            array($tipo_grafico));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $id_control_id_ratio_widget_valor_analogico_sensor = "control_id_ratio_widget_valor_analogico_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_valor_analogico_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_valor_analogico_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_analogico_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_valor_analogico_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_VALOR_ANALOGICO_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_valor_analogico_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

         $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad").": "."</span><br/>
                    <select id='granularidad_sensor_widget_valor_analogico_sensor' class='select-administracion'>";
        $clase_sensor_widget_valor_analogico_sensor = $clase_sensor;
        $contenido .= dame_lista_granularidades_sensor_widget($clase_sensor_widget_valor_analogico_sensor, $granularidad_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_valor_analogico_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_valor_analogico_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_VALOR_ANALOGICO_SENSOR,
            $clase_sensor_widget_valor_analogico_sensor,
            $granularidad_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_widget_valor_analogico_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_valor_analogico_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_valor_analogico_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_valor_analogico_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor mínimo de indicador").": "."</span><br/>
                    <input type='text' id='valor_minimo_indicador_widget_valor_analogico_sensor'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$valor_minimo_indicador."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor máximo de indicador").": "."</span><br/>
                    <input type='text' id='valor_maximo_indicador_widget_valor_analogico_sensor'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$valor_maximo_indicador."'>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_valor_analogico_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_valor_analogico_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor digital").": "."</span><br/>
                    <select id='valor_digital_widget_valor_analogico_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_digitales_tipo_widget_valor_analogico_sensor($clase_sensor, $valor_digital);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_analogico_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_valor_analogico_medio_acumulado_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR)
        {
            $tipo_grafico = TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ;
            $clase_sensor = CLASE_NINGUNA;
            $granularidad_sensor = GRANULARIDAD_TIEMPO_REAL;
            $periodo_tiempo = PERIODO_TIEMPO_DIA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $tipo_grafico = $parametros_tipo["tipo_grafico"];
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $granularidad_sensor = $parametros_tipo["granularidad_sensor"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $valor_minimo_indicador = $parametros_tipo["valor_minimo_indicador"];
            $valor_maximo_indicador = $parametros_tipo["valor_maximo_indicador"];
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $valor_digital = $parametros_tipo["valor_digital"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Valor analógico medio / acumulado de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-analogico-medio-acumulado-sensor'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de gráfico").": "."</span><br/>
                    <select id='tipo_grafico_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ, $idiomas->_("Reloj")),
                array(TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR, $idiomas->_("Circular"))),
            array($tipo_grafico));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $id_control_id_ratio_widget_valor_analogico_medio_acumulado_sensor = "control_id_ratio_widget_valor_analogico_medio_acumulado_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_valor_analogico_medio_acumulado_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_valor_analogico_medio_acumulado_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_valor_analogico_medio_acumulado_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_valor_analogico_medio_acumulado_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR,
            $clase_sensor,
            $granularidad_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_widget_valor_analogico_medio_acumulado_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_valor_analogico_medio_acumulado_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_valor_analogico_medio_acumulado_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_valor_analogico_medio_acumulado_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_valores_medios_acumulados_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor mínimo de indicador").": "."</span><br/>
                    <input type='text' id='valor_minimo_indicador_widget_valor_analogico_medio_acumulado_sensor'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$valor_minimo_indicador."'>
                </div>
            </div>";
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor máximo de indicador").": "."</span><br/>
                    <input type='text' id='valor_maximo_indicador_widget_valor_analogico_medio_acumulado_sensor'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$valor_maximo_indicador."'>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_valor_analogico_medio_acumulado_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_valor_analogico_medio_acumulado_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor digital").": "."</span><br/>
                    <select id='valor_digital_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_digitales_tipo_widget_valor_analogico_medio_acumulado_sensor($valor_digital);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_analogico_medio_acumulado_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_grafica_valores_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_VALORES_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $periodo_tiempo = PERIODO_TIEMPO_DIA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
        }

        // Contenido de pestaña de tipo 'Gráfica de valores de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-valores-sensor'>";

        $id_control_id_ratio_widget_grafica_valores_sensor = "control_id_ratio_widget_grafica_valores_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_grafica_valores_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_grafica_valores_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_grafica_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_VALORES_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_grafica_valores_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_campo_widget_grafica_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_grafica_valores_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_grafica_valores_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_GRAFICA_VALORES_SENSOR,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_widget_grafica_valores_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_grafica_valores_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_grafica_valores_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_grafica_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_GRAFICA_VALORES_SENSOR, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_valores_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_sensor_widget(
            TIPO_WIDGET_GRAFICA_VALORES_SENSOR,
            $clase_sensor,
            $campo,
            $periodo_tiempo,
            $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_mapa_calor_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_MAPA_CALOR_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $colores_mapa_calor = COLORES_AZUL_ROJO;
            $tipo_mapa_calor = TIPO_MAPA_CALOR_NINGUNO;
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $colores_mapa_calor = $parametros_tipo["colores_mapa_calor"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Mapa de calor de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-mapa-calor-sensor'>";

        $id_control_id_ratio_widget_mapa_calor_sensor = "control_id_ratio_widget_mapa_calor_sensor";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_widget_mapa_calor_sensor .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_widget_mapa_calor_sensor."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_MAPA_CALOR_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_mapa_calor_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_mapa_calor_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_mapa_calor_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_MAPA_CALOR_SENSOR,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_widget_mapa_calor_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_mapa_calor_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_mapa_calor_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_mapa_calor_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de mapa de calor").": "."</span><br/>
                    <select id='colores_mapa_calor_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_colores_mapa_calor($colores_mapa_calor);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_MAPA_CALOR_NINGUNO, $idiomas->_("Ninguno")),
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
            array($tipo_mapa_calor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_mapa_calor_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_mapa_calor_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_mapa_calor_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_mapa_calor_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_mapa_calor_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de sensores (Comparación)
    //


    function anyade_controles_pestanyas_tipo_grafica_comparacion_periodos_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $iniciar_comienzo_periodo_tiempo = VALOR_NO;
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
        }

        // Contenido de pestaña de tipo 'Gráfica de comparación de periodos de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-comparacion-periodos-sensor'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_grafica_comparacion_periodos_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_campo_widget_grafica_comparacion_periodos_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_grafica_comparacion_periodos_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_widget_grafica_comparacion_periodos_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_grafica_comparacion_periodos_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_grafica_comparacion_periodos_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_grafica_comparacion_periodos_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widgets_comparacion_periodos_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_sensor_widget(
            TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR,
            $clase_sensor,
            $campo,
            $periodo_tiempo,
            $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_evolucion_valores_comparacion_periodos_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR)
        {
            $clase_sensor = CLASE_NINGUNA;
            $iniciar_comienzo_periodo_tiempo = VALOR_NO;
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $intervalo_valores = INTERVALO_VALORES_HORA;
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $tipo_valores_limite_colores_fondo = $parametros_tipo["tipo_valores_limite_colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Evolución de valores de comparación de periodos de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-evolucion-valores-comparacion-periodos-sensor'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_evolucion_valores_comparacion_periodos_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_campo_widget_evolucion_valores_comparacion_periodos_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $lista_campos_sensor_widget_evolucion_valores_comparacion_periodos_sensor = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_widget_evolucion_valores_comparacion_periodos_sensor;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_evolucion_valores_comparacion_periodos_sensor' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_evolucion_valores_comparacion_periodos_sensor' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_evolucion_valores_comparacion_periodos_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widgets_comparacion_periodos_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_sensor_widget(
            TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR,
            $clase_sensor,
            $campo,
            $periodo_tiempo,
            $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_evolucion_valores_comparacion_periodos_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= "
            <div class='row-fluid' id='control_tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores límite de colores de fondo").": "."</span><br/>
					<select id='tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_NINGUNO, $idiomas->_("Ninguno")),
                array(TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_ABSOLUTO, $idiomas->_("Absoluto")),
                array(TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_PORCENTAJE, $idiomas->_("Porcentaje"))),
            array($tipo_valores_limite_colores_fondo));
		$contenido .= "
					</select>
				</div>
			</div>";
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_evolucion_valores_comparacion_periodos_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_evolucion_valores_comparacion_periodos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_grafica_comparacion_campos_iguales_sensores(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES)
        {
            $clase_sensor = CLASE_NINGUNA;
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
        }

        // Contenido de pestañas de tipo 'Gráfica de comparación de campos iguales de sensores'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-comparacion-campos-iguales-sensores-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES, $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_campo_widget_grafica_comparacion_campos_iguales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $lista_campos_sensor_widget_grafica_comparacion_campos_iguales_sensores = dame_lista_campos_sensor_widget(
            TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES,
            $clase_sensor,
            GRANULARIDAD_NINGUNA,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_widget_grafica_comparacion_campos_iguales_sensores;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_widget_grafica_comparacion_campos_iguales_sensores' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_widget_grafica_comparacion_campos_iguales_sensores' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_widget_grafica_comparacion_campos_iguales_sensores'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_sensor_widget(
            TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES,
            $clase_sensor,
            $campo,
            $periodo_tiempo,
            $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores'>";

        // Lista doble de sensores

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_widget_grafica_comparacion_campos_iguales_sensores_no_visible' hidden></div>
                    <select id='ids_sensores_widget_grafica_comparacion_campos_iguales_sensores'
                        name='ids_sensores_widget_grafica_comparacion_campos_iguales_sensores'
                        max_selected='".(MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_COMPARACION_CAMPOS_IGUALES + 1)."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores($clase_sensor, $ids_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_grafica_comparacion_campos_diferentes_sensores(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES)
        {
            $clases_sensores = array();
            $periodo_tiempo = PERIODO_TIEMPO_DIA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_HORA;
            $unificar_escalas = VALOR_SI;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensores = $parametros_tipo["clases_sensores"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $unificar_escalas = $parametros_tipo["unificar_escalas"];
        }
        for ($i = count($clases_sensores); $i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; $i++)
        {
            array_push($clases_sensores, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipos 'Gráfica de comparación de campos diferentes de sensores'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_informe_comparacion_campos_diferentes($intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Unificar escalas").": "."</span><br/>
                    <select id='unificar_escalas_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($unificar_escalas);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestañas de sensores
        for ($i = 1; $i <= NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; $i++)
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES, $clases_sensores[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                        <select id='id_sensor_".$i."_widget_grafica_comparacion_campos_diferentes_sensores' class='chosen-select-administracion'>";
            $contenido .= dame_lista_sensores(
                $clases_sensores[$i - 1],
                array($ids_sensores[$i - 1]),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_widget_grafica_comparacion_campos_diferentes_sensores' class='select-administracion'>";
            $lista_campos_sensor_bucle_widget_grafica_comparacion_campos_diferentes_sensores = dame_lista_campos_sensor_widget(
                TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES,
                $clases_sensores[$i - 1],
                GRANULARIDAD_NINGUNA,
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_bucle_widget_grafica_comparacion_campos_diferentes_sensores;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_widget_grafica_comparacion_campos_diferentes_sensores' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_widget_grafica_comparacion_campos_diferentes_sensores' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_widget_grafica_comparacion_campos_diferentes_sensores'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }
    }


    function anyade_controles_pestanyas_tipo_grafica_valores_generales_sensores(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES)
        {
            $clases_sensor = array();
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensor = $parametros_tipo["clases_sensor"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];

            // Tipo de valores (para la carga de la lista de tipos de agregación)
            $tipo_valores = dame_tipo_valores_campo_clase_sensor($clases_sensor[0], $campos[0]);
        }
        for ($i = count($clases_sensor); $i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; $i++)
        {
            array_push($clases_sensor, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipo 'Gráfica de valores generales de sensores'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-valores-generales-sensores-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_valores_generales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_valores_generales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_valores_generales_sensores' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo($clases_sensor[0], $campos[0], $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_widget_grafica_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_agregaciones($tipo_valores, TIPOS_AGREGACION_TODOS, $agregacion);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestañas de campos
        for ($i = 1; $i <= NUMERO_CLASES_SENSOR_VALORES_GENERALES; $i++)
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-valores-generales-sensores-campo-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_widget_grafica_valores_generales_sensores' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES, $clases_sensor[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_widget_grafica_valores_generales_sensores' class='select-administracion'>";
            $lista_campos_sensor_bucle_widget_grafica_valores_generales_sensores = dame_lista_campos_sensor_widget(
                TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES,
                $clases_sensor[$i - 1],
                GRANULARIDAD_NINGUNA,
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_bucle_widget_grafica_valores_generales_sensores;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_widget_grafica_valores_generales_sensores' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_widget_grafica_valores_generales_sensores' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_widget_grafica_valores_generales_sensores'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-valores-generales-sensores-sensores'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_widget_grafica_valores_generales_sensores_no_visible' hidden></div>
                    <select id='ids_sensores_widget_grafica_valores_generales_sensores'
                        name='ids_sensores_widget_grafica_valores_generales_sensores'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_VALORES_GENERALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_clases($clases_sensor, $ids_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_valor_agregado_valores_generales_sensores(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES)
        {
            $clases_sensor = array();
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_NINGUNO;
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensor = $parametros_tipo["clases_sensor"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $icono = $parametros_tipo["icono"];

            // Tipo de valores (para la carga de la lista de tipos de agregación)
            $tipo_valores = dame_tipo_valores_campo_clase_sensor($clases_sensor[0], $campos[0]);
        }
        for ($i = count($clases_sensor); $i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; $i++)
        {
            array_push($clases_sensor, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipo 'Gráfica de valores generales de sensores'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-agregado-valores-generales-sensores-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_valor_agregado_valores_generales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_valor_agregado_valores_generales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_valor_agregado_valores_generales_sensores' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo($clases_sensor[0], $campos[0], $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_agregaciones($tipo_valores, TIPOS_AGREGACION_SIN_CLASES, $agregacion);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_valor_agregado_valores_generales_sensores", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_valor_agregado_valores_generales_sensores", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // - Pestañas de campos
        for ($i = 1; $i <= NUMERO_CLASES_SENSOR_VALORES_GENERALES; $i++)
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-agregado-valores-generales-sensores-campo-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES, $clases_sensor[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_widget_valor_agregado_valores_generales_sensores' class='select-administracion'>";
            $lista_campos_sensor_bucle_widget_valor_agregado_valores_generales_sensores = dame_lista_campos_sensor_widget(
                TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES,
                $clases_sensor[$i - 1],
                GRANULARIDAD_NINGUNA,
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_bucle_widget_valor_agregado_valores_generales_sensores;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_widget_valor_agregado_valores_generales_sensores' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_widget_valor_agregado_valores_generales_sensores' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_widget_valor_agregado_valores_generales_sensores'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-valor-agregado-valores-generales-sensores-sensores'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_widget_valor_agregado_valores_generales_sensores_no_visible' hidden></div>
                    <select id='ids_sensores_widget_valor_agregado_valores_generales_sensores'
                        name='ids_sensores_widget_valor_agregado_valores_generales_sensores'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_VALORES_GENERALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_clases($clases_sensor, $ids_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_grafica_incrementos_totales_sensores(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES)
        {
            $clases_sensor = array();
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $intervalo_valores = INTERVALO_VALORES_NINGUNO;
        }
        else
        {
            $tipo_grafica = $parametros_tipo["tipo_grafica"];
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensor = $parametros_tipo["clases_sensor"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];
        }
        for ($i = count($clases_sensor); $i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; $i++)
        {
            array_push($clases_sensor, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipo 'Gráfica de incrementos totales de sensores'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-incrementos-totales-sensores-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de gráfica").": "."</span><br/>
                    <select id='tipo_grafica_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_BARRAS_VALORES, $idiomas->_("Barras de valores")),
                array(TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_TARTA_VALORES, $idiomas->_("Tarta de valores"))),
            array($tipo_grafica));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_graficas_sensor(TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_incrementos_totales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_incrementos_totales_sensores'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_incrementos_totales_sensores' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo($clases_sensor[0], $campos[0], $intervalo_valores);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
        $contenido .= dame_lista_agregaciones(TIPO_VALORES_SENSOR_INCREMENTALES, TIPOS_AGREGACION_CON_CLASES, $agregacion);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestañas de campos
        for ($i = 1; $i <= NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; $i++)
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-incrementos-totales-sensores-campo-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_widget(TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES, $clases_sensor[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_widget_grafica_incrementos_totales_sensores' class='select-administracion'>";
            $lista_campos_sensor_bucle_widget_grafica_incrementos_totales_sensores = dame_lista_campos_sensor_widget(
                TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES,
                $clases_sensor[$i - 1],
                GRANULARIDAD_NINGUNA,
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_bucle_widget_grafica_incrementos_totales_sensores;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_widget_grafica_incrementos_totales_sensores' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_widget_grafica_incrementos_totales_sensores' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_widget_grafica_incrementos_totales_sensores'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-incrementos-totales-sensores-sensores'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_widget_grafica_incrementos_totales_sensores_no_visible' hidden></div>
                    <select id='ids_sensores_widget_grafica_incrementos_totales_sensores'
                        name='ids_sensores_widget_grafica_incrementos_totales_sensores'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_INCREMENTOS_TOTALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_clases($clases_sensor, $ids_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de actuadores (Información)
    //


    function anyade_controles_pestanyas_tipo_informacion_actuador(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_INFORMACION_ACTUADOR)
        {
            $clase_actuador = CLASE_NINGUNA;
            $icono = ID_NINGUNO;
        }
        else
        {
            $clase_actuador = $parametros_tipo["clase_actuador"];
            $id_actuador = $parametros_tipo["id_actuador"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Información de actuador'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-informacion-actuador'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_widget_informacion_actuador' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador_widget($clase_actuador);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Actuador").": "."</span><br/>
                    <select id='id_actuador_widget_informacion_actuador' class='chosen-select-administracion'>";
        $contenido .= dame_lista_actuadores($clase_actuador, array($id_actuador), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_informacion_actuador' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_informacion_grupo_actuadores(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES)
        {
            $clase_actuador = CLASE_NINGUNA;
            $icono = ID_NINGUNO;
        }
        else
        {
            $clase_actuador = $parametros_tipo["clase_actuador"];
            $id_grupo_actuadores = $parametros_tipo["id_grupo_actuadores"];
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Información de grupo de actuadores'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-informacion-grupo-actuadores'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_widget_informacion_grupo_actuadores' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador_widget($clase_actuador);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de actuadores").": "."</span><br/>
                    <select id='id_grupo_actuadores_widget_informacion_grupo_actuadores' class='chosen-select-administracion'>";
        $contenido .= dame_lista_grupos_actuadores_widget($clase_actuador, $id_grupo_actuadores);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_informacion_grupo_actuadores' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de SmartMeter (Consumos y costes)
    //


    function anyade_controles_pestanyas_tipo_grafica_consumos_costes_tramos_sensor(
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR)
        {
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $valor = $parametros_tipo["valor"];
            $agrupacion_valores = $parametros_tipo["agrupacion_valores"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }

        // Contenido de pestaña de tipo 'Gráfica de consumos y costes por tramo de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grafica-consumos-costes-tramos-sensor'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_widget_grafica_consumos_costes_tramos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_grafica_consumos_costes_tramos_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores(CLASE_SENSOR_ENERGIA_ACTIVA, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor").": "."</span><br/>
                    <select id='valor_widget_grafica_consumos_costes_tramos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(VALOR_CONSUMO, $idiomas->_("Consumo")),
                array(VALOR_COSTE, $idiomas->_("Coste"))),
            array($valor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agrupación").": "."</span><br/>
                    <select id='agrupacion_valores_widget_grafica_consumos_costes_tramos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(AGRUPACION_VALORES_HORA, $idiomas->_("Hora")),
                array(AGRUPACION_VALORES_FECHA, $idiomas->_("Fecha")),
                array(AGRUPACION_VALORES_DIA_SEMANA, $idiomas->_("Día de la semana"))),
            array($agrupacion_valores));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_valores_medios_acumulados_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de SmartMeter (Facturas)
    //


    function anyade_controles_pestanyas_tipo_coste_factura_sensor(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_COSTE_FACTURA_SENSOR)
        {
            $medicion = dame_medicion_defecto();
            $periodo_tiempo = PERIODO_TIEMPO_MES;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $utilizar_colores_fondo = VALOR_NO;
            $colores_fondo = [COLOR_BLANCO, COLOR_BLANCO, COLOR_BLANCO];
            $icono = ID_NINGUNO;
        }
        else
        {
            $medicion = $parametros_tipo["medicion"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $concepto_factura = $parametros_tipo["concepto_factura"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
            $colores_fondo = $parametros_tipo["colores_fondo"];
            $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
            $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
            $icono = $parametros_tipo["icono"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestaña de tipo 'Coste de factura de un sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-coste-factura-sensor'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_widget_coste_factura_sensor' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_FACTURAS_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_widget_coste_factura_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Concepto").": "."</span><br/>
                    <select id='concepto_factura_widget_coste_factura_sensor' class='select-administracion'>";
        $contenido .= dame_lista_conceptos_factura_widget_coste_factura_sensor($medicion, $concepto_factura);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_coste_factura_sensor' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_coste_factura_sensor($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_coste_factura_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_coste_factura_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_coste_factura_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_coste_factura_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= dame_controles_colores_fondo_widget_sensor("widget_coste_factura_sensor", $utilizar_colores_fondo, $colores_fondo);
        $contenido .= dame_controles_valores_limites_colores_fondo_widget_sensor("widget_coste_factura_sensor", $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2);

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_coste_factura_sensor' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de proyectos (Líneas base)
    //


    function anyade_controles_pestanyas_tipo_simulador_linea_base(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_SIMULADOR_LINEA_BASE)
        {
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_NO;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
        }
        else
        {
            $id_linea_base = $parametros_tipo["id_linea_base"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }

        // Contenido de pestaña de tipo 'Simulador de línea base'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-simulador-linea-base'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Línea base").": "."</span><br/>
                    <select id='id_linea_base_widget_simulador_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_lineas_base($id_linea_base);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_simulador_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_simulador_linea_base($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_simulador_linea_base'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_simulador_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_simulador_linea_base'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_simulador_linea_base' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Widgets de proyectos (Información)
    //


    function anyade_controles_pestanyas_tipo_informacion_proyecto(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_WIDGET_INFORMACION_PROYECTO)
        {
            $id_proyecto = ID_NINGUNO;
            $periodo_tiempo = ID_NINGUNO;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $cadena_fecha_inicio_periodo_tiempo_local = date($_SESSION["formato_fecha_local"]);
            $icono = ID_NINGUNO;
        }
        else
        {
            $id_proyecto = $parametros_tipo["id_proyecto"];
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $icono = $parametros_tipo["icono"];
        }

        // Contenido de pestaña de tipo 'Información de proyecto'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-informacion-proyecto'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Proyecto").": "."</span><br/>
                    <select id='id_proyecto_widget_informacion_proyecto' class='chosen-select-administracion'";
        $contenido .= dame_lista_proyectos($id_proyecto);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_periodo_tiempo_widget_informacion_proyecto'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_widget_informacion_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_tipo_widget_informacion_proyecto($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_widget_informacion_proyecto'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_widget_informacion_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_widget_informacion_proyecto'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_widget_informacion_proyecto' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono").": "."</span><br/>
                    <select id='icono_widget_informacion_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_iconos_widget($icono);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }
?>


