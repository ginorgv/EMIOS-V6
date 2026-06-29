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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_COMENTARIO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
    $contenido = "";
    $pie = "";

    // Parámetros
    $tipo_comentario = $_POST["tipo_comentario"];
    $origen_comentario = $_POST["origen_comentario"];
    $parametros_origen_comentario = $_POST["parametros_origen_comentario"];
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];
    $objeto = $_POST["objeto"];

    $id_comentario = $_POST["id_comentario"];
    if ($id_comentario === NULL)
    {
        $id_comentario = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar comentario
    $anyadir_comentario = ($id_comentario == ID_NINGUNO);
    if ($anyadir_comentario == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }

    // Se muestra el contenido de la ventana de añadir o modificar comentario
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_comentario">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    switch ($origen_comentario)
    {
        case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
        case ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED:
        {
            $titulo .= " ".$idiomas->_("comentario");
            break;
        }
        case ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
        {
            $titulo .= " ".$idiomas->_("comentario de sensor");
            break;
        }
        case ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
        {
            switch ($tipo_comentario)
            {
                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                {
                    $titulo .= " ".$idiomas->_("comentario de grupo de actuadores");
                    break;
                }
                default:
                {
                    $titulo .= " ".$idiomas->_("comentario de actuador");
                    break;
                }
            }
            break;
        }
        case ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
        {
            switch ($tipo_comentario)
            {
                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                {
                    $titulo .= " ".$idiomas->_("comentario de actuador");
                    break;
                }
                default:
                {
                    $titulo .= " ".$idiomas->_("comentario de grupo de actuadores");
                    break;
                }
            }
            break;
        }
        default:
        {
            throw new Exception("Origen de comentario desconocido: '".$origen_comentario."'");
        }
    }

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_comentario(
        $anyadir_comentario,
        $id_comentario,
        $origen_comentario,
        $parametros_origen_comentario,
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
	// Funciones para mostrar el contenido de la ventana de añadir o modificar comentario
	//


	// Función que devuelve los controles para añadir un comentario
	function rellena_contenido_ventana_anyadir_modificar_comentario(
        $anyadir_comentario,
        $id_comentario,
        $origen_comentario,
        $parametros_origen_comentario,
        $cadena_fecha_hora_local_local,
        $objeto,
        &$contenido)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Si hay que modificar el comentario, se recupera la información actual de la base de datos
        if ($anyadir_comentario == false)
        {
            $consulta_comentario = "
                SELECT *
                FROM comentarios
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (id = '".$bd_datos->_($id_comentario)."')";
            $res_comentario = $bd_datos->ejecuta_consulta($consulta_comentario);
            if (($res_comentario == false) || ($res_comentario->dame_numero_filas() == 0))
            {
                throw new Exception("Error en la consulta: '".$consulta_comentario."'");
            }
            $fila_comentario = $res_comentario->dame_siguiente_fila();

            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_base_datos_utc = $fila_comentario["hora"];
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $fecha_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $hora_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $tipo = $fila_comentario["tipo"];
            $visibilidad = $fila_comentario["visibilidad"];
            $objeto = $fila_comentario["objeto"];
            $descripcion = $fila_comentario["descripcion"];
        }
        else
        {
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
            switch ($origen_comentario)
            {
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                {
                    $tipo = TIPO_COMENTARIO_ANOTACION_SENSOR;
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                {
                    $tipo = TIPO_COMENTARIO_ANOTACION_ACTUADOR;
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    $tipo = TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES;
                    break;
                }
                default:
                {
                    throw new Exception("Origen de comentario desconocido o incorrecto: '".$origen_comentario."'");
                }
            }
            $visibilidad = VISIBILIDAD_PUBLICA;
            $descripcion = "";
        }

        // Parámetros de ventana de administración de comentarios
        switch ($origen_comentario)
        {
            case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $parametros_ventana_administracion_comentarios = array(
                    "tipo_ventana" => TIPO_VENTANA_ANYADIR_COMENTARIO);
                break;
            }
            default:
            {
                $parametros_ventana_administracion_comentarios = NULL;
                break;
            }
        }

        // Fecha y hora
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha").": "."</span><br/>";
        $contenido .= "
                    <input size='10' type='text' id='fecha_comentario' class='selector-fecha datepicker'
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
                        <input type='text' id='hora_comentario' class='selector-hora timepicker' readonly='readonly'";
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
                    <select id='tipo_comentario' class='select-administracion'>";
        $contenido .= dame_lista_tipos_comentario(
            $origen_comentario,
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
                    <select id='visibilidad_comentario' class='select-administracion'>";
        $contenido .= dame_lista_visibilidades_comentario($visibilidad, OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_SIN_OPCIONES_EXTRA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Objeto
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Objeto').": "."</span><br/>
					<input disabled type='text' id='objeto_comentario'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$objeto."'>
				</div>
			</div>";

        // Descripción
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea tabindex='-1' id='descripcion_comentario'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Parámetros no visibles (en un 'div' oculto)
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_comentario"
                anyadir_comentario ="'.$anyadir_comentario.'"
                id_comentario ="'.$id_comentario.'"
                origen_comentario="'.$origen_comentario.'"
                parametros_origen_comentario ="'.$parametros_origen_comentario.'"
                objeto="'.$objeto.'"
                hidden>
            </div>';
        return ("OK");
    }
?>
