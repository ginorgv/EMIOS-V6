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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_herramientas_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ASIGNACION_LOCALIZACION_NODOS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parametros
    $tipo_nodo = $_POST["tipo_nodo"];

    // Asignar localización
    $pie .= '<button class="btn btn-success boton_asignar_localizacion_nodos">'.$idiomas->_("Asignar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Asignar localización");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_asignacion_localizacion_nodos($tipo_nodo, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de asignar localización a nodos
	//


	// Función que rellena el contenido de la ventana de asignar localización a nodos
	function rellena_contenido_ventana_asignacion_localizacion_nodos($tipo_nodo, &$contenido)
	{
        $idiomas = new Idiomas();

        // Clase de nodo inicial
        $clase_nodo = CLASE_NINGUNA;

        // Localización actual
        $id_localizacion = $_SESSION["id_localizacion"];
        switch ($id_localizacion)
        {
            case ID_DESACTIVADO: {
                $id_localizacion = ID_NINGUNO;
                break;
            }
        }

        // Localización
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_asignacion_localizacion_nodos' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Clase de nodo
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_nodo_asignacion_localizacion_nodos' class='select-administracion'>";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista_clases_nodos = dame_lista_clases_sensor($clase_nodo, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista_clases_nodos = dame_lista_clases_actuador($clase_nodo, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS);
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $contenido .= $lista_clases_nodos;
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Títulos de listas de nodos
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $titulo_lista_nodos = $idiomas->_("Sensores");
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $titulo_lista_nodos = $idiomas->_("Actuadores");
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $titulo_lista_grupos_nodos = $idiomas->_("Grupos");

        // Se recuperan los nodos de la localización
        // Nota: Si la localización es ninguna, se comprueba si los nodos son visibles por el usuario
        $ids_nodos_localizacion = dame_ids_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);
        $ids_grupos_nodos_localizacion = dame_ids_grupos_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);
        if ($id_localizacion == ID_NINGUNO)
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_nodos_usuario_actual = dame_ids_sensores_usuario_actual(true);
                    $ids_grupos_nodos_usuario_actual = dame_ids_grupos_sensores_usuario_actual(true);
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_nodos_usuario_actual = dame_ids_actuadores_usuario_actual(true);
                    $ids_grupos_nodos_usuario_actual = dame_ids_grupos_actuadores_usuario_actual(true);
                    break;
                }
            }
            $ids_nodos_localizacion = array_intersect($ids_nodos_localizacion, $ids_nodos_usuario_actual);
            $ids_grupos_nodos_localizacion = array_intersect($ids_grupos_nodos_localizacion, $ids_grupos_nodos_usuario_actual);
        }
        $lista_nodos = dame_lista_nodos(
            $tipo_nodo,
            $clase_nodo,
            $ids_nodos_localizacion,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $lista_grupos_nodos = dame_lista_grupos_nodos(
            $tipo_nodo,
            $clase_nodo,
            $ids_grupos_nodos_localizacion,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$titulo_lista_nodos.": "."</span><br/>
                    <div id='select_nodos_asignacion_localizacion_nodos_no_visible' hidden></div>
                    <select id='ids_nodos_asignacion_localizacion_nodos'
                        name='ids_nodos_asignacion_localizacion_nodos'
                        max_selected='".MAX_NODOS_SELECCIONADOS_LISTA_NODOS_ASIGNACION_LOCALIZACION."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= $lista_nodos;
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$titulo_lista_grupos_nodos.": "."</span><br/>
                    <div id='select_grupos_nodos_asignacion_localizacion_nodos_no_visible' hidden></div>
                    <select id='ids_grupos_nodos_asignacion_localizacion_nodos'
                        name='ids_grupos_nodos_asignacion_localizacion_nodos'
                        max_selected='".MAX_NODOS_SELECCIONADOS_LISTA_NODOS_ASIGNACION_LOCALIZACION."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= $lista_grupos_nodos;
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_asignacion_localizacion_nodos"
                tipo_nodo="'.$tipo_nodo.'"
                hidden>
            </div>';

        return ("OK");
	}
?>


