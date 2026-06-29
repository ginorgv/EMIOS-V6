<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre_tarifa_electrica = $_POST['nombre_tarifa_electrica'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $ciclo = $_POST['ciclo'];
    $region = $_POST['region'];
    $id_grupo = $_POST['id_grupo'];
    $expiracion = $_POST['expiracion'];
    $fecha_expiracion = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];
    $precio_consumo_ponta = $_POST['precio_consumo_ponta'];
    $precio_consumo_cheia = $_POST['precio_consumo_cheia'];
    $precio_consumo_vazio_normal = $_POST['precio_consumo_vazio_normal'];
    $precio_consumo_super_vazio = $_POST['precio_consumo_super_vazio'];
    $precio_acceso_ponta = $_POST['precio_acceso_ponta'];
    $precio_acceso_cheia = $_POST['precio_acceso_cheia'];
    $precio_acceso_vazio_normal = $_POST['precio_acceso_vazio_normal'];
    $precio_acceso_super_vazio = $_POST['precio_acceso_super_vazio'];
    $potencia_contratada = $_POST['potencia_contratada'];
    $precio_potencia_contratada = $_POST['precio_potencia_contratada'];
    $precio_potencia_ponta = $_POST['precio_potencia_ponta'];
    $energia_reactiva_inductiva  = $_POST['energia_reactiva_inductiva'];
    $energia_reactiva_capacitiva = $_POST['energia_reactiva_capacitiva'];
    $impuesto_electrico = $_POST['impuesto_electrico'];
    $iva = $_POST['iva'];
    $contribucion_audiovisual = $_POST['contribucion_audiovisual'];
    $iva_reducido = $_POST['iva_reducido'];
    $id_tarifa_electrica_anterior = $_POST['id_tarifa_electrica'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($fecha_expiracion, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

	// Se comprueba si existe una tarifa eléctrica con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
        WHERE
            (nombre = '".$bd_red->_($nombre_tarifa_electrica)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una tarifa con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de añadir la tarifa eléctrica:
        // - Si el tipo de tarifa eléctrica es 'pass-through', se valida la fórmula de cálculo de precio de consumo
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        $anyadir_tarifa = true;

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        if (($anyadir_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_electricas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')";
            $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
            if ($res_tarifas_electricas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
            }
            if ($res_tarifas_electricas->dame_numero_filas() > 0)
            {
                $anyadir_tarifa = False;

                $fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila();
                $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_electrica.")";
            }
        }

        // Se añade la tarifa eléctrica
        if ($anyadir_tarifa == true)
        {

            // Se añade la tarifa eléctrica
            $operacion_insercion = "
                INSERT INTO ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL." (
                    nombre,
                    red,
                    descripcion,
                    tipo,
                    ciclo,
                    region,
                    grupo,
                    expiracion,
                    fecha_expiracion,
                    numero_dias_preaviso_expiracion,
                    iva,
                    impuesto_electrico,
                    contribucion_audiovisual,
                    iva_reducido
                ) VALUES (
                    '".$bd_red->_($nombre_tarifa_electrica)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($ciclo)."',
                    '".$bd_red->_($region)."',
                    '".$bd_red->_($id_grupo)."',
                    '".$bd_red->_($expiracion)."',
                    '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    '".$bd_red->_($iva)."',
                    '".$bd_red->_($impuesto_electrico)."',
                    '".$bd_red->_($contribucion_audiovisual)."',
                    '".$bd_red->_($iva_reducido)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }

            // Se recuperan el id y la fila de la tarifa eléctrica añadida
            $id_tarifa_electrica = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa_electrica);

            // ELISABET TODO : Crear la tabla de conceptos adicionales de tarifa
            // Si el identificador de tarifa existe, es un duplicado de una tarifa existente:
            // - Se duplican los conceptos_adicionales de factura de la tarifa anterior
            /*
            if ($id_tarifa_electrica_anterior != ID_NINGUNO)
            {
                duplica_conceptos_adicionales_factura_tarifa_anterior(
                    TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA,
                    $id_tarifa_electrica_anterior,
                    $id_tarifa_electrica);
            }*/

            // Se añade la información de los tramos de la tarifa eléctrica
            $operacion_insercion_tramo = "
                INSERT INTO ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL." (
                    red,
                    tarifa_electrica,
                    precio_consumo_ponta,
                    precio_consumo_cheia,
                    precio_consumo_vazio,
                    precio_consumo_super_vazio,
                    precio_consumo_tarifa_acceso_ponta,
                    precio_consumo_tarifa_acceso_cheia,
                    precio_consumo_tarifa_acceso_vazio,
                    precio_consumo_tarifa_acceso_super_vazio,
                    precio_potencia_contratada,
                    precio_potencia_ponta,
                    potencia_contratada,
                    precio_inductiva,
                    precio_capacitiva
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_tarifa_electrica)."',
                    '".$bd_red->_($precio_consumo_ponta)."',
                    '".$bd_red->_($precio_consumo_cheia)."',
                    '".$bd_red->_($precio_consumo_vazio_normal)."',
                    '".$bd_red->_($precio_consumo_super_vazio)."',
                    '".$bd_red->_($precio_acceso_ponta)."',
                    '".$bd_red->_($precio_acceso_cheia)."',
                    '".$bd_red->_($precio_acceso_vazio_normal)."',
                    '".$bd_red->_($precio_acceso_super_vazio)."',
                    '".$bd_red->_($precio_potencia_contratada)."',
                    '".$bd_red->_($precio_potencia_ponta)."',
                    '".$bd_red->_($potencia_contratada)."',
                    '".$bd_red->_($energia_reactiva_inductiva)."',
                    '".$bd_red->_($energia_reactiva_capacitiva)."'
                )";
            $res_insercion_tramo = $bd_red->ejecuta_operacion($operacion_insercion_tramo);
            if ($res_insercion_tramo == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_tramo."'");
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_tarifa_electricidad_Portugal($fila_tarifa_electrica, $filas_tramos_tarifa_electrica);

            $res = "OK";
            $msg = $idiomas->_("Tarifa añadida correctamente").".\n".
                $idiomas->_("Recuerde que debe asignar la tarifa eléctrica a un sensor de energía activa").".\n".
                $idiomas->_("Si este sensor ya tiene datos, tendrá que recalcular los datos eléctricos para que se calculen los costes y tramos de los sensores asociados a esta tarifa eléctrica");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición de la tarifa eléctrica
    function anyade_accion_usuario_anyadir_tarifa_electricidad_Portugal($fila, $filas_tramos)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_TARIFA;
        $objeto_accion_usuario = $fila["nombre"];

        // Características de tipo de tarifa eléctrica
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($fila["tipo"]);

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL, $fila["grupo"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CICLO_TARIFA_ELECTRICA] = $fila["ciclo"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_REGION_TARIFA_ELECTRICA] = $fila["region"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila["expiracion"];
        if ($fila["expiracion"] == EXPIRACION_TARIFA_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila["fecha_expiracion"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila["numero_dias_preaviso_expiracion"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila["impuesto_electrico"];
      	$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila["iva"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTRIBUCION_AUDIOVISUAL] = $fila["contribucion_audiovisual"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_REDUCIDO] = $fila["iva_reducido"];

				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_PONTA] = $filas_tramos["precio_consumo_ponta"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_CHEIA] = $filas_tramos["precio_consumo_cheia"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_VAZIO] = $filas_tramos["precio_consumo_vazio"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_SUPER_VAZIO] = $filas_tramos["precio_consumo_super_vazio"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_PONTA] = $filas_tramos["precio_consumo_tarifa_acceso_ponta"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_CHEIA] = $filas_tramos["precio_consumo_tarifa_acceso_cheia"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_VAZIO] = $filas_tramos["precio_consumo_tarifa_acceso_vazio"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_SUPER_VAZIO] = $filas_tramos["precio_consumo_tarifa_acceso_super_vazio"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_CONTRATADA] = $filas_tramos["precio_potencia_contratada"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_PONTA] = $filas_tramos["precio_potencia_ponta"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIA_CONTRATADA] = $filas_tramos["potencia_contratada"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_INDUCTIVA] = $filas_tramos["precio_inductiva"];
				$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_CAPACITIVA] = $filas_tramos["precio_capacitiva"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
