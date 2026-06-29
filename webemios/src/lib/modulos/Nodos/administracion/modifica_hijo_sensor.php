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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_HIJO_SENSOR, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    $id_hijo_sensor = $_POST['id_hijo_sensor'];
    $id_sensor_padre = $_POST['id_sensor_padre'];
    $clase_sensor_hijo = $_POST['clase_sensor_hijo'];
    $id_sensor_hijo = $_POST['id_sensor_hijo'];
    $tipo_sensor_padre = $_POST['tipo_sensor_padre'];
    $cadena_parametros_tipo = $_POST['parametros_tipo'];
    $id_sensor_hijo_anterior = $_POST['id_sensor_hijo_anterior'];

    // Se comprueba si existe otro hijo de sensor con los mismos sensores padre e hijo
    $consulta_existe = "
        SELECT *
        FROM hijos_sensores
        WHERE
            (sensor_padre = '".$bd_red->_($id_sensor_padre)."')
            AND (sensor_hijo = '".$bd_red->_($id_sensor_hijo)."')
            AND (id <> '".$bd_red->_($id_hijo_sensor)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe este sensor hijo");
    }
    else
    {
        // Comprobaciones antes de modificar el hijo de sensor:
        // - Comprobación de bucle en los sensores hijos
        // - Comprobación de variable no repetida en sensores de procesado
        // - Comprobación de valores obligatorios con campos puntuales
        // - Comprobación de clase de sensor hijo con procesado de valores en sensores de procesado
        $modificar_hijo_sensor = true;

        // Comprobación de bucle en los sensores hijos
        if ($modificar_hijo_sensor == true)
        {
            // Tipos de sensores hijos
            $tipo_sensor_hijo_anterior = dame_tipo_sensor($id_sensor_hijo_anterior);
            $tipo_sensor_hijo = dame_tipo_sensor($id_sensor_hijo);

            // Comprobación de bucle en los sensores hijos (sólo si alguno de los hijos es del mismo tipo que el sensor padre)
            if (($tipo_sensor_hijo_anterior == $tipo_sensor_padre) || ($tipo_sensor_hijo == $tipo_sensor_padre))
            {
                $info_sensores_padres = NULL;
                $info_sensores_hijos = NULL;
                carga_informacion_sensores_padres_hijos($tipo_sensor_padre, $_SESSION["id_red"], $info_sensores_padres, $info_sensores_hijos);
                elimina_sensor_padre($info_sensores_padres, $id_sensor_padre, $id_sensor_hijo_anterior);
                elimina_sensor_hijo($info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo_anterior);
                if ($tipo_sensor_hijo == $tipo_sensor_padre)
                {
                    anyade_sensor_padre($info_sensores_padres, $id_sensor_padre, $id_sensor_hijo);
                    anyade_sensor_hijo($info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo);
                }

                $existe_bucle = existe_bucle_sensores_hijos($info_sensores_hijos);
                if ($existe_bucle == true)
                {
                    $modificar_hijo_sensor = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay un bucle en los sensores hijos");
                }
            }
        }

        // Comprobación de variable no repetida en sensores de procesado
        if ($modificar_hijo_sensor == true)
        {
            if ($tipo_sensor_padre == TIPO_SENSOR_PROCESADO)
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $variable_hijo = $parametros_tipo[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                $consulta_hijos_sensor = "
                    SELECT
                        parametros_tipo
                    FROM hijos_sensores
                    WHERE
                        (sensor_padre = '".$bd_red->_($id_sensor_padre)."')
                        AND (id <> '".$bd_red->_($id_hijo_sensor)."')";
                $res_hijos_sensor = $bd_red->ejecuta_consulta($consulta_hijos_sensor);
                if ($res_hijos_sensor == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_hijos_sensor."'");
                }
                while ($fila_hijo_sensor = $res_hijos_sensor->dame_siguiente_fila())
                {
                    $parametros_tipo_bucle = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_hijo_sensor["parametros_tipo"]);
                    $variable_hijo_bucle = $parametros_tipo_bucle[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                    if ($variable_hijo_bucle == $variable_hijo)
                    {
                        $modificar_hijo_sensor = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Ya existe un sensor hijo con la misma variable");
                        break;
                    }
                }
            }
        }

        // Comprobación de valores obligatorios con campos puntuales
        if ($modificar_hijo_sensor == true)
        {
            if ($tipo_sensor_padre == TIPO_SENSOR_PROCESADO)
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $valores_obligatorios = $parametros_tipo[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS];
                if ($valores_obligatorios == VALOR_NO)
                {
                    $campos_sensor_hijo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS]);
                    foreach ($campos_sensor_hijo as $campo_sensor_hijo)
                    {
                        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor_hijo, $campo_sensor_hijo);
                        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES)
                        {
                            $modificar_hijo_sensor = false;

                            $res = "ERROR";
                            $msg = $idiomas->_("Los valores deben ser obligatorios con campos puntuales");
                            break;
                        }
                    }
                }
            }
        }

        // Comprobación de clase de sensor hijo con procesado de valores en sensores de procesado
        if ($modificar_hijo_sensor == true)
        {
            if ($tipo_sensor_padre == TIPO_SENSOR_PROCESADO)
            {
                $caracteristicas_clase_sensor_hijo = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor_hijo);
                if ($caracteristicas_clase_sensor_hijo["procesado_valores"] == false)
                {
                    $modificar_hijo_sensor = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Los sensores de esta clase no pueden ser de hijos de sensores de procesado");
                }
            }
        }

        // Si el padre es un sensor virtual y no hay parámetros de tipo,
        // Se establece por defecto la operación 'suma' (+)
        // (para poder cambiar de clase de sensor virtual y los hijos siempre tengan una operación en las clases correspondientes)
        if ($modificar_hijo_sensor == true)
        {
            if ($tipo_sensor_padre == TIPO_SENSOR_VIRTUAL)
            {
                if ($cadena_parametros_tipo === NULL)
                {
                    $cadena_parametros_tipo = "+";
                }
            }
        }

        // Se modifica el hijo de sensor
        if ($modificar_hijo_sensor == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_hijo_sensor_anterior = dame_fila_hijo_sensor($id_hijo_sensor);

            // Se modifica el hijo de sensor
            $operacion_modificacion = "
                UPDATE hijos_sensores
                SET
                    sensor_hijo = '".$bd_red->_($id_sensor_hijo)."',
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo)."'
                WHERE
                    id = '".$bd_red->_($id_hijo_sensor)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se actualiza el orden del sensor padre (y sus padres recursivamente)
                // (sólo si alguno de los hijos es el mismo tipo que el sensor padre)
                if (($tipo_sensor_hijo_anterior == $tipo_sensor_padre) || ($tipo_sensor_hijo == $tipo_sensor_padre))
                {
                    $ordenes_sensores = NULL;
                    carga_ordenes_sensores_padres_hijos(
                        $tipo_sensor_padre,
                        $_SESSION["id_red"],
                        $ordenes_sensores);
                    actualiza_orden_sensores_ascendientes(
                        $info_sensores_padres,
                        $info_sensores_hijos,
                        $ordenes_sensores,
                        $id_sensor_padre);
                }

                // Se recupera la fila actual
                $fila_hijo_sensor_actual = dame_fila_hijo_sensor($id_hijo_sensor);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_hijo_sensor(
                    $fila_hijo_sensor_actual,
                    $fila_hijo_sensor_anterior);

                $res = "OK";
                $msg = $idiomas->_("Sensor hijo modificado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
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


    // Añade la acción de usuario de modificación del sensor hijo
    function anyade_accion_usuario_modificar_hijo_sensor($fila_actual, $fila_anterior)
    {
        // Identificadores del sensor padre e hijos
        $id_sensor_padre = $fila_actual["sensor_padre"];
        $id_sensor_hijo = $fila_actual["sensor_hijo"];
        $id_sensor_hijo_anterior = $fila_anterior["sensor_hijo"];

        // Se recuperan las filas de los sensores padre e hijos
        $fila_sensor_padre = dame_fila_sensor($id_sensor_padre);
        $fila_sensor_hijo = dame_fila_sensor($id_sensor_hijo);
        $fila_sensor_hijo_anterior = dame_fila_sensor($id_sensor_hijo_anterior);
        $tipo_sensor_padre = $fila_sensor_padre["tipo"];
        $nombre_sensor_padre = $fila_sensor_padre["nombre"];
        $nombre_sensor_hijo = $fila_sensor_hijo["nombre"];
        $nombre_sensor_hijo_anterior = $fila_sensor_hijo_anterior["nombre"];
        $clase_sensor_hijo = $fila_sensor_hijo["clase"];
        $clase_sensor_hijo_anterior = $fila_sensor_hijo_anterior["clase"];

        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_HIJO_SENSOR;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($nombre_sensor_hijo != $nombre_sensor_hijo_anterior)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_HIJO] = $nombre_sensor_hijo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_HIJO] = $nombre_sensor_hijo_anterior;
        }
        if ($fila_actual["parametros_tipo"] != $fila_anterior["parametros_tipo"])
        {
            $parametros_tipo_hijo_sensor_actual = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_tipo"]);
            $parametros_tipo_hijo_sensor_anterior = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_tipo"]);
            switch ($tipo_sensor_padre)
            {
                case TIPO_SENSOR_VIRTUAL:
                {
                    if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_VIRTUAL_OPERACION] != $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_VIRTUAL_OPERACION])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_OPERACION_HIJO_SENSOR_VIRTUAL] = $parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_VIRTUAL_OPERACION];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_OPERACION_HIJO_SENSOR_VIRTUAL] = $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_VIRTUAL_OPERACION0];
                    }
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS] != $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPOS_HIJO_SENSOR_PROCESADO] = array(
                            "clase_sensor" => $clase_sensor_hijo,
                            "campos" => $parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS]);
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPOS_HIJO_SENSOR_PROCESADO] = array(
                            "clase_sensor" => $clase_sensor_hijo_anterior,
                            "campos" => $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS]);
                    }
                    if ($parametros_tipo_hijo_sensor_actual[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO] != $parametros_tipo_hijo_sensor_anterior[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_actual[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_anterior[PARAMETRO_ACCION_USUARIO_FUNCION_HIJO_SENSOR_PROCESADO];
                    }
                    if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION] != $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION])
                    {
                        // Nota: Actualmente si hay parámetros de función, sólo es el número de horas
                        if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION] != "")
                        {
                            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION];
                        }
                        if ($parametros_accion_usuario_anteriores[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION] != "")
                        {
                            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION];
                        }
                    }
                    if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE] != $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VARIABLE_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VARIABLE_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                    }
                    if ($parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS] != $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALORES_OBLIGATORIOS_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_actual[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VALORES_OBLIGATORIOS_HIJO_SENSOR_PROCESADO] = $parametros_tipo_hijo_sensor_anterior[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS];
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de sensor padre incorrecto: '".$tipo_sensor_padre."'");
                }
            }
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($nombre_sensor_hijo == $nombre_sensor_hijo_anterior)
        {
            $objeto_accion_usuario = $nombre_sensor_hijo." (".$nombre_sensor_padre.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $nombre_sensor_hijo." (".$nombre_sensor_padre.")",
                $nombre_sensor_hijo_anterior));
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
