<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    // Devuelve la tabla con el filtro de validaciones de facturas
    function dame_tabla_filtro_validaciones_facturas_electricidad_Espanya()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $filtro = dame_filtro_texto_fechas(
            "smartmeter_filtro_validaciones_facturas",
            "00:00",
            "23:59",
            $idiomas->_("Sensor de energía activa y estado"),
            PERIODO_DEFECTO_SMARTMETER_VALIDACIONES_FACTURAS,
            "");

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-smartmeter-filtro-validaciones-facturas",
            $idiomas->_("Filtro de validaciones de facturas y cierres"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_VALIDACIONES_FACTURAS_ELECTRICIDAD_ESPANYA),
        );
        $tabla->anyade_fila("filtro-validaciones-facturas", $filtro, $params_fila);

        return ($tabla->dame_tabla());
    }


    // Devuelve la lista de tipos de fichero de validación de facturas
    function dame_lista_tipos_fichero_validacion_facturas_electricidad_Espanya($tipo_seleccionado)
    {
        $tipos_fichero = array();
        array_push($tipos_fichero, array(
            "id" => TIPO_NINGUNO,
            "nombre" => dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya(TIPO_NINGUNO)));
        array_push($tipos_fichero, array(
            "id" => TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CIERRE_FACTURACION_ENERGY_MINUS,
            "nombre" => dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya(TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CIERRE_FACTURACION_ENERGY_MINUS)));
        array_push($tipos_fichero, array(
            "id" => TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ATR_DISTRIBUIDORA_XML,
            "nombre" => dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya(TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ATR_DISTRIBUIDORA_XML)));
        array_push($tipos_fichero, array(
            "id" => TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB,
            "nombre" => dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya(TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB)));

        foreach ($tipos_fichero as $tipo_fichero)
        {
            $lista .= "<option value='".$tipo_fichero['id']."'";
			if ($tipo_fichero['id'] == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_fichero['nombre']."</option>";
        }

        return ($lista);
    }


    // Devuelve la descripción de tipo de fichero de validación de facturas
    function dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya($tipo_fichero)
    {
        switch ($tipo_fichero)
        {
            case TIPO_NINGUNO:
            {
                $descripcion = "Ninguno";
                break;
            }
            case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CIERRE_FACTURACION_ENERGY_MINUS:
            {
                $descripcion = "Cierre de facturación (Energy Minus)";
                break;
            }
            case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ATR_DISTRIBUIDORA_XML:
            {
                $descripcion = "Factura eléctrica (ATR de distribuidora XML)";
                break;
            }
            case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB:
            {
                $descripcion = "Factura eléctrica (Gemweb)";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }
?>
