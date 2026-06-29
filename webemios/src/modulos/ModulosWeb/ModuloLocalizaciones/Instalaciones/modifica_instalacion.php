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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloInstalaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_INSTALACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_instalacion = $_POST['id_instalacion'];
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

    // Parámetros auxiliares
    $id_localizacion_anterior = $_POST['id_localizacion_anterior'];
    $imagen_anterior = $_POST['imagen_anterior'];

    // Se comprueba si existe otra instalación con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM instalaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_instalacion)."')";
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
        // Comprobaciones antes de modificar la instalación:
        // - Comprobación de que no existen sensores o actuadores asignados si se cambia la localización
        $modificar_instalacion = true;

        // Comprobación de que no existen sensores o actuadores asignados si se cambia la localización
        if ($modificar_instalacion == true)
        {
            if ($id_localizacion_anterior != $id_localizacion)
            {
                $numero_sensores_instalacion = Instalacion::dame_numero_nodos($id_instalacion, TIPO_NODO_SENSOR);
                $numero_actuadores_instalacion = Instalacion::dame_numero_nodos($id_instalacion, TIPO_NODO_ACTUADOR);
                if (($numero_sensores_instalacion > 0) || ($numero_actuadores_instalacion > 0))
                {
                    $modificar_instalacion = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede modificar la localización si hay sensores o actuadores asignados a la instalación");
                }
            }
        }

        // Se modifica la instalación
        if ($modificar_instalacion == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_instalacion_anterior = dame_fila_instalacion($id_instalacion);

            // Se recupera la información de mapa anterior (antes de la modificación)
            $info_posicion_mapa_anterior = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_INSTALACION,
                $id_instalacion,
                ORIGEN_MAPA_LOCALIZACION,
                $id_localizacion);

            // Si antes había imagen y ahora no, se elimina la imagen anterior
            // y se eliminan las imágenes con origen de mapa correspondiente
            if (($imagen_anterior == VALOR_SI) && ($imagen == VALOR_NO))
            {
                elimina_imagen_base_datos(ORIGEN_IMAGEN_INSTALACION_IMAGEN, $id_instalacion);
                elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_INSTALACION, $id_instalacion);
            }

            // Se modifica la instalación
            $operacion_modificacion = "
                UPDATE instalaciones
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    localizacion = '".$bd_red->_($id_localizacion)."',
                    imagen = '".$bd_red->_($imagen)."',
                    nombre_imagen = '".$bd_red->_($nombre_imagen)."',
                    factor_reduccion_imagen = '".$bd_red->_($factor_reduccion_imagen)."',
                    latitud_imagen_defecto = '".$bd_red->_($latitud_imagen_defecto)."',
                    longitud_imagen_defecto = '".$bd_red->_($longitud_imagen_defecto)."',
                    zoom_imagen_defecto = '".$bd_red->_($zoom_imagen_defecto)."'
                WHERE
                    id = '".$bd_red->_($id_instalacion)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se guarda o elimina la información de la posición en el mapa
                if ($mostrar_en_mapa == VALOR_SI)
                {
                    $info_posicion_mapa_actual = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_INSTALACION,
                        "id_elemento" => $id_instalacion,
                        "origen" => ORIGEN_MAPA_LOCALIZACION,
                        "id_origen" => $id_localizacion,
                        "latitud" => $latitud_mapa,
                        "longitud" => $longitud_mapa,
                        "zoom" => $zoom_mapa);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
                }
                else
                {
                    elimina_info_posicion_mapa_base_datos(
                        TIPO_ELEMENTO_MAPA_INSTALACION,
                        $id_instalacion,
                        ORIGEN_MAPA_LOCALIZACION,
                        $id_localizacion);
                }

                // Se recupera la fila actual
                $fila_instalacion_actual = dame_fila_instalacion($id_instalacion);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_instalacion(
                    $fila_instalacion_actual,
                    $fila_instalacion_anterior,
                    $info_posicion_mapa_actual,
                    $info_posicion_mapa_anterior);

                $res = "OK";
                $msg = $idiomas->_("Instalación modificada correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la instalación
    function anyade_accion_usuario_modificar_instalacion(
        $fila_actual,
        $fila_anterior,
        $info_posicion_mapa_actual,
        $info_posicion_mapa_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_LOCALIZACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }

        // Parámetros de opciones de imagen e imagen (mapa)
        if ($fila_actual["imagen"] != $fila_anterior["imagen"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMAGEN] = $fila_actual["imagen"];
            if ($fila_actual["imagen"] == VALOR_SI)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_IMAGEN] = $fila_actual["nombre_imagen"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN] = $fila_actual["factor_reduccion_imagen"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_IMAGEN_DEFECTO] = $fila_actual["longitud_imagen_defecto"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_IMAGEN_DEFECTO] = $fila_actual["latitud_imagen_defecto"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_IMAGEN_DEFECTO] = $fila_actual["zoom_imagen_defecto"];
            }
            if ($fila_anterior["imagen"] == VALOR_SI)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_IMAGEN] = $fila_anterior["nombre_imagen"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN] = $fila_anterior["factor_reduccion_imagen"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LONGITUD_IMAGEN_DEFECTO] = $fila_anterior["longitud_imagen_defecto"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LATITUD_IMAGEN_DEFECTO] = $fila_anterior["latitud_imagen_defecto"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ZOOM_IMAGEN_DEFECTO] = $fila_anterior["zoom_imagen_defecto"];
            }
        }
        else
        {
            if ($fila_actual["imagen"] == VALOR_SI)
            {
                if ($fila_actual["nombre_imagen"] != $fila_anterior["nombre_imagen"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_IMAGEN] = $fila_actual["nombre_imagen"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_IMAGEN] = $fila_anterior["nombre_imagen"];
                }
                if ($fila_actual["factor_reduccion_imagen"] != $fila_anterior["factor_reduccion_imagen"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN] = $fila_actual["factor_reduccion_imagen"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN] = $fila_anterior["factor_reduccion_imagen"];
                }
                if ($fila_actual["longitud_imagen_defecto"] != $fila_anterior["longitud_imagen_defecto"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_IMAGEN_DEFECTO] = $fila_actual["longitud_imagen_defecto"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LONGITUD_IMAGEN_DEFECTO] = $fila_anterior["longitud_imagen_defecto"];
                }
                if ($fila_actual["latitud_imagen_defecto"] != $fila_anterior["latitud_imagen_defecto"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_IMAGEN_DEFECTO] = $fila_actual["latitud_imagen_defecto"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LATITUD_IMAGEN_DEFECTO] = $fila_anterior["latitud_imagen_defecto"];
                }
                if ($fila_actual["zoom_imagen_defecto"] != $fila_anterior["zoom_imagen_defecto"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_IMAGEN_DEFECTO] = $fila_actual["zoom_imagen_defecto"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ZOOM_IMAGEN_DEFECTO] = $fila_anterior["zoom_imagen_defecto"];
                }
            }
        }

        // Información de posición en mapa
        if ($info_posicion_mapa_actual !== $info_posicion_mapa_anterior)
        {
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_actual, $parametros_accion_usuario);
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_anterior, $parametros_accion_usuario_anteriores);
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
