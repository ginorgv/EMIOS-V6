<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_ANOTACION_EQUIPO_INSTALACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_anotacion = $_POST['id_anotacion'];
    $id_instalacion = $_POST["id_instalacion"];
    $id_equipo = $_POST["id_equipo"];
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];
    $texto = $_POST['texto'];
    $foto = $_POST['foto'];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    // Parámetros auxiliares
    $foto_anterior = $_POST['foto_anterior'];

    // Si antes había foto y ahora no, se elimina la foto anterior
    if (($foto_anterior == VALOR_SI) && ($foto == VALOR_NO))
    {
        $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
            $id_instalacion,
            $id_equipo,
            $id_anotacion));
        elimina_imagen_base_datos(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, $id_origen);
    }

    // Se recupera la fila anterior (antes de la modificación)
    $fila_anotacion_anterior = dame_fila_anotacion_equipo_instalacion($id_anotacion);

    // Se modifica la anotación
    $operacion_modificacion = "
        UPDATE anotaciones_equipos_instalaciones
        SET
            instalacion = '".$bd_red->_($id_instalacion)."',
            equipo = '".$bd_red->_($id_equipo)."',
            hora = '".$bd_red->_($cadena_fecha_hora_base_datos_utc)."',
            texto = '".$bd_red->_($texto)."',
            foto = '".$bd_red->_($foto)."'
        WHERE
            id = '".$bd_red->_($id_anotacion)."'";
    $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
    if ($res_modificacion == true)
    {
        // Se recupera la fila actual
        $fila_anotacion_actual = dame_fila_anotacion_equipo_instalacion($id_anotacion);

        // Se añade la acción de usuario
        anyade_accion_usuario_modificar_anotacion_equipo_instalacion(
            $fila_anotacion_actual,
            $fila_anotacion_anterior);

        $res = "OK";
        $msg = $idiomas->_("Anotación modificada correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la anotación
    function anyade_accion_usuario_modificar_anotacion_equipo_instalacion(
        $fila_actual,
        $fila_anterior)
    {
        // Nombres de la instalación y del equipo
        $nombre_instalacion = dame_nombre_instalacion($fila_actual["instalacion"]);
        $nombre_equipo = dame_nombre_equipo_instalacion($fila_actual["equipo"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_ANOTACION_EQUIPO_INSTALACION;
        $objeto_accion_usuario = $nombre_equipo." (".$nombre_instalacion.")";

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["hora"] != $fila_anterior["hora"])
        {
            // Conversión de fechas
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($fila_actual["hora"], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_base_datos_local_anterior = cambia_zona_horaria_cadena_fecha_hora($fila_anterior["hora"], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_HORA] = $cadena_fecha_hora_base_datos_local;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_HORA] = $cadena_fecha_hora_base_datos_local_anterior;
        }
        if ($fila_actual["texto"] != $fila_anterior["texto"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TEXTO] = $fila_actual["texto"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TEXTO] = $fila_anterior["texto"];
        }
        if ($fila_actual["foto"] != $fila_anterior["foto"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FOTO] = $fila_actual["foto"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FOTO] = $fila_anterior["foto"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
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
