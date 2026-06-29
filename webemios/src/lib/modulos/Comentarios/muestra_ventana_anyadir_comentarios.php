<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_COMENTARIOS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
    $contenido = "";
    $pie = "";

    // Parámetros
    $cadena_ids_objetos = $_POST["ids_objetos"];
    $cadena_ids_sensores = $_POST["ids_sensores"];
    $cadena_ids_actuadores = $_POST["ids_actuadores"];
    $cadena_ids_grupos_actuadores = $_POST["ids_grupos_actuadores"];
    $origen_comentarios = $_POST["origen_comentarios"];
    $parametros_origen_comentarios = $_POST["parametros_origen_comentarios"];
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];
    $objeto = $_POST["objeto"];

    // Título
    $titulo .= $idiomas->_("Añadir comentarios");

    // Se muestra el contenido de la ventana de añadir comentarios
    $pie .= '<button class="btn btn-success boton_anyadir_comentarios">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_comentarios(
        $cadena_ids_objetos,
        $cadena_ids_sensores,
        $cadena_ids_actuadores,
        $cadena_ids_grupos_actuadores,
        $origen_comentarios,
        $parametros_origen_comentarios,
        $cadena_fecha_hora_local_local,
        $objeto,
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

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funciones para mostrar el contenido de la ventana de añadir comentarios
	//


	// Función que devuelve los controles para añadir comentarios
	function rellena_contenido_ventana_anyadir_comentarios(
        $cadena_ids_objetos,
        $cadena_ids_sensores,
        $cadena_ids_actuadores,
        $cadena_ids_grupos_actuadores,
        $origen_comentarios,
        $parametros_origen_comentarios,
        $cadena_fecha_hora_local_local,
        $objeto,
        &$contenido)
    {
        $idiomas = new Idiomas();

        // Cadena de ids de objetos (original)
        $cadena_ids_objetos_original = $cadena_ids_objetos;

        // Fecha y hora
        if ($cadena_fecha_hora_local_local !== "")
        {
            $fecha_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $hora_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_HORA);
        }
        else
        {
            $fecha_local = NULL;
            $hora_local = NULL;
        }

        // Inicialización de valores por defecto de los controles
        $parametros_ventana_administracion_comentarios = NULL;
        switch ($origen_comentarios)
        {
            case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $administracion_comentarios_sensores = NodoSensor::dame_administracion_comentarios_sensores();
                $administracion_comentarios_actuadores = NodoActuador::dame_administracion_comentarios_actuadores();
                if ($administracion_comentarios_sensores == true)
                {
                    $tipo_nodo = TIPO_NODO_SENSOR;
                    $tipo = TIPO_COMENTARIO_ANOTACION_SENSOR;
                    $descripcion_objetos = $idiomas->_("Sensores");
                    $cadena_ids_objetos = $cadena_ids_sensores;
                }
                else
                {
                    if ($administracion_comentarios_actuadores == true)
                    {
                        $tipo_nodo = TIPO_NODO_ACTUADOR;
                        $tipo = TIPO_COMENTARIO_ANOTACION_ACTUADOR;
                        $descripcion_objetos = $idiomas->_("Actuadores");
                        $cadena_ids_objetos = $cadena_ids_actuadores;
                    }
                    else
                    {
                        throw new Exception("Permisos de administración de comentarios incorrecto: '".$origen_comentarios."'");
                    }
                }

                // Parámetros de ventana de administración de comentarios
                $parametros_ventana_administracion_comentarios = array(
                    "tipo_ventana" => TIPO_VENTANA_ANYADIR_COMENTARIOS,
                    "administracion_comentarios_sensores" => $administracion_comentarios_sensores,
                    "administracion_comentarios_actuadores" => $administracion_comentarios_actuadores);
                break;
            }
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES:
            case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
            case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
            {
                $tipo_nodo = TIPO_NODO_SENSOR;
                $tipo = TIPO_COMENTARIO_ANOTACION_SENSOR;
                $descripcion_objetos = $idiomas->_("Sensores");
                break;
            }
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES:
            {
                $tipo_nodo = TIPO_NODO_ACTUADOR;
                $tipo = TIPO_COMENTARIO_ANOTACION_ACTUADOR;
                $descripcion_objetos = $idiomas->_("Actuadores");
                break;
            }
            default:
            {
                throw new Exception("Origen de comentarios desconocido o incorrecto: '".$origen_comentarios."'");
            }
        }
        switch ($origen_comentarios)
        {
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES:
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES:
            {
                $mostrar_lista_clases_objetos = true;
                $clase_objetos = CLASE_NINGUNA;
                break;
            }
            default:
            {
                $mostrar_lista_clases_objetos = false;
                $clase_objetos = CLASE_TODAS;
                break;
            }
        }

        // Fecha y hora
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha").": "."</span><br/>";
        $contenido .= "
                    <input size='10' type='text' id='fecha_comentarios' class='selector-fecha datepicker'
                        readonly='readonly'";
        if ($fecha_local !== NULL)
        {
            $contenido .= " value='".$fecha_local."'";
        }
        else
        {
            $contenido .= " value='".date($_SESSION["formato_fecha_local"])."'";
        }
        $contenido .= ">";

        $contenido .= "
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_comentarios' class='selector-hora timepicker' readonly='readonly'";
        if ($hora_local !== NULL)
        {
            $contenido .= " value='".$hora_local."'";
        }
        $contenido .= ">
                    </span>
				</div>
			</div>";

        // Tipo
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_tipos_comentario(
            $origen_comentarios,
            $parametros_ventana_administracion_comentarios,
            $tipo);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Visibilidad
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Visibilidad").": "."</span><br/>
                    <select id='visibilidad_comentarios' class='select-administracion'>";
        $contenido .= dame_lista_visibilidades_comentario(VISIBILIDAD_PUBLICA, OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Clase de objetos
        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_lista_clases_objetos == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_objetos_comentarios' class='select-administracion'>";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista_clases_objetos = dame_lista_clases_sensor($clase_objetos, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista_clases_objetos = dame_lista_clases_actuador($clase_objetos, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
                break;
            }
        }
        $contenido .= $lista_clases_objetos;
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion' id='titulo_objetos_comentarios'>".$descripcion_objetos.": "."</span><br/>
                    <div id='select_objetos_comentarios_no_visible' hidden></div>
					<select id='ids_objetos_comentarios'
                        name='ids_objetos_comentarios'
                        max_selected='".NUMERO_MAXIMO_ADICION_COMENTARIOS."' multiple='multiple'
						class='select-administracion' hidden>";
        $lista_objetos = dame_lista_objetos_comentarios(
            $origen_comentarios,
            $tipo,
            $clase_objetos,
            $cadena_ids_objetos,
            $objeto);
        $contenido .= $lista_objetos;
        $contenido .= "
					</select>
				</div>
			</div>";

        // Descripción
        $descripcion = "";
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='descripcion_comentarios'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Parámetros no visibles (en un 'div' oculto) (se muestra aquí porque se modifican algunas variables)
        $contenido .= '
            <div id="parametros_ventana_anyadir_comentarios"
                ids_objetos = "'.$cadena_ids_objetos_original.'"
                ids_sensores = "'.$cadena_ids_sensores.'"
                ids_actuadores = "'.$cadena_ids_actuadores.'"
                ids_grupos_actuadores = "'.$cadena_ids_grupos_actuadores.'"
                origen_comentarios="'.$origen_comentarios.'"
                parametros_origen_comentarios="'.$parametros_origen_comentarios.'"
                tipo_comentarios="'.$tipo.'"
                clase_objetos="'.$clase_objetos.'"
                hidden>
            </div>';
        return ("OK");
    }
?>
