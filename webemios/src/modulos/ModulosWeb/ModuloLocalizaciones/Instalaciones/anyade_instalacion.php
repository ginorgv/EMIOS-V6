<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_INSTALACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $imagen = $_POST['imagen'];
    $nombre_imagen = $_POST['nombre_imagen'];
    $factor_reduccion_imagen = $_POST['factor_reduccion_imagen'];
    $latitud_imagen_defecto = $_POST['latitud_imagen_defecto'];
    $longitud_imagen_defecto = $_POST['longitud_imagen_defecto'];
    $zoom_imagen_defecto = $_POST['zoom_imagen_defecto'];
    $mostrar_en_mapa = $_POST['mostrar_en_mapa'];
    $latitud_mapa = $_POST['latitud_mapa'];
    $longitud_mapa = $_POST['longitud_mapa'];
    $zoom_mapa = $_POST['zoom_mapa'];
    $id_instalacion_anterior = $_POST["id_instalacion_anterior"];

    // Se comprueba si existe una instalación con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM instalaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una instalación con el mismo nombre");
    }
    else
    {
        // Se añade la instalación
        $operacion_insercion = "
            INSERT INTO instalaciones (
                nombre,
                descripcion,
                red,
                localizacion,
                imagen,
                nombre_imagen,
                factor_reduccion_imagen,
                latitud_imagen_defecto,
                longitud_imagen_defecto,
                zoom_imagen_defecto
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$bd_red->_($descripcion)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_localizacion)."',
                '".$bd_red->_($imagen)."',
                '".$bd_red->_($nombre_imagen)."',
                '".$bd_red->_($factor_reduccion_imagen)."',
                '".$bd_red->_($latitud_imagen_defecto)."',
                '".$bd_red->_($longitud_imagen_defecto)."',
                '".$bd_red->_($zoom_imagen_defecto)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila de la instalación añadida
            $id_instalacion = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_instalacion = dame_fila_instalacion($id_instalacion);

            // Se guarda la información de la posición en el mapa
            if ($mostrar_en_mapa == VALOR_SI)
            {
                $info_posicion_mapa = array(
                    "tipo_elemento" => TIPO_ELEMENTO_MAPA_INSTALACION,
                    "id_elemento" => $id_instalacion,
                    "origen" => ORIGEN_MAPA_LOCALIZACION,
                    "id_origen" => $id_localizacion,
                    "latitud" => $latitud_mapa,
                    "longitud" => $longitud_mapa,
                    "zoom" => $zoom_mapa);
                guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
            }

            // Si el identificador de instalación existe, es un duplicado de una instalación existente:
            // - Se duplican los equipos (pero sin los sensores o actuadores asignados) (si los hay)
            if ($id_instalacion_anterior != ID_NINGUNO)
            {
                // Se duplican los equipos de la instalación anterior
                duplica_equipos_instalacion_anterior($id_instalacion_anterior, $id_instalacion);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_instalacion($fila_instalacion, $info_posicion_mapa);

            $res = "OK";
            $msg = $idiomas->_("Instalación añadida correctamente").".\n".
                $idiomas->_("Haga click en la instalación para desplegar sus detalles y añadir los equipos correspondientes");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_instalacion" => $id_instalacion))
    );


    //
    // Funciones auxiliares
    //


    // Duplica los equipos de la instalación anterior (no se duplican las anotaciones)
    function duplica_equipos_instalacion_anterior($id_instalacion_anterior, $id_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los equipos de la instalación anterior
        $consulta_equipos_anteriores = "
            SELECT *
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion_anterior)."'";
        $res_equipos_anteriores = $bd_red->ejecuta_consulta($consulta_equipos_anteriores);
        if ($res_equipos_anteriores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos_anteriores."'");
        }

        // Se recorren los equipos de la instalación anterior, se cambia la instalación padre
        // (y se eliminan la lista de sensores y actuadores) y se añaden
        $ids_equipos_anteriores_nuevos = array();
        while ($fila_equipo_anterior = $res_equipos_anteriores->dame_siguiente_fila())
        {
            $operacion_insercion_equipo = "
                INSERT INTO equipos_instalaciones (
                    nombre,
                    descripcion,
                    red,
                    instalacion,
                    equipo_padre,
                    orden,
                    sensores,
                    actuadores,
                    estado,
                    observaciones,
                    icono_imagen
                ) VALUES (
                    '".$bd_red->_($fila_equipo_anterior["nombre"])."',
                    '".$bd_red->_($fila_equipo_anterior["descripcion"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_instalacion)."',
                    '".$bd_red->_($fila_equipo_anterior["equipo_padre"])."',
                    '".$bd_red->_($fila_equipo_anterior["orden"])."',
                    '',
                    '',
                    '".$bd_red->_($fila_equipo_anterior["estado"])."',
                    '".$bd_red->_($fila_equipo_anterior["observaciones"])."',
                    '".$bd_red->_($fila_equipo_anterior["icono_imagen"])."'
                )";
            $res_insercion_equipo = $bd_red->ejecuta_operacion($operacion_insercion_equipo);
            if ($res_insercion_equipo == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_equipo."'");
            }

            // Identificadores de equipos anteriores y nuevos
            // (para actualizar los equipos padres posteriormente)
            $id_equipo_anterior = $fila_equipo_anterior["id"];
            $id_equipo = $bd_red->dame_id_autoincremental_ultima_insercion();
            $ids_equipos_anteriores_nuevos[$id_equipo_anterior] = $id_equipo;
        }

        // Se recuperan los equipos de la instalación
        $consulta_equipos_nuevos = "
            SELECT *
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_equipos_nuevos = $bd_red->ejecuta_consulta($consulta_equipos_nuevos);
        if ($res_equipos_nuevos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos_nuevos."'");
        }

        // Se recorren los equipos de la instalación y se cambia el equipo padre (si es necesario)
        while ($fila_equipo_nuevo = $res_equipos_nuevos->dame_siguiente_fila())
        {
            $id_equipo_padre_anterior = $fila_equipo_nuevo["equipo_padre"];
            if ($id_equipo_padre_anterior != ID_NINGUNO)
            {
                $id_equipo_padre_nuevo = $ids_equipos_anteriores_nuevos[$id_equipo_padre_anterior];
                $operacion_modificacion = "
                    UPDATE equipos_instalaciones
                    SET
                        equipo_padre = '".$bd_red->_($id_equipo_padre_nuevo)."'
                    WHERE
                        id = '".$bd_red->_($fila_equipo_nuevo["id"])."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$res_modificacion."'");
                }
            }
        }
    }


    // Añade la acción de usuario de adición de la instalación
    function anyade_accion_usuario_anyadir_instalacion($fila, $info_posicion_mapa)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_INSTALACION;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_localizacion = dame_nombre_localizacion($fila["localizacion"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;

        // Parámetros de opciones de imagen e imagen (mapa)
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMAGEN] = $fila["imagen"];
        if ($fila["imagen"] == VALOR_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_IMAGEN] = $fila["nombre_imagen"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN] = $fila["factor_reduccion_imagen"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_IMAGEN_DEFECTO] = $fila["longitud_imagen_defecto"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_IMAGEN_DEFECTO] = $fila["latitud_imagen_defecto"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_IMAGEN_DEFECTO] = $fila["zoom_imagen_defecto"];
        }

        // Información de posición en mapa
        anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
