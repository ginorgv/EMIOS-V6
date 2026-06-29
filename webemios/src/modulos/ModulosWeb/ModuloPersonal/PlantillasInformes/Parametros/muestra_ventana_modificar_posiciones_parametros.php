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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_POSICIONES_PARAMETROS_PLANTILLA_INFORME, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];

    // Título y pie de ventana
    $titulo .= $idiomas->_("Modificar posiciones de parámetros");
    $pie .= "<button id='modifica_posiciones_parametros_plantilla_informe__".$id_plantilla_informe."' class='btn btn-success boton_modificar_posiciones_parametros_plantilla_informe'>".$idiomas->_("Modificar").'</button>';
    $pie .= "<button class='btn' data-dismiss='modal' aria-hidden='true'>".$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_modificar_posiciones_parametros_plantilla_informe($id_plantilla_informe, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de modificar las posiciciones de los parametros de la plantilla de informe
	//


	// Función que rellena el contenido de la ventana de modificar las posiciciones de los parametros de la plantilla de informe
 	function rellena_contenido_ventana_modificar_posiciones_parametros_plantilla_informe($id_plantilla_informe, &$contenido)
	{
        $idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Posiciones de los parámetros").": "."</span><br/>
                    <div class='lista-posiciones'>
                        <select id='posicion_parametros' size='20' class='select-administracion'>";

        // Se recuperan los parámetros de la plantilla de widgets y se añaden a la lista de parámetros de la plantilla de informe
        $consulta_parametros = "
            SELECT *
            FROM parametros_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if (($res_parametros == false))
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }
        while ($fila = $res_parametros->dame_siguiente_fila())
        {
            $id = $fila['id'];
            $nombre = $fila['nombre'];
            $posicion = $fila['posicion'];
            $contenido .= "<option id='".$id."' value='".$posicion."'>".htmlspecialchars($nombre, ENT_QUOTES)."</option>";
        }

        $contenido .= "
                        </select>
                    </div>
                    <div>
                        <p><button id='boton_subir_posicion_parametro' class='btn-mini btn btn-success'><i class='icon-arrow-up color-blanco'></i></button></p>
                        <p><button id='boton_bajar_posicion_parametro' class='btn-mini btn btn-success'><i class='icon-arrow-down color-blanco'></i></button></p>
                    </div>
                </div>
            </div>";

        return ("OK");
	}
?>