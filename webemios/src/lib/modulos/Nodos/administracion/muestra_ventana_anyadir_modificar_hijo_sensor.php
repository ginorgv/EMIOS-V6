<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_HIJO_SENSOR, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    $id_sensor_padre = $_POST["id_sensor_padre"];
    $id_sensor_hijo = $_POST["id_sensor_hijo"];
    $id_hijo_sensor = $_POST["id_hijo_sensor"];
    if ($id_hijo_sensor === NULL)
    {
        $id_hijo_sensor = ID_NINGUNO;
    }

    $consulta_sensor_padre = "
        SELECT
            clase,
            tipo,
            parametros_tipo,
            tipo_valores
        FROM sensores
        WHERE
            id = '".$bd_red->_($id_sensor_padre)."'";
    $res_sensor_padre = $bd_red->ejecuta_consulta($consulta_sensor_padre);
    if (($res_sensor_padre == false) || ($res_sensor_padre->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_padre."'");
    }

    $fila_sensor_padre = $res_sensor_padre->dame_siguiente_fila();
    $clase_sensor_padre = $fila_sensor_padre["clase"];
    $tipo_sensor_padre = $fila_sensor_padre["tipo"];
    $parametros_tipo_sensor_padre = $fila_sensor_padre["parametros_tipo"];
    $tipo_valores_sensor_padre = $fila_sensor_padre["tipo_valores"];

    // Se recupera el número de campos del sensor padre
    switch ($tipo_valores_sensor_padre)
    {
        case TIPO_VALORES_SENSOR_PUNTUALES:
        {
            $campos_sensor_padre = dame_campos_puntuales_clase_sensor($clase_sensor_padre);
            break;
        }
        case TIPO_VALORES_SENSOR_INCREMENTALES:
        {
            $campos_sensor_padre = dame_campos_incrementos_clase_sensor($clase_sensor_padre);
            break;
        }
    }
    $numero_campos_sensor_padre = count($campos_sensor_padre);

    // Añadir o modificar hijo
    $anyadir_hijo = ($id_hijo_sensor == ID_NINGUNO);
    if ($anyadir_hijo == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_hijo_sensor">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("hijo");
    $error = rellena_contenido_ventana_anyadir_modificar_hijo_sensor(
        $anyadir_hijo,
        $id_hijo_sensor,
        $id_sensor_padre,
        $clase_sensor_padre,
        $tipo_sensor_padre,
        $parametros_tipo_sensor_padre,
        $tipo_valores_sensor_padre,
        $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_hijo_sensor"
            anyadir_hijo="'.$anyadir_hijo.'"
            id_sensor_padre="'.$id_sensor_padre.'"
            id_sensor_hijo="'.$id_sensor_hijo.'"
            id_hijo_sensor="'.$id_hijo_sensor.'"
            tipo_sensor_padre="'.$tipo_sensor_padre.'"
            numero_campos_sensor_padre="'.$numero_campos_sensor_padre.'"
            hidden>
        </div>';

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar hijo
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar hijo de sensor
	function rellena_contenido_ventana_anyadir_modificar_hijo_sensor(
        $anyadir_hijo,
        $id_hijo_sensor,
        $id_sensor_padre,
        $clase_sensor_padre,
        $tipo_sensor_padre,
        $parametros_tipo_sensor_padre,
        $tipo_valores_sensor_padre,
        &$contenido)
	{
		$bd_red = BaseDatosRed::dame_base_datos();

		// Si hay que modificar el hijo, se recupera la información actual de la base de datos
		if ($anyadir_hijo == false)
		{
			$fila_hijo_sensor = dame_fila_hijo_sensor($id_hijo_sensor);

            $id_sensor_hijo = $fila_hijo_sensor["sensor_hijo"];
            $parametros_tipo_hijo = $fila_hijo_sensor["parametros_tipo"];

            // Se recupera la clase del sensor hijo
            switch ($tipo_sensor_padre)
            {
                case TIPO_SENSOR_PROCESADO:
                {
                    $consulta_sensor = "
                        SELECT clase
                        FROM sensores
                        WHERE
                            id = '".$bd_red->_($id_sensor_hijo)."'";
                    $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
                    if (($res_sensor == false) || ($res_sensor->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor."'");
                    }

                    $fila_sensor = $res_sensor->dame_siguiente_fila();
                    $clase_sensor_hijo = $fila_sensor["clase"];
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $clase_sensor_hijo = $clase_sensor_padre;
                    break;
                }
            }
		}
        else
        {
            // Clase y parámetros del sensor hijo
            switch ($tipo_sensor_padre)
            {
                case TIPO_SENSOR_PROCESADO:
                {
                    $clase_sensor_hijo = CLASE_NINGUNA;
                    $parametros_tipo_hijo = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                        array(),
                        FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD,
                        "",
                        "",
                        VALOR_SI));
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $clase_sensor_hijo = $clase_sensor_padre;
                    $parametros_tipo_hijo = "";
                    break;
                }
            }
        }

        // Se recuperan los controles del hijo del sensor
		$contenido .= dame_controles_hijo_sensor(
            $id_hijo_sensor,
            $id_sensor_padre,
            $clase_sensor_padre,
            $tipo_sensor_padre,
            $parametros_tipo_sensor_padre,
            $tipo_valores_sensor_padre,
            $id_sensor_hijo,
            $clase_sensor_hijo,
            $parametros_tipo_hijo);

        return ("OK");
	}


    function dame_controles_hijo_sensor(
        $id_hijo_sensor,
        $id_sensor_padre,
        $clase_sensor_padre,
        $tipo_sensor_padre,
        $parametros_tipo_sensor_padre,
        $tipo_valores_sensor_padre,
        $id_sensor_hijo,
        $clase_sensor_hijo,
        $parametros_tipo_hijo)
    {
        $idiomas = new Idiomas();

        // Controles de hijo
        $controles_hijo = "";
        switch ($tipo_sensor_padre)
        {
            case TIPO_SENSOR_PROCESADO:
            {
                $controles_hijo = "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                            <select id='clase_sensor_hijo_sensor_procesado' class='select-administracion'>";
                $controles_hijo .= dame_lista_clases_sensor($clase_sensor_hijo, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
                $controles_hijo .= "
                            </select>
                        </div>
                    </div>";
                break;
            }
        }

        // Flags de habilitación inicial de lista de campos (se hace aquí porque los controles de campos se muestran dinámicamente y no se pueden asignar
        // eventos 'show()' porque no se sabe el número de controles que hay)
        $numero_campos_clase_sensor = count(dame_todos_campos_clase_sensor($clase_sensor_hijo));
        if ($numero_campos_clase_sensor <= 1)
        {
            $habilitar_lista_campos_sensor = false;
        }
        else
        {
            $habilitar_lista_campos_sensor = true;
        }
        if ($clase_sensor_hijo != CLASE_NINGUNA)
        {
            $habilitar_lista_sensores = true;
        }
        else
        {
            $habilitar_lista_sensores = false;
        }
        $controles_hijo .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_hijo' class='chosen-select-administracion'>";
        if ($habilitar_lista_sensores == false)
        {
            $controles_hijo .= " disabled";
        }
        $controles_hijo .= ">";
        $controles_hijo .= dame_lista_sensores_hijos_administracion(
            $id_sensor_padre,
            $tipo_sensor_padre,
            $id_sensor_hijo,
            $clase_sensor_hijo);
        $controles_hijo .= "
                    </select>
                </div>
            </div>";

        switch ($tipo_sensor_padre)
        {
            case TIPO_SENSOR_VIRTUAL:
            {
                $clase_virtual = $parametros_tipo_sensor_padre;
                if ($clase_virtual == CLASE_SENSOR_VIRTUAL_SUMA_VALORES)
                {
                    $operacion_hijo_sensor_virtual = $parametros_tipo_hijo;

                    $controles_hijo .= "
                        <div class='row-fluid'>
                            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Operación").": "."</span><br/>
                                <select id='operacion_hijo_sensor_virtual' class='select-administracion'>";
                    $controles_hijo .= dame_opcion_valor_lista_simple($idiomas->_("Suma"), OPERACION_HIJO_SENSOR_VIRTUAL_SUMA, $operacion_hijo_sensor_virtual);
                    $controles_hijo .= dame_opcion_valor_lista_simple($idiomas->_("Resta"), OPERACION_HIJO_SENSOR_VIRTUAL_RESTA, $operacion_hijo_sensor_virtual);
                    $controles_hijo .= "
                                </select>
                            </div>
                        </div>";
                }
                break;
            }
            case TIPO_SENSOR_PROCESADO:
            {
                $parametros_hijo_sensor_procesado = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_hijo);
                $campos_hijo_sensor_procesado = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS]);
                $funcion_hijo_sensor_procesado = $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_FUNCION];
                $parametros_funcion_hijo_sensor_procesado = $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION];
                $variable_funcion_hijo_sensor_procesado = $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                $valores_obligatorios_hijo_sensor_procesado = $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS];

                // Se muestran tantos campos de valores como número de valores tiene el sensor padre
                switch ($tipo_valores_sensor_padre)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        $campos_sensor_padre = dame_campos_puntuales_clase_sensor($clase_sensor_padre);
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        $campos_sensor_padre = dame_campos_incrementos_clase_sensor($clase_sensor_padre);
                        break;
                    }
                }
                for ($i = 0; $i < count($campos_sensor_padre); $i++)
                {
                    $campo_sensor_padre = $campos_sensor_padre[$i];
                    $descripcion_campo_sensor_padre = strtolower(dame_descripcion_campo_clase_sensor($clase_sensor_padre, $campo_sensor_padre));
                    $controles_hijo .= "
                        <div class='row-fluid'>
                            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo")." (".$descripcion_campo_sensor_padre."): "."</span><br/>
                                <select id='campo_hijo_sensor_procesado_".$i."' class='select-administracion'";
                    if ($habilitar_lista_campos_sensor == false)
                    {
                        $controles_hijo .= " disabled";
                    }
                    $controles_hijo .= ">";
                    if ($id_sensor_hijo != "")
                    {
                        $campo_hijo_sensor_procesado = $campos_hijo_sensor_procesado[$i];
                        $controles_hijo .= dame_lista_campos_clase_sensor($clase_sensor_hijo, $campo_hijo_sensor_procesado);
                    }
                    $controles_hijo .= "
                                </select>
                            </div>
                        </div>";
                }

                $controles_hijo .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Función").": "."</span><br/>
                            <select id='funcion_hijo_sensor_procesado' class='select-administracion'>";
                $controles_hijo .= dame_opcion_valor_lista_simple(NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado(FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD), FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD, $funcion_hijo_sensor_procesado);
                $controles_hijo .= dame_opcion_valor_lista_simple(NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado(FUNCION_HIJO_SENSOR_PROCESADO_MEDIA), FUNCION_HIJO_SENSOR_PROCESADO_MEDIA, $funcion_hijo_sensor_procesado);
                $controles_hijo .= dame_opcion_valor_lista_simple(NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado(FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR), FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR, $funcion_hijo_sensor_procesado);
                $controles_hijo .= dame_opcion_valor_lista_simple(NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado(FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO), FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO, $funcion_hijo_sensor_procesado);
                $controles_hijo .= dame_opcion_valor_lista_simple(NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado(FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO), FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO, $funcion_hijo_sensor_procesado);
                $controles_hijo .= "
                            </select>
                        </div>
                    </div>";

                $controles_hijo .= "
                    <div class='row-fluid' id='id_controles_parametros_funcion_hijo_sensor_procesado'>";
                $controles_hijo .= dame_controles_parametros_funcion_hijo_sensor_procesado($id_hijo_sensor, $funcion_hijo_sensor_procesado, $parametros_funcion_hijo_sensor_procesado);
                $controles_hijo .= "
                    </div>";

                $controles_hijo .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Variable").": "."</span><br/>
                            <input type='text' id='variable_hijo_sensor_procesado'
                                class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($variable_funcion_hijo_sensor_procesado, ENT_QUOTES)."'>
                        </div>
                    </div>";

                $controles_hijo .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores obligatorios").": "."</span><br/>
                            <select id='valores_obligatorios_hijo_sensor_procesado' class='select-administracion'>";
                $controles_hijo .= dame_lista_valores_si_no($valores_obligatorios_hijo_sensor_procesado);
                $controles_hijo .= "
                            </select>
                        </div>
                    </div>";

                break;
            }
        }

        return ($controles_hijo);
    }
?>
