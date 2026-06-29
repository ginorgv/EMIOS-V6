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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_VALOR_ADICIONAL_PROYECTO, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_proyecto = $_POST['id_proyecto'];
    $nombre = $_POST['nombre'];
    $destino = $_POST['destino'];
    $valor = $_POST['valor'];
    $periodicidad = $_POST['periodicidad'];
    $cadena_fecha_inicio_local_local = $_POST['fecha_inicio'];
    $cadena_fecha_fin_local_local = $_POST['fecha_fin'];
    $aplicar_intervalos_sin_valores = $_POST['aplicar_intervalos_sin_valores'];

    // Se comprueba si existe un valor adicional con el mismo nombre en el mismo proyecto
    $consulta_existe = "
        SELECT *
        FROM valores_adicionales_proyectos
        WHERE
            (proyecto = '".$bd_red->_($id_proyecto)."')
            AND (nombre = '".$bd_red->_($nombre)."')";
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

        // Se añade el valor adicional del proyecto
        $operacion_insercion = "
            INSERT INTO valores_adicionales_proyectos (
                nombre,
                red,
                proyecto,
                destino,
                valor,
                periodicidad,
                fecha_inicio,
                fecha_fin,
                aplicar_intervalos_sin_valores
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_proyecto)."',
                '".$bd_red->_($destino)."',
                '".$bd_red->_($valor)."',
                '".$bd_red->_($periodicidad)."',
                ".$cadena_fecha_inicio_base_datos_local.",
                ".$cadena_fecha_fin_base_datos_local.",
                '".$bd_red->_($aplicar_intervalos_sin_valores)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del valor adicional añadido
            $id_valor_adicional = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_valor_adicional = dame_fila_valor_adicional_proyecto($id_valor_adicional);

            // Se invalida el avance y el estado del proyecto
            invalida_avance_estado_proyecto($id_proyecto);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_valor_adicional_proyecto($fila_valor_adicional);

            $res = "OK";
            $msg = $idiomas->_("Valor adicional añadido correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del valor adicional del proyecto
    function anyade_accion_usuario_anyadir_valor_adicional_proyecto($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_VALOR_ADICIONAL_PROYECTO;
        $objeto_accion_usuario = $fila["nombre"]." (".dame_nombre_proyecto($fila["proyecto"]).")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESTINO_VALOR_ADICIONAL_PROYECTO] = $fila["destino"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_ADICIONAL_PROYECTO] = $fila["valor"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PERIODICIDAD_VALOR_ADICIONAL_PROYECTO] = $fila["periodicidad"];
        if ($fila["fecha_inicio"] != "")
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila["fecha_inicio"];
        }
        if ($fila["fecha_fin"] != "")
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila["fecha_fin"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_APLICAR_INTERVALOS_SIN_VALORES_VALOR_ADICIONAL_PROYECTO] = $fila["aplicar_intervalos_sin_valores"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
