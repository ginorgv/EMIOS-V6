<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    //
    // Funciones de obtención de información de instalaciones
    //


    function dame_fila_instalacion($id_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_instalacion = "
            SELECT *
            FROM instalaciones
            WHERE
                id = '".$bd_red->_($id_instalacion)."'";
        $res_instalacion = $bd_red->ejecuta_consulta($consulta_instalacion);
        if (($res_instalacion == false) || ($res_instalacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_instalacion."'");
        }
        $fila_instalacion = $res_instalacion->dame_siguiente_fila();
        return ($fila_instalacion);
    }


    function dame_nombre_instalacion($id_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_instalacion = "
            SELECT nombre
            FROM instalaciones
            WHERE
                id = '".$bd_red->_($id_instalacion)."'";
        $res_instalacion = $bd_red->ejecuta_consulta($consulta_instalacion);
        if (($res_instalacion == false) || ($res_instalacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_instalacion."'");
        }
        $fila_instalacion = $res_instalacion->dame_siguiente_fila();
        $nombre_instalacion = $fila_instalacion["nombre"];
        return ($nombre_instalacion);
    }


    function dame_info_instalaciones_localizacion($id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $info_instalaciones = array();
        $consulta_instalaciones = "
            SELECT
                id,
                nombre
            FROM instalaciones
            WHERE
                localizacion = '".$bd_red->_($id_localizacion)."'
            ORDER BY nombre ASC";
        $res_instalaciones = $bd_red->ejecuta_consulta($consulta_instalaciones);
        if ($res_instalaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_instalaciones."'");
        }
        while ($fila_instalacion = $res_instalaciones->dame_siguiente_fila())
        {
            $ids_sensores = Instalacion::dame_ids_nodos($fila_instalacion["id"], TIPO_NODO_SENSOR);
            $ids_actuadores = Instalacion::dame_ids_nodos($fila_instalacion["id"], TIPO_NODO_ACTUADOR);

            $info_instalacion = array(
                "id" => $fila_instalacion["id"],
                "nombre" => $fila_instalacion["nombre"],
                "ids_sensores" => $ids_sensores,
                "ids_actuadores" => $ids_actuadores);
            array_push($info_instalaciones, $info_instalacion);
        }
        return ($info_instalaciones);
    }


    function dame_info_equipos_instalacion($id_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $info_equipos = array();
        $consulta_equipos = "
            SELECT
                id,
                nombre,
                sensores,
                actuadores
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'
            ORDER BY nombre ASC";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            $ids_sensores = array();
            if ($fila_equipo["sensores"] != "")
            {
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_equipo["sensores"]);
            }
            $ids_actuadores = array();
            if ($fila_equipo["actuadores"] != "")
            {
                $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_equipo["actuadores"]);
            }

            $info_equipo = array(
                "id" => $fila_equipo["id"],
                "nombre" => $fila_equipo["nombre"],
                "ids_sensores" => $ids_sensores,
                "ids_actuadores" => $ids_actuadores);
            array_push($info_equipos, $info_equipo);
        }
        return ($info_equipos);
    }


    //
    // Funciones de mapas e imágenes de instalaciones
    //


    function dame_info_mapa_instalaciones($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_localizacion = $parametros["id_localizacion"];
        $etiquetas_mapa_instalaciones = $parametros["etiquetas_mapa_instalaciones"];

        // Se comprueba si la localización es visible por el usuario actual
        $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
        if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == false)
        {
            throw new Exception("Localización no visible por el usuario actual (id: '".$id_localizacion."')");
        }

        // Nota: en el mapa de localizaciones siempre se muestran las etiquetas
        $etiquetas_mapa = $_SESSION["etiquetas_mapa"];
        $_SESSION["etiquetas_mapa"] = $etiquetas_mapa_instalaciones;

        // Información de mapa de instalaciones y equipos
        $info_mapa_instalaciones = array();
        $info_mapa_equipos_instalaciones = array();

        // Se recuperan las instalaciones de la localización
        $consulta_instalaciones = "
            SELECT *
            FROM instalaciones
            WHERE
                localizacion = '".$bd_red->_($id_localizacion)."'";
        $res_instalaciones = $bd_red->ejecuta_consulta($consulta_instalaciones);
        if ($res_instalaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_instalaciones."'");
        }
        while ($fila_instalacion = $res_instalaciones->dame_siguiente_fila())
        {
            $id_instalacion = $fila_instalacion["id"];

            // Se recupera la posición del mapa de la instalación
            $info_posicion_mapa_instalacion = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_INSTALACION,
                $id_instalacion,
                ORIGEN_MAPA_LOCALIZACION,
                $id_localizacion);
            if ($info_posicion_mapa_instalacion === NULL)
            {
                continue;
            }

            // Información de mapa de la instalación
            $instalacion = new Instalacion($fila_instalacion);
            $info_mapa_instalacion = dame_info_mapa_objeto(
                $instalacion,
                $info_posicion_mapa_instalacion,
                ID_MAPA_MAPA_INSTALACIONES);

            // Se añade la información del mapa de la instalación
            array_push($info_mapa_instalaciones, $info_mapa_instalacion);

            // Se recuperan los equipos de la instalación
            $consulta_equipos = "
                SELECT *
                FROM equipos_instalaciones
                WHERE
                    instalacion = '".$bd_red->_($id_instalacion)."'";
            $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
            if ($res_equipos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_equipos."'");
            }
            while ($fila_equipo = $res_equipos->dame_siguiente_fila())
            {
                // Información de mapa del equipo
                $equipo_instalacion = new EquipoInstalacion($fila_equipo);
                $info_mapa_equipo_instalacion = dame_info_mapa_objeto(
                    $equipo_instalacion,
                    $info_posicion_mapa_instalacion,
                    ID_MAPA_MAPA_INSTALACIONES);

                // Se añade la información del mapa del equipo de la instalación
                array_push($info_mapa_equipos_instalaciones, $info_mapa_equipo_instalacion);
            }
        }

        // Se restaura el valor de las etiquetas
        $_SESSION["etiquetas_mapa"] = $etiquetas_mapa;

        // Resultado
        $resultado = array(
            "res" => "OK",
            "info_mapa_instalaciones" => $info_mapa_instalaciones,
            "info_mapa_equipos_instalaciones" => $info_mapa_equipos_instalaciones);
        return ($resultado);
    }


    function dame_info_imagen_instalacion($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_instalacion = $parametros["id_instalacion"];
        $etiquetas_imagen_instalacion = $parametros["etiquetas_imagen_instalacion"];

        // Se recupera la localización de la instalación
        $fila_instalacion = dame_fila_instalacion($id_instalacion);
        $id_localizacion = $fila_instalacion["localizacion"];

        // Se comprueba si la localización es visible por el usuario actual
        $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
        if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == false)
        {
            throw new Exception("Localización no visible por el usuario actual (id: '".$id_localizacion."')");
        }

        // Nota: en el mapa de localizaciones siempre se muestran las etiquetas
        $etiquetas_mapa = $_SESSION["etiquetas_mapa"];
        $_SESSION["etiquetas_mapa"] = $etiquetas_imagen_instalacion;

        // Información de mapa de instalaciones y equipos
        $info_mapa_equipos_instalacion = array();
        $info_mapa_sensores_equipos_instalacion = array();
        $info_mapa_actuadores_equipos_instalacion = array();

        // Se recuperan los equipos de la instalación
        $consulta_equipos = "
            SELECT *
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            $id_equipo = $fila_equipo["id"];
            $cadena_ids_sensores_equipo = $fila_equipo["sensores"];
            $cadena_ids_actuadores_equipo = $fila_equipo["actuadores"];

            // Se recupera la posición del mapa del equipo
            $info_posicion_mapa_equipo = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                $id_equipo,
                ORIGEN_MAPA_INSTALACION,
                $id_instalacion);
            if ($info_posicion_mapa_equipo === NULL)
            {
                continue;
            }

            // Información de mapa del equipo
            $equipo_instalacion = new EquipoInstalacion($fila_equipo);
            $info_mapa_equipo_instalacion = dame_info_mapa_objeto(
                $equipo_instalacion,
                $info_posicion_mapa_equipo,
                ID_MAPA_IMAGEN_INSTALACION);

            // Se añade la información del mapa de la instalación
            array_push($info_mapa_equipos_instalacion, $info_mapa_equipo_instalacion);



            // Se añade la información de los sensores y actuadores del equipo (si se tiene el módulo)
            if (dame_modulo_disponible_sesion(MODULO_SENSORES) == true)
            {
                if ($cadena_ids_sensores_equipo != "")
                {
                    // Se desactiva el ratio de los sensores
                    // (para recuperar las cadenas de valores de los sensores sin ratio)
                    $id_ratio_sensores_anterior = $_SESSION["id_ratio_sensores"];
                    $_SESSION["id_ratio_sensores"] = ID_NINGUNO;
                    try
                    {
                        $ids_sensores_equipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_equipo);
                        foreach ($ids_sensores_equipo as $id_sensor_equipo)
                        {
                            $fila_sensor_equipo = dame_fila_sensor($id_sensor_equipo);
                            $sensor = Nodo::crea_nodo($id_sensor_equipo, TIPO_NODO_SENSOR, $fila_sensor_equipo);
                            array_push($info_mapa_sensores_equipos_instalacion, dame_info_mapa_objeto(
                                $sensor,
                                $info_posicion_mapa_equipo,
                                ID_MAPA_IMAGEN_INSTALACION));
                        }
                        // Nota: No hay finally en PHP (a partir de 5.5 sí lo hay)
                        // Se restaura el ratio de los sensores anterior
                        $_SESSION["id_ratio_sensores"] = $id_ratio_sensores_anterior;
                    }
                    catch (Exception $e)
                    {
                        // Se restaura el ratio de los sensores anterior
                        $_SESSION["id_ratio_sensores"] = $id_ratio_sensores_anterior;

                        // Se relanza la excepción
                        throw $e;
                    }
                }
            }
            if (dame_modulo_disponible_sesion(MODULO_ACTUADORES) == true)
            {
                if ($cadena_ids_actuadores_equipo != "")
                {
                    $ids_actuadores_equipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_actuadores_equipo);
                    foreach ($ids_actuadores_equipo as $id_actuador_equipo)
                    {
                        $fila_actuador_equipo = dame_fila_actuador($id_actuador_equipo);
                        $actuador = Nodo::crea_nodo($id_actuador_equipo, TIPO_NODO_ACTUADOR, $fila_actuador_equipo);
                        array_push($info_mapa_actuadores_equipos_instalacion, dame_info_mapa_objeto(
                            $actuador,
                            $info_posicion_mapa_equipo,
                            ID_MAPA_IMAGEN_INSTALACION));
                    }
                }
            }
        }

        // Se restaura el valor de las etiquetas
        $_SESSION["etiquetas_mapa"] = $etiquetas_mapa;

        // Resultado
        $resultado = array(
            "res" => "OK",
            "info_mapa_equipos_instalacion" => $info_mapa_equipos_instalacion,
            "info_mapa_sensores_equipos_instalacion" => $info_mapa_sensores_equipos_instalacion,
            "info_mapa_actuadores_equipos_instalacion" => $info_mapa_actuadores_equipos_instalacion);
        return ($resultado);
    }
?>
