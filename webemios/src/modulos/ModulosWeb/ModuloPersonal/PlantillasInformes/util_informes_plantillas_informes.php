<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/util_plantillas_informes_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/util_informes_analisis.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_informes_comparacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_informes_eventos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_informes_compra_energia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/util_informes_consumos_costes_gas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones de datos de informes de plantillas de informes
    //


    function dame_datos_informe_plantilla_informe($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        // Nota: Los parámetros del informe se pasan en formato JSON (se utiliza 'ajax' directamente, no 'POST')
        // (https://stackoverflow.com/questions/16104078/appending-array-to-formdata-and-send-via-ajax)
        $id_plantilla_informe = $parametros["id_plantilla_informe"];
        $ids_parametros = json_decode($parametros["ids_parametros"], true);
        $valores_parametros = json_decode($parametros["valores_parametros"], true);
        $ids_elementos_portada = json_decode($parametros["ids_elementos_portada"], true);
        $subtitulos_elementos_portada = json_decode($parametros["subtitulos_elementos_portada"], true);
        $ids_elementos_titulo = json_decode($parametros["ids_elementos_titulo"], true);
        $titulos_elementos_titulo = json_decode($parametros["titulos_elementos_titulo"], true);
        $ids_elementos_texto = json_decode($parametros["ids_elementos_texto"], true);
        $textos_elementos_texto = json_decode($parametros["textos_elementos_texto"], true);
        $ids_elementos_notas = json_decode($parametros["ids_elementos_notas"], true);
        $textos_elementos_notas = json_decode($parametros["textos_elementos_notas"], true);
        $ids_elementos_imagen = json_decode($parametros["ids_elementos_imagen"], true);
        $rutas_ficheros_imagenes_elementos_imagen = json_decode($parametros["rutas_ficheros_imagenes_elementos_imagen"], true);
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];

        // Se añaden los parámetros vacíos (si no hay)
        if (array_key_exists("ids_elementos_texto", $parametros) == false)
        {
            $ids_elementos_texto = array();
        }
        if (array_key_exists("ids_elementos_notas", $parametros) == false)
        {
            $ids_elementos_notas = array();
        }

        // Información de plantilla de informe
        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $nombre_plantilla_informe = $fila_plantilla_informe["nombre"];
        $tipo_plantilla_informe = $fila_plantilla_informe["tipo"];
        $tipo_seleccion_horario_semanal_fechas_plantilla_informe = $fila_plantilla_informe["tipo_seleccion_horario_semanal_fechas"];

        // Título del informe (para las cabeceras de las páginas)
        $titulo_informe = $fila_plantilla_informe["titulo_informe"];
        if ($titulo_informe == "")
        {
            $titulo_informe = $nombre_plantilla_informe;
        }

        // Se recuperan los elementos de la plantilla de informe
        $consulta_elementos = "
            SELECT *
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
            ORDER BY posicion ASC";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        // Se recorren y guardan las filas de los elementos
        $filas_elementos = array();
        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            array_push($filas_elementos, $fila_elemento);
        }

        // Si no hay elementos no se hace nada
        if (count($filas_elementos) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_elementos" => false);
            return ($resultado);
        }

        // Se recorren los elementos y se recuperan si son visibles
        $filas_elementos_visibles = array();
        $numero_elementos_visibles = 0;
        foreach ($filas_elementos as $fila_elemento)
        {
            // Modo de visibilidad e identificadores de los parámetros requeridos del elemento
            $modo_visibilidad_elemento = $fila_elemento["modo_visibilidad"];
            $cadena_ids_parametros_requeridos_elemento = $fila_elemento["parametros_requeridos"];
            if ($cadena_ids_parametros_requeridos_elemento == "")
            {
                $ids_parametros_requeridos_elemento = array();
            }
            else
            {
                $ids_parametros_requeridos_elemento = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_requeridos_elemento);
            }

            // Se recupera si el elemento es visible
            $elemento_visible = dame_elemento_plantilla_informe_visible(
                $modo_visibilidad_elemento,
                $ids_parametros_requeridos_elemento,
                $ids_parametros,
                $valores_parametros);
            if ($elemento_visible == true)
            {
                array_push($filas_elementos_visibles, $fila_elemento);
                $numero_elementos_visibles += 1;
            }
        }

        // Si no hay elementos visibles no se hace nada
        if ($numero_elementos_visibles == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_elementos_visibles" => false);
            return ($resultado);
        }

        // Filas de valores de sensores (para no repetir consultas)
        $filas_valores_sensores = array();

        // Se recorren los elementos visibles y se recuperan los datos de cada uno de ellos
        $html_elementos = "";
        $info_elementos = array();
        $datos_elementos = array();
        $claves_datos_elementos = array();
        $elementos_informes_elementos = array();
        $numero_elemento = 1;
        $numero_pagina = 1;
        for ($i = 0; $i < count($filas_elementos_visibles); $i++)
        {
            $fila_elemento = $filas_elementos_visibles[$i];

            $id_elemento = $fila_elemento["id"];
            $nombre_elemento = $fila_elemento["nombre"];
            $tipo_elemento = $fila_elemento["tipo"];
            $cadena_parametros_tipo_elemento = $fila_elemento["parametros_tipo"];
            $cadena_parametros_tipo_json_elemento = $fila_elemento["parametros_tipo_json"];
            $elementos_informe_elemento = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_elemento["elementos_informe"]);

            // Tipos de elemento anterior y siguiente
            $tipo_elemento_anterior = NULL;
            $tipo_elemento_siguiente = NULL;
            if ($i > 0)
            {
                $fila_elemento_anterior = $filas_elementos_visibles[$i - 1];
                $tipo_elemento_anterior = $fila_elemento_anterior["tipo"];
            }
            if ($i > 1)
            {
                $fila_elemento_anterior_anterior = $filas_elementos_visibles[$i - 2];
                $tipo_elemento_anterior_anterior = $fila_elemento_anterior_anterior["tipo"];
            }
            if ($numero_elementos_visibles > ($i + 1))
            {
                $fila_elemento_siguiente = $filas_elementos_visibles[$i + 1];
                $tipo_elemento_siguiente = $fila_elemento_siguiente["tipo"];
            }

            // Se añaden los elementos de informes
            array_push($elementos_informes_elementos, $elementos_informe_elemento);

            // Se recuperan los parámetros del tipo de elemento
            $parametros_tipo_elemento = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                $tipo_elemento,
                $cadena_parametros_tipo_elemento,
                $cadena_parametros_tipo_json_elemento);

            // Si el tipo de selección de horario semanal y fechas es configurable y existen,
            // se sustituyen por los parámetros de la plantilla de informe
            if ($tipo_seleccion_horario_semanal_fechas_plantilla_informe == TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE)
            {
                if (array_key_exists("horario_semanal", $parametros_tipo_elemento) == true)
                {
                    $parametros_tipo_elemento["horario_semanal"] = $horario_semanal;
                }
                if (array_key_exists("exclusion_fechas", $parametros_tipo_elemento) == true)
                {
                    $parametros_tipo_elemento["exclusion_fechas"] = $exclusion_fechas;
                }
                if (array_key_exists("inclusion_fechas", $parametros_tipo_elemento) == true)
                {
                    $parametros_tipo_elemento["inclusion_fechas"] = $inclusion_fechas;
                }
            }

            // Información del elemento
            $info_elemento = array(
                "numero_elemento" => $numero_elemento,
                "tipo" => $tipo_elemento,
                "parametros_tipo" => $parametros_tipo_elemento);
            array_push($info_elementos, $info_elemento);

            // Se recupera el código HTML del elemento
            $html_elemento = dame_html_elemento_plantilla_informe(
                $titulo_informe,
                $numero_pagina,
                $id_elemento,
                $numero_elemento,
                $numero_elementos_visibles,
                $nombre_elemento,
                $tipo_elemento,
                $parametros_tipo_elemento,
                $tipo_informe,
                $tipo_elemento_anterior,
                $tipo_elemento_anterior_anterior,
                $tipo_elemento_siguiente);
            $html_elementos .= $html_elemento;

            // Se recuperan los datos del elemento
            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
                {
                    // Si el elemento no es un informe, la clave es el identificador del elemento (única)
                    // (de esta forma siempre se recuperan los datos de estos elementos)
                    $clave_datos_elemento = $id_elemento;
                    break;
                }
                default:
                {
                    // Si el elemento es un informe, si el tipo de informe y los parámetros son los mismos
                    // no se recuperan de nuevo los datos de este informe si ya se han recuperado antes
                    // (aunque sean diferentes elementos, los datos serán los mismos)
                    $clave_datos_elemento = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                        $tipo_elemento,
                        $cadena_parametros_tipo_elemento,
                        $cadena_parametros_tipo_json_elemento));
                    break;
                }
            }
            if (array_key_exists($clave_datos_elemento, $datos_elementos) == false)
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe(
                    $tipo_plantilla_informe,
                    $id_elemento,
                    $numero_elemento,
                    $tipo_elemento,
                    $parametros_tipo_elemento,
                    $ids_parametros,
                    $valores_parametros,
                    $ids_elementos_portada,
                    $subtitulos_elementos_portada,
                    $ids_elementos_titulo,
                    $titulos_elementos_titulo,
                    $ids_elementos_texto,
                    $textos_elementos_texto,
                    $ids_elementos_notas,
                    $textos_elementos_notas,
                    $ids_elementos_imagen,
                    $rutas_ficheros_imagenes_elementos_imagen,
                    $cadena_fecha_hora_inicio_local_local,
                    $cadena_fecha_hora_fin_local_local,
                    $minutos_desfase_utc,
                    $tipo_informe,
                    $filas_valores_sensores);
                $datos_elementos[$clave_datos_elemento] = $datos_elemento;
            }
            array_push($claves_datos_elementos, $clave_datos_elemento);
            $numero_elemento += 1;
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_elementos" => true,
            "hay_elementos_visibles" => true,
            "info_elementos" => $info_elementos,
            "html_elementos" => $html_elementos,
            "datos_elementos" => $datos_elementos,
            "claves_datos_elementos" => $claves_datos_elementos,
            "elementos_informes_elementos" => $elementos_informes_elementos);
        return ($resultado);
    }


    function dame_elemento_plantilla_informe_visible(
        $modo_visibilidad_elemento,
        $ids_parametros_requeridos_elemento,
        $ids_parametros,
        $valores_parametros)
    {
        // Si no hay parámetros requeridos, el elemento es siempre visible
        if (count($ids_parametros_requeridos_elemento) == 0)
        {
            return (true);
        }

        // Si algún parámetro requerido no está seleccionado, no se muestra el elemento
        switch ($modo_visibilidad_elemento)
        {
            case MODO_VISIBILIDAD_ELEMENTO_CUALQUIER_PARAMETRO:
            {
                $elemento_visible = false;
                break;
            }
            case MODO_VISIBILIDAD_ELEMENTO_TODOS_PARAMETROS:
            {
                $elemento_visible = true;
                break;
            }
        }
        foreach ($ids_parametros_requeridos_elemento as $id_parametro_requerido_elemento)
        {
            $indice_parametro = array_search($id_parametro_requerido_elemento, $ids_parametros);
            $valor_parametro = $valores_parametros[$indice_parametro];
            $salir_bucle = false;
            switch ($modo_visibilidad_elemento)
            {
                case MODO_VISIBILIDAD_ELEMENTO_CUALQUIER_PARAMETRO:
                {
                    if ($valor_parametro != ID_NINGUNO)
                    {
                        $elemento_visible = true;
                        $salir_bucle = true;
                    }
                    break;
                }
                case MODO_VISIBILIDAD_ELEMENTO_TODOS_PARAMETROS:
                {
                    if ($valor_parametro == ID_NINGUNO)
                    {
                        $elemento_visible = false;
                        $salir_bucle = true;
                    }
                    break;
                }
            }
            if ($salir_bucle == true)
            {
                break;
            }
        }
        return ($elemento_visible);
    }


    //
    // Funciones para recuperar los datos para el informe de plantilla de informe
    //


    function dame_html_elemento_plantilla_informe(
        $titulo_informe,
        &$numero_pagina,
        $id_elemento,
        $numero_elemento,
        $numero_elementos,
        $nombre_elemento,
        $tipo_elemento,
        $parametros_tipo_elemento,
        $tipo_informe,
        $tipo_elemento_anterior,
        $tipo_elemento_anterior_anterior,
        $tipo_elemento_siguiente)
    {
        $idiomas = new Idiomas();

        // Se crean los datos del elemento
        $html_elemento = "";
        if (($tipo_informe == TIPO_INFORME_FICHERO) && ($numero_elemento == 1))
        {
            $html_elemento .= "<div class='pagina-informe-fichero' id='pagina-informe-fichero-plantilla-informe-".$numero_pagina."'>";
            $html_elemento .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, $titulo_informe);
        }
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_elemento)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_FICHERO:
                    {
                        $ignorar_salto_pagina = false;
                        if (($tipo_elemento_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA) ||
                            ($tipo_elemento_anterior_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA) && ($tipo_elemento_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN))
                        {
                            $ignorar_salto_pagina = true;
                        }
                        if ($ignorar_salto_pagina == false)
                        {
                            $html_elemento .= "
                                        <div class='fin-pagina-informe-fichero'></div>
                                    </div>";
                            $numero_pagina += 1;
                            if ($numero_elemento < $numero_elementos)
                            {
                                $html_elemento .= "
                                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-plantilla-informe-".$numero_pagina."'>";
                                $html_elemento .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, $titulo_informe);
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "<br/>";
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-portada-informe' class='titulo-informe'>".$idiomas->_("Portada")."</div>
                            </div>
                            <div class='texto-grande-portada-informe' id='".$prefijo_elemento."titulo-portada'></div>
                            <div class='texto-mediano-portada-informe' id='".$prefijo_elemento."subtitulo-portada'></div>
                            <div class='texto-pequenyo-portada-informe' id='".$prefijo_elemento."fechas-portada'></div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        if ($tipo_elemento_siguiente != TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN)
                        {
                            $clase_titulo_portada = "texto-grande-portada-informe-informe-fichero-con-salto-pagina";
                        }
                        else
                        {
                            $clase_titulo_portada = "texto-grande-portada-informe-informe-fichero-sin-salto-pagina";
                        }
                        if ($numero_elemento > 1)
                        {
                            $html_elemento .= "
                                    <div class='fin-pagina-informe-fichero'></div>
                                </div>";
                            $numero_pagina += 1;
                            $html_elemento .= "
                                <div class='pagina-informe-fichero' id='pagina-informe-fichero-plantilla-informe-".$numero_pagina."'>";
                            $html_elemento .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, $titulo_informe);
                        }
                        $html_elemento .= "
                                    <div class='".$clase_titulo_portada."' id='".$prefijo_elemento."titulo-portada'></div>
                                    <div class='texto-mediano-portada-informe-informe-fichero' id='".$prefijo_elemento."subtitulo-portada'></div>
                                    <div class='texto-pequenyo-portada-informe-informe-fichero' id='".$prefijo_elemento."fechas-portada'></div>";
                        if ($tipo_elemento_siguiente != TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN)
                        {
                            $html_elemento .= "
                                    <div class='fin-pagina-informe-fichero'></div>
                                </div>";
                            $numero_pagina += 1;
                            if ($numero_elemento < $numero_elementos)
                            {
                                $html_elemento .= "
                                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-plantilla-informe-".$numero_pagina."'>";
                                $html_elemento .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, $titulo_informe);
                            }
                        }
                        else
                        {
                            // Nota: Si no hay salto de página, se añade espacio de separación
                            $html_elemento .= "<br/><br/>";
                        }
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='contenedor-titulo-informe'>
                                <div class='titulo-informe' id='".$prefijo_elemento."titulo-titulo'></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "<div class='titulo-informe-fichero' id='".$prefijo_elemento."titulo-titulo'></div>";
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $titulo = $parametros_tipo_elemento["titulo"];
                        if (trim($titulo) == "")
                        {
                            $titulo = $idiomas->_("Texto");
                        }
                        $html_elemento .= "
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".htmlspecialchars($titulo, ENT_QUOTES).": "."</span><br/>
                                <textarea class='area-texto-informe' id='".$prefijo_elemento."texto-texto' rows='1' readonly=''></textarea>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "<div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."texto-texto'></div>";
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $titulo = $parametros_tipo_elemento["titulo"];
                        if (trim($titulo) == "")
                        {
                            $titulo = $idiomas->_("Notas");
                        }
                        $numero_caracteres_actuales = 0;
                        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_NOTAS;
                        $html_elemento .= "
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".htmlspecialchars($titulo, ENT_QUOTES).": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                                    "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                                <textarea class='area-texto-informe texto-elemento-notas-plantilla-informe'
                                    id='".$prefijo_elemento."texto-notas' id_elemento_plantilla_informe='".$id_elemento."' rows='1'></textarea>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "<div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."texto-notas'></div>";
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                $titulo = $parametros_tipo_elemento["titulo"];
                $altura_maxima = $parametros_tipo_elemento["altura_maxima"];
                if ($altura_maxima == "")
                {
                    $altura_maxima = ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_DEFECTO;
                    if ($tipo_elemento_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA)
                    {
                        if (($tipo_elemento_siguiente == TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA) ||
                            ($tipo_elemento_siguiente === NULL))
                        {
                            $altura_maxima = ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_PAGINA_COMPLETA;
                        }
                    }
                }
                else
                {
                    if ($tipo_elemento_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA)
                    {
                        if ($altura_maxima > ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN_PORTADA)
                        {
                            $altura_maxima = ALTURA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN_PORTADA;
                        }
                    }
                }
                $html_elemento .= "
                    <div class='contenedor-imagen-informe'>
                        <a style='text-decoration: none;'>
                            <img class='imagen-informe' style='max-height: ".$altura_maxima."px;' src='' id='".$prefijo_elemento."imagen'>
                            <div style='text-align: center;'>".htmlspecialchars($titulo, ENT_QUOTES)."</div>
                        </a>
                    </div>";
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_FICHERO:
                    {
                        if ($tipo_elemento_anterior == TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA)
                        {
                            $html_elemento .= "
                                    <div class='fin-pagina-informe-fichero'></div>
                                </div>";
                            $numero_pagina += 1;
                            if ($numero_elemento < $numero_elementos)
                            {
                                $html_elemento .= "
                                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-plantilla-informe-".$numero_pagina."'>";
                                $html_elemento .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, $titulo_informe);
                            }
                        }
                        break;
                    }
                }
                break;
            }
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_comentarios(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_activaciones_eventos(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_informacion(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_analisis_horario(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_analisis_diario(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_periodos(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_analisis_comparativo(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_valores_generales(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_incrementos_totales(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_histograma(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_sensores_correlacion(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Compra de energía)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_proyectos_simulador_linea_base(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $html_elemento .= dame_html_elemento_plantilla_informe_tipo_proyectos_informacion_proyecto(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("Tipo de elemento desconocido: '".$tipo_elemento."'");
            }
        }
        switch ($tipo_elemento)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
            {
                break;
            }
            default:
            {
                $html_elemento .= "<div class='fin-elemento-plantilla-informe'></div>";
                break;
            }
        }
        if (($tipo_informe == TIPO_INFORME_FICHERO) && ($numero_elemento == $numero_elementos))
        {
            $html_elemento .= "
                    <div class='fin-pagina-informe-fichero'></div>
                </div>";
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe(
        $tipo_plantilla_informe,
        $id_elemento,
        $numero_elemento,
        $tipo_elemento,
        $parametros_tipo_elemento,
        $ids_parametros,
        $valores_parametros,
        $ids_elementos_portada,
        $subtitulos_elementos_portada,
        $ids_elementos_titulo,
        $titulos_elementos_titulo,
        $ids_elementos_texto,
        $textos_elementos_texto,
        $ids_elementos_notas,
        $textos_elementos_notas,
        $ids_elementos_imagen,
        $rutas_ficheros_imagenes_elementos_imagen,
        $cadena_fecha_hora_inicio_local_local,
        $cadena_fecha_hora_fin_local_local,
        $minutos_desfase_utc,
        $tipo_informe,
        &$filas_valores_sensores)
    {
        // Se añaden parámetros utilizados en la obtención de datos de la mayoría de informes
        $parametros_informe = array();
        $parametros_informe["fecha_hora_inicio"] = $cadena_fecha_hora_inicio_local_local;
        $parametros_informe["fecha_hora_fin"] = $cadena_fecha_hora_fin_local_local;
        $parametros_informe["minutos_desfase_utc"] = $minutos_desfase_utc;
        $parametros_informe["tipo_informe"] = $tipo_informe;

        // Fecha de inicio e información de periodos
        $hay_parametros_periodo_tiempo = false;
        switch ($tipo_elemento)
        {
            // Fecha de inicio
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $cadena_fecha_hora_inicio_local_local = dame_fecha_inicio_parametros_periodo_tiempo(
                    $cadena_fecha_hora_inicio_local_local,
                    $cadena_fecha_hora_fin_local_local,
                    $parametros_tipo_elemento);
                $parametros_informe["fecha_hora_inicio"] = $cadena_fecha_hora_inicio_local_local;
                $hay_parametros_periodo_tiempo = true;
                break;
            }
            // Información de periodos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $info_periodos = dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
                    $cadena_fecha_hora_inicio_local_local,
                    $cadena_fecha_hora_fin_local_local,
                    $parametros_tipo_elemento);
                $parametros_informe["fecha_hora_inicio_anterior"] = $info_periodos["cadena_fecha_hora_inicio_anterior_local_local"];
                $parametros_informe["fecha_hora_inicio_posterior"] = $info_periodos["cadena_fecha_hora_inicio_posterior_local_local"];
                $parametros_informe["fecha_inicio_anterior"]= $info_periodos["cadena_fecha_inicio_anterior_local_local"];
                $parametros_informe["fecha_inicio_posterior"] = $info_periodos["cadena_fecha_inicio_posterior_local_local"];
                $parametros_informe["numero_dias_periodo"] = $info_periodos["numero_dias_periodo"];
                $parametros_informe["numero_dias_periodo_anterior"] = $info_periodos["numero_dias_periodo_anterior"];
                break;
            }
        }

        // Se recuperan los datos del elemento
        $datos_elemento = array();
        switch ($tipo_elemento)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
            {
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
            {
                // Se crea la portada (con el subtítulo especificado en el informe si existe)
                $datos_elemento["titulo"] = htmlspecialchars($parametros_tipo_elemento["titulo"], ENT_QUOTES);
                switch ($tipo_plantilla_informe)
                {
                    case TIPO_PLANTILLA_INFORME_FIJO:
                    {
                        $subtitulo_portada = $parametros_tipo_elemento["subtitulo"];
                    }
                    case TIPO_PLANTILLA_INFORME_CONFIGURABLE:
                    {
                        // Nota: Si no se encuentra el identificador del elemento, se recupera el subtítulo de la portada del elemento
                        $indice_elemento_portada = array_search($id_elemento, $ids_elementos_portada);
                        if ($indice_elemento_portada !== false)
                        {
                            $subtitulo_portada = $subtitulos_elementos_portada[$indice_elemento_portada];
                        }
                        else
                        {
                            $subtitulo_portada = $parametros_tipo_elemento["subtitulo"];
                        }
                        break;
                    }
                }
                $datos_elemento["subtitulo"] = htmlspecialchars($subtitulo_portada, ENT_QUOTES);
                $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
                $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
                $cadena_fechas = $cadena_fecha_inicio_local_local." - ".$cadena_fecha_fin_local_local;
                $datos_elemento["cadena_fechas"] = $cadena_fechas;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
            {
                switch ($tipo_plantilla_informe)
                {
                    case TIPO_PLANTILLA_INFORME_FIJO:
                    {
                        $titulo = $parametros_tipo_elemento["titulo"];
                    }
                    case TIPO_PLANTILLA_INFORME_CONFIGURABLE:
                    {
                        // Nota: Si no se encuentra el identificador del elemento, se recupera el título del elemento
                        $indice_elemento_titulo = array_search($id_elemento, $ids_elementos_titulo);
                        if ($indice_elemento_titulo !== false)
                        {
                            $titulo = $titulos_elementos_titulo[$indice_elemento_titulo];
                        }
                        else
                        {
                            $titulo = $parametros_tipo_elemento["titulo"];
                        }
                        break;
                    }
                }
                $datos_elemento["titulo"] = htmlspecialchars($titulo, ENT_QUOTES);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
            {
                $indice_elemento_texto = array_search($id_elemento, $ids_elementos_texto);
                $texto = $textos_elementos_texto[$indice_elemento_texto];
                $datos_elemento["texto"] = $texto;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $datos_elemento["texto"] = "";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $indice_elemento_notas = array_search($id_elemento, $ids_elementos_notas);
                        if (($indice_elemento_notas !== false) && ($indice_elemento_notas !== NULL))
                        {
                            $texto_elemento_notas = $textos_elementos_notas[$indice_elemento_notas];
                        }
                        else
                        {
                            $texto_elemento_notas = "";
                        }
                        $datos_elemento["texto"] = $texto_elemento_notas;
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                $indice_elemento_imagen = array_search($id_elemento, $ids_elementos_imagen);
                if (($indice_elemento_imagen !== false) && ($indice_elemento_imagen !== NULL))
                {
                    $ruta_fichero_imagen = $rutas_ficheros_imagenes_elementos_imagen[$indice_elemento_imagen];
                }
                else
                {
                    $ruta_fichero_imagen = "";
                }
                $datos_elemento["ruta_fichero_imagen"] = $ruta_fichero_imagen;
                break;
            }
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                // Establece los identificadores de los sensores, actuadores y grupos de actuadores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);
                establece_ids_actuadores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_actuadores",
                    NULL,
                    "ids_actuadores",
                    $ids_parametros,
                    $valores_parametros);
                establece_ids_actuadores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_grupos_actuadores",
                    NULL,
                    "ids_grupos_actuadores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_comentarios(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                // Establece el identificador del sensor (o grupo de sensores) (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_origen_evento",
                    "id_origen_evento",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_informacion(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe,
                    $filas_valores_sensores);
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_horario(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_diario(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_periodos(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                // Establece el identificador del sensor principal (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor_principal",
                    "id_sensor_principal",
                    $ids_parametros,
                    $valores_parametros);

                // Establece los identificadores de los sensores secundarios (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores_secundarios",
                    NULL,
                    "ids_sensores_secundarios",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    NULL,
                    "tipos_seleccion_sensores",
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                // Establece los identificadores de los sensores agregados (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores_agregados",
                    NULL,
                    "ids_sensores_agregados",
                    $ids_parametros,
                    $valores_parametros);

                // Establece el identificador del sensor destacado (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor_destacado",
                    "id_sensor_destacado",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_comparativo(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_valores_generales(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_incrementos_totales(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_histograma(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    NULL,
                    "tipos_seleccion_sensores_independientes",
                    "ids_sensores_independientes",
                    $ids_parametros,
                    $valores_parametros);
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor_dependiente",
                    "id_sensor_dependiente",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_sensores_correlacion(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                // Establece el identificador del actuador (o grupo de actuadores) (si es necesario)
                establece_id_actuador_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_destino_accion",
                    "id_destino_accion",
                    $ids_parametros,
                    $valores_parametros);

                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    NULL,
                    "ids_sensores",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Nota: Puede ocurrir que en los parámetros de tipo haya tarifas que ya no son visibles por el usuario
                // (pero si lo eran al añadir el elemento de la plantilla de informe)
                // (no se eliminan automáticamente)

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de SmartMeter (Compra de energía)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe,
                    $filas_valores_sensores);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores",
                    "id_sensor_hijo",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                // Establece los identificadores de los sensores (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);
                establece_ids_sensores_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensores_reparto_costes",
                    NULL,
                    "ids_sensores_reparto_costes",
                    $ids_parametros,
                    $valores_parametros);


                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                // Establece el identificador del sensor (si es necesario)
                establece_id_sensor_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_sensor",
                    "id_sensor",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                // Establece el identificador del proyecto (si es necesario)
                establece_id_linea_base_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_linea_base",
                    "id_linea_base",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_proyectos_simulador_linea_base(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                // Establece el identificador del proyecto (si es necesario)
                establece_id_proyecto_elemento_informe_plantilla_informe(
                    $parametros_tipo_elemento,
                    "tipo_seleccion_proyecto",
                    "id_proyecto",
                    $ids_parametros,
                    $valores_parametros);

                // Obtención de datos del elemento
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_proyectos_informacion_proyecto(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            default:
            {
                throw new Exception("Tipo de elemento desconocido: '".$tipo_elemento."'");
            }
        }

        // Si hay parámetros de periodo de tiempo se guardan la fecha y hora de inicio
        // (para dibujar las gráficas desde la fecha de inicio 'real' - se puede haber modificado por parámetros de periodo de tiempo)
        // Nota: No es necesario en comparación de periodos porque en los datos del elemento ya se guardan las fechas de inicio y fin
        if ($hay_parametros_periodo_tiempo == true)
        {
            $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], "H:i:s");
            $datos_elemento["fecha_inicio"] = $cadena_fecha_inicio_local_local;
            $datos_elemento["hora_inicio"] = $cadena_hora_inicio_local_local;
        }
        return ($datos_elemento);
    }


    function dame_filas_valores_sensores_elemento_plantilla_informe($parametros_informe, &$filas_valores_sensores)
    {
        $id_sensor = $parametros_informe["id_sensor"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                break;
            }
        }
        $cadena_fecha_hora_inicio_local_local = $parametros_informe["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros_informe["fecha_hora_fin"];
        $clave_filas_valores_sensor = $id_sensor."-".$intervalo_valores."-".$cadena_fecha_hora_inicio_local_local."-".$cadena_fecha_hora_fin_local_local;
        if (array_key_exists($clave_filas_valores_sensor, $filas_valores_sensores) == false)
        {
            $filas_valores_sensor = dame_filas_valores_sensor($parametros_informe);
            $filas_valores_sensores[$clave_filas_valores_sensor] = $filas_valores_sensor;
        }
        else
        {
            $filas_valores_sensor = $filas_valores_sensores[$clave_filas_valores_sensor];
        }
        return ($filas_valores_sensor);
    }


    //
    // Funciones de plantillas de informes configurables
    //


    function dame_controles_parametros_informe_plantilla_informe($id_plantilla_informe)
    {
        $html = "";
        if (($id_plantilla_informe != ID_NINGUNO) &&
            (dame_tipo_plantilla_informe($id_plantilla_informe) == TIPO_PLANTILLA_INFORME_CONFIGURABLE))
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Controles de los parámetros configurables
            $consulta_parametros = "
                SELECT
                    id,
                    nombre,
                    tipo,
                    parametros_tipo
                FROM parametros_plantillas_informes
                WHERE
                    plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
                ORDER BY posicion ASC";
            $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
            if ($res_parametros == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_parametros."'");
            }

            // Filas de los parámetros
            $filas_parametros = array();
            while ($fila_parametro = $res_parametros->dame_siguiente_fila())
            {
                array_push($filas_parametros, $fila_parametro);
            }

            // Se añade el parámetro y se guardan los parámetros asociados a cada uno de los parámetros
            // (para no buscar parámetros asociados al seleccionar el valor si no tiene)
            $ids_parametros_asociados_parametros = array();
            foreach ($filas_parametros as $fila_parametro)
            {
                $id_parametro = $fila_parametro["id"];
                $tipo_parametro = $fila_parametro["tipo"];
                $cadena_parametros_tipo = $fila_parametro["parametros_tipo"];
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

                switch ($tipo_parametro)
                {
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                    {
                        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                        $id_parametro_sensor_asociado = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_ID_PARAMETRO_SENSOR_ASOCIADO];
                        if ($id_parametro_sensor_asociado != ID_NINGUNO)
                        {
                            if (array_key_exists($id_parametro_sensor_asociado, $ids_parametros_asociados_parametros) == false)
                            {
                                $ids_parametros_asociados_parametros[$id_parametro_sensor_asociado] = array();
                            }
                            array_push($ids_parametros_asociados_parametros[$id_parametro_sensor_asociado], $id_parametro);
                        }
                        break;
                    }
                    default:
                    {
                        continue;
                    }
                }
            }

            // Se añaden los controles de los parámetros
            $ids_parametros = array();
            foreach ($filas_parametros as $fila_parametro)
            {
                $id_parametro = $fila_parametro["id"];
                $nombre_parametro = $fila_parametro["nombre"];
                $tipo_parametro = $fila_parametro["tipo"];
                $cadena_parametros_tipo = $fila_parametro["parametros_tipo"];
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

                // Parámetros asociados
                if (array_key_exists($id_parametro, $ids_parametros_asociados_parametros) == true)
                {
                    $cadena_ids_parametros_asociados = implode(",", $ids_parametros_asociados_parametros[$id_parametro]);
                }
                else
                {
                    $cadena_ids_parametros_asociados = "";
                }

                // Se crea el control del parámetro
                $control_parametro = "
                    <div class='fila-tabla-datos'>
                        <div class='desplegable-simple' style='width: ".ANCHURA_PARAMETRO_PLANTILLA_INFORME."%;'>
                            <div id='etiqueta_parametro_plantilla_informe_".$id_parametro."'>".htmlspecialchars($nombre_parametro, ENT_QUOTES).": "."</div>
                            <select id='valor_parametro_plantilla_informe_".$id_parametro."'
                                tipo='".$tipo_parametro."'
                                parametros_tipo='".$cadena_parametros_tipo."'
                                ids_parametros_asociados='".$cadena_ids_parametros_asociados."'
                                class='chosen-select' hidden>";
                switch ($tipo_parametro)
                {
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                    {
                        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                        $control_parametro .= dame_lista_sensores($clase_sensor, array(), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                        break;
                    }
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
                    {
                        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES_CLASE_SENSOR];
                        $control_parametro .= dame_lista_grupos_sensores($clase_sensor, array(), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                        break;
                    }
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
                    {
                        $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR_CLASE_ACTUADOR];
                        $control_parametro .= dame_lista_actuadores($clase_actuador, array(), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                        break;
                    }
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                    {
                        $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES_CLASE_ACTUADOR];
                        $control_parametro .= dame_lista_grupos_actuadores($clase_actuador, array(), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                        break;
                    }
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE:
                    {
                        $control_parametro .= dame_lista_lineas_base(ID_NINGUNO);
                        break;
                    }
                    case TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO:
                    {
                        $control_parametro .= dame_lista_proyectos(ID_NINGUNO);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de parámetro desconocido: '".$tipo_parametro."'");
                    }
                }
                $control_parametro .= "
                            </select>
                        </div>
                    </div>";
                $html .= $control_parametro;

                // Se añade el identificador del parámetro
                array_push($ids_parametros, $id_parametro);
            }

            // Si parámetros, se añade la información oculta
            if (count($ids_parametros) > 0)
            {
                $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                $html .= '
                    <div id="parametros_informe_plantilla_informe"
                        ids_parametros="'.$cadena_ids_parametros.'"
                        hidden>
                    </div>';
            }
        }
        return ($html);
    }


    function dame_controles_subtitulos_portadas_informe_plantilla_informe($id_plantilla_informe)
    {
        $html = "";
        if (($id_plantilla_informe != ID_NINGUNO) &&
            (dame_tipo_plantilla_informe($id_plantilla_informe) == TIPO_PLANTILLA_INFORME_CONFIGURABLE))
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Controles de los subtítulos de portadas
            $consulta_elementos_portadas = "
                SELECT
                    id,
                    parametros_tipo_json
                FROM elementos_plantillas_informes
                WHERE
                    (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                    AND (tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA."')
                ORDER BY posicion ASC";
            $res_elementos_portadas = $bd_red->ejecuta_consulta($consulta_elementos_portadas);
            if ($res_elementos_portadas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_portadas."'");
            }

            // Si hay elementos de portada
            if ($res_elementos_portadas->dame_numero_filas() > 0)
            {
                $html .= "
                    <div id='subtitulos-portadas-personal-plantilla-informe' class='controles-textos-informe'>";
                $ids_elementos_portada = array();
                while ($fila_elemento_portada = $res_elementos_portadas->dame_siguiente_fila())
                {
                    $id_elemento_portada = $fila_elemento_portada["id"];
                    $cadena_parametros_tipo_json = $fila_elemento_portada["parametros_tipo_json"];

                    // Parámetros de tipo
                    $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                        TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA,
                        NULL,
                        $cadena_parametros_tipo_json);
                    $titulo = $parametros_tipo["titulo"];
                    $subtitulo = $parametros_tipo["subtitulo"];

                    $html .= "
                        <div class='fila-tabla-datos'>
                            <div class='filtro-informes' style='width: ".ANCHURA_SUBTITULO_PORTADA_PLANTILLA_INFORME."%;'>
                                <div>".htmlspecialchars($titulo, ENT_QUOTES).": "."</div>
                                <input type='text' id='subtitulo_portada_plantilla_informe_".$id_elemento_portada."'
                                    class='subtitulo-portada-plantilla-informe'
                                    value='".htmlspecialchars($subtitulo, ENT_QUOTES)."'>
                            </div>
                        </div>";

                    // Se añade el identificador del elemento portada
                    array_push($ids_elementos_portada, $id_elemento_portada);
                }
                $html .= "
                    </div>";

                // Se añade la información oculta
                $cadena_ids_elementos_portada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_portada);
                $html .= '
                    <div id="subtitulos_portadas_informe_plantilla_informe"
                        ids_elementos_portada="'.$cadena_ids_elementos_portada.'"
                        hidden>
                    </div>';
            }
        }
        return ($html);
    }


    function dame_controles_titulos_informe_plantilla_informe($id_plantilla_informe)
    {
        $html = "";
        if (($id_plantilla_informe != ID_NINGUNO) &&
            (dame_tipo_plantilla_informe($id_plantilla_informe) == TIPO_PLANTILLA_INFORME_CONFIGURABLE))
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Controles de los título
            $consulta_elementos_titulos = "
                SELECT
                    id,
                    nombre,
                    parametros_tipo_json
                FROM elementos_plantillas_informes
                WHERE
                    (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                    AND (tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO."')
                ORDER BY posicion ASC";
            $res_elementos_titulos = $bd_red->ejecuta_consulta($consulta_elementos_titulos);
            if ($res_elementos_titulos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_titulos."'");
            }

            // Si hay elementos de título
            if ($res_elementos_titulos->dame_numero_filas() > 0)
            {
                $html .= "
                    <div id='titulos-personal-plantilla-informe' class='controles-textos-informe'>";
                $ids_elementos_titulo = array();
                while ($fila_elemento_titulo = $res_elementos_titulos->dame_siguiente_fila())
                {
                    $id_elemento_titulo = $fila_elemento_titulo["id"];
                    $cadena_parametros_tipo_json = $fila_elemento_titulo["parametros_tipo_json"];

                    // Parámetros de tipo
                    $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                        TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO,
                        NULL,
                        $cadena_parametros_tipo_json);
                    $titulo = $parametros_tipo["titulo"];

                    $html .= "
                        <div class='fila-tabla-datos'>
                            <div class='filtro-informes' style='width: ".ANCHURA_TITULO_PLANTILLA_INFORME."%;'>
                                <div>".htmlspecialchars($titulo, ENT_QUOTES).": "."</div>
                                <input type='text' id='titulo_plantilla_informe_".$id_elemento_titulo."'
                                    class='titulo-plantilla-informe'
                                    value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                            </div>
                        </div>";

                    // Se añade el identificador del elemento título
                    array_push($ids_elementos_titulo, $id_elemento_titulo);
                }
                $html .= "
                    </div>";

                // Se añade la información oculta
                $cadena_ids_elementos_titulo = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_titulo);
                $html .= '
                    <div id="titulos_informe_plantilla_informe"
                        ids_elementos_titulo="'.$cadena_ids_elementos_titulo.'"
                        hidden>
                    </div>';
            }
        }
        return ($html);
    }


    function establece_id_sensor_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_sensor,
        $nombre_parametro_id_sensor,
        $ids_parametros,
        $valores_parametros)
    {
        if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_sensor] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            $id_sensor_parametro = $parametros_tipo_elemento[$nombre_parametro_id_sensor];
            $indice_parametro = array_search($id_sensor_parametro, $ids_parametros);
            if (($indice_parametro === false) || ($indice_parametro === NULL))
            {
                $valor_parametro = $id_sensor_parametro;
            }
            else
            {
                $valor_parametro = $valores_parametros[$indice_parametro];
            }
            $parametros_tipo_elemento[$nombre_parametro_id_sensor] = $valor_parametro;
        }
    }


    function establece_ids_sensores_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_sensores,
        $nombre_parametro_tipos_seleccion_sensores,
        $nombre_parametro_ids_sensores,
        $ids_parametros,
        $valores_parametros)
    {
        if ($nombre_parametro_tipo_seleccion_sensores !== NULL)
        {
            if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_sensores] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
            {
                $ids_sensores_parametros = $parametros_tipo_elemento[$nombre_parametro_ids_sensores];
                $ids_sensores = array();
                foreach ($ids_sensores_parametros as $id_sensor_parametro)
                {
                    $indice_parametro = array_search($id_sensor_parametro, $ids_parametros);
                    if (($indice_parametro === false) || ($indice_parametro === NULL))
                    {
                        $valor_parametro = $id_sensor_parametro;
                    }
                    else
                    {
                        $valor_parametro = $valores_parametros[$indice_parametro];
                    }
                    if ($valor_parametro != ID_NINGUNO)
                    {
                        array_push($ids_sensores, $valor_parametro);
                    }
                }
                $parametros_tipo_elemento[$nombre_parametro_ids_sensores] = $ids_sensores;
            }
        }

        if ($nombre_parametro_tipos_seleccion_sensores !== NULL)
        {
            $tipos_seleccion_sensores = $parametros_tipo_elemento[$nombre_parametro_tipos_seleccion_sensores];
            $ids_sensores_parametros = $parametros_tipo_elemento[$nombre_parametro_ids_sensores];
            $ids_sensores = $ids_sensores_parametros;
            for ($i = 0; $i < count($ids_sensores_parametros); $i++)
            {
                if ($tipos_seleccion_sensores[$i] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_sensor_parametro = $ids_sensores_parametros[$i];
                    $indice_parametro = array_search($id_sensor_parametro, $ids_parametros);
                    if (($indice_parametro === false) || ($indice_parametro === NULL))
                    {
                        $valor_parametro = $id_sensor_parametro;
                    }
                    else
                    {
                        $valor_parametro = $valores_parametros[$indice_parametro];
                    }
                    $ids_sensores[$i] = $valor_parametro;
                }
            }
            $parametros_tipo_elemento[$nombre_parametro_ids_sensores] = $ids_sensores;
        }
    }


    function establece_id_actuador_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_actuador,
        $nombre_parametro_id_actuador,
        $ids_parametros,
        $valores_parametros)
    {
        if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_actuador] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            $id_actuador_parametro = $parametros_tipo_elemento[$nombre_parametro_id_actuador];
            $indice_parametro = array_search($id_actuador_parametro, $ids_parametros);
            if (($indice_parametro === false) || ($indice_parametro === NULL))
            {
                $valor_parametro = $id_actuador_parametro;
            }
            else
            {
                $valor_parametro = $valores_parametros[$indice_parametro];
            }
            $valor_parametro = $valores_parametros[$indice_parametro];
            $parametros_tipo_elemento[$nombre_parametro_id_actuador] = $valor_parametro;
        }
    }


    function establece_ids_actuadores_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_actuadores,
        $nombre_parametro_tipos_seleccion_actuadores,
        $nombre_parametro_ids_actuadores,
        $ids_parametros,
        $valores_parametros)
    {
        if ($nombre_parametro_tipo_seleccion_actuadores !== NULL)
        {
            if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_actuadores] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
            {
                $ids_actuadores_parametros = $parametros_tipo_elemento[$nombre_parametro_ids_actuadores];
                $ids_actuadores = array();
                foreach ($ids_actuadores_parametros as $id_actuador_parametro)
                {
                    $indice_parametro = array_search($id_actuador_parametro, $ids_parametros);
                    if (($indice_parametro === false) || ($indice_parametro === NULL))
                    {
                        $valor_parametro = $id_actuador_parametro;
                    }
                    else
                    {
                        $valor_parametro = $valores_parametros[$indice_parametro];
                    }
                    if ($valor_parametro != ID_NINGUNO)
                    {
                        array_push($ids_actuadores, $valor_parametro);
                    }
                }
                $parametros_tipo_elemento[$nombre_parametro_ids_actuadores] = $ids_actuadores;
            }
        }

        if ($nombre_parametro_tipos_seleccion_actuadores !== NULL)
        {
            $tipos_seleccion_actuadores = $parametros_tipo_elemento[$nombre_parametro_tipos_seleccion_actuadores];
            $ids_actuadores_parametros = $parametros_tipo_elemento[$nombre_parametro_ids_actuadores];
            $ids_actuadores = $ids_actuadores_parametros;
            for ($i = 0; $i < count($ids_actuadores_parametros); $i++)
            {
                if ($tipos_seleccion_actuadores[$i] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_actuador_parametro = $ids_actuadores_parametros[$i];
                    $indice_parametro = array_search($id_actuador_parametro, $ids_parametros);
                    if (($indice_parametro === false) || ($indice_parametro === NULL))
                    {
                        $valor_parametro = $id_actuador_parametro;
                    }
                    else
                    {
                        $valor_parametro = $valores_parametros[$indice_parametro];
                    }
                    $ids_actuadores[$i] = $valor_parametro;
                }
            }
            $parametros_tipo_elemento[$nombre_parametro_ids_actuadores] = $ids_actuadores;
        }
    }


    function establece_id_linea_base_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_linea_base,
        $nombre_parametro_id_linea_base,
        $ids_parametros,
        $valores_parametros)
    {
        if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_linea_base] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            $id_linea_base_parametro = $parametros_tipo_elemento[$nombre_parametro_id_linea_base];
            $indice_parametro = array_search($id_linea_base_parametro, $ids_parametros);
            if (($indice_parametro === false) || ($indice_parametro === NULL))
            {
                $valor_parametro = $id_linea_base_parametro;
            }
            else
            {
                $valor_parametro = $valores_parametros[$indice_parametro];
            }
            $parametros_tipo_elemento[$nombre_parametro_id_linea_base] = $valor_parametro;
        }
    }


    function establece_id_proyecto_elemento_informe_plantilla_informe(
        &$parametros_tipo_elemento,
        $nombre_parametro_tipo_seleccion_proyecto,
        $nombre_parametro_id_proyecto,
        $ids_parametros,
        $valores_parametros)
    {
        if ($parametros_tipo_elemento[$nombre_parametro_tipo_seleccion_proyecto] == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            $id_proyecto_parametro = $parametros_tipo_elemento[$nombre_parametro_id_proyecto];
            $indice_parametro = array_search($id_proyecto_parametro, $ids_parametros);
            if (($indice_parametro === false) || ($indice_parametro === NULL))
            {
                $valor_parametro = $id_proyecto_parametro;
            }
            else
            {
                $valor_parametro = $valores_parametros[$indice_parametro];
            }
            $parametros_tipo_elemento[$nombre_parametro_id_proyecto] = $valor_parametro;
        }
    }


    //
    // Funciones de controles de plantillas de informes
    //


    function dame_controles_textos_informe_plantilla_informe($id_plantilla_informe)
    {
        $html = "";
        if ($id_plantilla_informe != ID_NINGUNO)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Controles de los textos
            $consulta_elementos_textos = "
                SELECT
                    id,
                    parametros_tipo_json
                FROM elementos_plantillas_informes
                WHERE
                    (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                    AND (tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO."')
                ORDER BY posicion ASC";
            $res_elementos_textos = $bd_red->ejecuta_consulta($consulta_elementos_textos);
            if ($res_elementos_textos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_textos."'");
            }

            // Si hay elementos de texto
            if ($res_elementos_textos->dame_numero_filas() > 0)
            {
                $html .= "
                    <div class='textos-informe'>
                        <div id='textos-personal-plantilla-informe' class='controles-textos-informe'>";
                $ids_elementos_texto = array();
                while ($fila_elemento_texto = $res_elementos_textos->dame_siguiente_fila())
                {
                    $id_elemento_texto = $fila_elemento_texto["id"];
                    $cadena_parametros_tipo_json = $fila_elemento_texto["parametros_tipo_json"];

                    // Parámetros de tipo
                    $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                        TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO,
                        NULL,
                        $cadena_parametros_tipo_json);
                    $titulo = $parametros_tipo["titulo"];
                    $texto = $parametros_tipo["texto"];

                    // Nota: El contenido del texto se pone en la misma línea porque si no coge los saltos de línea como contenido del propio texto ...
                    $numero_caracteres_actuales = dame_numero_caracteres($texto);
                    $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TEXTO;
                    $html .= "
                        <div class='contenedor-texto-informe-sin-margen-superior'>
                            <span>".htmlspecialchars($titulo, ENT_QUOTES).": "."</span>".
                            "<span class='contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                                "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                            <textarea class='area-texto-informe' id='texto_plantilla_informe_".$id_elemento_texto."' rows='1'>".htmlspecialchars($texto, ENT_QUOTES)."</textarea>
                        </div>";

                    // Se añade el identificador del elemento texto
                    array_push($ids_elementos_texto, $id_elemento_texto);
                }
                $html .= "
                        </div>
                    </div>";

                // Se añade la información oculta
                $cadena_ids_elementos_texto = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_texto);
                $html .= '
                    <div id="textos_informe_plantilla_informe"
                        ids_elementos_texto="'.$cadena_ids_elementos_texto.'"
                        hidden>
                    </div>';
            }
        }
        return ($html);
    }


    function dame_controles_imagenes_informe_plantilla_informe($id_plantilla_informe)
    {
        $idiomas = new Idiomas();

        $html = "";
        if ($id_plantilla_informe != ID_NINGUNO)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Controles de las imágenes
            $consulta_elementos_imagenes = "
                SELECT
                    id,
                    parametros_tipo_json
                FROM elementos_plantillas_informes
                WHERE
                    (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                    AND (tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN."')
                ORDER BY posicion ASC";
            $res_elementos_imagenes = $bd_red->ejecuta_consulta($consulta_elementos_imagenes);
            if ($res_elementos_imagenes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_imagenes."'");
            }

            // Si hay elementos de imagen
            if ($res_elementos_imagenes->dame_numero_filas() > 0)
            {
                $html .= "
                    <div class='imagenes-informe'>
                        <div id='imagenes-personal-plantilla-informe' class='controles-imagenes-informe'>";
                $ids_elementos_imagen = array();
                while ($fila_elemento_imagen = $res_elementos_imagenes->dame_siguiente_fila())
                {
                    $id_elemento_imagen = $fila_elemento_imagen["id"];
                    $cadena_parametros_tipo_json = $fila_elemento_imagen["parametros_tipo_json"];

                    // Parámetros de tipo
                    $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                        TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN,
                        NULL,
                        $cadena_parametros_tipo_json);
                    $titulo = $parametros_tipo["titulo"];
                    $nombre_imagen = $parametros_tipo["nombre_imagen"];

                    $html .= "
                        <div id='imagen_plantilla_informe_imagen__".$id_elemento_imagen."'>".htmlspecialchars($titulo, ENT_QUOTES).": "."</span><br/>
                            <input type='text' id='nombre_imagen_plantilla_informe_".$id_elemento_imagen."' value='".$nombre_imagen."'
                                class='nombre-fichero-imagen-plantilla-informe TLNT_input_valid_characters' readonly>
                            <input type='file' id='fichero_imagen_plantilla_informe_file_".$id_elemento_imagen."'>
                            <input type='text' id='fichero_imagen_plantilla_informe_text_".$id_elemento_imagen."'
                                class='nombre-fichero-imagen-plantilla-informe TLNT_input_valid_characters' readonly>
                            <button id='boton_imagen_plantilla_informe_seleccionar_fichero_imagen_".$id_elemento_imagen."' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>
                            <button id='boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_".$id_elemento_imagen."' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>".$idiomas->_("Deseleccionar fichero")."</button>
                        </div>";

                    // Se añade el identificador del elemento imagen
                    array_push($ids_elementos_imagen, $id_elemento_imagen);
                }
                $html .= "
                        </div>
                    </div>";

                // Se añade la información oculta
                $cadena_ids_elementos_imagen = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_imagen);
                $html .= '
                    <div id="imagenes_informe_plantilla_informe"
                        ids_elementos_imagen="'.$cadena_ids_elementos_imagen.'"
                        hidden>
                    </div>';
            }
        }
        return ($html);
    }
?>
