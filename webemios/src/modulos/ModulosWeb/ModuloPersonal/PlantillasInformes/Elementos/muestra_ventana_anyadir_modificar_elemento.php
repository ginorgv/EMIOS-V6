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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_administracion_elementos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_ELEMENTO_PLANTILLA_INFORME, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $id_elemento = $_POST["id_elemento"];
    if ($id_elemento === NULL)
    {
        $id_elemento = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar elemento
    $anyadir_elemento = (($id_elemento == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_elemento == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_personal_anyadir_modificar_elemento_plantilla_informe">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("elemento");
    if (($anyadir_elemento == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_elemento($anyadir_elemento, $id_plantilla_informe, $id_elemento, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar elemento
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar elemento
	function rellena_contenido_ventana_anyadir_modificar_elemento($anyadir_elemento, $id_plantilla_informe, $id_elemento, &$contenido)
	{
		$idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();
        $GLOBALS["reutilizar_consultas_bases_datos"] = true;

        // Si hay que modificar el elemento (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_elemento != ID_NINGUNO)
		{
            $consulta_elemento = "
				SELECT *
				FROM elementos_plantillas_informes
				WHERE
					id = '".$bd_red->_($id_elemento)."'";
			$res_elemento = $bd_red->ejecuta_consulta($consulta_elemento);
			if (($res_elemento == false) || ($res_elemento->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_elemento."'");
            }
			$fila_elemento = $res_elemento->dame_siguiente_fila();

            $nombre = $fila_elemento["nombre"];
            $tipo = $fila_elemento["tipo"];
            $cadena_parametros_tipo = $fila_elemento["parametros_tipo"];
            $cadena_parametros_tipo_json = $fila_elemento["parametros_tipo_json"];
            $elementos_informe = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_elemento["elementos_informe"]);
            $modo_visibilidad = $fila_elemento["modo_visibilidad"];
            $parametros_requeridos = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_elemento["parametros_requeridos"]);

            // Se recuperan los parámetros de tipo de elemento
            $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                $tipo,
                $cadena_parametros_tipo,
                $cadena_parametros_tipo_json);
        }
        else
        {
            $nombre = "";
            $tipo = TIPO_NINGUNO;

            $parametros_tipo = NULL;
            $elementos_informe = array();
            $modo_visibilidad = MODO_VISIBILIDAD_ELEMENTO_TODOS_PARAMETROS;
            $parametros_requeridos = array();
        }

        // Fila de plantilla de informe
        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $tipo_plantilla_informe = $fila_plantilla_informe["tipo"];
        $tipo_seleccion_horario_semanal_fechas_plantilla_informe = $fila_plantilla_informe["tipo_seleccion_horario_semanal_fechas"];

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
        $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);

        // Ids de pestañas de elementos de varios módulos (para que se muestren o no dependiende de si el usuario tiene esos módulos)
        $id_pestanya_comentarios_sensores = "titulo-tab-tipo-comentarios-sensores";
        $id_pestanya_comentarios_actuadores = "titulo-tab-tipo-comentarios-actuadores";
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $id_pestanya_comentarios_sensores = "titulo-tab-tipo-comentarios-sensores-oculta";
        }
        if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
        {
            $id_pestanya_comentarios_actuadores = "titulo-tab-tipo-comentarios-actuadores-oculta";
        }

        // Se muestran las pestañas
        $contenido = "
            <div id='tabs-administracion-elementos-plantillas-informes' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya  elemento-oculto' href='#tab-tipo-portada' id='titulo-tab-tipo-portada' style='display: none;'>".$idiomas->_("Portada")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-titulo' id='titulo-tab-tipo-titulo' style='display: none;'>".$idiomas->_("Título")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-texto' id='titulo-tab-tipo-texto' style='display: none;'>".$idiomas->_("Texto")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-notas' id='titulo-tab-tipo-notas' style='display: none;'>".$idiomas->_("Notas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-imagen' id='titulo-tab-tipo-imagen' style='display: none;'>".$idiomas->_("Imagen")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-comentarios-principal' id='titulo-tab-tipo-comentarios-principal' style='display: none;'>".$idiomas->_("Comentarios")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-comentarios-sensores' id='".$id_pestanya_comentarios_sensores."' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-comentarios-actuadores' id='".$id_pestanya_comentarios_actuadores."' style='display: none;'>".$idiomas->_("Actuadores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-activaciones-eventos-principal' id='titulo-tab-tipo-sensores-activaciones-eventos-principal' style='display: none;'>".$idiomas->_("Activaciones de eventos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-activaciones-eventos-eventos' id='titulo-tab-tipo-sensores-activaciones-eventos-eventos' style='display: none;'>".$idiomas->_("Eventos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-informacion' id='titulo-tab-tipo-sensores-informacion' style='display: none;'>".$idiomas->_("Información de sensor")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-horario' id='titulo-tab-tipo-sensores-analisis-horario' style='display: none;'>".$idiomas->_("Análisis horario")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-diario' id='titulo-tab-tipo-sensores-analisis-diario' style='display: none;'>".$idiomas->_("Análisis diario")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-comportamiento-principal' id='titulo-tab-tipo-sensores-analisis-comportamiento-principal' style='display: none;'>".$idiomas->_("Análisis de comportamiento")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-comportamiento-sensores' id='titulo-tab-tipo-sensores-analisis-comportamiento-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-periodos' id='titulo-tab-tipo-sensores-comparacion-periodos' style='display: none;'>".$idiomas->_("Comparación de periodos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-perfil-horario-principal' id='titulo-tab-tipo-sensores-comparacion-perfil-horario-principal' style='display: none;'>".$idiomas->_("Comparación con perfil horario")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-perfil-horario-perfil-horario' id='titulo-tab-tipo-sensores-comparacion-perfil-horario-perfil-horario' style='display: none;'>".$idiomas->_("Perfil horario")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-iguales-principal' id='titulo-tab-tipo-sensores-comparacion-campos-iguales-principal' style='display: none;'>".$idiomas->_("Comparación de campos iguales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios' id='titulo-tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios' style='display: none;'>".$idiomas->_("Sensores secundarios")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-principal' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-principal' style='display: none;'>".$idiomas->_("Comparación de campos diferentes")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-sensor-1' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-1' style='display: none;'>".$idiomas->_("Sensor")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-sensor-2' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-2' style='display: none;'>".$idiomas->_("Sensor")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-sensor-3' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-3' style='display: none;'>".$idiomas->_("Sensor")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-sensor-4' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-4' style='display: none;'>".$idiomas->_("Sensor")." 4"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-comparacion-campos-diferentes-sensor-5' id='titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-5' style='display: none;'>".$idiomas->_("Sensor")." 5"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-comparativo-principal' id='titulo-tab-tipo-sensores-analisis-comparativo-principal' style='display: none;'>".$idiomas->_("Análisis comparativo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-analisis-comparativo-sensores' id='titulo-tab-tipo-sensores-analisis-comparativo-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-valores-generales-principal' id='titulo-tab-tipo-sensores-valores-generales-principal' style='display: none;'>".$idiomas->_("Valores generales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-valores-generales-campo-1' id='titulo-tab-tipo-sensores-valores-generales-campo-1' style='display: none;'>".$idiomas->_("Campo")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-valores-generales-campo-2' id='titulo-tab-tipo-sensores-valores-generales-campo-2' style='display: none;'>".$idiomas->_("Campo")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-valores-generales-campo-3' id='titulo-tab-tipo-sensores-valores-generales-campo-3' style='display: none;'>".$idiomas->_("Campo")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-valores-generales-sensores' id='titulo-tab-tipo-sensores-valores-generales-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-incrementos-totales-principal' id='titulo-tab-tipo-sensores-incrementos-totales-principal' style='display: none;'>".$idiomas->_("Incrementos totales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-incrementos-totales-campo-1' id='titulo-tab-tipo-sensores-incrementos-totales-campo-1' style='display: none;'>".$idiomas->_("Campo")." 1"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-incrementos-totales-campo-2' id='titulo-tab-tipo-sensores-incrementos-totales-campo-2' style='display: none;'>".$idiomas->_("Campo")." 2"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-incrementos-totales-campo-3' id='titulo-tab-tipo-sensores-incrementos-totales-campo-3' style='display: none;'>".$idiomas->_("Campo")." 3"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-incrementos-totales-sensores' id='titulo-tab-tipo-sensores-incrementos-totales-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-histograma' id='titulo-tab-tipo-sensores-histograma' style='display: none;'>".$idiomas->_("Histograma")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-principal' id='titulo-tab-tipo-sensores-correlacion-principal' style='display: none;'>".$idiomas->_("Correlación")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-sensor-independiente-1' id='titulo-tab-tipo-sensores-correlacion-sensor-independiente-1' style='display: none;'>".$idiomas->_("Sensor independiente 1")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-sensor-independiente-2' id='titulo-tab-tipo-sensores-correlacion-sensor-independiente-2' style='display: none;'>".$idiomas->_("Sensor independiente 2")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-sensor-independiente-3' id='titulo-tab-tipo-sensores-correlacion-sensor-independiente-3' style='display: none;'>".$idiomas->_("Sensor independiente 3")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-sensor-independiente-4' id='titulo-tab-tipo-sensores-correlacion-sensor-independiente-4' style='display: none;'>".$idiomas->_("Sensor independiente 4")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensores-correlacion-sensor-dependiente' id='titulo-tab-tipo-sensores-correlacion-sensor-dependiente' style='display: none;'>".$idiomas->_("Sensor dependiente")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-actuadores-informacion-acciones-enviadas-principal' id='titulo-tab-tipo-actuadores-informacion-acciones-enviadas-principal' style='display: none;'>".$idiomas->_("Información de acciones enviadas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-actuadores-informacion-acciones-enviadas-sensor' id='titulo-tab-tipo-actuadores-informacion-acciones-enviadas-sensor' style='display: none;'>".$idiomas->_("Sensor")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-consumos-costes-generales-principal' id='titulo-tab-tipo-smartmeter-consumos-costes-generales-principal' style='display: none;'>".$idiomas->_("Consumos y costes generales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-consumos-costes-generales-sensores' id='titulo-tab-tipo-smartmeter-consumos-costes-generales-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-consumos-costes-totales-principal' id='titulo-tab-tipo-smartmeter-consumos-costes-totales-principal' style='display: none;'>".$idiomas->_("Consumos y costes totales")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-consumos-costes-totales-sensores' id='titulo-tab-tipo-smartmeter-consumos-costes-totales-sensores' style='display: none;'>".$idiomas->_("Sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-comparacion-periodos' id='titulo-tab-tipo-smartmeter-comparacion-periodos' style='display: none;'>".$idiomas->_("Comparación de periodos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-simulador-tarifas-principal' id='titulo-tab-tipo-smartmeter-simulador-tarifas-principal' style='display: none;'>".$idiomas->_("Simulador de tarifas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-simulador-tarifas-tarifas' id='titulo-tab-tipo-smartmeter-simulador-tarifas-tarifas' style='display: none;'>".$idiomas->_("Tarifas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-consumos-costes-tramos-electricidad' id='titulo-tab-tipo-smartmeter-consumos-costes-tramos-electricidad' style='display: none;'>".$idiomas->_("Consumos y costes por tramo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-cortes-tension-electricidad' id='titulo-tab-tipo-smartmeter-cortes-tension-electricidad' style='display: none;'>".$idiomas->_("Cortes de tensión")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-excesos-potencia-electricidad' id='titulo-tab-tipo-smartmeter-excesos-potencia-electricidad' style='display: none;'>".$idiomas->_("Excesos de potencia")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-excesos-energia-reactiva-electricidad' id='titulo-tab-tipo-smartmeter-excesos-energia-reactiva-electricidad' style='display: none;'>".$idiomas->_("Excesos de energía reactiva")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-excesos-caudal-gas' id='titulo-tab-tipo-smartmeter-excesos-caudal-gas' style='display: none;'>".$idiomas->_("Excesos de caudal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-desvios-compra-energia' id='titulo-tab-tipo-smartmeter-desvios-compra-energia' style='display: none;'>".$idiomas->_("Desvíos de compra de energía")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-desvios-ponderados-compra-energia' id='titulo-tab-tipo-smartmeter-desvios-ponderados-compra-energia' style='display: none;'>".$idiomas->_("Desvíos ponderados de compra de energía")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-simulador-factura-principal' id='titulo-tab-tipo-smartmeter-simulador-factura-principal' style='display: none;'>".$idiomas->_("Simulador de factura")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-simulador-factura-reparto-costes' id='titulo-tab-tipo-smartmeter-simulador-factura-reparto-costes' style='display: none;'>".$idiomas->_("Reparto de costes")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-smartmeter-instalacion' id='titulo-tab-tipo-smartmeter-instalacion' style='display: none;'>".$idiomas->_("Instalación")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-proyectos-simulador-linea-base' id='titulo-tab-tipo-proyectos-simulador-linea-base' style='display: none;'>".$idiomas->_("Simulador de línea base")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-proyectos-informacion-proyecto' id='titulo-tab-tipo-proyectos-informacion-proyecto' style='display: none;'>".$idiomas->_("Información de proyecto")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-periodo-tiempo' id='titulo-tab-periodo-tiempo' style='display: none;'>".$idiomas->_("Periodo de tiempo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-duracion-separacion-periodos' id='titulo-tab-duracion-separacion-periodos' style='display: none;'>".$idiomas->_("Duración y separación de periodos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-horario-semanal-fechas' id='titulo-tab-horario-semanal-fechas";
        if ($tipo_seleccion_horario_semanal_fechas_plantilla_informe == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            $contenido .= "-oculto' style='display: none;'";
        }
        $contenido .= "'>".$idiomas->_("Horario semanal y fechas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-elementos-informe' id='titulo-tab-elementos-informe' style='display: none;'>".$idiomas->_("Elementos del informe")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-parametros-requeridos'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " style='display: none;'";
        }
        $contenido .= ">".$idiomas->_("Parámetros requeridos")."</a></li>
                </ul>
                <div id='tabs-content-administracion-elemento-plantillas-informes' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Contenido de pestaña principal
        $mostrar_identificador = false;
        if ($anyadir_elemento == false)
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
                        <input type='text' id='id_elemento_plantilla_informe'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$id_elemento."' disabled>
                    </div>
                </div>";
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_elemento_plantilla_informe'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_elemento_plantilla_informe' class='chosen-select-administracion'>";
        $tipos_elemento_disponibles = dame_tipos_elemento_plantillas_informes_disponibles();
        $contenido .= dame_lista_valores($tipos_elemento_disponibles, array($tipo));
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='id_plantilla_informe_destino_elementos_plantilla_informe'";
        if (($anyadir_elemento == false) || ($id_elemento == ID_NINGUNO))
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Plantilla de informe destino").": "."</span><br/>
                    <select id='id_plantilla_informe_destino_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_plantillas_informes(ID_NINGUNO, OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_ACTUAL);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Se añaden los controles de las pestañas de cada uno de los tipos de elementos de plantillas de informes

        // Se recupera si se muestran los controles de localización
        $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();

        // Portada y título
        anyade_controles_pestanyas_tipo_portada(
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_titulo(
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_texto(
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_notas(
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_imagen(
            $anyadir_elemento,
            $id_plantilla_informe,
            $id_elemento,
            $nombre,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de varios módulos
        anyade_controles_pestanyas_tipo_comentarios(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de sensores (Eventos)
        anyade_controles_pestanyas_tipo_sensores_activaciones_eventos(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de sensores (Información)
        anyade_controles_pestanyas_tipo_sensores_informacion(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de sensores (Análisis)
        anyade_controles_pestanyas_tipo_sensores_analisis_horario(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_analisis_diario(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_analisis_comportamiento(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de sensores (Comparación)
        anyade_controles_pestanyas_tipo_sensores_comparacion_periodos(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_comparacion_perfil_horario(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_comparacion_campos_iguales(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_comparacion_campos_diferentes(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_analisis_comparativo(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_valores_generales(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_incrementos_totales(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de sensores (Estadística)
        anyade_controles_pestanyas_tipo_sensores_histograma(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_sensores_correlacion(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de actuadores (Información)
        anyade_controles_pestanyas_tipo_actuadores_informacion_acciones_enviadas(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de SmartMeter (Consumos y costes)
        anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_generales(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_totales(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_comparacion_periodos(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_simulador_tarifas(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_tramos_electricidad(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $mostrar_controles_localizaciones,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_cortes_tension_electricidad(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_excesos_potencia_electricidad(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_excesos_energia_reactiva_electricidad(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_excesos_caudal_gas(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de SmartMeter (Compra de energía)
        anyade_controles_pestanyas_tipo_smartmeter_desvios_compra_energia(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);
        anyade_controles_pestanyas_tipo_smartmeter_desvios_ponderados_compra_energia(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de SmartMeter (Facturas)
        anyade_controles_pestanyas_tipo_smartmeter_simulador_factura(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de SmartMeter (Tarifas)
        anyade_controles_pestanyas_tipo_smartmeter_instalacion(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de proyectos (Líneas base)
        anyade_controles_pestanyas_tipo_proyectos_simulador_linea_base(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Elementos de proyectos (Información)
        anyade_controles_pestanyas_tipo_proyectos_informacion_proyecto(
            $id_plantilla_informe,
            $tipo_plantilla_informe,
            $tipo,
            $parametros_tipo,
            $contenido);

        // Contenido de pestaña de periodo de tiempo (utilizada por tipos de elementos con fechas inicial y final)
        anyade_controles_pestanya_periodo_tiempo($tipo, $parametros_tipo, $contenido);

        // Contenido de pestaña de duración y separación de periodos (utilizada por tipos de elementos de comparación de periodos)
        anyade_controles_pestanya_duracion_separacion_periodos($tipo, $parametros_tipo, $contenido);

        // Contenido de pestaña de horario semanal, exclusión e inclusión de fechas (utilizada por varios tipos de elementos)
        anyade_controles_pestanya_horario_semanal_fechas("elemento_plantilla_informe", $parametros_tipo, $contenido);

        // Contenido de pestaña de tipo 'Elementos de informe' (utilizada por varios tipos de elementos)
        $contenido .= "
                    <div class='tab-pane' id='tab-elementos-informe'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Elementos").": "."</span><br/>
                    <div id='select_elementos_informe_elemento_plantilla_informe_no_visible' hidden></div>
                    <select id='elementos_informe_elemento_plantilla_informe'
                        name='elementos_informe_elemento_plantilla_informe'
                        max_selected='".ID_NINGUNO."' multiple='multiple'
                        class='select-administracion' hidden>";
        $parametros_informe = array();
        switch ($tipo)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $parametros_informe["clase_sensor"] = $parametros_tipo["clase_sensor"];
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $parametros_informe["medicion"] = $parametros_tipo["medicion"];
                break;
            }
        }
        $contenido .= dame_lista_elementos_informe_elemento_plantilla_informe($tipo, $parametros_informe, $elementos_informe);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de tipo 'Parámetros requeridos' (utilizada por todos los tipos de elementos en plantillas de informe 'configurables')
        $contenido .= "
                    <div class='tab-pane' id='tab-parametros-requeridos'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modo de visibilidad").": "."</span><br/>
					<select id='modo_visibilidad_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_modos_visibilidad_elemento_plantilla_informe($modo_visibilidad);
        $contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Parámetros").": "."</span><br/>
                    <div id='select_parametros_requeridos_elemento_plantilla_informe_no_visible' hidden></div>
                    <select id='parametros_requeridos_elemento_plantilla_informe'
                        name='parametros_requeridos_elemento_plantilla_informe'
                        max_selected='".ID_NINGUNO."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_parametros_plantilla_informe($id_plantilla_informe, $parametros_requeridos);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_elemento_plantilla_informe"
                anyadir_elemento="'.$anyadir_elemento.'"
                id_plantilla_informe="'.$id_plantilla_informe.'"
                id_elemento="'.$id_elemento.'"
                tipo_elemento="'.$tipo.'"
                hidden>
            </div>';

        return ("OK");
	}


    //
    // Funciones de controles de tipos de elementos de plantillas de informes
    //


    //
    // Elementos generales
    //


    function anyade_controles_pestanyas_tipo_portada(
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA)
        {
        }
        else
        {
            $titulo = $parametros_tipo["titulo"];
            $subtitulo = $parametros_tipo["subtitulo"];
        }

        // Contenido de pestaña de tipo 'Portada'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-portada'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Título").": "."</span><br/>
                    <input type='text' id='titulo_elemento_plantilla_informe_portada'
                        class='TLNT_input_mandatory input-administracion' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Subtítulo").": "."</span><br/>
                    <input type='text' id='subtitulo_elemento_plantilla_informe_portada'
                        class='input-administracion' value='".htmlspecialchars($subtitulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_titulo(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO)
        {
        }
        else
        {
            $titulo = $parametros_tipo["titulo"];
        }

        // Contenido de pestaña de tipo 'Título'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-titulo'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Título").": "."</span><br/>
                    <input type='text' id='titulo_elemento_plantilla_informe_titulo'
                        class='TLNT_input_mandatory input-administracion' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_texto(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO)
        {
        }
        else
        {
            $titulo = $parametros_tipo["titulo"];
            $texto = $parametros_tipo["texto"];
        }

        // Contenido de pestaña de tipo 'Texto'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-texto'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Título").": "."</span><br/>
                    <input type='text' id='titulo_elemento_plantilla_informe_texto'
                        class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $numero_caracteres_actuales = dame_numero_caracteres($texto);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TEXTO;
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Texto').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='texto_elemento_plantilla_informe_texto'
						class='TLNT_input_mandatory input-administracion' rows='5'>".htmlspecialchars($texto, ENT_QUOTES)."</textarea>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_notas(
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS)
        {
        }
        else
        {
            $titulo = $parametros_tipo["titulo"];
        }

        // Contenido de pestaña de tipo 'Notas'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-notas'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Título").": "."</span><br/>
                    <input type='text' id='titulo_elemento_plantilla_informe_notas'
                        class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_imagen(
        $anyadir_elemento,
        $id_plantilla_informe,
        $id_elemento,
        $nombre,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN)
        {
        }
        else
        {
            $titulo = $parametros_tipo["titulo"];
            $nombre_imagen = $parametros_tipo["nombre_imagen"];
            $altura_maxima = $parametros_tipo["altura_maxima"];
        }

        // Contenido de pestaña de tipo 'Imagen'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-imagen'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Título").": "."</span><br/>
                    <input type='text' id='titulo_elemento_plantilla_informe_imagen'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de imagen").": "."</span><br/>
                    <input type='text' id='nombre_imagen_elemento_plantilla_informe_imagen'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_imagen, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen").": "."</span><br/>
                    <input type='file' id='fichero_imagen_elemento_plantilla_informe_imagen_file'>
                    <input type='text' id='fichero_imagen_elemento_plantilla_informe_imagen_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_elemento_plantilla_informe_imagen_seleccionar_fichero_imagen' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_elemento == false) && ($tipo == TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN))
        {
            $origen = ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN;
            $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $id_plantilla_informe,
                $id_elemento));
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
                    <input type='text' id='altura_maxima_elemento_plantilla_informe_imagen'
                        class='TLNT_input_integer input-administracion' value='".$altura_maxima."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de varios módulos
    //


    function anyade_controles_pestanyas_tipo_comentarios(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS)
        {
            $visibilidad_comentarios = VISIBILIDAD_PUBLICA;
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $clase_actuador = CLASE_NINGUNA;
            $tipo_seleccion_actuadores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $tipo_seleccion_grupos_actuadores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $visibilidad_comentarios = $parametros_tipo["visibilidad_comentarios"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $clase_actuador = $parametros_tipo["clase_actuador"];
            $tipo_seleccion_actuadores = $parametros_tipo["tipo_seleccion_actuadores"];
            $ids_actuadores = $parametros_tipo["ids_actuadores"];
            $tipo_seleccion_grupos_actuadores = $parametros_tipo["tipo_seleccion_grupos_actuadores"];
            $ids_grupos_actuadores = $parametros_tipo["ids_grupos_actuadores"];
        }

        // Contenido de pestañas de tipo 'Comentarios'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-comentarios-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Visibilidad de comentarios").": "."</span><br/>
                    <select id='visibilidad_comentarios_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_visibilidades_comentario($visibilidad_comentarios, OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_TODAS);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-comentarios-sensores'>";

        // Clase de sensores
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Tipo de selección de sensores
        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_comentarios_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_comentarios'
                        name='ids_sensores_elemento_plantilla_informe_comentarios'
                        multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clase_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de actuadores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-comentarios-actuadores'>";

        // Clase de actuadores
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_actuador_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de actuadores").": "."</span><br/>
                    <select id='tipo_seleccion_actuadores_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_actuadores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Actuadores").": "."</span><br/>
                    <div id='select_actuadores_elemento_plantilla_informe_comentarios_no_visible' hidden></div>
                    <select id='ids_actuadores_elemento_plantilla_informe_comentarios'
                        name='ids_actuadores_elemento_plantilla_informe_comentarios'
                        multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_actuadores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_actuadores,
            $clase_actuador,
            $ids_actuadores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de grupos de actuadores").": "."</span><br/>
                    <select id='tipo_seleccion_grupos_actuadores_elemento_plantilla_informe_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_grupos_actuadores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupos de actuadores").": "."</span><br/>
                    <div id='select_grupos_actuadores_elemento_plantilla_informe_comentarios_no_visible' hidden></div>
                    <select id='ids_grupos_actuadores_elemento_plantilla_informe_comentarios'
                        name='ids_grupos_actuadores_elemento_plantilla_informe_comentarios'
                        multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_grupos_actuadores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_grupos_actuadores,
            $clase_actuador,
            $ids_grupos_actuadores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de sensores (Eventos)
    //


    function anyade_controles_pestanyas_tipo_sensores_activaciones_eventos(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS)
        {
            $clase_sensor = CLASE_NINGUNA;
            $origen_evento = ORIGEN_EVENTO_SENSOR;
            $id_origen_evento = ID_TODOS;
            $granularidad_evento = GRANULARIDAD_TODAS;
            $tipo_seleccion_origen_evento = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $origen_evento = $parametros_tipo["origen_evento"];
            $tipo_seleccion_origen_evento = $parametros_tipo["tipo_seleccion_origen_evento"];
            $id_origen_evento = $parametros_tipo["id_origen_evento"];
            $granularidad_evento = $parametros_tipo["granularidad_evento"];
            $campo = $parametros_tipo["campo"];
            $ids_eventos = $parametros_tipo["ids_eventos"];
            $filtro_nombres_eventos = $parametros_tipo["filtro_nombres_eventos"];
        }

        // Contenido de pestañas de tipo 'Activaciones de eventos'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-activaciones-eventos-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de origen").": "."</span><br/>
                    <select id='origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos' class='select-administracion'>";
        $contenido .= dame_lista_origenes_evento($origen_evento, OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_TODOS);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de origen").": "."</span><br/>
                    <select id='tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_origen_evento);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Origen").": "."</span><br/>
                    <select id='id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos' class='chosen-select-administracion'>";
        $clase_sensor_origen_evento = $clase_sensor;
        if ($clase_sensor_origen_evento == CLASE_TODAS)
        {
            $clase_sensor_origen_evento = CLASE_NINGUNA;
        }
        switch ($origen_evento)
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                $contenido .= dame_lista_sensores_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $tipo_seleccion_origen_evento,
                    $clase_sensor_origen_evento,
                    array($id_origen_evento),
                    OPCIONES_EXTRA_LISTA_NODOS_TODOS);
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $contenido .= dame_lista_grupos_sensores_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $tipo_seleccion_origen_evento,
                    $clase_sensor_origen_evento,
                    array($id_origen_evento),
                    OPCIONES_EXTRA_LISTA_NODOS_TODOS);
                break;
            }
        }
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad").": "."</span><br/>
                    <select id='granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos' class='select-administracion'>";
        $contenido .= dame_lista_granularidades_evento($clase_sensor, $granularidad_evento, OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_activaciones_eventos' class='select-administracion'>";
        $id_sensor_activaciones_eventos = ID_NINGUNO;
        if ($origen_evento == ORIGEN_EVENTO_SENSOR)
        {
            $id_sensor_activaciones_eventos = $id_origen_evento;
        }
        $lista_campos_sensor_elemento_sensores_activaciones_eventos = dame_lista_campos_sensor_activaciones_eventos(
            $clase_sensor,
            $id_sensor_activaciones_eventos,
            $granularidad_evento,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_activaciones_eventos;
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de eventos
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-activaciones-eventos-eventos'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_eventos_elemento_plantilla_informe_sensores_activaciones_eventos'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Eventos").": "."</span><br/>
                    <div id='select_eventos_elemento_plantilla_informe_sensores_activaciones_eventos_no_visible' hidden></div>
                    <select id='ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos'
                        name='ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos'
                        max_selected='".MAX_EVENTOS_SELECCIONADOS_LISTA_EVENTOS_ACTIVACIONES_EVENTOS."' multiple='multiple'
                        class='select-administracion' hidden>";
        if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= dame_lista_eventos(
                $clase_sensor,
                $origen_evento,
                $id_origen_evento,
                $granularidad_evento,
                $ids_eventos);
        }
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
			<div class='row-fluid' id='control_filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Filtro de nombres de eventos").": "."</span><br/>
					<input type='text' id='filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($filtro_nombres_eventos, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de sensores (Información)
    //


    function anyade_controles_pestanyas_tipo_sensores_informacion(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $comentarios = COMENTARIOS_GRAFICA_TABLA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $comentarios = $parametros_tipo["comentarios"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestaña de tipo 'Información de sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-informacion'>";

        $id_control_id_ratio_elemento_sensores_informacion = "control_id_ratio_elemento_plantilla_informe_sensores_informacion";
        if ($mostrar_controles_localizaciones == false)
        {
            $id_control_id_ratio_elemento_sensores_informacion .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_ratio_elemento_sensores_informacion."' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_informacion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_campo_elemento_plantilla_informe_sensores_informacion'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_informacion = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_informacion;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_informacion' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_informacion' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_informacion'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid' id='control_intervalo_valores_elemento_plantilla_informe_sensores_informacion'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_informacion = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_informacion;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Comentarios").": "."</span><br/>
                    <select id='comentarios_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(COMENTARIOS_NINGUNO, dame_descripcion_comentarios(COMENTARIOS_NINGUNO)),
                array(COMENTARIOS_GRAFICA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA)),
                array(COMENTARIOS_GRAFICA_TABLA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA_TABLA))),
            array($comentarios));
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_mapa_calor_elemento_plantilla_informe_sensores_informacion'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_informacion' class='select-administracion'>";
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
                    </div>";
    }


    //
    // Elementos de sensores (Análisis)
    //


    function anyade_controles_pestanyas_tipo_sensores_analisis_horario(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestaña de tipo 'Análisis horario'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-horario'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_analisis_horario' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_analisis_horario' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_horario' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_analisis_horario' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_analisis_horario' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_analisis_horario = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO,
            $clase_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_analisis_horario;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_horario' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_horario' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_horario'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_horario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
            array($tipo_mapa_calor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_analisis_diario(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestaña de tipo 'Análisis diario'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-diario'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_analisis_diario' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_analisis_diario' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_diario' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_analisis_diario' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_analisis_diario' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_analisis_diario = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO,
            $clase_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_analisis_diario;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_diario' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_diario' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_diario'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_diario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
            array($tipo_mapa_calor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_analisis_comportamiento(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
        }

        // Contenido de pestañas de tipo 'Análisis de comportamiento'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-comportamiento-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_analisis_comportamiento' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_analisis_comportamiento' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_analisis_comportamiento = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO,
            $clase_sensor,
            INTERVALO_VALORES_NINGUNO,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_analisis_comportamiento;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comportamiento' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comportamiento' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comportamiento'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-comportamiento-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento'
                        name='ids_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPORTAMIENTO."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clase_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de sensores (Comparación)
    //


    function anyade_controles_pestanyas_tipo_sensores_comparacion_periodos(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestaña de tipo 'Comparación de periodos'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-periodos'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_comparacion_periodos = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_comparacion_periodos;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_periodos' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_periodos' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_periodos'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_comparacion_periodos = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_comparacion_periodos;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid''>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_MAPA_CALOR_NINGUNO, $idiomas->_("Ninguno")),
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO))),
            array($tipo_mapa_calor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_comparacion_perfil_horario(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $timestamp_inicio_perfil_horario = strtotime('-1 '.PERIODO_SEMANA);
            $timestamp_fin_perfil_horario = strtotime("now");
            $cadena_fecha_inicio_perfil_horario_local = date($_SESSION["formato_fecha_local"], $timestamp_inicio_perfil_horario);
            $cadena_fecha_fin_perfil_horario_local = date($_SESSION["formato_fecha_local"], $timestamp_fin_perfil_horario);
            $cadena_agrupaciones_dias_semana = "1-2-3-4-5, 6-7";
        }
        else
        {
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
            $cadena_fecha_inicio_perfil_horario = $parametros_tipo["fecha_inicio_perfil_horario"];
            $cadena_fecha_fin_perfil_horario = $parametros_tipo["fecha_fin_perfil_horario"];
            $cadena_fecha_inicio_perfil_horario_local = convierte_formato_fecha($cadena_fecha_inicio_perfil_horario, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_perfil_horario_local = convierte_formato_fecha($cadena_fecha_fin_perfil_horario, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $tipo_perfil_horario = $parametros_tipo["tipo_perfil_horario"];
            $cadena_agrupaciones_dias_semana = $parametros_tipo["agrupaciones_dias_semana"];
            $cadena_agrupaciones_dias_semana = str_replace(" ", "", $cadena_agrupaciones_dias_semana);
            $cadena_agrupaciones_dias_semana = str_replace(SEPARADOR_PARAMETROS_SIMPLES, SEPARADOR_PARAMETROS_SIMPLES." ", $cadena_agrupaciones_dias_semana);
        }

        // Contenido de pestañas de tipo 'Comparación de periodos'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-perfil-horario-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_comparacion_perfil_horario = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_comparacion_perfil_horario;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_comparacion_perfil_horario = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_comparacion_perfil_horario;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid''>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_MAPA_CALOR_NINGUNO, $idiomas->_("Ninguno")),
                array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO))),
            array($tipo_mapa_calor));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de perfil horario
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-perfil-horario-perfil-horario'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio de perfil horario").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_perfil_horario_local."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin de perfil horario").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_fin_perfil_horario_local."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid''>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de perfil horario").": "."</span><br/>
                    <select id='tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_PERFIL_HORARIO_SEMANAL, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_SEMANAL)),
                array(TIPO_PERFIL_HORARIO_DIARIO, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_DIARIO)),
                array(TIPO_PERFIL_HORARIO_CONFIGURABLE, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_CONFIGURABLE))),
            array($tipo_perfil_horario));
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agrupaciones de días de la semana").": "."</span><br/>
                    <input type='text' id='agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$cadena_agrupaciones_dias_semana."'>
                    <span id='boton_ayuda_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_comparacion_campos_iguales(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor_principal = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $tipo_seleccion_sensores_secundarios = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor_principal = $parametros_tipo["tipo_seleccion_sensor_principal"];
            $id_sensor_principal = $parametros_tipo["id_sensor_principal"];
            $tipo_seleccion_sensores_secundarios = $parametros_tipo["tipo_seleccion_sensores_secundarios"];
            $ids_sensores_secundarios = $parametros_tipo["ids_sensores_secundarios"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestañas de tipo 'Comparación de campos iguales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-campos-iguales-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor principal").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor_principal);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor principal").": "."</span><br/>
                    <select id='id_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor_principal,
            $clase_sensor,
            array($id_sensor_principal),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_comparacion_campos_iguales = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_comparacion_campos_iguales;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_comparacion_campos_iguales = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_comparacion_campos_iguales;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
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
                    </div>";

        // - Pestaña de sensores secundarios
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores secundarios").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores_secundarios);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores secundarios").": "."</span><br/>
                    <div id='select_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales_no_visible' hidden></div>
                    <select id='ids_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales'
                        name='ids_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales'
                        max_selected='".NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores_secundarios,
            $clase_sensor,
            $ids_sensores_secundarios,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_comparacion_campos_diferentes(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES)
        {
            $clases_sensores = array();
            $tipos_seleccion_sensores = array();
            $intervalo_valores = INTERVALO_VALORES_HORA;
            $unificar_escalas = VALOR_SI;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensores = $parametros_tipo["clases_sensores"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $tipos_seleccion_sensores = $parametros_tipo["tipos_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $unificar_escalas = $parametros_tipo["unificar_escalas"];
        }
        for ($i = count($clases_sensores); $i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; $i++)
        {
            array_push($clases_sensores, CLASE_NINGUNA);
        }
        for ($i = count($tipos_seleccion_sensores); $i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; $i++)
        {
            array_push($tipos_seleccion_sensores, TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO);
        }

        // Contenido de pestañas de tipo 'Comparación de campos diferentes'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-campos-diferentes-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_comparacion_campos_diferentes = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES,
            CLASE_NINGUNA,
            CAMPO_NINGUNO,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_comparacion_campos_diferentes;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Unificar escalas").": "."</span><br/>
                    <select id='unificar_escalas_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
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
                    <div class='tab-pane' id='tab-tipo-sensores-comparacion-campos-diferentes-sensor-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES,
                $clases_sensores[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'";
            if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
            {
                $contenido .= " hidden";
            }
            $contenido .= ">
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                        <select id='tipo_seleccion_sensor_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
            $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipos_seleccion_sensores[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                        <select id='id_sensor_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='chosen-select-administracion'>";
            $contenido .= dame_lista_sensores_elemento_plantilla_informe(
                $id_plantilla_informe,
                $tipos_seleccion_sensores[$i - 1],
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
                        <select id='campo_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='select-administracion'>";
            $lista_campos_sensor_bucle_elemento_sensores_comparacion_campos_diferentes = dame_lista_campos_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES,
                $clases_sensores[$i - 1],
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_bucle_elemento_sensores_comparacion_campos_diferentes;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_comparacion_campos_diferentes'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }
    }


    function anyade_controles_pestanyas_tipo_sensores_analisis_comparativo(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensores_agregados = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $tipo_seleccion_sensor_destacado = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensores_agregados = $parametros_tipo["tipo_seleccion_sensores_agregados"];
            $ids_sensores_agregados = $parametros_tipo["ids_sensores_agregados"];
            $tipo_seleccion_sensor_destacado = $parametros_tipo["tipo_seleccion_sensor_destacado"];
            $id_sensor_destacado = $parametros_tipo["id_sensor_destacado"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        }

        // Contenido de pestañas de tipo 'Comparación de campos iguales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-comparativo-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_comparacion_campos_iguales = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_comparacion_campos_iguales;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comparativo' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comparativo' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comparativo'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_analisis_comparativo = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_analisis_comparativo;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa de calor").": "."</span><br/>
                    <select id='tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
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
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-analisis-comparativo-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores agregados").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores_agregados);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores agregados").": "."</span><br/>
                    <div id='select_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo_no_visible' hidden></div>
                    <select id='ids_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo'
                        name='ids_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPARATIVO."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores_agregados,
            $clase_sensor,
            $ids_sensores_agregados,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor destacado").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor_destacado);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor destacado").": "."</span><br/>
                    <select id='id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor_destacado,
            $clase_sensor,
            array($id_sensor_destacado),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }

    function anyade_controles_pestanyas_tipo_sensores_valores_generales(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES)
        {
            $clases_sensor = array();
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensor = $parametros_tipo["clases_sensor"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];

            // Tipo de valores (para la carga de la lista de tipos de agregación)
            $tipo_valores = dame_tipo_valores_campo_clase_sensor($clases_sensor[0], $campos[0]);
        }
        for ($i = count($clases_sensor); $i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; $i++)
        {
            array_push($clases_sensor, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipo 'Valores generales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-valores-generales-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_valores_generales = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES,
            $clases_sensor[0],
            $campos[0],
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_valores_generales;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
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
                    <div class='tab-pane' id='tab-tipo-sensores-valores-generales-campo-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES,
                $clases_sensor[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
            $lista_campos_sensor_elemento_sensores_valores_generales = dame_lista_campos_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES,
                $clases_sensor[$i - 1],
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_elemento_sensores_valores_generales;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_valores_generales' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_valores_generales' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_valores_generales'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-valores-generales-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_sensores_valores_generales_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_sensores_valores_generales'
                        name='ids_sensores_elemento_plantilla_informe_sensores_valores_generales'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_VALORES_GENERALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_clases_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clases_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_incrementos_totales(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES)
        {
            $clases_sensor = array();
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensor = $parametros_tipo["clases_sensor"];
            $campos = $parametros_tipo["campos"];
            $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];
        }
        for ($i = count($clases_sensor); $i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; $i++)
        {
            array_push($clases_sensor, CLASE_NINGUNA);
        }

        // Contenido de pestañas de tipo 'Incrementos totales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-incrementos-totales-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_incrementos_totales = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES,
            $clases_sensor[0],
            $campos[0],
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_incrementos_totales;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
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
                    <div class='tab-pane' id='tab-tipo-sensores-incrementos-totales-campo-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_".$i."_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES,
                $clases_sensor[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_".$i."_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
            $lista_campos_sensor_elemento_sensores_incrementos_totales = dame_lista_campos_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES,
                $clases_sensor[$i - 1],
                $intervalo_valores,
                $campos[$i - 1]);
            $contenido .= $lista_campos_sensor_elemento_sensores_incrementos_totales;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_incrementos_totales' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_incrementos_totales' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_".$i."_elemento_plantilla_informe_sensores_incrementos_totales'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-incrementos-totales-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_sensores_incrementos_totales_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales'
                        name='ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_INCREMENTOS_TOTALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_clases_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clases_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de sensores (Estadística)
    //


    function anyade_controles_pestanyas_tipo_sensores_histograma(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA)
        {
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $detalle = $parametros_tipo["detalle"];
        }

        // Contenido de pestaña de tipo 'Histograma'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-histograma'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_sensores_histograma' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_histograma = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_histograma;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_sensores_histograma' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_sensores_histograma' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_sensores_histograma'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_histograma = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_histograma;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Detalle").": "."</span><br/>
                    <select id='detalle_elemento_plantilla_informe_sensores_histograma' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(DETALLE_MINIMO, $idiomas->_("Mínimo")),
                array(DETALLE_MEDIO, $idiomas->_("Medio")),
                array(DETALLE_MAXIMO, $idiomas->_("Máximo"))),
            array($detalle));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_sensores_correlacion(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION)
        {
            $clases_sensores_independientes = array();
            $clase_sensor_dependiente = CLASE_NINGUNA;
            $tipos_seleccion_sensores_independientes = array();
            $tipo_seleccion_sensor_dependiente = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $clases_sensores_independientes = $parametros_tipo["clases_sensores_independientes"];
            $campos_independientes = $parametros_tipo["campos_independientes"];
            $parametros_extra_campos_independientes = $parametros_tipo["parametros_extra_campos_independientes"];
            $tipos_seleccion_sensores_independientes = $parametros_tipo["tipos_seleccion_sensores_independientes"];
            $ids_sensores_independientes = $parametros_tipo["ids_sensores_independientes"];
            $clase_sensor_dependiente = $parametros_tipo["clase_sensor_dependiente"];
            $campo_dependiente = $parametros_tipo["campo_dependiente"];
            $parametros_extra_campo_dependiente = $parametros_tipo["parametros_extra_campo_dependiente"];
            $tipo_seleccion_sensor_dependiente = $parametros_tipo["tipo_seleccion_sensor_dependiente"];
            $id_sensor_dependiente = $parametros_tipo["id_sensor_dependiente"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $funcion_correlacion = $parametros_tipo["funcion_correlacion"];
        }
        for ($i = count($clases_sensores_independientes); $i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; $i++)
        {
            array_push($clases_sensores_independientes, CLASE_NINGUNA);
        }
        for ($i = count($tipos_seleccion_sensores_independientes); $i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; $i++)
        {
            array_push($tipos_seleccion_sensores_independientes, TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO);
        }

        // Contenido de pestañas de tipo 'Correlación'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-correlacion-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_correlacion = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION,
            CLASE_NINGUNA,
            CAMPO_NINGUNO,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_correlacion;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Función de correlación").": "."</span><br/>
                    <select id='funcion_correlacion_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(FUNCION_CORRELACION_AUTOMATICA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_AUTOMATICA)),
                array(FUNCION_CORRELACION_LINEAL, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_LINEAL)),
                array(FUNCION_CORRELACION_POLINOMIO_GRADO_2, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_POLINOMIO_GRADO_2)),
                array(FUNCION_CORRELACION_LOGARITMICA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_LOGARITMICA)),
                array(FUNCION_CORRELACION_RAIZ_CUADRADA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_RAIZ_CUADRADA))),
            array($funcion_correlacion));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestañas de sensores independientes
        for ($i = 1; $i <= NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; $i++)
        {
            $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-correlacion-sensor-independiente-".$i."'>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                        <select id='clase_sensor_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
            $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION,
                $clases_sensores_independientes[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'";
            if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
            {
                $contenido .= " hidden";
            }
            $contenido .= ">
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                        <select id='tipo_seleccion_sensor_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
            $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipos_seleccion_sensores_independientes[$i - 1]);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                        <select id='id_sensor_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' class='chosen-select-administracion'>";
            $contenido .= dame_lista_sensores_elemento_plantilla_informe(
                $id_plantilla_informe,
                $tipos_seleccion_sensores_independientes[$i - 1],
                $clases_sensores_independientes[$i - 1],
                array($ids_sensores_independientes[$i - 1]),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            $contenido .= "
                        </select>
                    </div>
                </div>";

            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                        <select id='campo_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
            $lista_campos_sensor_independiente_bucle_elemento_sensores_correlacion = dame_lista_campos_sensor_elemento_plantilla_informe(
                TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION,
                $clases_sensores_independientes[$i - 1],
                $intervalo_valores,
                $campos_independientes[$i - 1]);
            $contenido .= $lista_campos_sensor_independiente_bucle_elemento_sensores_correlacion;
            $contenido .= "
                        </select>
                    </div>
                </div>

                <div class='row-fluid' id='control_parametros_extra_campo_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' hidden>
                    <div class='span12'><span id='etiqueta_parametros_extra_campo_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                        <input type='text' id='parametros_extra_campo_independiente_".$i."_elemento_plantilla_informe_sensores_correlacion'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campos_independientes[$i - 1]."'>
                    </div>
                </div>";

            $contenido .= "
                    </div>";
        }

        // - Pestaña de sensor dependiente
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensores-correlacion-sensor-dependiente'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION,
            $clase_sensor_dependiente);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor_dependiente);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor_dependiente,
            $clase_sensor_dependiente,
            array($id_sensor_dependiente),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_dependiente_elemento_plantilla_informe_sensores_correlacion' class='select-administracion'>";
        $lista_campos_sensor_dependiente_elemento_sensores_correlacion = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION,
            $clase_sensor_dependiente,
            $intervalo_valores,
            $campo_dependiente);
        $contenido .= $lista_campos_sensor_dependiente_elemento_sensores_correlacion;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_dependiente_elemento_plantilla_informe_sensores_correlacion' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_dependiente_elemento_plantilla_informe_sensores_correlacion' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_dependiente_elemento_plantilla_informe_sensores_correlacion'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo_dependiente."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de actuadores (Información)
    //


    function anyade_controles_pestanyas_tipo_actuadores_informacion_acciones_enviadas(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS)
        {
            $clase_actuador = CLASE_NINGUNA;
            $destino_accion = DESTINO_ACCION_ACTUADOR;
            $tipo_seleccion_destino_accion = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $origen_acciones = ORIGEN_ACCIONES_TODOS;
            $clase_sensor = CLASE_NINGUNA;
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $comentarios = COMENTARIOS_GRAFICA_TABLA;
        }
        else
        {
            $clase_actuador = $parametros_tipo["clase_actuador"];
            $destino_accion = $parametros_tipo["destino_accion"];
            $tipo_seleccion_destino_accion = $parametros_tipo["tipo_seleccion_destino_accion"];
            $id_destino_accion = $parametros_tipo["id_destino_accion"];
            $origen_acciones = $parametros_tipo["origen_accion"];
            $clase_sensor = $parametros_tipo["clase_sensor"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $campo = $parametros_tipo["campo"];
            $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $comentarios = $parametros_tipo["comentarios"];
        }

        // Contenido de pestañas de tipo 'Información de acciones enviadas'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-actuadores-informacion-acciones-enviadas-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de destino").": "."</span><br/>
                    <select id='destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_destinos_accion($destino_accion);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de destino").": "."</span><br/>
                    <select id='tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_destino_accion);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Destino").": "."</span><br/>
                    <select id='id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='chosen-select-administracion'>";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $contenido .= dame_lista_actuadores_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $tipo_seleccion_destino_accion,
                    $clase_actuador,
                    array($id_destino_accion),
                    OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $contenido .= dame_lista_grupos_actuadores_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $tipo_seleccion_destino_accion,
                    $clase_actuador,
                    array($id_destino_accion),
                    OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                break;
            }
        }
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Origen").": "."</span><br/>
                    <select id='origen_acciones_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_origenes_acciones($origen_acciones);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Comentarios").": "."</span><br/>
                    <select id='comentarios_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(COMENTARIOS_NINGUNO, dame_descripcion_comentarios(COMENTARIOS_NINGUNO)),
                array(COMENTARIOS_GRAFICA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA)),
                array(COMENTARIOS_GRAFICA_TABLA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA_TABLA))),
            array($comentarios));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensor
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-actuadores-informacion-acciones-enviadas-sensor'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS,
            $clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $lista_campos_sensor_elemento_sensores_analisis_horario = dame_lista_campos_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS,
            $clase_sensor,
            $intervalo_valores,
            $campo);
        $contenido .= $lista_campos_sensor_elemento_sensores_analisis_horario;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de sensor").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_actuadores_informacion_acciones_enviadas = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS,
            $clase_sensor,
            $campo,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_actuadores_informacion_acciones_enviadas;
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de SmartMeter (Consumos y costes)
    //


    function anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_generales(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $intervalo_valores = INTERVALO_VALORES_HORA;
            $agregacion = AGREGACION_NINGUNA;
            $comentarios = COMENTARIOS_GRAFICA_TABLA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
            $agregacion = $parametros_tipo["agregacion"];
            $comentarios = $parametros_tipo["comentarios"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestañas de tipo 'Consumos y costes generales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-consumos-costes-generales-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_valores_generales = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES,
            $clase_sensor,
            CAMPO_NINGUNO,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_valores_generales;
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Agregación").": "."</span><br/>
                    <select id='agregacion_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $contenido .= dame_lista_agregaciones(TIPO_VALORES_SENSOR_INCREMENTALES, TIPOS_AGREGACION_SIN_CLASES, $agregacion);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Comentarios").": "."</span><br/>
                    <select id='comentarios_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(COMENTARIOS_NINGUNO, dame_descripcion_comentarios(COMENTARIOS_NINGUNO)),
                array(COMENTARIOS_GRAFICA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA)),
                array(COMENTARIOS_GRAFICA_TABLA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA_TABLA))),
            array($comentarios));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-consumos-costes-generales-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales'
                        name='ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_GENERALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clase_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_totales(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $ids_sensores = $parametros_tipo["ids_sensores"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestañas de tipo 'Consumos y costes totales'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-consumos-costes-totales-principal'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_totales' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_totales' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_incrementos_totales = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES,
            $clase_sensor,
            CAMPO_NINGUNO,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_incrementos_totales;
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-consumos-costes-totales-sensores'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales_no_visible' hidden></div>
                    <select id='ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales'
                        name='ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_TOTALES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            $clase_sensor,
            $ids_sensores,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_comparacion_periodos(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $intervalo_valores = $parametros_tipo["intervalo_valores"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestaña de tipo 'Comparación de periodos'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-comparacion-periodos'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_smartmeter_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_elemento_plantilla_informe_smartmeter_comparacion_periodos' class='select-administracion'>";
        $lista_intervalos_valores_sensor_elemento_sensores_comparacion_periodos = dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
            TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS,
            $clase_sensor,
            CAMPO_NINGUNO,
            $intervalo_valores);
        $contenido .= $lista_intervalos_valores_sensor_elemento_sensores_comparacion_periodos;
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_simulador_tarifas(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $ids_tarifas = $parametros_tipo["ids_tarifas"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestañas de tipo 'Simulador de tarifas'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-simulador-tarifas-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_CURVA_COSTE_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de tarifas
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-simulador-tarifas-tarifas'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas").": "."</span><br/>
                    <div id='select_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas_no_visible' hidden></div>
					<select id='ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas'
                        name='ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas'
                        max_selected='".ID_NINGUNO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_tarifas($medicion, $ids_tarifas, OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_consumos_costes_tramos_electricidad(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $mostrar_controles_localizaciones,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $id_ratio = $parametros_tipo["id_ratio"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Contenido de pestaña de tipo 'Consumos y costes por tramo'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-consumos-costes-tramos-electricidad'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_controles_localizaciones == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ratio").": "."</span><br/>
                    <select id='id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_ratios($id_ratio);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_ENERGIA_ACTIVA,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_cortes_tension_electricidad(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Contenido de pestaña de tipo 'Cortes de tensión'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-cortes-tension-electricidad'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_CORTES_TENSION,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_excesos_potencia_electricidad(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $granularidad = GRANULARIDAD_CUARTOHORARIA;
        }
        else
        {
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $granularidad = $parametros_tipo["granularidad"];
        }

        // Contenido de pestaña de tipo 'Excesos de potencia'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-excesos-potencia-electricidad'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_ENERGIA_ACTIVA,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad").": "."</span><br/>
                    <select id='granularidad_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_granularidades_informe_excesos_potencia($granularidad);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_excesos_energia_reactiva_electricidad(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Contenido de pestaña de tipo 'Excesos de energía reactiva'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-excesos-energia-reactiva-electricidad'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_ENERGIA_REACTIVA,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_excesos_caudal_gas(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Contenido de pestaña de tipo 'Excesos de caudal'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-excesos-caudal-gas'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_GAS,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de SmartMeter (Compra de energía)
    //


    function anyade_controles_pestanyas_tipo_smartmeter_desvios_compra_energia(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA)
        {
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Contenido de pestaña de tipo 'Desvíos de compra de energía'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-desvios-compra-energia'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            CLASE_SENSOR_COMPRA_ENERGIA,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    function anyade_controles_pestanyas_tipo_smartmeter_desvios_ponderados_compra_energia(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA)
        {
            $tipo_seleccion_sensores = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $id_sensor = ID_NINGUNO;
        }
        else
        {
            $tipo_seleccion_sensores = $parametros_tipo["tipo_seleccion_sensores"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $id_sensor_hijo = $parametros_tipo["id_sensor_hijo"];
        }

        // Contenido de pestaña de tipo 'Desvíos ponderados de compra de energía'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-desvios-ponderados-compra-energia'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores,
            CLASE_SENSOR_COMPRA_ENERGIA,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor hijo").": "."</span><br/>
                    <select id='id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia' class='chosen-select-administracion'>";
        switch ($tipo_seleccion_sensores)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $contenido .= dame_lista_sensores_hijos(
                    CLASE_SENSOR_COMPRA_ENERGIA,
                    $id_sensor,
                    array($id_sensor_hijo),
                    OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $contenido .= dame_lista_sensores_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $tipo_seleccion_sensores,
                    CLASE_SENSOR_ENERGIA_ACTIVA,
                    array($id_sensor_hijo),
                    OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de sensores desconocido: '".$tipo_seleccion_sensores."'");
            }
        }
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de SmartMeter (Facturas)
    //


    function anyade_controles_pestanyas_tipo_smartmeter_simulador_factura(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $tipo_seleccion_sensores_reparto_costes = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
            $id_tarifa = $parametros_tipo["id_tarifa"];
            $tipo_seleccion_sensores_reparto_costes = $parametros_tipo["tipo_seleccion_sensores_reparto_costes"];
            $ids_sensores_reparto_costes = $parametros_tipo["ids_sensores_reparto_costes"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestañas de tipo 'Simulador de factura'

        // - Pestaña principal
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-simulador-factura-principal'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_simulador_factura' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_factura' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_simulador_factura' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas["curva_coste"] == true)
        {
            $opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS;
        }
        else
        {
            $opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL;
        }
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa").": "."</span><br/>
                    <select id='id_tarifa_elemento_plantilla_informe_smartmeter_simulador_factura' class='chosen-select-administracion'>";
        $contenido .= dame_lista_tarifas(
            $medicion,
            array($id_tarifa),
            $opciones_extra_lista_tarifas);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // - Pestaña de reparto de costes
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-simulador-factura-reparto-costes'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensores").": "."</span><br/>
                    <select id='tipo_seleccion_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensores_reparto_costes);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura_no_visible' hidden></div>
                    <select id='ids_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura'
                        name='ids_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_REPARTO_COSTES_SIMULADOR_FACTURA."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensores_reparto_costes,
            $clase_sensor,
            $ids_sensores_reparto_costes,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de SmartMeter (Instalación)
    //


    function anyade_controles_pestanyas_tipo_smartmeter_instalacion(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION)
        {
            $medicion = dame_medicion_defecto();
            $tipo_seleccion_sensor = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
        }
        else
        {
            $medicion = $parametros_tipo["medicion"];
            $tipo_seleccion_sensor = $parametros_tipo["tipo_seleccion_sensor"];
            $id_sensor = $parametros_tipo["id_sensor"];
        }

        // Clase de sensor de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);

        // Contenido de pestaña de tipo 'Instalación'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-smartmeter-instalacion'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición").": "."</span><br/>
                    <select id='medicion_elemento_plantilla_informe_smartmeter_instalacion' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion, OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de sensor").": "."</span><br/>
                    <select id='tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_instalacion' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_sensor);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_elemento_plantilla_informe_smartmeter_instalacion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_sensor,
            $clase_sensor,
            array($id_sensor),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de proyectos (Líneas base)
    //


    function anyade_controles_pestanyas_tipo_proyectos_simulador_linea_base(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE)
        {
            $tipo_seleccion_linea_base = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $id_linea_base = ID_NINGUNO;
            $comentarios = COMENTARIOS_GRAFICA_TABLA;
        }
        else
        {
            $tipo_seleccion_linea_base = $parametros_tipo["tipo_seleccion_linea_base"];
            $id_linea_base = $parametros_tipo["id_linea_base"];
            $comentarios = $parametros_tipo["comentarios"];
        }

        // Contenido de pestaña de tipo 'Simulador de línea base'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-proyectos-simulador-linea-base'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de línea base").": "."</span><br/>
                    <select id='tipo_seleccion_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_linea_base);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Línea base").": "."</span><br/>
                    <select id='id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_lineas_base_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_linea_base,
            $id_linea_base);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Comentarios").": "."</span><br/>
                    <select id='comentarios_elemento_plantilla_informe_proyectos_simulador_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(COMENTARIOS_NINGUNO, dame_descripcion_comentarios(COMENTARIOS_NINGUNO)),
                array(COMENTARIOS_GRAFICA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA)),
                array(COMENTARIOS_GRAFICA_TABLA, dame_descripcion_comentarios(COMENTARIOS_GRAFICA_TABLA))),
            array($comentarios));
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Elementos de proyectos (Información)
    //


    function anyade_controles_pestanyas_tipo_proyectos_informacion_proyecto(
        $id_plantilla_informe,
        $tipo_plantilla_informe,
        $tipo,
        $parametros_tipo,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO)
        {
            $tipo_seleccion_proyecto = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
            $id_proyecto = ID_NINGUNO;
        }
        else
        {
            $tipo_seleccion_proyecto = $parametros_tipo["tipo_seleccion_proyecto"];
            $id_proyecto = $parametros_tipo["id_proyecto"];
        }

        // Contenido de pestaña de tipo 'Información de proyecto'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-proyectos-informacion-proyecto'>";

        $contenido .= "
            <div class='row-fluid'";
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de proyecto").": "."</span><br/>
                    <select id='tipo_seleccion_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_proyecto);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Proyecto").": "."</span><br/>
                    <select id='id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto' class='chosen-select-administracion'>";
        $contenido .= dame_lista_proyectos_elemento_plantilla_informe(
            $id_plantilla_informe,
            $tipo_seleccion_proyecto,
            $id_proyecto);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    //
    // Pestañas de varios tipos
    //


    // Contenido de pestaña de periodo de tiempo (utilizada por tipos de elementos con fechas inicial y final)
    function anyade_controles_pestanya_periodo_tiempo($tipo, $parametros_tipo, &$contenido)
    {
        $idiomas = new Idiomas();

        if (array_key_exists("modificar_periodo_tiempo", $parametros_tipo) == true)
        {
            $modificar_periodo_tiempo = $parametros_tipo["modificar_periodo_tiempo"];
        }
        else
        {
            $modificar_periodo_tiempo = VALOR_NO;
        }
        if (array_key_exists("periodo_tiempo", $parametros_tipo) == true)
        {
            $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        }
        else
        {
            switch ($tipo)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $periodo_tiempo = ID_NINGUNO;
                    break;
                }
                default:
                {
                    $periodo_tiempo = PERIODO_TIEMPO_DIA;
                    break;
                }
            }
        }
        if (array_key_exists("iniciar_comienzo_periodo_tiempo", $parametros_tipo) == true)
        {
            $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        }
        else
        {
            $iniciar_comienzo_periodo_tiempo = VALOR_NO;
        }
        if (array_key_exists("numero_periodos_tiempo", $parametros_tipo) == true)
        {
            $numero_periodos_tiempo = $parametros_tipo["numero_periodos_tiempo"] + 1;
        }
        else
        {
            $numero_periodos_tiempo = 1;
        }
        if (array_key_exists("fecha_inicio_periodo_tiempo", $parametros_tipo) == true)
        {
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_local_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }
        else
        {
            $cadena_fecha_inicio_periodo_tiempo_local_local = date($_SESSION["formato_fecha_local"]);
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-periodo-tiempo'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar periodo de tiempo").": "."</span><br/>
					<select id='modificar_periodo_tiempo_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($modificar_periodo_tiempo);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_periodo_tiempo_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_elemento_plantilla_informe($tipo, $periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_periodos_tiempo_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de periodos de tiempo").": "."</span><br/>
                    <input type='text' id='numero_periodos_tiempo_elemento_plantilla_informe'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_periodos_tiempo."'>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_inicio_periodo_tiempo_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_tiempo_elemento_plantilla_informe' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_tiempo_local_local."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";
    }


    // Contenido de pestaña de duración y separación de periodos (utilizada por tipos de elementos de comparación de periodos)
    function anyade_controles_pestanya_duracion_separacion_periodos($tipo, $parametros_tipo, &$contenido)
    {
        $idiomas = new Idiomas();

        if (array_key_exists("modificar_duracion_periodos", $parametros_tipo) == true)
        {
            $modificar_duracion_periodos = $parametros_tipo["modificar_duracion_periodos"];
        }
        else
        {
            $modificar_duracion_periodos = VALOR_NO;
        }
        if (array_key_exists("periodo_tiempo_duracion_periodos", $parametros_tipo) == true)
        {
            $periodo_tiempo_duracion_periodos = $parametros_tipo["periodo_tiempo_duracion_periodos"];
        }
        else
        {
            $periodo_tiempo_duracion_periodos = PERIODO_TIEMPO_DIA;
        }
        if (array_key_exists("duracion_periodos_completos", $parametros_tipo) == true)
        {
            $duracion_periodos_completos = $parametros_tipo["duracion_periodos_completos"];
        }
        else
        {
            $duracion_periodos_completos = VALOR_SI;
        }
        if (array_key_exists("iniciar_comienzo_periodo_tiempo_duracion_periodos", $parametros_tipo) == true)
        {
            $iniciar_comienzo_periodo_tiempo_duracion_periodos = $parametros_tipo["iniciar_comienzo_periodo_tiempo_duracion_periodos"];
        }
        else
        {
            $iniciar_comienzo_periodo_tiempo_duracion_periodos = VALOR_NO;
        }
        if (array_key_exists("numero_periodos_tiempo_duracion_periodos", $parametros_tipo) == true)
        {
            $numero_periodos_tiempo_duracion_periodos = $parametros_tipo["numero_periodos_tiempo_duracion_periodos"] + 1;
        }
        else
        {
            $numero_periodos_tiempo_duracion_periodos = 1;
        }
        if (array_key_exists("modificar_desplazamiento_periodo_anterior", $parametros_tipo) == true)
        {
            $modificar_desplazamiento_periodo_anterior = $parametros_tipo["modificar_desplazamiento_periodo_anterior"];
        }
        else
        {
            $modificar_desplazamiento_periodo_anterior = VALOR_NO;
        }
        if (array_key_exists("periodo_tiempo_desplazamiento_periodo_anterior", $parametros_tipo) == true)
        {
            $periodo_tiempo_desplazamiento_periodo_anterior = $parametros_tipo["periodo_tiempo_desplazamiento_periodo_anterior"];
        }
        else
        {
            $periodo_tiempo_desplazamiento_periodo_anterior = PERIODO_TIEMPO_DIA;
        }
        if (array_key_exists("numero_periodos_tiempo_desplazamiento_periodo_anterior", $parametros_tipo) == true)
        {
            $numero_periodos_tiempo_desplazamiento_periodo_anterior = $parametros_tipo["numero_periodos_tiempo_desplazamiento_periodo_anterior"];
        }
        else
        {
            $numero_periodos_tiempo_desplazamiento_periodo_anterior = 0;
        }
        if (array_key_exists("ajustar_dias_semana", $parametros_tipo) == true)
        {
            $ajustar_dias_semana = $parametros_tipo["ajustar_dias_semana"];
        }
        else
        {
            $ajustar_dias_semana = VALOR_SI;
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-duracion-separacion-periodos'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar duración de periodos").": "."</span><br/>
					<select id='modificar_duracion_periodos_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($modificar_duracion_periodos);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_periodo_tiempo_duracion_periodos_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo de duración de periodos").": "."</span><br/>
                    <select id='periodo_tiempo_duracion_periodos_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_elemento_plantilla_informe($tipo, $periodo_tiempo_duracion_periodos);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_duracion_periodos_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_duracion_periodos_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo_duracion_periodos);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de periodos de tiempo de duración de periodos").": "."</span><br/>
                    <input type='text' id='numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_periodos_tiempo_duracion_periodos."'>
                </div>
            </div>

            <div class='row-fluid' id='control_duracion_periodos_completos_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Duración de periodos completos").": "."</span><br/>
                    <select id='duracion_periodos_completos_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($duracion_periodos_completos);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modificar desplazamiento de periodo anterior").": "."</span><br/>
					<select id='modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($modificar_desplazamiento_periodo_anterior);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_periodo_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo de desplazamiento de periodo anterior").": "."</span><br/>
                    <select id='periodo_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_elemento_plantilla_informe($tipo, $periodo_tiempo_desplazamiento_periodo_anterior);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de periodos de tiempo de desplazamiento de periodo anterior").": "."</span><br/>
                    <input type='text' id='numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_periodos_tiempo_desplazamiento_periodo_anterior."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ajustar días de la semana").": "."</span><br/>
					<select id='ajustar_dias_semana_elemento_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($ajustar_dias_semana);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";
    }
?>
