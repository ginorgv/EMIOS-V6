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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_herramientas_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ASIGNACION_GRUPO_NODOS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parametros
    $tipo_nodo = $_POST["tipo_nodo"];

    // Asignar grupo
    $pie .= '<button class="btn btn-success boton_asignar_grupo_nodos">'.$idiomas->_("Asignar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Asignar grupo");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_asignacion_grupo_nodos($tipo_nodo, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de asignar grupo a nodos
	//


	// Función que rellena el contenido de la ventana de asignar grupo a nodos
	function rellena_contenido_ventana_asignacion_grupo_nodos($tipo_nodo, &$contenido)
	{
        $idiomas = new Idiomas();

        // Grupo y clase de nodo iniciales
        $id_grupo = ID_NINGUNO;
        $clase_nodo = CLASE_NINGUNA;

        // Clase de nodo
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_nodo_asignacion_grupo_nodos' class='select-administracion'>";
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

        // Grupo
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo").": "."</span><br/>
					<select id='id_grupo_asignacion_grupo_nodos' class='chosen-select-administracion'>";
        $contenido .= dame_lista_grupos_nodos(
            $tipo_nodo,
            $clase_nodo,
            array($id_grupo),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Título de lista de nodos
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

        // Se recuperan los nodos del grupo
        // Nota: Si el grupo es ninguno, se comprueba si los nodos son visibles por el usuario
        $ids_nodos_grupo = dame_ids_nodos_grupo($tipo_nodo, $id_grupo);
        if ($id_grupo == ID_NINGUNO)
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_nodos_usuario_actual = dame_ids_sensores_usuario_actual(true);
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_nodos_usuario_actual = dame_ids_actuadores_usuario_actual(true);
                    break;
                }
            }
            $ids_nodos_grupo = array_intersect($ids_nodos_grupo, $ids_nodos_usuario_actual);
        }
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista_nodos = dame_lista_sensores($clase_nodo, $ids_nodos_grupo, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista_nodos = dame_lista_actuadores($clase_nodo, $ids_nodos_grupo, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                break;
            }
        }

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$titulo_lista_nodos.": "."</span><br/>
                    <div id='select_nodos_asignacion_grupo_nodos_no_visible' hidden></div>
                    <select id='ids_nodos_asignacion_grupo_nodos'
                        name='ids_nodos_asignacion_grupo_nodos'
                        max_selected='".MAX_NODOS_SELECCIONADOS_LISTA_NODOS_ASIGNACION_GRUPO."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= $lista_nodos;
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_asignacion_grupo_nodos"
                tipo_nodo="'.$tipo_nodo.'"
                hidden>
            </div>';

        return ("OK");
	}
?>


