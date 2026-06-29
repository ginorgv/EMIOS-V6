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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_VALOR_ADICIONAL_PROYECTO, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_valor_adicional = $_POST['id_valor_adicional'];
    $id_proyecto = $_POST['id_proyecto'];
    $nombre = $_POST['nombre'];
    $destino = $_POST['destino'];
    $valor = $_POST['valor'];
    $periodicidad = $_POST['periodicidad'];
    $cadena_fecha_inicio_local_local = $_POST['fecha_inicio'];
    $cadena_fecha_fin_local_local = $_POST['fecha_fin'];
    $aplicar_intervalos_sin_valores = $_POST['aplicar_intervalos_sin_valores'];

    // Se comprueba si existe otro valor adicional con el mismo nombre en el mismo proyecto
    $consulta_existe = "
        SELECT *
        FROM valores_adicionales_proyectos
        WHERE
            (proyecto = '".$bd_red->_($id_proyecto)."')
            AND (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_valor_adicional)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un valor adicional con el mismo nombre");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_valor_adicional_anterior = dame_fila_valor_adicional_proyecto($id_valor_adicional);

        // Conversión de fechas
        if ($cadena_fecha_inicio_local_local == "")
        {
            $cadena_fecha_inicio_base_datos_local = "NULL";
        }
        else
        {
            $cadena_fecha_inicio_base_datos_local = "'".convierte_formato_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS)."'";
        }
        if ($cadena_fecha_fin_local_local == "")
        {
            $cadena_fecha_fin_base_datos_local = "NULL";
        }
        else
        {
            $cadena_fecha_fin_base_datos_local = "'".convierte_formato_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS)."'";
        }

        // Se modifica el valor adicional del proyecto
        $operacion_modificacion = "
            UPDATE valores_adicionales_proyectos
            SET
                nombre = '".$bd_red->_($nombre)."',
                valor = '".$bd_red->_($valor)."',
                destino = '".$bd_red->_($destino)."',
                periodicidad = '".$bd_red->_($periodicidad)."',
                fecha_inicio = ".$cadena_fecha_inicio_base_datos_local.",
                fecha_fin = ".$cadena_fecha_fin_base_datos_local.",
                aplicar_intervalos_sin_valores = ".$aplicar_intervalos_sin_valores."
            WHERE
                id = '".$bd_red->_($id_valor_adicional)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se invalida el avance y el estado del proyecto
            invalida_avance_estado_proyecto($id_proyecto);

            // Se recupera la fila actual
            $fila_valor_adicional_actual = dame_fila_valor_adicional_proyecto($id_valor_adicional);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_valor_adicional_proyecto(
                $fila_valor_adicional_actual,
                $fila_valor_adicional_anterior);

            $res = "OK";
            $msg = $idiomas->_("Valor adicional modificado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del valor adicional del proyecto
    function anyade_accion_usuario_modificar_valor_adicional_proyecto($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_VALOR_ADICIONAL_PROYECTO;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["destino"] != $fila_anterior["destino"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESTINO_VALOR_ADICIONAL_PROYECTO] = $fila_actual["destino"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESTINO_VALOR_ADICIONAL_PROYECTO] = $fila_anterior["destino"];
        }
        if ($fila_actual["valor"] != $fila_anterior["valor"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_ADICIONAL_PROYECTO] = $fila_actual["valor"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VALOR_ADICIONAL_PROYECTO] = $fila_anterior["valor"];
        }
        if ($fila_actual["periodicidad"] != $fila_anterior["periodicidad"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PERIODICIDAD_VALOR_ADICIONAL_PROYECTO] = $fila_actual["periodicidad"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PERIODICIDAD_VALOR_ADICIONAL_PROYECTO] = $fila_anterior["periodicidad"];
        }
        if ($fila_actual["fecha_inicio"] != $fila_anterior["fecha_inicio"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_actual["fecha_inicio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_anterior["fecha_inicio"];
        }
        if ($fila_actual["fecha_fin"] != $fila_anterior["fecha_fin"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_actual["fecha_fin"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_anterior["fecha_fin"];
        }
        if ($fila_actual["aplicar_intervalos_sin_valores"] != $fila_anterior["aplicar_intervalos_sin_valores"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_APLICAR_INTERVALOS_SIN_VALORES_VALOR_ADICIONAL_PROYECTO] = $fila_actual["aplicar_intervalos_sin_valores"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_APLICAR_INTERVALOS_SIN_VALORES_VALOR_ADICIONAL_PROYECTO] = $fila_anterior["aplicar_intervalos_sin_valores"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Nombre del proyecto
        $nombre_proyecto = dame_nombre_proyecto($fila_actual["proyecto"]);

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"]." (".$nombre_proyecto.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"]." (".$nombre_proyecto.")",
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
