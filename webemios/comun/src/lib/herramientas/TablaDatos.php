<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');


    // Constantes de TablaDatos

	// Tipos de tabla
	define("TIPO_TABLA_DATOS_LISTA", "LISTA");
    define("TIPO_TABLA_DATOS_CONTENEDOR", "CONTENEDOR");

    // Tipos de fila de la tabla
	define("TIPO_FILA_TABLA_DATOS_NORMAL", "NORMAL");
    define("TIPO_FILA_TABLA_DATOS_DETALLES", "DETALLES");

    // Tipos de datos
    define("TIPO_DATOS_CABECERA", "CABECERA");
    define("TIPO_DATOS_FILA", "FILA");
    define("TIPO_DATOS_CONTENIDO", "CONTENIDO");


	class TablaDatos
	{
        //
        // Funciones estáticas
        //


        // Devuelve el contenido de la fila (para actualizar sólo una fila) (html)
		static function dame_fila(
			$datos,
            $params)
		{
            $fila = "";

            $parametro_clase_dato = $params["clase_dato"];
            $tipo = $params["tipo"];
            if ($tipo == "")
            {
                $tipo = TIPO_FILA_TABLA_DATOS_NORMAL;
            }
            $opciones = $params["opciones"];
            $anchuras_columnas = $params["anchuras_columnas"];
            $numero_columnas = $params["numero_columnas"];

            if ($parametro_clase_dato !== NULL)
            {
                $clase_dato = $parametro_clase_dato;
            }
            else
            {
                $clase_dato = "dato-tabla-datos";
            }
            if ($tipo == TIPO_FILA_TABLA_DATOS_DETALLES)
            {
                $clase_dato .= " clickable";
            }
            $numero_columna = 0;

            if (($opciones === NULL) || (count($opciones) == 0))
            {
                $fila .= "<div class='contenedor-datos-fila-tabla-datos-sin-opciones'>";
            }
            else
            {
                $fila .= "<div class='contenedor-datos-fila-tabla-datos-con-opciones'>";
            }
            foreach ($datos as $valor)
			{
				$fila .= "<span class='".$clase_dato." dato-fila-tabla-datos'";
                $anchura_columna = TablaDatos::dame_anchura_numero_columna_fila(
                    $anchuras_columnas,
                    $numero_columnas,
                    $numero_columna);
                $fila .= " style='width: ".$anchura_columna.";'";
                $valor_etiquetas_auxiliares = "<columna-lista-tabla-datos>".$valor."</columna-lista-tabla-datos>";
                $fila .= ">".$valor_etiquetas_auxiliares."</span>";
                $numero_columna += 1;
            }
            $fila .= "</div>";
			if ($opciones !== NULL)
			{
                $fila .= "<div class='contenedor-opciones-fila-tabla-datos'>";
				foreach ($opciones as $opcion)
				{
					$fila .= "<span class='opciones-tabla-datos'>".$opcion."</span>";
				}
                $fila .= "</div>";
			}
            return ($fila);
		}


        // Devuelve los detalles de una fila (html)
        static function dame_detalles_fila($herramientas_detalles, $detalles)
        {
            $detalles_fila = "";
            if (($herramientas_detalles !== NULL) && ($herramientas_detalles != ""))
            {
                $detalles_fila .= $herramientas_detalles;
            }
            $detalles_fila .= "<div class='contenido-detalle-tabla-datos'>";
            $detalles_fila .= $detalles;
            $detalles_fila .= "</div>";
            return ($detalles_fila);
        }


        // Devuelve la anchura de la columna especificada de una fila
        static function dame_anchura_numero_columna_fila($anchuras_columnas, $numero_columnas, $numero_columna)
        {
            if ($anchuras_columnas === NULL)
            {
                $anchura_columna = (int) floor(100 / $numero_columnas);
                $anchura_columna .= "%";
            }
            else
            {
                $anchura_columna = $anchuras_columnas[$numero_columna];
                if ($anchura_columna == -1)
                {
                    $anchura_columna = "auto";
                }
                else
                {
                    $anchura_columna .= "%";
                }
            }
            return ($anchura_columna);
        }


        // Miembros de TablaDatos
        public $id;
        public $tipo;

        public $titulo;
        public $opciones;

        public $numero_columnas_lista;
        public $anchuras_columnas_lista;
        public $clase_dato;

        public $tipo_fila;

        public $datos;
        public $pie;

        public $filas_con_opciones;

        public $titulo_html;
        public $datos_html;
        public $pie_html;

        public $generar_valores_xml;
        public $valores_xml;


        // Funciones de TablaDatos


        // Constructor
		function __construct($id, $titulo, $tipo, $params = array())
		{
            $this->id = $id;
            $this->titulo = $titulo;
            $this->tipo = $tipo;

            // Tipo de tabla:
            // - Se comprueban las anchuras de columnas y se establece la clase de dato:
            //   - Centrado para las listas
            //   - Alineado izquierda para el contenido
            // - Se comprueba si hay que generar los valores en xml para las listas
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    if (array_key_exists("numero_columnas", $params) == true)
                    {
                        $this->numero_columnas_lista = $params["numero_columnas"];
                        $this->anchuras_columnas_lista = $params["anchuras_columnas"];
                        if ($this->anchuras_columnas_lista !== NULL)
                        {
                            if (count($this->anchuras_columnas_lista) != $this->numero_columnas_lista)
                            {
                                throw new Exception("El número de anchuras de columnas no coincide con el número de columnas de la tabla");
                            }
                        }
                    }
                    else
                    {
                        throw new Exception("Se debe especificar un número de columnas");
                    }
                    if (array_key_exists("filas_con_opciones", $params) == true)
                    {
                        $this->filas_con_opciones = $params["filas_con_opciones"];
                    }
                    else
                    {
                        $this->filas_con_opciones = false;
                    }
                    if (array_key_exists("generar_valores_xml", $params) == true)
                    {
                        $this->generar_valores_xml = $params["generar_valores_xml"];
                    }
                    else
                    {
                        $this->generar_valores_xml = false;
                    }
                    $this->clase_dato = "dato-tabla-datos";
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $this->filas_con_opciones = false;
                    $this->clase_dato = "dato-tabla-datos-izda";
                    if (array_key_exists("contenido_desplegable", $params) == true)
                    {
                        $this->contenido_desplegable = $params["contenido_desplegable"];
                        if (array_key_exists("contenido_oculto", $params) == true)
                        {
                            $this->contenido_oculto = $params["contenido_oculto"];
                        }
                        else
                        {
                            $this->contenido_oculto = false;
                        }
                    }
                    else
                    {
                        $this->contenido_desplegable = false;
                        $this->contenido_oculto = false;
                    }

                    if ($this->contenido_desplegable == true)
                    {
                        if ($params["opciones"] === NULL)
                        {
                            $params["opciones"] = array();
                        }
                        if ($this->contenido_oculto == false)
                        {
                            $opcion_desplegar_contenido = "<i class='opcion-desplegar-contenido icon-caret-up color-blanco'></i>";
                        }
                        else
                        {
                            $opcion_desplegar_contenido = "<i class='opcion-desplegar-contenido icon-caret-down color-blanco'></i>";
                        }
                        array_push($params["opciones"], $opcion_desplegar_contenido);

                        // Nota: Si hay contenido desplegable y hay botón de ayuda, hay que añadir la siguiente línea para que al pulsar
                        // sobre al ayuda no se muestre/oculte el contenido: event.stopPropagation();
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de tabla desconocido: '".$this->tipo."'");
                }
            }

            // Tipo de fila
            if (array_key_exists("tipo_fila", $params) == true)
            {
                $this->tipo_fila = $params["tipo_fila"];
                switch ($this->tipo_fila)
                {
                    case TIPO_FILA_TABLA_DATOS_NORMAL:
                    case TIPO_FILA_TABLA_DATOS_DETALLES:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de fila desconocido: '".$this->tipo_fila."'");
                    }
                }
            }
            else
            {
                $this->tipo_fila = TIPO_FILA_TABLA_DATOS_NORMAL;
            }

            // Opciones (en el título de la tabla)
            $this->opciones = $params["opciones"];

            // Datos (cabeceras, filas y contenidos) y pie
            $this->datos = array();
            $this->pie = "";
        }


        // Añade la cabecera a la tabla
        function anyade_cabecera($id, $valores)
		{
            $cabecera = array(
                "id" => $id,
                "tipo_datos" => TIPO_DATOS_CABECERA,
                "valores" => $valores,
                "ids_elementos_desplegables" => NULL,
                "elementos_desplegables_visibles_inicio" => NULL);
            array_push($this->datos, $cabecera);
        }


        // Añade la cabecera a la tabla
        function anyade_cabecera_elementos_desplegables(
            $id,
            $valores,
            $ids_elementos_desplegbles,
            $elementos_desplegables_visibles_inicio = false)
		{
            $cabecera = array(
                "id" => $id,
                "tipo_datos" => TIPO_DATOS_CABECERA,
                "valores" => $valores,
                "ids_elementos_desplegables" => $ids_elementos_desplegbles,
                "elementos_desplegables_visibles_inicio" => $elementos_desplegables_visibles_inicio);
            array_push($this->datos, $cabecera);
        }


        // Añade una fila de valores
		function anyade_fila(
			$id,
			$datos,
            $params = array())
		{
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    if (count($datos) <> $this->numero_columnas_lista)
                    {
                        throw new Exception("El número de valores de la fila no coincide con el número de columnas de la lista");
                    }
                    $opciones = $params["opciones"];
                    if (($opciones !== NULL) && (count($opciones) > 0))
                    {
                        $this->filas_con_opciones = true;
                    }
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $numero_columnas = count($datos);
                    $anchuras_columnas = $params["anchuras_columnas"];
                    if ($anchuras_columnas !== NULL)
                    {
                        if ($numero_columnas > count($anchuras_columnas))
                        {
                            throw new Exception("El número de valores del contenedor es mayor que el número de anchuras de columnas".
                                " (número de columnas: '".$numero_columnas.", número de anchuras de columnas: '".count($anchuras_columnas)."')");
                        }
                    }
                    break;
                }
            }

            $fila = array(
                "tipo_datos" => TIPO_DATOS_FILA,
                "id" => $id,
                "datos" => $datos,
                "params" => $params);
            array_push($this->datos, $fila);
		}


        // Añade contenido una tabla de tipo 'contenedor'
        function anyade_contenido(
            $id,
            $html,
            $params = array())
		{
            if ($this->tipo != TIPO_TABLA_DATOS_CONTENEDOR)
            {
                throw new Exception("Tipo de tabla incorrecto: '".$this->tipo."'");
            }

            $contenido = array(
                "tipo_datos" => TIPO_DATOS_CONTENIDO,
                "id" => $id,
                "html" => $html,
                "params" => $params);
            array_push($this->datos, $contenido);
		}


        // Añade el pie de la tabla
        function anyade_pie($valor)
		{
            $this->pie = $valor;
		}


        // Devuelve el contenido de la tabla (html)
		function dame_tabla($incluir_salto_linea = true)
		{
            // Inicializacion de contenido (html) de la tabla
            $this->titulo_html = "";
            $this->datos_html = "";
            $this->pie_html = "";

            // Valores XML
            $this->valores_xml = "";

            // Se genera el título
            $this->anyade_titulo_html();

            // Se añaden los datos
            $numero_datos = count($this->datos);
            $numero_dato = 1;
            foreach ($this->datos as $dato)
            {
                switch ($dato["tipo_datos"])
                {
                    case TIPO_DATOS_CABECERA:
                    {
                        $this->anyade_cabecera_html(
                            $dato["id"],
                            $dato["valores"],
                            $dato["ids_elementos_desplegables"],
                            $dato["elementos_desplegables_visibles_inicio"]);
                        break;
                    }
                    case TIPO_DATOS_FILA:
                    {
                        $ultima_fila = ($numero_dato == $numero_datos);
                        $hay_pie_pagina = ($this->pie != "");
                        $this->anyade_fila_html(
                            $dato["id"],
                            $dato["datos"],
                            $dato["params"],
                            $numero_dato,
                            $ultima_fila,
                            $hay_pie_pagina);
                        break;
                    }
                    case TIPO_DATOS_CONTENIDO:
                    {
                        if (($this->pie == "") && ($numero_dato == $numero_datos))
                        {
                            $ultimo_contenido = true;
                        }
                        else
                        {
                            $ultimo_contenido = false;
                        }
                        $this->anyade_contenido_html(
                            $dato["id"],
                            $dato["html"],
                            $dato["params"],
                            $ultimo_contenido);
                        break;
                    }
                }
                $numero_dato += 1;
            }

            // Se genera el pie de tabla
            $this->anyade_pie_html($numero_dato);

            // Clase de tabla
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    $clase_tipo_tabla_datos = "tabla-datos-lista";
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $clase_tipo_tabla_datos = "tabla-datos-contenedor";
                    break;
                }
            }

            // Se genera el contenido de la tabla (html)
            $contenido = "
                <div id='".$this->id."'>
                    <div class='tabla-datos ".$clase_tipo_tabla_datos."'>";
			$contenido .= $this->titulo_html;
            $contenido .= "
                        <div>";
            $contenido .= $this->datos_html;
            if ($this->pie_html != "")
            {
                $contenido .= $this->pie_html;
            }
			$contenido .= "
                        </div>
                    </div>";

            // Nota: El <br/> se mete dentro de la tabla porque si no al generar la imagen de la tabla
            // en ocasiones no se muestra la línea inferior (visto en FireFox)
            $contenido .= "
                    <div class='fin-tabla-datos'></div>";
            if ($incluir_salto_linea == true)
            {
                $contenido .= "
                    <br/>";
            }

            // Valores ocultos
            if (($this->tipo == TIPO_TABLA_DATOS_LISTA) && ($this->generar_valores_xml == true))
            {
                $this->valores_xml = "<valores>".$this->valores_xml."</valores>";
                $contenido .= "<div class='valores_xml' valores='".$this->valores_xml."' hidden></div>";
            }
            $contenido .= "
                </div>";

			return ($contenido);
		}


        //
        // Funciones auxiliares para generar el HTML de la tabla
        //


        // Añade el titulo a la tabla
        function anyade_titulo_html()
        {
            $this->titulo_html = "
                <div class='titulo-tabla-datos elemento-no-seleccionable";
            if ($this->contenido_desplegable == true)
            {
                $this->titulo_html .= " titulo-tabla-datos-contenido-desplegable clickable";
                if ($this->contenido_oculto == true)
                {
                    $this->titulo_html .= " titulo-tabla-datos-contenido-oculto";
                }
            }
            $this->titulo_html .= "'";
            if ($this->contenido_desplegable == true)
            {
                $this->titulo_html .= " desplegado='";
                if ($this->contenido_oculto == false)
                {
                    $this->titulo_html .= "1'";
                }
                else
                {
                    $this->titulo_html .= "0'";
                }
            }
            $this->titulo_html .= ">
                <span class='texto-titulo-tabla-datos'>".$this->titulo."</span>";
            if ($this->opciones !== NULL)
            {
                foreach ($this->opciones as $opcion)
                {
                    $this->titulo_html .= "<span class='opciones-tabla-datos'>".$opcion."</span>";
                }
            }
            $this->titulo_html .= "
                </div>";
        }


        // Añade la cabecera a la tabla
        function anyade_cabecera_html(
            $id,
            $nombres,
            $ids_elementos_desplegables,
            $elementos_desplegables_visibles_inicio)
        {
            $clase_cabecera = "cabecera-tabla-datos elemento-no-seleccionable";
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    $clase_cabecera .= " contenedor-fila-tabla-datos-par";
                    $this->datos_html .= "<div class='".$clase_cabecera."' id='".$id."'>";
                    if ($this->filas_con_opciones == true)
                    {
                        $this->datos_html .= "<div class='cabecera-tabla-datos-con-opciones'>";
                    }
                    else
                    {
                        $this->datos_html .= "<div class='cabecera-tabla-datos-sin-opciones'>";
                    }
                    $numero_columna = 0;
                    foreach ($nombres as $nombre)
                    {
                        $this->datos_html .= "<span class='".$this->clase_dato."'";
                        if ($this->tipo == TIPO_TABLA_DATOS_LISTA)
                        {
                            $anchura_columna = TablaDatos::dame_anchura_numero_columna_fila(
                                $this->anchuras_columnas_lista,
                                $this->numero_columnas_lista,
                                $numero_columna);
                            $this->datos_html .= " style='width: ".$anchura_columna.";'";
                        }
                        $this->datos_html .= ">".htmlspecialchars($nombre, ENT_QUOTES)."</span>";
                        $numero_columna += 1;
                    }
                    $this->datos_html .= "</div>";
                    $this->datos_html .= "</div>";
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    if ($ids_elementos_desplegables !== NULL)
                    {
                        $clase_cabecera .= " cabecera-tabla-datos-elementos-desplegables clickable";
                    }
                    $this->datos_html .= "<div class='".$clase_cabecera."' id='".$id."'";
                    if ($ids_elementos_desplegables !== NULL)
                    {
                        if ($elementos_desplegables_visibles_inicio == false)
                        {
                            $this->datos_html .= " desplegado=".VALOR_NO;
                        }
                        else
                        {
                            $this->datos_html .= " desplegado=".VALOR_SI;
                        }
                    }
                    $this->datos_html .= ">";
                    $this->datos_html .= "<div class='cabecera-tabla-datos-sin-opciones'>";
                    $this->datos_html .= "<span class='".$this->clase_dato."'>".
                        htmlspecialchars($nombres[0], ENT_QUOTES)."</span>";
                    if ($ids_elementos_desplegables !== NULL)
                    {
                        if ($elementos_desplegables_visibles_inicio == false)
                        {
                            $icono_desplegar_elementos_desplegables = "icon-caret-down";
                        }
                        else
                        {
                            $icono_desplegar_elementos_desplegables = "icon-caret-up";
                        }
                        $opcion_desplegar_elementos_desplegables = "<i class='opcion-desplegar-elementos-desplegables ".
                            $icono_desplegar_elementos_desplegables." color-gris-claro'></i>";
                        $this->datos_html .= "<span class='opciones-tabla-datos'>".$opcion_desplegar_elementos_desplegables."</span>";
                        $cadena_ids_elementos_desplegables = implode(",", $ids_elementos_desplegables);
                        $this->datos_html .= "<div class='ids_elementos_desplegables' ".
                            "ids_elementos_desplegables='".$cadena_ids_elementos_desplegables."' hidden></div>";
                    }
                    $this->datos_html .= "</div>";
                    $this->datos_html .= "</div>";
                    break;
                }
            }

            // Valores XML
            if (($this->tipo == TIPO_TABLA_DATOS_LISTA) && ($this->generar_valores_xml == true))
            {
                $this->valores_xml .= "<columnas>";
                foreach ($nombres as $nombre)
                {
                    // Se convierten los carácteres a ASCII estándar
                    $nombre_xml = escapeHtmlXml($nombre);
                    $nombre_xml = convierte_ascii_estandar($nombre_xml);
                    $this->valores_xml .= "<nombre>".$nombre_xml."</nombre>";
                }
                $this->valores_xml .= "</columnas>";
            }
        }


        // Añade una fila de valores
        function anyade_fila_html(
            $id,
            $datos,
            $params,
            $numero_dato,
            $ultima_fila,
            $hay_pie_pagina)
        {
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    $numero_columnas = $this->numero_columnas_lista;
                    $anchuras_columnas = $this->anchuras_columnas_lista;
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $numero_columnas = count($datos);
                    $anchuras_columnas = $params["anchuras_columnas"];
                    $cadena_numeros_columnas_fila_contenedor = $params["numero_columnas"];
                    if ($cadena_numeros_columnas_fila_contenedor !== NULL)
                    {
                        $numeros_columnas_fila_contenedor = explode(",", $cadena_numeros_columnas_fila_contenedor);
                    }
                    else
                    {
                        $numeros_columnas_fila_contenedor = NULL;
                    }

                    $clase_ultima_fila = $params["clase_ultima_fila"];
                    $numero_fila_actual_contenedor = 1;
                    $numero_columna_fila_actual_contenedor = 1;
                    if ($numeros_columnas_fila_contenedor !== NULL)
                    {
                        $numero_numeros_columnas_fila_contenedor = count($numeros_columnas_fila_contenedor);
                        if ($numero_numeros_columnas_fila_contenedor == 1)
                        {
                            $numero_filas_totales_contenedor = ceil(count($datos) / $numeros_columnas_fila_contenedor[0]);
                        }
                        else
                        {
                            $numero_fila_actual = 1;
                            $numero_datos_fila = count($datos);
                            while ($numero_datos_fila > 0)
                            {
                                if ($numero_fila_actual <= $numero_numeros_columnas_fila_contenedor)
                                {
                                    $numero_columnas_fila_actual = $numeros_columnas_fila_contenedor[$numero_fila_actual - 1];
                                }
                                $numero_datos_fila -= $numero_columnas_fila_actual;
                                if ($numero_datos_fila > 0)
                                {
                                    $numero_fila_actual += 1;
                                }
                            }
                            $numero_filas_totales_contenedor = $numero_fila_actual;
                        }
                    }
                    else
                    {
                        $numero_filas_totales_contenedor = 1;
                    }
                    break;
                }
            }

            $parametro_clase_fila = $params["clase_fila"];
            $parametro_clase_dato = $params["clase_dato"];
            $sin_borde_inferior = $params["sin_borde_inferior"];
            $opciones = $params["opciones"];
            if (array_key_exists("oculta", $params) == true)
            {
                $oculta = $params["oculta"];
            }
            else
            {
                $oculta = false;
            }

            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    $clase_fila = "contenedor-fila-tabla-datos";
                    if ($ultima_fila == true)
                    {
                        $clase_fila .= " contenedor-ultima-fila-tabla-datos";
                    }
                    $fila_lista_impar = $numero_dato % 2;
                    if ($fila_lista_impar == 0)
                    {
                        $clase_fila .= " contenedor-fila-tabla-datos-impar";
                    }
                    else
                    {
                        $clase_fila .= " contenedor-fila-tabla-datos-par";
                    }
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $clase_fila = "contenedor-datos-tabla-datos";
                    break;
                }
            }
            if ($sin_borde_inferior == true)
            {
                $clase_fila = "contenedor-datos-tabla-datos-sin-borde-inferior";
            }
            else
            {
                if (($ultima_fila == true) && ($hay_pie_pagina == false))
                {
                    $clase_fila .= " contenedor-datos-tabla-datos-sin-pie";
                }
            }
            if (($oculta == true) || ($this->contenido_oculto == true))
            {
                $clase_fila .= " elemento-oculto";
            }
            $this->datos_html.= "
                <div class='".$clase_fila."' id='".$id."'>
                    <div class='contenido-fila-tabla-datos";
            if ($parametro_clase_fila !== NULL)
            {
                $this->datos_html .= " ".$parametro_clase_fila;
            }
            $this->datos_html .= "' id='fila_".$id."'>";
            if ($parametro_clase_dato !== NULL)
            {
                $clase_dato = $parametro_clase_dato;
            }
            else
            {
                $clase_dato = $this->clase_dato;
            }
            switch ($this->tipo)
            {
                case TIPO_TABLA_DATOS_LISTA:
                {
                    $clase_dato .= " elemento-seleccionable";
                    break;
                }
                case TIPO_TABLA_DATOS_CONTENEDOR:
                {
                    $clase_dato .= " elemento-no-seleccionable";
                    break;
                }
            }
            if ($this->tipo_fila == TIPO_FILA_TABLA_DATOS_DETALLES)
            {
                $clase_dato .= " clickable";
            }

            $numero_columna = 0;
            if ($this->filas_con_opciones == true)
            {
                $this->datos_html .= "<div class='contenedor-datos-fila-tabla-datos-con-opciones'>";
            }
            else
            {
                $this->datos_html .= "<div class='contenedor-datos-fila-tabla-datos-sin-opciones'>";
            }
            foreach ($datos as $valor)
            {
                if ($this->tipo == TIPO_TABLA_DATOS_CONTENEDOR)
                {
                    if ($numeros_columnas_fila_contenedor !== NULL)
                    {
                        if ($numero_fila_actual_contenedor <= count($numeros_columnas_fila_contenedor))
                        {
                            $numero_columnas_fila_actual_contenedor = $numeros_columnas_fila_contenedor[$numero_fila_actual_contenedor - 1];
                        }
                        if ($numero_columna_fila_actual_contenedor > $numero_columnas_fila_actual_contenedor)
                        {
                            $this->datos_html .= "</div>";
                            $this->datos_html .= "<div class='contenido-fila-tabla-datos'>";
                            $numero_columna_fila_actual_contenedor = 1;
                            $numero_fila_actual_contenedor += 1;
                        }
                        $numero_columna_fila_actual_contenedor += 1;
                    }
                }
                $this->datos_html .= "<span class='".$clase_dato." dato-fila-tabla-datos";
                if (($this->tipo == TIPO_TABLA_DATOS_CONTENEDOR) && ($numero_fila_actual_contenedor == $numero_filas_totales_contenedor))
                {
                    if ($clase_ultima_fila !== NULL)
                    {
                        $this->datos_html .= " ".$clase_ultima_fila;
                    }
                }
                $this->datos_html .= "'";
                $anchura_columna = NULL;
                if (($this->tipo == TIPO_TABLA_DATOS_LISTA) or
                    (($this->tipo == TIPO_TABLA_DATOS_CONTENEDOR) && ($anchuras_columnas !== NULL)))
                {
                    $anchura_columna = TablaDatos::dame_anchura_numero_columna_fila(
                        $anchuras_columnas,
                        $numero_columnas,
                        $numero_columna);
                }
                if ($anchura_columna !== NULL)
                {
                    $this->datos_html .= " style='width: ".$anchura_columna.";'";
                }
                if ($this->tipo == TIPO_TABLA_DATOS_LISTA)
                {
                    $valor_etiquetas_auxiliares = "<columna-lista-tabla-datos>".$valor."</columna-lista-tabla-datos>";
                }
                else
                {
                    $valor_etiquetas_auxiliares = $valor;
                }
                $this->datos_html .= ">".$valor_etiquetas_auxiliares."</span>";
                $numero_columna += 1;
            }
            $this->datos_html .= "</div>";

            if ($opciones !== NULL)
            {
                $this->datos_html .= "<div class='contenedor-opciones-fila-tabla-datos'>";
                foreach ($opciones as $opcion)
                {
                    $this->datos_html .= "<span class='opciones-tabla-datos'>".$opcion."</span>";
                }
                $this->datos_html .= "</div>";
            }

            $this->datos_html .= "
                    </div>";
            if ($this->tipo_fila == TIPO_FILA_TABLA_DATOS_DETALLES)
            {
                // Si hay detalles en los parámetros se muestran los detalles expandidos
                if ($params["detalles_tabla"] !== NULL)
                {
                    $this->datos_html .= "
                        <div class='detalle-tabla-datos' style='display: block;'>";
                    $this->datos_html .= TablaDatos::dame_detalles_fila(
                        $params["herramientas_detalles_tabla"],
                        $params["detalles_tabla"]);
                    $this->datos_html .= "
                        </div>";
                }
                else
                {
                    $this->datos_html .= "
                        <div class='detalle-tabla-datos'></div>";
                }
            }
            $this->datos_html .= "
                </div>";

            // Valores XML
            if (($this->tipo == TIPO_TABLA_DATOS_LISTA) && ($this->generar_valores_xml == true))
            {
                $this->valores_xml .= "<fila>";
                for ($i = 0; $i < count($datos); $i++)
                {
                    $valor = $datos[$i];
                    $valor_xml = $this->convierte_valor_columna_fila_valor_xml($i, $valor, $params);
                    $this->valores_xml .= "<valor>".$valor_xml."</valor>";
                }
                $this->valores_xml .= "</fila>";
            }
        }


        // Convierte el valor de una columna de una fila al valor XML correspondiente (para la exportación de tabla a 'csv')
        function convierte_valor_columna_fila_valor_xml($numero_valor, $valor, $params)
        {
            // Se eliminan las imágenes de los valores, los cambios de fuentes y las etiquetas de fechas (si existen)
            $valor_xml = sustituye_subcadena_inicial_final_valor_etiqueta($valor, "<i ", "</i>", "texto");
            $valor_xml = sustituye_subcadena_inicial_final_valor_etiqueta($valor_xml, "<img ", "</img>", "texto");
            $valor_xml = elimina_subcadena_inicial_final($valor_xml, "<img ", ">");
            $valor_xml = elimina_subcadena_inicial_final($valor_xml, "<font ", ">");
            $valor_xml = str_replace("</font>", "", $valor_xml);
            $valor_xml = str_replace("<cadena_fecha class='cadena-fecha'>", "", $valor_xml);
            $valor_xml = str_replace("</cadena_fecha>", "", $valor_xml);
            $valor_xml = str_replace("<iconos-dato class='iconos-dato'>", "", $valor_xml);
            $valor_xml = str_replace("</iconos-dato>", "", $valor_xml);
            $valor_xml = trim($valor_xml);

            // Se elimina el texto a eliminar (si es necesario)
            if (array_key_exists("texto_eliminar_valores_xml", $params) == true)
            {
                $texto_eliminar = $params["texto_eliminar_valores_xml"];
                if ($texto_eliminar != "")
                {
                    $valor_xml = str_replace($texto_eliminar, "", $valor_xml);
                }
            }
            else
            {
                if (array_key_exists("texto_eliminar_valor_xml_".$numero_valor, $params) == true)
                {
                    $texto_eliminar = $params["texto_eliminar_valor_xml_".$numero_valor];
                    if ($texto_eliminar != "")
                    {
                        $valor_xml = str_replace($texto_eliminar, "", $valor_xml);
                    }
                }
            }

            // Se eliminan los textos a eliminar (si es necesario)
            if (array_key_exists("textos_eliminar_valores_xml", $params) == true)
            {
                $textos_eliminar = $params["textos_eliminar_valores_xml"];
                foreach ($textos_eliminar as $texto_eliminar)
                {
                    if ($texto_eliminar != "")
                    {
                        $valor_xml = str_replace($texto_eliminar, "", $valor_xml);
                    }
                }
            }
            else
            {
                if (array_key_exists("textos_eliminar_valor_xml_".$numero_valor, $params) == true)
                {
                    $textos_eliminar = $params["textos_eliminar_valor_xml_".$numero_valor];
                    foreach ($textos_eliminar as $texto_eliminar)
                    {
                        if ($texto_eliminar != "")
                        {
                            $valor_xml = str_replace($texto_eliminar, "", $valor_xml);
                        }
                    }
                }
            }

            // Se convierten los carácteres a ASCII estándar y se 'escapan' las cadenas
            $valor_xml = convierte_ascii_estandar($valor_xml);
            $valor_xml = htmlspecialchars($valor_xml, ENT_QUOTES);
            $valor_xml = escapeHtmlXml($valor_xml);
            return ($valor_xml);
        }


        // Añade contenido una tabla de tipo 'contenedor'
        function anyade_contenido_html(
            $id,
            $html,
            $params,
            $ultimo_contenido)
        {
            $parametro_clase_contenido = $params["clase_contenido"];
            $sin_margenes = $params["sin_margenes"];
            $sin_borde_inferior = $params["sin_borde_inferior"];
            if (array_key_exists("oculto", $params) == true)
            {
                $oculto = $params["oculto"];
            }
            else
            {
                $oculto = false;
            }

            $clase_contenedor = "contenedor-datos-tabla-datos";
            if ($sin_margenes !== NULL)
            {
                $clase_contenedor = "contenedor-datos-tabla-datos-sin-margenes";
            }
            if ($sin_borde_inferior == true)
            {
                $clase_contenedor = " contenedor-datos-tabla-datos-sin-borde-inferior";
            }
            else
            {
                if ($ultimo_contenido == true)
                {
                    $clase_contenedor .= " contenedor-datos-tabla-datos-sin-pie";
                }
            }
            if (($oculto == true) || ($this->contenido_oculto == true))
            {
                $clase_contenedor .= " elemento-oculto";
            }
            if ($parametro_clase_contenido !== NULL)
            {
                $clase_contenido = $parametro_clase_contenido;
            }
            else
            {
                $clase_contenido = "";
            }
            if ($clase_contenido != "")
            {
                $clase_contenido .= " ";
            }
            $clase_contenido .= "elemento-no-seleccionable";

            $this->datos_html .= "
                <div class='".$clase_contenedor."' id='".$id."'>
                    <div class='".$clase_contenido."'> ";
            $this->datos_html .= $html;
            $this->datos_html .= "
                    </div>
                </div>";
        }


        // Añade el pie de la tabla
        function anyade_pie_html($numero_datos)
        {
            if ($this->pie != "")
            {
                $clase_pie = "pie-tabla-datos elemento-no-seleccionable";
                if ($this->tipo == TIPO_TABLA_DATOS_LISTA)
                {
                    $fila_lista_impar = $numero_datos % 2;
                    if ($fila_lista_impar == 0)
                    {
                        $clase_pie .= " contenedor-fila-tabla-datos-impar";
                    }
                    else
                    {
                        $clase_pie .= " contenedor-fila-tabla-datos-par";
                    }
                }
                $this->pie_html = "<div class='".$clase_pie."'>".$this->pie."</div>";
            }
        }
    }
?>
