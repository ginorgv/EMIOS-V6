<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    // Constantes de sensores

    // Parámetro de tipo de sensor software
    define("PARAMETRO_TIPO_SENSOR_SOFTWARE", "tipo_sensor");

    // Valores de tipos de sensores software
    define("TIPO_SENSOR_SOFTWARE_METEOROLOGICO", "meteorologico");

    // Parámetros de sensores software de sensor 'meterologico'
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_TIPO_INFORMACION", "tipo_informacion");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_PROVEEDOR", "proveedor");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_CLAVE", "clave");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_TIPO_CLAVE", "tipo_clave");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LATITUD", "latitud");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LONGITUD", "longitud");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LOCALIDAD", "localidad");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_CODIGO_PAIS", "codigo_pais");
    define("PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_IDEMA", "idema");

    // Proveedores meteorológicos
    define("PROVEEDOR_METEOROLOGICO_AEMET", "aemet");
    define("PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE", "worldweatheronline");

    // Valores de tipos de informacion meterologica
    define("TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA", "temperatura");
    define("TIPO_INFORMACION_METEOROLOGICA_SENSACION_TERMICA", "sensacion_termica");
    define("TIPO_INFORMACION_METEOROLOGICA_HUMEDAD", "humedad");
    define("TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION", "precipitacion");
    define("TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA", "presion_atmosferica");
    define("TIPO_INFORMACION_METEOROLOGICA_VIENTO", "viento");
    define("TIPO_INFORMACION_METEOROLOGICA_NUBES", "nubes");


    //
    // Funciones de listas y descripciones
    //


    function dame_lista_tipos_sensor_software($tipo_sensor_software_seleccionado)
    {
        $lista_tipos_sensor_software = dame_lista_valores(
            array(
                array(TIPO_SENSOR_SOFTWARE_METEOROLOGICO, dame_descripcion_tipo_sensor_software(TIPO_SENSOR_SOFTWARE_METEOROLOGICO))),
            array($tipo_sensor_software_seleccionado));
        return ($lista_tipos_sensor_software);
    }


    function dame_lista_proveedores_meteorologicos($proveedor_meteorologico_seleccionado)
    {
        $lista_proveedores_meteorologicos = dame_lista_valores(
            array(
                array(PROVEEDOR_METEOROLOGICO_AEMET, dame_descripcion_proveedor_meteorologico(PROVEEDOR_METEOROLOGICO_AEMET)),
                array(PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE, dame_descripcion_proveedor_meteorologico(PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE))),
            array($proveedor_meteorologico_seleccionado));
        return ($lista_proveedores_meteorologicos);
    }


    function dame_lista_tipos_informacion_meteorologica($proveedor, $tipo_informacion_meteorologica_seleccionado)
    {
        switch ($proveedor)
        {
            case PROVEEDOR_METEOROLOGICO_AEMET:
            {
                $valores_informacion_meteorologica = array(
                    array(TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA)),
                    array(TIPO_INFORMACION_METEOROLOGICA_HUMEDAD, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_HUMEDAD)),
                    array(TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION)),
                    array(TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA)),
                    array(TIPO_INFORMACION_METEOROLOGICA_VIENTO, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_VIENTO)));
                break;
            }
            case PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE:
            {
                $valores_informacion_meteorologica = array(
                    array(TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA)),
                    array(TIPO_INFORMACION_METEOROLOGICA_SENSACION_TERMICA, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_SENSACION_TERMICA)),
                    array(TIPO_INFORMACION_METEOROLOGICA_HUMEDAD, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_HUMEDAD)),
                    array(TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION)),
                    array(TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA)),
                    array(TIPO_INFORMACION_METEOROLOGICA_VIENTO, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_VIENTO)),
                    array(TIPO_INFORMACION_METEOROLOGICA_NUBES, dame_descripcion_tipo_informacion_meteorologica(TIPO_INFORMACION_METEOROLOGICA_NUBES)));
                break;
            }
            default:
            {
                throw new Exception("Proveedor meteorológico desconocido: '".$proveedor."'");
            }
        }

        $lista_tipos_informacion_meteorologica = dame_lista_valores(
            $valores_informacion_meteorologica,
            array($tipo_informacion_meteorologica_seleccionado));
        return ($lista_tipos_informacion_meteorologica);
    }


    function dame_lista_modos_localizacion_meteorologica($proveedor, $modo_localizacion_meteorologica_seleccionado)
    {
        switch ($proveedor)
        {
            case PROVEEDOR_METEOROLOGICO_AEMET:
            {
                $valores_modo_localizacion_meteorologica = array(
                    array(MODO_LOCALIZACION_IDEMA, dame_descripcion_modo_localizacion(MODO_LOCALIZACION_IDEMA)));
                break;
            }
            case PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE:
            {
                $valores_modo_localizacion_meteorologica = array(
                    array(MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS, dame_descripcion_modo_localizacion(MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS)),
                    array(MODO_LOCALIZACION_LOCALIDAD, dame_descripcion_modo_localizacion(MODO_LOCALIZACION_LOCALIDAD)));
                break;
            }
            default:
            {
                throw new Exception("Proveedor meteorológico desconocido: '".$proveedor."'");
            }
        }

        $lista_modos_localizacion_meteorologica = dame_lista_valores(
            $valores_modo_localizacion_meteorologica,
            array($modo_localizacion_meteorologica_seleccionado));
        return ($lista_modos_localizacion_meteorologica);
    }


    function dame_descripcion_tipo_sensor_software($tipo_sensor)
    {
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_SOFTWARE_METEOROLOGICO:
            {
                $descripcion = "Meteorológico";
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


    function dame_descripcion_proveedor_meteorologico($proveedor)
    {
        $idiomas = new Idiomas();

        switch ($proveedor)
        {
            case PROVEEDOR_METEOROLOGICO_AEMET:
            {
                $descripcion = "Aemet";
                break;
            }
            case PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE:
            {
                $descripcion = "World Weather Online";
                break;
            }
            default:
            {
                $descripcion = $idiomas->_("Desconocido");
                break;
            }
        }

        return ($descripcion);
    }


    function dame_descripcion_tipo_informacion_meteorologica($tipo_informacion)
    {
        switch ($tipo_informacion)
        {
            case TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA:
            {
                $descripcion = "Temperatura";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_SENSACION_TERMICA:
            {
                $descripcion = "Sensación térmica";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_HUMEDAD:
            {
                $descripcion = "Humedad";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_PRECIPITACION:
            {
                $descripcion = "Precipitación";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_PRESION_ATMOSFERICA:
            {
                $descripcion = "Presión atmosférica";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_NUBES:
            {
                $descripcion = "Nubes";
                break;
            }
            case TIPO_INFORMACION_METEOROLOGICA_VIENTO:
            {
                $descripcion = "Viento";
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


    function dame_descripcion_modo_localizacion($modo_localizacion)
    {
        switch ($modo_localizacion)
        {
            case MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS:
            {
                $descripcion = "Coordenadas geográficas";
                break;
            }
            case MODO_LOCALIZACION_LOCALIDAD:
            {
                $descripcion = "Localidad";
                break;
            }
            case MODO_LOCALIZACION_IDEMA:
            {
                $descripcion = "Identificador de estación meteorológica";
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
