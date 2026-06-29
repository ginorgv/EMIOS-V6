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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ASIGNAR_TARIFA_GRUPO_TARIFAS_SENSORES, $_POST);

    // Se asigna la tarifa según la medición
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
                    $resultado = asigna_tarifa_grupo_tarifas_sensores_electricidad_Espanya($parametros);
                    break;
                }
								case PAIS_PORTUGAL:
                {
                    $resultado = asigna_tarifa_grupo_tarifas_sensores_electricidad_Portugal($parametros);
                    break;
                }
                default:
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
                    $resultado = asigna_tarifa_grupo_tarifas_sensores_gas_Espanya($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            break;
        }
        case MEDICION_AGUA:
        {
            $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
            switch ($pais_tarifas_agua)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = asigna_tarifa_grupo_tarifas_sensores_agua_Espanya($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                }
            }
            break;
        }
        default:
        {
            throw new Exception("Medición desconocida: '".$medicion."'");
        }
    }
    if ($resultado["res"] = "OK")
    {
        $id_tarifa = $parametros["id_tarifa"];
        $id_grupo_tarifas = $parametros["id_grupo_tarifas"];
        $ids_sensores = $parametros["ids_sensores"];

        // Se añade la acción de usuario
        anyade_accion_usuario_asignar_tarifa_grupo_tarifas_sensores(
            $medicion,
            $id_tarifa,
            $id_grupo_tarifas,
            $ids_sensores);
    }
    print(json_encode($resultado));


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de asignación de tarifas y grupos de tarifas
    function anyade_accion_usuario_asignar_tarifa_grupo_tarifas_sensores(
        $medicion,
        $id_tarifa,
        $id_grupo_tarifas,
        $ids_sensores)
    {
        // Nombre de tarifa o de grupo de tarifas
        $nombre_tarifa_grupo_tarifas = NULL;
        if ($id_tarifa != ID_NINGUNO)
        {
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $nombre_tarifa_grupo_tarifas = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        }
        if ($id_grupo_tarifas != ID_NINGUNO)
        {
            $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
            $nombre_tarifa_grupo_tarifas = dame_nombre_tarifa($tabla_grupos_tarifas, $id_grupo_tarifas);
        }

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ASIGNA_TARIFA_GRUPO_TARIFAS_SENSORES;
        $objeto_accion_usuario = $nombre_tarifa_grupo_tarifas;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
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
            case MEDICION_AGUA:
            {
                $tabla_tarifas = TABLA_TARIFAS_AGUA_ESPANYA;
                $tabla_grupos_tarifas = TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA;
                break;
            }
            default:
            {
                return;
            }
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_TARIFA] = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO_TARIFAS] = dame_nombre_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES] = dame_nombres_sensores($ids_sensores);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>

