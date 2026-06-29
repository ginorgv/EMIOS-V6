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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ELIMINAR_ELEMENTOS_PLANTILLA_INFORME, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];

    // Título y pie de ventana
    $titulo .= $idiomas->_("Eliminar elementos");
    $pie .= "<button id='modifica_posiciones_elementos_plantilla_informe__".$id_plantilla_informe."' class='btn btn-success boton_eliminar_elementos_plantilla_informe'>".$idiomas->_("Eliminar").'</button>';
    $pie .= "<button class='btn' data-dismiss='modal' aria-hidden='true'>".$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_eliminar_elementos_plantilla_informe($id_plantilla_informe, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de eliminar elementos de la plantilla de informe
	//


	// Función que rellena el contenido de la ventana de eliminar elementos de la plantilla de informe
 	function rellena_contenido_ventana_eliminar_elementos_plantilla_informe($id_plantilla_informe, &$contenido)
	{
        $idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Elementos").": "."</span><br/>
                    <div class='lista-elementos'>
                        <select id='elementos' size='20' class='select-administracion' multiple='multiple'>";

        // Se recuperan los elementos de la plantilla de informe y se añaden a la lista de elementos de la plantilla de informe
        $consulta_elementos = "
            SELECT *
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
            ORDER BY posicion ASC";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if (($res_elementos == false))
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }
        while ($fila = $res_elementos->dame_siguiente_fila())
        {
            $id = $fila['id'];
            $nombre = $fila['nombre'];
            $tipo = $fila['tipo'];

            $clase_elemento = NULL;
            switch ($tipo)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
                {
                    $clase_elemento = "color-gris-muy-claro";
                    break;
                }
            }
            $contenido .= "<option id='".$id."' tipo='".$tipo."'";
            if ($clase_elemento !== NULL)
            {
                $contenido .= " class='".$clase_elemento."'";
            }
            $contenido .= ">".htmlspecialchars($nombre, ENT_QUOTES)."</option>";
        }

        $contenido .= "
                        </select>
                    </div>
                </div>
            </div>";

        return ("OK");
	}
?>