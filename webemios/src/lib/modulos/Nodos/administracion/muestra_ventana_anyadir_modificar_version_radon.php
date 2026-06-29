<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores_externos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');


    $log = dame_log();
	$idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_NODO, $_POST);

    // Parámetros
    $tipo_accion = $_POST["tipo_accion"];
    $id_dispositivo = $_POST["id_dispositivo"];

    // Añadir o modificar nodo
    
    switch ($tipo_accion) {
        case 'actualiza':
            {
                $titulo .= $idiomas->_("Actualizar");
                $pie .= '<button class="btn btn-success boton_actualizar_dispositivo" id="'.$id_dispositivo.'">'.$titulo.'</button>';
                break;
            }
        case 'elimina':
            {
                $titulo .= $idiomas->_("Eliminar");
                $pie .= '<button class="btn btn-success boton_eliminar_dispositivo" id="'.$id_dispositivo.'">'.$titulo.'</button>';
                break;
            }
        case 'anyade':
            {
                $titulo .= $idiomas->_("Añadir Versión");
                $pie .= '<button class="btn btn-success boton_anyadir_version_firmware" id="'.$id_dispositivo.'">'.$titulo.'</button>';
                break;
            }
        default:
            break;
    }
    
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título y contenido de la ventana
    // (se muestra un contenido diferente en la ventana modal dependiendo del tipo de nodo)
    switch ($tipo_accion)
    {

        case 'actualiza':
        case 'elimina':
        {
            $error = rellena_contenido_ventana_actualizar_dispositivo($contenido);
            break;
        }
        case 'anyade':
        {
            $error = rellena_contenido_ventana_anyadir_version($contenido);
            break;
        }
        default:
        {
            $error .= "Tipo accion: '".$tipo_accion."' no implementado";
        }
    }
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
    if ($mostrar_controles_localizaciones == true)
    {
        $valor_mostrar_controles_localizaciones = VALOR_SI;
    }
    else
    {
        $valor_mostrar_controles_localizaciones = VALOR_NO;
    }
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_nodo"
            tipo_nodo="'.$tipo_nodo.'"
            anyadir_nodo="'.$anyadir_nodo.'"
            id_nodo="'.$id_nodo.'"
            mostrar_controles_localizaciones="'.$valor_mostrar_controles_localizaciones.'"
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
	// Funciones para mostrar el contenido de las ventanas de anyadir/modificar nodos
	//

    function rellena_contenido_ventana_anyadir_version(&$contenido)
	{
		$idiomas = new Idiomas();

		
        // Se muestran dos pestañas: Datos y posición en mapa
        $contenido = "
            <div id='tabs-administracion-dispositivo' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-datos'>".$idiomas->_("Datos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-versiones'>".$idiomas->_("Versiones")."</a></li>
                </ul>
                <div id='tabs-content-administracion-dispositivo' class='tab-content'>";

        $contenido .= "
            <div class='tab-pane active' id='tab-datos'>";

        // Contenido de pestaña de datos
		$contenido .= "
			    <div class='row-fluid'>
			    	<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Id Versión").": "."</span><br/>
			    		<input type='text' id='id_version'
			    			class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($id_version, ENT_QUOTES)."'>
			    	</div>
			    </div>
                <div class='row-fluid'>
			    	<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre fichero").": "."</span><br/>
			    		<input type='text' id='nombre_fichero'
			    			class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_fichero, ENT_QUOTES)."'>
			    	</div>
			    </div>
                <div class='row-fluid'>
			    	<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Servidor").": "."</span><br/>
			    		<input type='text' id='servidor'
			    			class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_fichero, ENT_QUOTES)."'>
			    	</div>
			    </div>";

        $contenido .= "
            </div>";
        // Tab que contiene las versiones registradas actualmente
        //$contenido .= "<div id='tablaPreferencias'>".dame_tabla_versiones_firmware_radon()."</div>";

        $contenido .="
            <div class='tab-pane' id='tab-versiones'>
                <div class='row-fluid'>
                ".dame_tabla_versiones_firmware_radon()."
                </div>
            </div>";

        $contenido .= "
                </div>
            </div>";

        return ("OK");
	}

    function rellena_contenido_ventana_actualizar_dispositivo(&$contenido)
    {
        $idiomas = new Idiomas();

		
        // Se muestran dos pestañas: Datos y posición en mapa
        $contenido = "
            <div id='tabs-administracion-dispositivo' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-versiones'>".$idiomas->_("Versiones")."</a></li>
                </ul>
                <div id='tabs-content-administracion-dispositivo' class='tab-content'>";

        
        // Tab que contiene las versiones registradas actualmente
        $contenido .= "
        <div class='row-fluid'>
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Version de la actualización").": "."</span><br/>
                <select id='nombre_version_firmware' class='select-administracion'>";
    $contenido .= dame_listado_versiones($version);
    $contenido .= "
                </select>
            </div>
        </div>";

        $contenido .="
            <div class='tab-pane active' id='tab-versiones'>
                <div class='row-fluid'>
                ".dame_tabla_versiones_firmware_radon()."
                </div>
            </div>";

        $contenido .= "
                </div>
            </div>";

        return ("OK");
    }


    function dame_tabla_versiones_firmware_radon()
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();


            // Se crea la tabla
            $params_tabla = array(
                "opciones" => NULL,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_USUARIOS,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_NORMAL,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-administracion-preferencias",
                $idiomas->_("Versiones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
				$idiomas->_("Id Versión"),
                $idiomas->_("Nombre fichero"),
                $idiomas->_("Servidor"),
			);
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las preferencias a la tabla y el pie de tabla
            $consulta = "
				SELECT id_version,nombre_fichero,servidor 
				FROM versiones_firmware_radon
				ORDER BY id_version ASC";
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_filas= $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $preferencias = new Preferencias($fila);
                $tabla->anyade_fila(
                    "datosPreferencias__".$fila['id'],
                    array(
                        $fila['id_version'],
                        $fila['nombre_fichero'],
                        $fila['servidor'])
                );
            }
			$tabla->anyade_pie($idiomas->_("Nº de versiones").": ".$numero_filas);

            return ($tabla->dame_tabla());
        }
        //// Clase que representa las tablas del firmware
    function dame_listado_versiones()
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $consulta = "
				SELECT id_version,nombre_fichero,servidor 
				FROM versiones_firmware_radon
				ORDER BY id_version ASC";
		$res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        while ($fila = $res->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila['id_version']."__".$fila['nombre_fichero']."__".$fila['servidor']."'";
			$lista_sensores .= ">".htmlspecialchars($fila['nombre_fichero'], ENT_QUOTES)." - ".htmlspecialchars($fila['servidor'], ENT_QUOTES)."</option>";
        }
        return $lista_sensores;
    }
?>
