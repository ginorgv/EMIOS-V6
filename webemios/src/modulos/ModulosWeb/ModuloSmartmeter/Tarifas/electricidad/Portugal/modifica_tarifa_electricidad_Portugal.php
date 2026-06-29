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
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/TarifaElectrica_Portugal.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFA_ELECTRICA, $_POST);

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
    $id_tarifa_electrica = $_POST['id_tarifa_electrica'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($fecha_expiracion, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    // Se comprueba si existe otra tarifa eléctrica con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_tarifa_electrica)."')";
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
        $modificar_tarifa = true;

				// Comprobaciones antes de modificar la tarifa eléctrica:
        // Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        if (($modificar_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_electricas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa_electrica)."')";
            $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
            if ($res_tarifas_electricas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
            }
            if ($res_tarifas_electricas->dame_numero_filas() > 0)
            {
                $modificar_tarifa = False;

                $fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila();
                $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_electrica.")";
            }
        }

        // Se modifica la tarifa eléctrica
        if ($modificar_tarifa == true)
        {
            // Se recuperan la filas de la tarifa y de los tramos de la tarifa anteriores (antes de la modificación)
        		$fila_tarifa_electrica_anterior = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa_electrica);
            $filas_tramos_tarifa_electrica_anteriores = dame_filas_tramos_tarifa_electricidad_Portugal($id_tarifa_electrica);

            // Se modifica la tarifa eléctrica
            $operacion_modificacion = "
                UPDATE ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
                SET
										nombre = '".$bd_red->_($nombre_tarifa_electrica)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    ciclo = '".$bd_red->_($ciclo)."',
										region = '".$bd_red->_($region)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    expiracion = '".$bd_red->_($expiracion)."',
                    fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    numero_dias_preaviso_expiracion = '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    impuesto_electrico = '".$bd_red->_($impuesto_electrico)."',
                    iva = '".$bd_red->_($iva)."',
                    contribucion_audiovisual = '".$bd_red->_($contribucion_audiovisual)."',
                    iva_reducido = '".$bd_red->_($iva_reducido)."'
                WHERE
                    id = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            // Se actualiza la información de los tramos de la tarifa eléctrica (se eliminan y se añaden)
            $operacion_modificacion_tramos_tarifa_electrica = "
                UPDATE ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL."
								SET
										precio_consumo_ponta = '".$bd_red->_($precio_consumo_ponta)."',
										precio_consumo_cheia = '".$bd_red->_($precio_consumo_cheia)."',
										precio_consumo_vazio = '".$bd_red->_($precio_consumo_vazio_normal)."',
										precio_consumo_super_vazio = '".$bd_red->_($precio_consumo_super_vazio)."',
										precio_consumo_tarifa_acceso_ponta = '".$bd_red->_($precio_acceso_ponta)."',
										precio_consumo_tarifa_acceso_cheia = '".$bd_red->_($precio_acceso_cheia)."',
										precio_consumo_tarifa_acceso_vazio = '".$bd_red->_($precio_acceso_vazio_normal)."',
										precio_consumo_tarifa_acceso_super_vazio = '".$bd_red->_($precio_acceso_super_vazio)."',
										precio_potencia_contratada = '".$bd_red->_($precio_potencia_contratada)."',
										precio_potencia_ponta = '".$bd_red->_($precio_potencia_ponta)."',
										potencia_contratada = '".$bd_red->_($potencia_contratada)."',
										precio_inductiva = '".$bd_red->_($energia_reactiva_inductiva)."',
										precio_capacitiva = '".$bd_red->_($energia_reactiva_capacitiva)."'
                WHERE
                    tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_modificacion_tramos_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_modificacion_tramos_tarifa_electrica);
            if ($res_modificacion_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_tramos_tarifa_electrica."'");
            }

            // Si tiene grupo asignado se asigna el grupo a los sensores que tenían asignada la tarifa eléctrica
            if ($id_grupo != ID_NINGUNO)
            {
                asigna_grupo_tarifas_sensores_tarifa(MEDICION_ELECTRICIDAD, $id_grupo, $id_tarifa_electrica);
            }

            // Se recuperan la filas de la tarifa y de los tramos de la tarifa actuales
            $fila_tarifa_electrica_actual = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa_electrica);
            $filas_tramos_tarifa_electrica_actuales = dame_filas_tramos_tarifa_electricidad_Portugal($id_tarifa_electrica);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_tarifa_electricidad_Portugal(
                $fila_tarifa_electrica_actual,
                $fila_tarifa_electrica_anterior,
                $filas_tramos_tarifa_electrica_actuales,
                $filas_tramos_tarifa_electrica_anteriores);

            $res = "OK";
            $msg = $idiomas->_("Tarifa modificada correctamente").".\n".
                $idiomas->_("Los nuevos parámetros tendrán efecto a partir de los siguientes datos recibidos").".\n".
                $idiomas->_("Si quiere que se vuelvan a calcular datos anteriores con los nuevos parámetros de la tarifa eléctrica, tendrá que recalcular datos eléctricos seleccionando esta tarifa eléctrica y el tiempo a partir del cual quiere que sean efectivos");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la tarifa
    function anyade_accion_usuario_modificar_tarifa_electricidad_Portugal(
        $fila_actual,
        $fila_anterior,
        $filas_tramos_actuales,
        $filas_tramos_anteriores)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_TARIFA;

        // Características de tipos de tarifas eléctricas
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($fila_actual["tipo"]);
        $caracteristicas_tipo_tarifa_electrica_anterior = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($fila_anterior["tipo"]);
        $tipo_calculo_coste_potencias_anterior = $caracteristicas_tipo_tarifa_electrica_anterior["tipo_calculo_coste_potencias"];
        $parametros_medida_datos_facturacion_anterior = $caracteristicas_tipo_tarifa_electrica_anterior["parametros_medida_datos_facturacion"];

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
        $parametros_accion_usuario_anteriores = array();
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
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
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["ciclo"] != $fila_anterior["ciclo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CICLO_TARIFA_ELECTRICA] = $fila_actual["ciclo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CICLO_TARIFA_ELECTRICA] = $fila_anterior["ciclo"];
        }
				if ($fila_actual["region"] != $fila_anterior["region"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_REGION_TARIFA_ELECTRICA] = $fila_actual["region"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_REGION_TARIFA_ELECTRICA] = $fila_anterior["region"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL, $fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL, $fila_anterior["grupo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo_anterior;
        }
        if ($fila_actual["expiracion"] != $fila_anterior["expiracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_actual["expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_anterior["expiracion"];
        }
        if ($fila_actual["fecha_expiracion"] != $fila_anterior["fecha_expiracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila_actual["fecha_expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila_anterior["fecha_expiracion"];
        }
        if (($fila_actual["numero_dias_preaviso_expiracion"] != $fila_anterior["numero_dias_preaviso_expiracion"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila_actual["numero_dias_preaviso_expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila_anterior["numero_dias_preaviso_expiracion"];
        }
        if ($fila_actual["impuesto_electrico"] != $fila_anterior["impuesto_electrico"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila_actual["impuesto_electrico"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila_anterior["impuesto_electrico"];
        }
        if ($fila_actual["iva"] != $fila_anterior["iva"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila_actual["iva"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA] = $fila_anterior["iva"];
        }
        if ($fila_actual["iva_reducido"] != $fila_anterior["iva_reducido"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_REDUCIDO] = $fila_actual["iva_reducido"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA_REDUCIDO] = $fila_anterior["iva_reducido"];
        }
        if ($fila_actual["contribucion_audiovisual"] != $fila_anterior["contribucion_audiovisual"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTRIBUCION_AUDIOVISUAL] = $fila_actual["contribucion_audiovisual"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CONTRIBUCION_AUDIOVISUAL] = $fila_anterior["contribucion_audiovisual"];
        }

				if ($filas_tramos_actuales["precio_consumo_ponta"] != $filas_tramos_anteriores["precio_consumo_ponta"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_PONTA] = $filas_tramos_actuales["precio_consumo_ponta"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_PONTA] = $filas_tramos_anteriores["precio_consumo_ponta"];
        }
				if ($filas_tramos_actuales["precio_consumo_cheia"] != $filas_tramos_anteriores["precio_consumo_cheia"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_CHEIA] = $filas_tramos_actuales["precio_consumo_cheia"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_CHEIA] = $filas_tramos_anteriores["precio_consumo_cheia"];
        }
				if ($filas_tramos_actuales["precio_consumo_vazio"] != $filas_tramos_anteriores["precio_consumo_vazio"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_VAZIO] = $filas_tramos_actuales["precio_consumo_vazio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_VAZIO] = $filas_tramos_anteriores["precio_consumo_vazio"];
        }
				if ($filas_tramos_actuales["precio_consumo_super_vazio"] != $filas_tramos_anteriores["precio_consumo_super_vazio"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_SUPER_VAZIO] = $filas_tramos_actuales["precio_consumo_super_vazio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_SUPER_VAZIO] = $filas_tramos_anteriores["precio_consumo_super_vazio"];
        }

				if ($filas_tramos_actuales["precio_consumo_tarifa_acceso_ponta"] != $filas_tramos_anteriores["precio_consumo_tarifa_acceso_ponta"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_PONTA] = $filas_tramos_actuales["precio_consumo_tarifa_acceso_ponta"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_PONTA] = $filas_tramos_anteriores["precio_consumo_tarifa_acceso_ponta"];
        }
				if ($filas_tramos_actuales["precio_consumo_tarifa_acceso_cheia"] != $filas_tramos_anteriores["precio_consumo_tarifa_acceso_cheia"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_CHEIA] = $filas_tramos_actuales["precio_consumo_tarifa_acceso_cheia"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_CHEIA] = $filas_tramos_anteriores["precio_consumo_tarifa_acceso_cheia"];
        }
				if ($filas_tramos_actuales["precio_consumo_tarifa_acceso_vazio"] != $filas_tramos_anteriores["precio_consumo_tarifa_acceso_vazio"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_VAZIO] = $filas_tramos_actuales["precio_consumo_tarifa_acceso_vazio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_VAZIO] = $filas_tramos_anteriores["precio_consumo_tarifa_acceso_vazio"];
        }
				if ($filas_tramos_actuales["precio_consumo_tarifa_acceso_super_vazio"] != $filas_tramos_anteriores["precio_consumo_tarifa_acceso_super_vazio"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_SUPER_VAZIO] = $filas_tramos_actuales["precio_consumo_tarifa_acceso_super_vazio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_ACCESO_SUPER_VAZIO] = $filas_tramos_anteriores["precio_consumo_tarifa_acceso_super_vazio"];
        }

				if ($filas_tramos_actuales["precio_potencia_contratada"] != $filas_tramos_anteriores["precio_potencia_contratada"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_CONTRATADA] = $filas_tramos_actuales["precio_potencia_contratada"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_CONTRATADA] = $filas_tramos_anteriores["precio_potencia_contratada"];
        }
				if ($filas_tramos_actuales["precio_potencia_ponta"] != $filas_tramos_anteriores["precio_potencia_ponta"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_PONTA] = $filas_tramos_actuales["precio_potencia_ponta"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_POTENCIA_PONTA] = $filas_tramos_anteriores["precio_potencia_ponta"];
        }
				if ($filas_tramos_actuales["potencia_contratada"] != $filas_tramos_anteriores["potencia_contratada"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIA_CONTRATADA] = $filas_tramos_actuales["potencia_contratada"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_POTENCIA_CONTRATADA] = $filas_tramos_anteriores["potencia_contratada"];
        }

				if ($filas_tramos_actuales["precio_inductiva"] != $filas_tramos_anteriores["precio_inductiva"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_INDUCTIVA] = $filas_tramos_actuales["precio_inductiva"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_INDUCTIVA] = $filas_tramos_anteriores["precio_inductiva"];
        }
				if ($filas_tramos_actuales["precio_capacitiva"] != $filas_tramos_anteriores["precio_capacitiva"])
        {
        		$parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_CAPACITIVA] = $filas_tramos_actuales["precio_capacitiva"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_REACTIVA_CAPACITIVA] = $filas_tramos_anteriores["precio_capacitiva"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        // (siempre se añade el parámetro de medición)
        if (count($parametros_accion_usuario) == 1)
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
