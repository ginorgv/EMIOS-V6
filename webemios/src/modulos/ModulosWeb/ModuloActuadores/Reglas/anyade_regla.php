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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_REGLA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $modo_activacion = $_POST['modo_activacion'];
    $numero_dias_caducidad_acciones = $_POST['numero_dias_caducidad_acciones'];
    $id_regla_anterior = $_POST["id_regla_anterior"];

    // Se comprueba si existe una regla con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM reglas
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
        $msg = $idiomas->_("Ya existe una regla con el mismo nombre");
    }
    else
    {
        // Se añade la regla
        $operacion_insercion = "
            INSERT INTO reglas (
                nombre,
                red,
                descripcion,
                tipo,
                modo_activacion,
                numero_dias_caducidad_acciones,
                activaciones,
                habilitada
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($modo_activacion)."',
                '".$bd_red->_($numero_dias_caducidad_acciones)."',
                '0',
                '".VALOR_SI."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila de la regla añadida
            $id_regla = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_regla = dame_fila_regla($id_regla);

            // Si el identificador de regla existe, es un duplicado de una regla existente:
            // - Se duplican los rangos de días, periodos, sucesos y acciones de la regla anterior
            if ($id_regla_anterior != ID_NINGUNO)
            {
                duplica_rangos_dias_regla_anterior($id_regla_anterior, $id_regla);
                duplica_periodos_regla_anterior($id_regla_anterior, $id_regla);
                duplica_sucesos_regla_anterior($id_regla_anterior, $id_regla);
                duplica_acciones_regla_anterior($id_regla_anterior, $id_regla);
            }

            // Se envía mensaje MQTT de administración de reglas
            notifica_operacion_administracion_regla(OPERACION_ADICION, $id_regla);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_regla($fila_regla);

            $res = "OK";
            $msg = $idiomas->_("Regla añadida correctamente").".\n".
                $idiomas->_("Haga click en la regla para desplegar sus detalles y configurar su comportamiento");
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


    // Duplica los periodos de la regla anterior
    function duplica_periodos_regla_anterior($id_regla_anterior, $id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los periodos de la regla anterior (origen de la actual), se cambia el id de origen y se añaden
        $consulta_periodos = "
            SELECT
                dia_inicio,
                dia_fin,
                hora_inicio,
                hora_fin
            FROM periodos
            WHERE
                (origen = '".ORIGEN_PERIODOS_REGLA."')
                AND (id_origen = '".$bd_red->_($id_regla_anterior)."')";
        $res_periodos = $bd_red->ejecuta_consulta($consulta_periodos);
        if ($res_periodos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodos."'");
        }

        while ($fila_periodo = $res_periodos->dame_siguiente_fila())
        {
            $operacion_insercion_periodo = "
                INSERT INTO periodos (
                    red,
                    origen,
                    id_origen,
                    dia_inicio,
                    dia_fin,
                    hora_inicio,
                    hora_fin
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".ORIGEN_PERIODOS_REGLA."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($fila_periodo["dia_inicio"])."',
                    '".$bd_red->_($fila_periodo["dia_fin"])."',
                    '".$bd_red->_($fila_periodo["hora_inicio"])."',
                    '".$bd_red->_($fila_periodo["hora_fin"])."'
                )";
            $res_insercion_periodo = $bd_red->ejecuta_operacion($operacion_insercion_periodo);
            if ($res_insercion_periodo == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_periodo."'");
            }
        }
    }


    // Duplica los rangos de días de la regla anterior
    function duplica_rangos_dias_regla_anterior($id_regla_anterior, $id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los rangos de días de la regla anterior (origen de la actual), se cambia el id de origen y se añaden
        $consulta_rangos_dias = "
            SELECT
                dia_anyo_inicio,
                dia_anyo_fin
            FROM rangos_dias
            WHERE
                (origen = '".ORIGEN_RANGOS_DIAS_REGLA."')
                AND (id_origen = '".$bd_red->_($id_regla_anterior)."')";
        $res_rangos_dias = $bd_red->ejecuta_consulta($consulta_rangos_dias);
        if ($res_rangos_dias == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_rangos_dias."'");
        }

        while ($fila_rango_dias = $res_rangos_dias->dame_siguiente_fila())
        {
            $operacion_insercion_rango_dias = "
                INSERT INTO rangos_dias (
                    red,
                    origen,
                    id_origen,
                    dia_anyo_inicio,
                    dia_anyo_fin
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".ORIGEN_RANGOS_DIAS_REGLA."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($fila_rango_dias["dia_anyo_inicio"])."',
                    '".$bd_red->_($fila_rango_dias["dia_anyo_fin"])."'
                )";
            $res_insercion_rango_dias = $bd_red->ejecuta_operacion($operacion_insercion_rango_dias);
            if ($res_insercion_rango_dias == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_rango_dias."'");
            }
        }
    }


    // Duplica los sucesos de la regla anterior
    function duplica_sucesos_regla_anterior($id_regla_anterior, $id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los sucesos de la regla anterior (origen de la actual), se cambia la regla y se añaden
        $consulta_sucesos = "
            SELECT
                nombre,
                causa,
                id_causa,
                origen,
                id_origen,
                modo_activacion,
                parametros_modo_activacion,
                numero_activaciones
            FROM sucesos_reglas
            WHERE
                regla = '".$bd_red->_($id_regla_anterior)."'";
        $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
        if ($res_sucesos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
        }

        while ($fila_suceso = $res_sucesos->dame_siguiente_fila())
        {
            $operacion_insercion_suceso = "
                INSERT INTO sucesos_reglas (
                    nombre,
                    red,
                    regla,
                    causa,
                    id_causa,
                    origen,
                    id_origen,
                    modo_activacion,
                    parametros_modo_activacion,
                    numero_activaciones,
                    activaciones
                ) VALUES (
                    '".$bd_red->_($fila_suceso["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($fila_suceso["causa"])."',
                    '".$bd_red->_($fila_suceso["id_causa"])."',
                    '".$bd_red->_($fila_suceso["origen"])."',
                    '".$bd_red->_($fila_suceso["id_origen"])."',
                    '".$bd_red->_($fila_suceso["modo_activacion"])."',
                    '".$bd_red->_($fila_suceso["parametros_modo_activacion"])."',
                    '".$bd_red->_($fila_suceso["numero_activaciones"])."',
                    0
                )";
            $res_insercion_suceso = $bd_red->ejecuta_operacion($operacion_insercion_suceso);
            if ($res_insercion_suceso == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_suceso."'");
            }
        }
    }


    // Duplica las acciones de la regla anterior
    function duplica_acciones_regla_anterior($id_regla_anterior, $id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los periodos del evento anterior (origen del actual), se cambia el id y se añaden
        $consulta_acciones = "
            SELECT
                nombre,
                tipo,
                causa,
                clase,
                destino,
                id_destino,
                contenido_accion
            FROM acciones_reglas
            WHERE
                regla = '".$bd_red->_($id_regla_anterior)."'";
        $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }

        while ($fila_accion = $res_acciones->dame_siguiente_fila())
        {
            $operacion_insercion_accion = "
                INSERT INTO acciones_reglas (
                    nombre,
                    red,
                    regla,
                    tipo,
                    causa,
                    clase,
                    destino,
                    id_destino,
                    contenido_accion
                ) VALUES (
                    '".$bd_red->_($fila_accion["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_regla)."',
                    '".$bd_red->_($fila_accion["tipo"])."',
                    '".$bd_red->_($fila_accion["causa"])."',
                    '".$bd_red->_($fila_accion["clase"])."',
                    '".$bd_red->_($fila_accion["destino"])."',
                    '".$bd_red->_($fila_accion["id_destino"])."',
                    '".$bd_red->_($fila_accion["contenido_accion"])."'
                )";
            $res_insercion_accion = $bd_red->ejecuta_operacion($operacion_insercion_accion);
            if ($res_insercion_accion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_accion."'");
            }
        }
    }


    // Añade la acción de usuario de adición de la regla
    function anyade_accion_usuario_anyadir_regla($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_REGLA;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_REGLA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MODO_ACTIVACION_REGLA] = $fila["modo_activacion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_CADUCIDAD_ACCIONES] = $fila["numero_dias_caducidad_acciones"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
