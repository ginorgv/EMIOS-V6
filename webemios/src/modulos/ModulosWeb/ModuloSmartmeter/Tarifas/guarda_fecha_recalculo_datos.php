<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GUARDAR_FECHA_RECALCULO_DATOS, $_POST);

    // Se guarda la fecha de recálculo de datos
    $parametros = $_POST;
    $medicion = $parametros["medicion"];
    switch ($medicion)
    {
        case MEDICION_ELECTRICIDAD:
        {
            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = guarda_fecha_recalculo_datos_electricidad_Espanya($parametros);
                    break;
                }
                default:
								case PAIS_PORTUGAL:
                {
                    $resultado = guarda_fecha_recalculo_datos_electricidad_Portugal($parametros);
                    break;
                }
                {
                    throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                }
            }
            break;
        }
        case MEDICION_GAS:
        {
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = guarda_fecha_recalculo_datos_gas_Espanya($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            break;
        }
        // Nota: La clase de agua no tiene valores de clase (el coste se calcula sólo en la factura)
        default:
        {
            throw new Exception("Medición incorrecta: '".$medicion."'");
        }
    }
    if ($resultado["res"] = "OK")
    {
        $ids_tarifas = $parametros["ids_tarifas"];
        $ids_grupos_tarifas = $parametros["ids_grupos_tarifas"];
        $cadena_fecha_hora_local_local = $parametros["fecha_hora"];

        // Conversión de fechas
        $cadena_fecha_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_BASE_DATOS);

        // Se añade la acción de usuario
        anyade_accion_usuario_guardar_fecha_recalculo_datos(
            $medicion,
            $ids_tarifas,
            $ids_grupos_tarifas,
            $cadena_fecha_base_datos_local);
    }
    print(json_encode($resultado));


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de recálculo de datos de sensores
    function anyade_accion_usuario_guardar_fecha_recalculo_datos(
        $medicion,
        $ids_tarifas,
        $ids_grupos_tarifas,
        $cadena_fecha_base_datos_local)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_GUARDAR_FECHA_RECALCULO_DATOS;
        $objeto_accion_usuario = NULL;

        // Nombres de parámetros
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $tabla_tarifas = TABLA_TARIFAS_ELECTRICAS_ESPANYA;
                $tabla_grupos_tarifas = TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA;
                break;
            }
            case MEDICION_GAS:
            {
                $tabla_tarifas = TABLA_TARIFAS_GAS_ESPANYA;
                $tabla_grupos_tarifas = TABLA_GRUPOS_TARIFAS_GAS_ESPANYA;
                break;
            }
            default:
            {
                return;
            }
        }
        $nombres_tarifas = dame_nombres_tarifas($tabla_tarifas, $ids_tarifas);
        $nombres_grupos_tarifas = dame_nombres_grupos_tarifas($tabla_grupos_tarifas, $ids_grupos_tarifas);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_TARIFAS] = $nombres_tarifas;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_GRUPOS_TARIFAS] = $nombres_grupos_tarifas;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $cadena_fecha_base_datos_local;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>

