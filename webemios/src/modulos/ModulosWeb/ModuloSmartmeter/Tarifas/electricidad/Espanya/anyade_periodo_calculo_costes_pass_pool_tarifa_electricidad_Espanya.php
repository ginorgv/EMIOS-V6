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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PERIODO_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_electrica = $_POST['id_tarifa_electrica'];
    $cadena_fecha_inicio_local_local = $_POST['fecha_inicio'];
    $cadena_fecha_fin_local_local = $_POST['fecha_fin'];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $cadena_fecha_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Se comprueba si existe un periodo de cálculo de costes con las mismas fechas en la misma tarifa eléctrica
    $consulta_existe = "
        SELECT *
        FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            (tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."')
            AND (fecha_inicio = '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."')
            AND (fecha_fin = '".$bd_red->_($cadena_fecha_fin_base_datos_local)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un periodo de cálculo de costes igual");
    }
    else
    {
        // Comprobaciones antes de añadir el periodo de cálculo de costes:
        // - Comprobación de periodo válido (dia de inicio <= dia de fin)
        // - Comprobación de solapamiento del periodo con otros periodos
        $anyadir_periodo_calculo_costes = true;

        // Comprobación de periodo válido (dia de inicio <= dia de fin)
        if ($anyadir_periodo_calculo_costes == true)
        {
            if ($fecha_inicio_local > $fecha_fin_local)
            {
                $anyadir_periodo_calculo_costes = false;

                $res = "ERROR";
                $msg = $idiomas->_("Las fechas del periodo de cálculo de costes son incorrectas");
            }
        }

        // Comprobación de solapamiento del periodo con otros periodos
        if ($anyadir_periodo_calculo_costes == true)
        {
            $consulta_solapamiento = "
                SELECT *
                FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    (tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."')
                    AND (((fecha_inicio >= '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."') AND (fecha_inicio <= '".$bd_red->_($cadena_fecha_fin_base_datos_local)."'))
                        OR ((fecha_fin >='".$bd_red->_($cadena_fecha_inicio_base_datos_local)."') AND (fecha_fin <= '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."'))
                        OR ((fecha_inicio <= '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."') AND (fecha_fin >= '".$bd_red->_($cadena_fecha_fin_base_datos_local)."')))";
            $res_solapamiento = $bd_red->ejecuta_consulta($consulta_solapamiento);
            if ($res_solapamiento == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_solapamiento."'");
            }
            if ($res_solapamiento->dame_numero_filas() > 0)
            {
                $anyadir_periodo_calculo_costes = false;

                $res = "ERROR";
                $msg = $idiomas->_("El periodo de cálculo de costes se solapa con otros periodos de cálculo de costes");
            }
        }

        // Se añade el periodo de cálculo de costes
        if ($anyadir_periodo_calculo_costes == true)
        {
            // Se añade el periodo de cálculo de costes
            $operacion_insercion = "
                INSERT INTO ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA." (
                    red,
                    tarifa_electrica,
                    fecha_inicio,
                    fecha_fin
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_tarifa_electrica)."',
                    '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."',
                    '".$bd_red->_($cadena_fecha_fin_base_datos_local)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila del periodo de cálculo de costes añadido
                $id_periodo_calculo_costes = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_periodo_calculo_costes = dame_fila_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($id_periodo_calculo_costes);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($fila_periodo_calculo_costes);

                $res = "OK";
                $msg = $idiomas->_("Periodo de cálculo de costes añadido correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
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


    // Añade la acción de usuario de adición del periodo de cálculo de costes de pass pool de una tarifa
    function anyade_accion_usuario_anyadir_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($fila)
    {
        // Nombre de tarifa
        $nombre_tarifa = dame_nombre_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $fila["tarifa_electrica"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_PERIODO_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICIDAD_ESPANYA;
        $objeto_accion_usuario = $nombre_tarifa;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila["fecha_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila["fecha_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
