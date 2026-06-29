<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ASIGNACION_TARIFA_GRUPO_TARIFAS_SENSORES, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parametros
    $medicion = $_POST["medicion"];
    $id_tarifa = $_POST["id_tarifa"];
    $id_grupo_tarifas = $_POST["id_grupo_tarifas"];
    if ($id_tarifa === NULL)
    {
        $id_tarifa = ID_NINGUNO;
    }
    if ($id_grupo_tarifas === NULL)
    {
        $id_grupo_tarifas = ID_NINGUNO;
    }

    // Asignar tarifa o grupo de tarifas a sensores
    $pie .= '<button class="btn btn-success boton_smartmeter_asignar_tarifa_grupo_tarifas_sensores">'.$idiomas->_("Asignar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Asignar tarifa o grupo de tarifas a sensores");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_asignacion_tarifa_grupo_tarifas_sensores($medicion, $id_tarifa, $id_grupo_tarifas, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de asignar tarifa o grupo a sensores
	//


	// Función que rellena el contenido de la ventana de asignación de tarifa o grupo a sensores
	function rellena_contenido_ventana_asignacion_tarifa_grupo_tarifas_sensores(
        $medicion,
        $id_tarifa,
        $id_grupo_tarifas,
        &$contenido)
	{
        $idiomas = new Idiomas();

        $contenido = "";
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        // Se recupera el grupo de tarifas de la tarifa (si lo hay, se asigna el grupo en lugar de la tarifa)
                        if ($id_tarifa != ID_NINGUNO)
                        {
                            $fila_tarifa = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
                            $id_grupo_tarifas = $fila_tarifa["grupo"];
                            if ($id_grupo_tarifas != ID_NINGUNO)
                            {
                                $id_tarifa = ID_NINGUNO;
                            }
                        }

                        $contenido .= "";
                        $contenido .= "
                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa eléctrica")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                                    <select id='id_tarifa_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_tarifas_electricidad_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>

                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas eléctricas").": "."</span><br/>
                                    <select id='id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>";
                        break;
                    }
										case PAIS_PORTUGAL:
                    {
                        // Se recupera el grupo de tarifas de la tarifa (si lo hay, se asigna el grupo en lugar de la tarifa)
                        if ($id_tarifa != ID_NINGUNO)
                        {
                            $fila_tarifa = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa);
                            $id_grupo_tarifas = $fila_tarifa["grupo"];
                            if ($id_grupo_tarifas != ID_NINGUNO)
                            {
                                $id_tarifa = ID_NINGUNO;
                            }
                        }

                        $contenido .= "";
                        $contenido .= "
                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa eléctrica")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                                    <select id='id_tarifa_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_tarifas_electricidad_Portugal(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>

                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas eléctricas").": "."</span><br/>
                                    <select id='id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>";
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
                $clase_sensor = CLASE_SENSOR_GAS;
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        // Se recupera el grupo de tarifas de la tarifa (si lo hay, se asigna el grupo en lugar de la tarifa)
                        if ($id_tarifa != ID_NINGUNO)
                        {
                            $fila_tarifa = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
                            $id_grupo_tarifas = $fila_tarifa["grupo"];
                            if ($id_grupo_tarifas != ID_NINGUNO)
                            {
                                $id_tarifa = ID_NINGUNO;
                            }
                        }

                        $contenido .= "";
                        $contenido .= "
                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa de gas")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                                    <select id='id_tarifa_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_tarifas_gas_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>

                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas de gas").": "."</span><br/>
                                    <select id='id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_grupos_tarifas(MEDICION_GAS, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>";
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
                $clase_sensor = CLASE_SENSOR_AGUA;
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        // Se recupera el grupo de tarifas de la tarifa (si lo hay, se asigna el grupo en lugar de la tarifa)
                        if ($id_tarifa != ID_NINGUNO)
                        {
                            $fila_tarifa = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);
                            $id_grupo_tarifas = $fila_tarifa["grupo"];
                            if ($id_grupo_tarifas != ID_NINGUNO)
                            {
                                $id_tarifa = ID_NINGUNO;
                            }
                        }

                        $contenido .= "";
                        $contenido .= "
                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa de agua")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                                    <select id='id_tarifa_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_tarifas_agua_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>

                            <div class='row-fluid'>
                                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas de agua").": "."</span><br/>
                                    <select id='id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores' class='chosen-select-administracion'>";
                        $contenido .= dame_lista_grupos_tarifas(MEDICION_AGUA, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                        $contenido .= "
                                    </select>
                                </div>
                            </div>";
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
                throw new Exception("Medición desconocida: '".$pais_tarifas_electricas."'");
            }
        }

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_asignacion_tarifa_grupo_tarifas_sensores_no_visible' hidden></div>
                    <select id='ids_sensores_asignacion_tarifa_grupo_tarifas_sensores'
                        name='ids_sensores_asignacion_tarifa_grupo_tarifas_sensores'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ASIGNACION_TARIFA_SENSORES."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores($clase_sensor, array(), OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}
?>


