<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_herramientas_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Parámetros
    $clase_nodo = $_POST["clase_nodo"];
    $id_grupo = $_POST["id_grupo"];
    $tipo_nodo = $_POST["tipo_nodo"];

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
    if ($clase_nodo == CLASE_TODAS)
    {
        switch ($id_grupo)
        {
            case ID_NINGUNO:
            {
                $clase_nodo = CLASE_TODAS;
                break;
            }
            default:
            {
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $fila_grupo = dame_fila_grupo_sensores($id_grupo);
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    {
                        $fila_grupo = dame_fila_grupo_actuadores($id_grupo);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                    }
                }
                $clase_nodo = $fila_grupo["clase"];
            }
        }
    }
    switch ($tipo_nodo)
    {
        case TIPO_NODO_SENSOR:
        {
            $html = dame_lista_sensores($clase_nodo, $ids_nodos_grupo, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
            break;
        }
        case TIPO_NODO_ACTUADOR:
        {
            $html = dame_lista_actuadores($clase_nodo, $ids_nodos_grupo, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
            break;
        }
        default:
        {
            throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
        }
    }

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
