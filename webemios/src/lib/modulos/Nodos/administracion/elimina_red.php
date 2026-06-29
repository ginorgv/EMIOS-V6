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
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_RED, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_red = $_POST['id_red'];

    // Comprobaciones antes de eliminar la red:
    // - No se puede eliminar la red actual
    // - No se puede eliminar la red si tiene:
    //   - Dispositivos
    //   - Sensores o actuadores
    //   - Usuarios
    $eliminar_red = true;

    // No se puede eliminar la red actual
    if ($eliminar_red == true)
    {
        if ($_SESSION["id_red"] == $id_red)
        {
            $eliminar_red = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la red actual");
        }
    }

    // Se comprueba si existe algun dispositivo en la red
    if ($eliminar_red == true)
    {
        $consulta_dispositivos = "
            SELECT nombre
            FROM dispositivos
            WHERE
                red = '".$bd_red->_($id_red)."'
            ORDER BY nombre ASC";
        $res_dispositivos = $bd_red->ejecuta_consulta($consulta_dispositivos);
        if ($res_dispositivos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_dispositivos."'");
        }
        if ($res_dispositivos->dame_numero_filas() > 0)
        {
            $eliminar_red = false;

            $fila_dispositivo = $res_dispositivos->dame_siguiente_fila();
            $nombre_dispositivo = $fila_dispositivo["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la red porque tiene dispositivos asignados")."\n(".
                $nombre_dispositivo.")";
        }
    }

    // Se comprueba si existe algun sensor en la red
    if ($eliminar_red == true)
    {
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                red = '".$bd_red->_($id_red)."'
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        if ($res_sensores->dame_numero_filas() > 0)
        {
            $eliminar_red = false;

            $fila_sensor = $res_sensores->dame_siguiente_fila();
            $nombre_sensor = $fila_sensor["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la red porque tiene sensores asignados")."\n(".
                $nombre_sensor.")";
        }
    }

    // Se comprueba si existe algun actuador en la red
    if ($eliminar_red == true)
    {
        $consulta_actuadores = "
            SELECT nombre
            FROM actuadores
            WHERE
                red = '".$bd_red->_($id_red)."'
            ORDER BY nombre ASC";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }
        if ($res_actuadores->dame_numero_filas() > 0)
        {
            $eliminar_red = false;

            $fila_actuador = $res_actuadores->dame_siguiente_fila();
            $nombre_actuador = $fila_actuador["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la red porque tiene actuadores asignados")."\n(".
                $nombre_actuador.")";
        }
    }

    // Se comprueba si existe algun usuario en la red
    if ($eliminar_red == true)
    {
        $consulta_redes_usuarios = "
            SELECT usuario
            FROM redes_usuarios
            WHERE
                red = '".$bd_red->_($id_red)."'
            ORDER BY usuario";
        $res_redes_usuarios = $bd_red->ejecuta_consulta($consulta_redes_usuarios);
        if ($res_redes_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_redes_usuarios."'");
        }
        if ($res_redes_usuarios->dame_numero_filas() > 0)
        {
            $eliminar_red = false;

            $fila_red_usuario = $res_redes_usuarios->dame_siguiente_fila();
            $id_usuario_red_usuario = $fila_red_usuario["usuario"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la red porque tiene usuarios asignados")."\n(".
                $id_usuario_red_usuario.")";
        }
    }

    // Borrado de valores de sensores de la red
    if ($eliminar_red == true)
    {
        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_BORRA_VALORES_SENSORES_RED,
                "id_red" => $id_red
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si los datos de sensores están bloqueados (hay alguna operación de datos de sensores de esta red en ejecución)
        $datos_sensores_bloqueados = $resultado_funcion_externa["datos_sensores_bloqueados"];
        if ($datos_sensores_bloqueados == VALOR_SI)
        {
            $eliminar_red = False;

            $res = "ERROR";
            $msg = $idiomas->_("Se están realizando operaciones de datos en sensores de la red, inténtelo de nuevo en unos minutos");
        }
    }

    // Borrado de valores de proyectos de la red
    if ($eliminar_red == true)
    {
        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_BORRA_VALORES_REALES_SIMULADOS_PROYECTOS_RED,
                "id_red" => $id_red
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si los valores de los proyectos están bloqueados (hay alguna operación de valores de proyectos en ejecución)
        $valores_proyectos_bloqueados = $resultado_funcion_externa["valores_proyectos_bloqueados"];
        if ($datos_sensores_bloqueados == VALOR_SI)
        {
            $eliminar_red = False;

            $res = "ERROR";
            $msg = $idiomas->_("Se están actualizando el avance y el estado de proyectos, inténtelo de nuevo en unos minutos");
        }
    }

    // Se elimina la red y los elementos y datos correspondientes
    if ($eliminar_red == true)
    {
        // Se elimina la red
        $operacion_borrado = "
            DELETE
            FROM redes
            WHERE
                id = '".$bd_red->_($id_red)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan los elementos y datos de la red
            borra_elementos_red_base_datos_red($id_red);
            borra_datos_red_base_datos_datos($id_red);

            $res = "OK";
            $msg = $idiomas->_("Red eliminada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    function borra_elementos_red_base_datos_red($id_red)
    {
        // Elementos a borrar:
        // - Licencias
        // - Módulos de usuarios (superadministradores)
        // - Plantillas de informes
        // - Localizaciones
        // - Instalaciones
        // - Grupos de sensores
        // - Grupos de actuadores
        // - Programaciones
        // - Reglas
        // - Tarifas eléctricas
        // - Tarifas de gas
        // - Tarifas de agua
        // - Rangos de días
        // - Periodos
        // - Posiciones de mapa
        // - Imágenes

        // Nota: El campo 'red' está en todas las tablas aunque haya tablas que dependan de otras
        // (p.e. 'acciones_reglas' y 'reglas')

        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_licencias = "
            DELETE
            FROM licencias
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_licencias = $bd_red->ejecuta_operacion($operacion_borrado_licencias);
        if ($res_borrado_licencias == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_licencias."'");
        }

        $operacion_borrado_modulos_usuarios = "
            DELETE
            FROM modulos_usuarios
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_modulos_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_modulos_usuarios);
        if ($res_borrado_modulos_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_modulos_usuarios."'");
        }

        $operacion_borrado_plantillas_informes = "
            DELETE
            FROM plantillas_informes
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_plantillas_informes);
        if ($res_borrado_plantillas_informes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_plantillas_informes."'");
        }

        $operacion_borrado_parametros_plantillas_informes = "
            DELETE
            FROM parametros_plantillas_informes
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_parametros_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_parametros_plantillas_informes);
        if ($res_borrado_parametros_plantillas_informes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_plantillas_informes."'");
        }

        $operacion_borrado_elementos_plantillas_informes = "
            DELETE
            FROM elementos_plantillas_informes
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_elementos_plantillas_informes);
        if ($res_borrado_elementos_plantillas_informes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_elementos_plantillas_informes."'");
        }

        $operacion_borrado_localizaciones = "
            DELETE
            FROM localizaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_localizaciones = $bd_red->ejecuta_operacion($operacion_borrado_localizaciones);
        if ($res_borrado_localizaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_localizaciones."'");
        }

        $operacion_borrado_instalaciones = "
            DELETE
            FROM instalaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_instalaciones = $bd_red->ejecuta_operacion($operacion_borrado_instalaciones);
        if ($res_borrado_instalaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_instalaciones."'");
        }

        $operacion_borrado_equipos_instalaciones = "
            DELETE
            FROM equipos_instalaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_equipos_instalaciones = $bd_red->ejecuta_operacion($operacion_borrado_equipos_instalaciones);
        if ($res_borrado_equipos_instalaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_equipos_instalaciones."'");
        }

        $operacion_borrado_anotaciones_equipos_instalaciones = "
            DELETE
            FROM anotaciones_equipos_instalaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_anotaciones_equipos_instalaciones = $bd_red->ejecuta_operacion($operacion_borrado_anotaciones_equipos_instalaciones);
        if ($res_borrado_anotaciones_equipos_instalaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_anotaciones_equipos_instalaciones."'");
        }

        $operacion_borrado_grupos_sensores = "
            DELETE
            FROM grupos_sensores
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_grupos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_grupos_sensores);
        if ($res_borrado_grupos_sensores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_grupos_sensores."'");
        }

        $operacion_borrado_grupos_actuadores = "
            DELETE
            FROM grupos_actuadores
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_grupos_actuadores = $bd_red->ejecuta_operacion($operacion_borrado_grupos_actuadores);
        if ($res_borrado_grupos_actuadores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_grupos_actuadores."'");
        }

        $operacion_borrado_programaciones = "
            DELETE
            FROM programaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_programaciones = $bd_red->ejecuta_operacion($operacion_borrado_programaciones);
        if ($res_borrado_programaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_programaciones."'");
        }

        $operacion_borrado_acciones_programaciones = "
            DELETE
            FROM acciones_programaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_acciones_programaciones = $bd_red->ejecuta_operacion($operacion_borrado_acciones_programaciones);
        if ($res_borrado_acciones_programaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_programaciones."'");
        }

        $operacion_borrado_excepciones_programaciones = "
            DELETE
            FROM excepciones_programaciones
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_excepciones_programaciones = $bd_red->ejecuta_operacion($operacion_borrado_excepciones_programaciones);
        if ($res_borrado_excepciones_programaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_excepciones_programaciones."'");
        }

        $operacion_borrado_reglas = "
            DELETE
            FROM reglas
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_reglas = $bd_red->ejecuta_operacion($operacion_borrado_reglas);
        if ($res_borrado_reglas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_reglas."'");
        }

        $operacion_borrado_sucesos_reglas = "
            DELETE
            FROM sucesos_reglas
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_sucesos_reglas = $bd_red->ejecuta_operacion($operacion_borrado_sucesos_reglas);
        if ($res_borrado_sucesos_reglas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_sucesos_reglas."'");
        }

        $operacion_borrado_acciones_reglas = "
            DELETE
            FROM acciones_reglas
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_acciones_reglas = $bd_red->ejecuta_operacion($operacion_borrado_acciones_reglas);
        if ($res_borrado_acciones_reglas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_reglas."'");
        }

        // Se borran las tablas de tarifas (electricidad - España)
        $operacion_borrado_grupos_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_grupos_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_grupos_tarifas_electricas_Espanya);
        if ($res_borrado_grupos_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_grupos_tarifas_electricas_Espanya."'");
        }

        $operacion_borrado_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_tarifas_electricas_Espanya);
        if ($res_borrado_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_tarifas_electricas_Espanya."'");
        }

        $operacion_borrado_tramos_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_tramos_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_tramos_tarifas_electricas_Espanya);
        if ($res_borrado_tramos_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_tramos_tarifas_electricas_Espanya."'");
        }

        $operacion_borrado_periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya);
        if ($res_borrado_periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_periodos_calculo_costes_pass_pool_tarifas_electricas_Espanya."'");
        }

        $operacion_borrado_conceptos_coste_pass_through_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_conceptos_coste_pass_through_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_coste_pass_through_tarifas_electricas_Espanya);
        if ($res_borrado_conceptos_coste_pass_through_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_coste_pass_through_tarifas_electricas_Espanya."'");
        }

        $operacion_borrado_conceptos_adicionales_factura_tarifas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_conceptos_adicionales_factura_tarifas_electricas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_adicionales_factura_tarifas_electricas_Espanya);
        if ($res_borrado_conceptos_adicionales_factura_tarifas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_adicionales_factura_tarifas_electricas_Espanya."'");
        }

        // Se borran las tablas de tarifas (gas - España)
        $operacion_borrado_grupos_tarifas_gas_Espanya = "
            DELETE
            FROM ".TABLA_GRUPOS_TARIFAS_GAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_grupos_tarifas_gas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_grupos_tarifas_gas_Espanya);
        if ($res_borrado_grupos_tarifas_gas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_grupos_tarifas_gas_Espanya."'");
        }

        $operacion_borrado_tarifas_gas_Espanya = "
            DELETE
            FROM ".TABLA_TARIFAS_GAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_tarifas_gas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_tarifas_gas_Espanya);
        if ($res_borrado_tarifas_gas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_tarifas_gas_Espanya."'");
        }

        $operacion_borrado_conceptos_adicionales_factura_tarifas_gas_Espanya = "
            DELETE
            FROM ".TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_GAS_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_conceptos_adicionales_factura_tarifas_gas_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_adicionales_factura_tarifas_gas_Espanya);
        if ($res_borrado_conceptos_adicionales_factura_tarifas_gas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_adicionales_factura_tarifas_gas_Espanya."'");
        }

        // Se borran las tablas de tarifas (agua - España)
        $operacion_borrado_grupos_tarifas_agua_Espanya = "
            DELETE
            FROM ".TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_grupos_tarifas_agua_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_grupos_tarifas_agua_Espanya);
        if ($res_borrado_grupos_tarifas_agua_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_grupos_tarifas_agua_Espanya."'");
        }

        $operacion_borrado_tarifas_agua_Espanya = "
            DELETE
            FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_tarifas_agua_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_tarifas_agua_Espanya);
        if ($res_borrado_tarifas_agua_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_tarifas_agua_Espanya."'");
        }

        $operacion_borrado_conceptos_adicionales_factura_tarifas_agua_Espanya = "
            DELETE
            FROM ".TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_AGUA_ESPANYA."
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_conceptos_adicionales_factura_tarifas_agua_Espanya = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_adicionales_factura_tarifas_agua_Espanya);
        if ($res_borrado_conceptos_adicionales_factura_tarifas_agua_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_adicionales_factura_tarifas_agua_Espanya."'");
        }

        $operacion_borrado_rangos_dias = "
            DELETE
            FROM rangos_dias
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_rangos_dias = $bd_red->ejecuta_operacion($operacion_borrado_rangos_dias);
        if ($res_borrado_rangos_dias == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_rangos_dias."'");
        }

        $operacion_borrado_periodos = "
            DELETE
            FROM periodos
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_periodos = $bd_red->ejecuta_operacion($operacion_borrado_periodos);
        if ($res_borrado_periodos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_periodos."'");
        }

        $operacion_borrado_posiciones_mapa = "
            DELETE
            FROM posiciones_mapa
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_posiciones_mapa = $bd_red->ejecuta_operacion($operacion_borrado_posiciones_mapa);
        if ($res_borrado_posiciones_mapa == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_posiciones_mapa."'");
        }

        $operacion_borrado_imagenes = "
            DELETE
            FROM imagenes
            WHERE
                red = '".$bd_red->_($id_red)."'";
        $res_borrado_imagenes = $bd_red->ejecuta_operacion($operacion_borrado_imagenes);
        if ($res_borrado_imagenes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes."'");
        }
    }


    function borra_datos_red_base_datos_datos($id_red)
    {
        // Borrado de datos de base de datos de datos
        // (los datos de los sensores y de los proyectos ya se han borrado con una función externa)
        // - Alarmas
        // - Acciones de usuario
        // - Comentarios
        // - Información de valores pendientes de borrado (de sensores)
        // - Activaciones de eventos
        // - Acciones de actuadores y grupos de actuadores
        // - Activaciones de reglas
        // - Validaciones de facturas eléctricas
        // - Importaciones de valores de sensores

        $bd_datos = BaseDatosDatos::dame_base_datos();

        $operacion_borrado_alarmas = "
            DELETE
            FROM alarmas
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_alarmas = $bd_datos->ejecuta_operacion($operacion_borrado_alarmas);
        if ($res_borrado_alarmas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_alarmas."'");
        }

        $operacion_borrado_acciones_usuario = "
            DELETE
            FROM acciones_usuario
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_acciones_usuario = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_usuario);
        if ($res_borrado_acciones_usuario == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_usuario."'");
        }

        $operacion_borrado_comentarios = "
            DELETE
            FROM comentarios
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_comentarios = $bd_datos->ejecuta_operacion($operacion_borrado_comentarios);
        if ($res_borrado_comentarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_comentarios."'");
        }

        $operacion_borrado_informacion_valores_pendientes_borrado = "
            DELETE
            FROM informacion_valores_pendientes_borrado
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_informacion_valores_pendientes_borrado = $bd_datos->ejecuta_operacion($operacion_borrado_informacion_valores_pendientes_borrado);
        if ($res_borrado_informacion_valores_pendientes_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_informacion_valores_pendientes_borrado."'");
        }

        $operacion_borrado_activaciones_eventos = "
            DELETE
            FROM activaciones_eventos
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_activaciones_eventos = $bd_datos->ejecuta_operacion($operacion_borrado_activaciones_eventos);
        if ($res_borrado_activaciones_eventos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_activaciones_eventos."'");
        }

        $operacion_borrado_acciones_actuadores = "
            DELETE
            FROM acciones_actuadores
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_acciones_actuadores = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_actuadores);
        if ($res_borrado_acciones_actuadores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_actuadores."'");
        }

        $operacion_borrado_acciones_grupos_actuadores = "
            DELETE
            FROM acciones_grupos_actuadores
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_acciones_grupos_actuadores = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_grupos_actuadores);
        if ($res_borrado_acciones_grupos_actuadores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_grupos_actuadores."'");
        }

        $operacion_borrado_activaciones_reglas = "
            DELETE
            FROM activaciones_reglas
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_activaciones_reglas = $bd_datos->ejecuta_operacion($operacion_borrado_activaciones_reglas);
        if ($res_borrado_activaciones_reglas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_activaciones_reglas."'");
        }

        $operacion_borrado_validaciones_facturas_electricas_Espanya = "
            DELETE
            FROM ".TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA."
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_validaciones_facturas_electricas_Espanya = $bd_datos->ejecuta_operacion($operacion_borrado_validaciones_facturas_electricas_Espanya);
        if ($res_borrado_validaciones_facturas_electricas_Espanya == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_validaciones_facturas_electricas_Espanya."'");
        }

        $operacion_borrado_importaciones_valores_sensores = "
            DELETE
            FROM importaciones_valores_sensores
            WHERE
                red = '".$bd_datos->_($id_red)."'";
        $res_borrado_importaciones_valores_sensores = $bd_datos->ejecuta_operacion($operacion_borrado_importaciones_valores_sensores);
        if ($res_borrado_importaciones_valores_sensores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_importaciones_valores_sensores."'");
        }
    }
?>
