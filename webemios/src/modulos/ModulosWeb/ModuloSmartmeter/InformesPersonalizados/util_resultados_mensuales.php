<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/electricidad/Espanya/util_informes_informes_personalizados_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/gas/Espanya/util_informes_informes_personalizados_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/agua/Espanya/util_informes_informes_personalizados_agua_Espanya.php');


    //
    // Funciones de ficheros de resultados mensuales
    //


    // Funcion que devuelve la tabla de los ficheros excel a listar
    function dame_tabla_ficheros_excel_disponibles()
    {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $boton_actualizar_tabla_ficheros_excel = "<i id='actualiza_tarifas_ficheros_excel' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_ficheros_excel boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_ficheros_excel);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => 2,
                "anchuras_columnas" => array(30,30),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_NORMAL,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-ficheros-resultados-mensuales",
                $idiomas->_("Ficheros Resultados Mensuales"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
		        $idiomas->_("Nombre"),
                $idiomas->_("Fichero"),
			);
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las tarifas a la tabla y el pie de tabla
            $id_red = $_SESSION["id_red"];

            $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores_usuario);
            $consulta = "SELECT * FROM ficheros_excel_almacenados WHERE (red =".$id_red.") AND (sensor IN (".$cadena_ids_sensores_consulta."))";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // TODO: Filtrar ficheros listados segun los permisos del usuario
            // Recuperar la lista de sensores para los cuales el usuario tiene permiso
            
            // Se añaden los ficheros a la lista a mostrar
            $numero_ficheros_excel = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $nombre_fichero = $fila["nombre_fichero"];
                $alias_fichero = $fila["alias_fichero"];
                $id_sensor = $fila["id"];
                
                $anyadir_fichero_excel = true;
                
                // TODO: Filtrar ficheros listados segun los permisos del usuario
                // Filtrar la lista de ficheros por id de sensor segun los permisos del usuario
                
                if ($anyadir_fichero_excel == true)
                {
                    $numero_ficheros_excel += 1;
                    
                    $params_fila = array(
                        "opciones" => dame_opciones_tabla($id_sensor, $nombre_fichero)
                    );
                    //$params_fila = array();
                    $tabla->anyade_fila(
                        "datosficherosexcel__".$fila['id'],
                        array($alias_fichero,$nombre_fichero),
                        $params_fila
                    );
                    
                }
            }
			$tabla->anyade_pie($idiomas->_("Ficheros").": ".$numero_ficheros_excel);

                        
        return ($tabla->dame_tabla());
    }
        

    // Guardar el fichero excel
    function sube_fichero_excel($parametros)
    {
        $idiomas = new Idiomas();
        
        // Recuperamos los parametros de la funcion
        $nombre_fichero = $parametros["nombre_fichero"];
        $alias_fichero = $parametros["alias_fichero"];
        $id_sensor = $parametros["sensor_asociado"];
        $id_red = $_SESSION["id_red"];

        // Comprobamos si existe un fichero con el mismo nombre
        $bd_red = BaseDatosRed::dame_base_datos();
        $consulta = "SELECT * FROM ficheros_excel_almacenados WHERE nombre_fichero ='" . $nombre_fichero . "'";
        $res_consulta = $bd_red->ejecuta_consulta($consulta);
        if ($res_consulta == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        $fila = $res_consulta->dame_siguiente_fila();
        if($fila != NULL){
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un fichero con ese nombre");
            return(array("res" => $res, "msg" => $msg));
        }
        
        // Llamamos a la funcion python para subir el fichero
        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_REGISTRA_FICHEROS_EXCEL,
                "nombre_fichero" => $nombre_fichero,
                "alias_fichero" => $alias_fichero,
                "sensor_asociado" => $id_sensor,
                "id_red" => $id_red
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        $res = "OK";
        $msg = $idiomas->_("Fichero subido correctamente");

        return(array("res" => $res, "msg" => $msg));
        
    }

    // Funcion que llama al python
    // Eliminar el fichero excel
    function elimina_fichero_excel($parametros)
    {
        $idiomas = new Idiomas();
        
        // Recuperamos los parametros de la funcion
        $nombre_fichero = $parametros["nombre_fichero"];
        $id_fichero = $parametros["id_fichero"];

        $log = dame_log();
        $log->info("SUBIR FICHERO EXCEL ". $nombre_fichero);
        
        // Llamamos a la funcion python para eliminar el fichero
        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_ELIMINA_FICHEROS_EXCEL,
                "nombre_fichero" => $nombre_fichero,
                "id_fichero" => $id_fichero
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        $log->info("RESULTADO FUNCION EXTERNA " . $resultado_funcion_externa["res"]);
        $res = "OK";
        $msg = $idiomas->_("Fichero borrado correctamente");

        return(array("res" => $res, "msg" => $msg));
        
    }

    function descarga_fichero_excel($parametros)
    {
        $idiomas = new Idiomas();
        
        $directorio_origen = "/opt/energyminus/tmp/xls/";
        // Recuperamos los parametros de la funcion
        $nombre_fichero = $parametros["nombre_fichero"];
        $ubicacion_fichero_origen = $directorio_origen.$nombre_fichero;
        
        
        // Ejecutamos un comando de sistema para mover el archivo al directorio temporal del usuario

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);
        $ubicacion_fichero_origen = str_replace("(", "\(", $ubicacion_fichero_origen);
        $ubicacion_fichero_origen = str_replace(")", "\)", $ubicacion_fichero_origen);
        $ubicacion_fichero_origen = str_replace(" ", "\ ", $ubicacion_fichero_origen);

        //$comando = "ls " . $directorio_origen;
	    $comando = "cp ".$ubicacion_fichero_origen." ".$directorio_absoluto_ficheros_temporales_usuario; 
        //if (dame_sistema_operativo_windows() == false)
        //{
        //$comando = "sudo ".$comando;
        //}


        $resultado_json = ejecuta_comando($comando);
        //session_write_close();
        //$resultado_json = shell_exec($comando);
        //session_start();

        $ruta_fichero_temporal_usuario = $directorio_relativo_ficheros_temporales_usuario."/".$nombre_fichero;
        $log = dame_log();
        $log->error("SUBIR FICHERO EXCEL ". $comando." ".$resultado_json);
        $res = $resultado_json;
        $msg = $idiomas->_("Fichero descargado correctamente");
        

        return(array("res" => $res, "msg" => $msg, "rutas_ficheros_valores_exportados" => $ruta_fichero_temporal_usuario));
        
    }

    // Devuelve las opciones para mostrar en la tabla
	function dame_opciones_tabla($id_sensor, $nombre_fichero)
	{
           $editar = "<i id='descarga_fichero_excel__".$id_sensor."'".
               "class='icon-download-alt color-gris boton_smartmeter_descargar_fichero_excel boton-tabla-datos'".
               "nombre_fichero='".$nombre_fichero."'></i>";
           if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR){
                $borrar = "<i id='elimina_fichero_excel__".$id_sensor."'".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_fichero_excel boton-tabla-datos'".
                    "nombre_fichero='".$nombre_fichero."'></i>";
           }
           $opciones = array($borrar, $editar);
		return ($opciones);
	}
?>
