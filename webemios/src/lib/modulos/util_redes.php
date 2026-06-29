<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    // Se cargan los parámetros de la red y se guardan en la sesión
	function carga_parametros_red($id_red)
	{
        if (($id_red == ID_NINGUNO) || ($id_red === NULL))
        {
            // Zona horaria local (del servidor)
            $_SESSION["zona_horaria"] = date_default_timezone_get();

            // Se establecen todas las preferencias y parámetros locales por defecto
            establece_todas_preferencias_defecto();
            establece_parametros_locales_defecto();
        }
        else
        {
            // Se guardan los parámetros de la red en la sesión
            $fila_red = dame_fila_red($id_red);
            $_SESSION["zona_horaria"] = $fila_red["zona_horaria"];
            $_SESSION["tipo_mapa"] = $fila_red["tipo_mapa"];
            $_SESSION["etiquetas_mapa"] = $fila_red["etiquetas_mapa"];

            // Establece las preferencias y los parámetros locales de la red
            establece_preferencias_red($id_red, $fila_red);
            establece_parametros_locales_red($id_red, $fila_red);
        }
    }


    // Establece las preferencias de la red
    function establece_preferencias_red($id_red, $fila)
    {
        // Se recupera la información de la red
        if ($fila === NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM redes
                WHERE
                    id = '".$bd_red->_($id_red)."'";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
            $fila = $res->dame_siguiente_fila();
        }

        // Flags para establecer el logo y el tema por defecto
        $logo_defecto = true;
        $titulo_web_defecto = true;
        $tema_defecto = true;
        $paleta_colores_graficas_defecto = true;

        // Preferencias de la red
        $logo_personalizado = $fila["logo_personalizado"];
        $url_logo = $fila["url_logo"];
        $titulo_web = $fila["titulo_web"];
        $tema = $fila["tema"];
        $paleta_colores_graficas = $fila["paleta_colores_graficas"];
        $periodo_completo_informes_defecto = $fila["periodo_completo_informes_defecto"];

        // Logo (y URL del logo)
        if ($logo_personalizado == VALOR_SI)
        {
            $info_imagen_logo = carga_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO, $id_red, null);
            $info_imagen_logo_pdf = carga_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO_PDF, $id_red, null);
            $_SESSION["ruta_logo"] = $info_imagen_logo["ruta_fichero_imagen"];
            $_SESSION["ruta_logo_pdf"] = $info_imagen_logo_pdf["ruta_fichero_imagen"];
            $_SESSION["url_logo"] = $url_logo;
            $logo_defecto = false;
        }

        // Título de la web
        if ($titulo_web != "")
        {
            $_SESSION["titulo_web"] = $titulo_web;
            $titulo_web_defecto = false;
        }

        // Tema de colores
        if ($tema != TEMA_DEFECTO)
        {
            $_SESSION["tema"] = $tema;
            $tema_defecto = false;
        }

        // Paleta de colores de las gráficas
        if ($paleta_colores_graficas != PALETA_COLORES_GRAFICAS_DEFECTO)
        {
            $_SESSION["paleta_colores_graficas"] = $paleta_colores_graficas;
            $paleta_colores_graficas_defecto = false;
        }

        // Periodo completo en informes por defecto
        $_SESSION["periodo_completo_informes_defecto"] = $periodo_completo_informes_defecto;

        // Se establecen las preferencias por defecto el tema y logo por defecto (si es necesario)
        if (($logo_defecto == true) ||
            ($titulo_web_defecto == true) ||
            ($tema_defecto == true) ||
            ($paleta_colores_graficas_defecto == true))
        {
            establece_preferencias_defecto(
                $logo_defecto,
                $titulo_web_defecto,
                $tema_defecto,
                $paleta_colores_graficas_defecto);
        }
    }


    // Establece los parámetros locales de la red
    function establece_parametros_locales_red($id_red, $fila)
    {
        // Se recupera la información de la red
        if ($fila === NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM redes
                WHERE
                    id = '".$bd_red->_($id_red)."'";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
            $fila = $res->dame_siguiente_fila();
        }

        // Formato de fecha local de la red
        $tipo_formato_fecha_local = $fila["tipo_formato_fecha_local"];
        establece_formatos_fecha_local($tipo_formato_fecha_local);

        // Formato de números de la red
        $_SESSION["separador_miles"] = $fila["separador_miles"];
        $_SESSION["punto_decimal"] = $fila["punto_decimal"];

        // Unidades de medida de la red
        $cadena_unidades_medida = $fila["unidades_medida"];
        $unidades_medida = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_unidades_medida);
        $moneda = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_MONEDA];
        $unidad_medida_temperatura = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_TEMPERATURA];
        $unidad_medida_velocidad = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_VELOCIDAD];
        $_SESSION["moneda"] = $moneda;
        $_SESSION["unidad_medida_temperatura"] = $unidad_medida_temperatura;
        $_SESSION["unidad_medida_velocidad"] = $unidad_medida_velocidad;

        // Paises de tarifas de la red
        $cadena_paises_tarifas = $fila["paises_tarifas"];
        $paises_tarifas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_paises_tarifas);
        $pais_tarifas_electricas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_ELECTRICAS];
        $pais_tarifas_gas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_GAS];
        $pais_tarifas_agua = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_AGUA];
        $_SESSION["pais_tarifas_electricas"] = $pais_tarifas_electricas;
        $_SESSION["pais_tarifas_gas"] = $pais_tarifas_gas;
        $_SESSION["pais_tarifas_agua"] = $pais_tarifas_agua;

        // Medición por defecto
        $medicion_defecto = $fila["medicion_defecto"];
        $_SESSION["medicion_defecto"] = $medicion_defecto;
    }


    // Se recupera una tabla con información de la red actual
	function dame_tabla_informacion_red_actual()
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $info = "";
        $info .= "<div class='informacion-tabla-datos'>";

        $fila_red = dame_fila_red($_SESSION["id_red"]);
        $info .= "<i class='icon-info-sign color-azul'></i> ".
            $idiomas->_("Nombre de la red").": ".$fila_red['nombre']."<br/>";

        $consulta_cliente = "
            SELECT nombre
            FROM clientes
            WHERE
                id = '".$bd_red->_($fila_red['cliente'])."'";
        $res_cliente = $bd_red->ejecuta_consulta($consulta_cliente);
        if (($res_cliente == false) || ($res_cliente->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_cliente."'");
        }
        $fila_cliente = $res_cliente->dame_siguiente_fila();
        $cliente = htmlspecialchars($fila_cliente['nombre'], ENT_QUOTES);
        $info .= "<i class='icon-info-sign color-azul'></i> ".
            $idiomas->_("Cliente de la red").": ".$cliente."<br/>";

        $info .= "<br/>";

        // Se crea un nodo de tipo red y se añade la información extendida
        $red = Nodo::crea_nodo($_SESSION["id_red"], TIPO_NODO_RED);
        $info .= $red->dame_detalles_tabla(true);

        $info .= "</div>";

        // Se introduce la información en una tabla
        $boton_actualizar_informacion_red = "<i id='boton_red_actualizar_informacion_red' class='icon-refresh color-blanco boton-tabla-datos'></i>";
        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $boton_actualizar_informacion_red = "<i id='boton_red_actualizar_informacion_red' class='icon-refresh color-blanco boton-tabla-datos'></i>";
                $opciones = array($boton_actualizar_informacion_red);
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $boton_mostrar_ventana_modificar_red_parcial = "<i id='modifica_red_parcial__".$_SESSION["id_red"]."' ".
                    "class='icon-pencil color-blanco boton_mostrar_ventana_modificar_red_parcial boton-tabla-datos'></i>";
                $opciones = array(
                    $boton_mostrar_ventana_modificar_red_parcial,
                    $boton_actualizar_informacion_red);
                break;
            }
        }
        $params_tabla = array(
            "opciones" => $opciones
        );
        $tabla = new TablaDatos(
            "tabla-red-informacion-red",
            $idiomas->_("Información de red"),
            TIPO_TABLA_DATOS_CONTENEDOR,
            $params_tabla
        );
        $tabla->anyade_contenido("", $info);

        // Se devuelve la tabla
        return ($tabla);
    }


    // Devuelve si existe la red
    function dame_existe_red($id_red)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_red = "
            SELECT *
            FROM redes
            WHERE
                id = '".$bd_red->_($id_red)."'";
        $res_red = $bd_red->ejecuta_consulta($consulta_red);
        if ($res_red == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_red."'");
        }
        if ($res_red->dame_numero_filas() == 0)
        {
            return (false);
        }
        else
        {
            return (true);
        }
    }


    //
    // Funciones de consultas de redes
    //


    // Devuelve la condición de consulta del filtro de redes
    function dame_condicion_consulta_filtro_redes($filtro)
    {
        $campos = array(
            "redes.nombre",
            "clientes.nombre");
        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
        return ($condicion_consulta_filtro_busqueda);
    }


    //
    // Funciones de obtención de información de redes
    //


    // Devuelve la fila de la red
    function dame_fila_red($id_red)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_red = "
            SELECT *
            FROM redes
            WHERE
                id = '".$bd_red->_($id_red)."'";
        $res_red = $bd_red->ejecuta_consulta($consulta_red);
        if (($res_red == false) || ($res_red->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_red."'");
        }
        $fila_red = $res_red->dame_siguiente_fila();
        return ($fila_red);
    }


    // Devuelve el nombre de la red
    function dame_nombre_red($id_red)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch (true)
        {
            case ($id_red === NULL):
            case ($id_red == ID_NINGUNO):
            {
                $nombre_red = $idiomas->_("Ninguna");
                break;
            }
            default:
            {
                $consulta_red = "
                    SELECT nombre
                    FROM redes
                    WHERE
                        id = '".$bd_red->_($id_red)."'";
                $res_red = $bd_red->ejecuta_consulta($consulta_red);
                if (($res_red == false) || ($res_red->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_red."'");
                }
                $fila_red = dame_fila_red($id_red);
                $nombre_red = $fila_red['nombre'];
                break;
            }
        }
        return ($nombre_red);
    }
?>
