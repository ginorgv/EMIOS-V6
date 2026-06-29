<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_SENSOR, $_POST);

    $idiomas = new Idiomas();

    // Parámetros
    $id_sensor = $_POST['id_sensor'];

    // Se recupera la fila del sensor
    $fila_sensor = dame_fila_sensor($id_sensor);

    // Comprobaciones antes de eliminar el sensor:
    // - Se recupera si es posible eliminar el sensor

    // Se recupera si es posible eliminar el sensor
    $msg = "";
    $eliminar_sensor = dame_posible_eliminar_sensor(
        $id_sensor,
        $fila_sensor,
        $msg,
        "");

    // Se elimina el sensor
    if ($eliminar_sensor == true)
    {
        // En el caso de que sea de un servicio externo
        // primero se intenta eliminar externamente
        //  a continuacion se borra el sensor de emios
        $tipo = $fila_sensor['tipo'];
        if ($tipo==TIPO_SENSOR_EXTERNO)
            {
                $parametros_tipo = $fila_sensor['parametros_tipo'];
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
                // Se comprueba si es de tipo API
                $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];

                if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_API)
                {

                    $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                    $opciones_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
                    $api_seleccionada = $opciones_valores[0];

                    switch ($api_seleccionada)
                    {
                        case API_AXONTIME:

                            elimina_sensor_axontime($opciones_valores);
                            break;

                        case API_SGCLIMA:

                            elimina_sensor_sgclima($fila_sensor);
                            break;
                    }
                }
                if  ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_FICHEROS_CSV)
                {
                    // Si es de CSV y de DATADIS
                    // se notifica al servicio externo
                    $cadena_opciones_generales = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                    $opciones_generales = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales);
                    if ($opciones_generales[1] == "datadis")
                    {
                        $cadena_opciones_valores_datadis = $parametros_tipo[4];
                        $opciones_valores_datadis = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_datadis);
                        $eliminar_sensor = elimina_sensor_datadis($fila_sensor);
                        if ($eliminar_sensor == False)
                        {
                            return;
                        }
                    }

                }
            }


        // Se elimina el sensor
        elimina_sensor($id_sensor, $fila_sensor);

        // Eliminación correcta
        $res = "OK";
        $msg = $idiomas->_("Sensor eliminado correctamente");

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_sensor($fila_sensor);
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del sensor
    function anyade_accion_usuario_eliminar_sensor($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_SENSOR;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }

    function elimina_sensor_axontime($opciones_valores)
    {
        $direccion_api_externa = API_EXTERNA_SENSORES_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);


        // Tras obtener el token se procede a borrar el sensor

        $cups_id = $opciones_valores[1];
        $campo_lectura = $opciones_valores[3];
        $ip_servidor = file_get_contents('http://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SENSORES_DIRECCION.'/eliminar_sensor_axon';
        $curl = curl_init();
        // Dependiendo de si es activa o reactiva
        // ataca con distintos metodos DELETE o PUT
        // ademas cambia el nombre de uno de los
        // parametros

        switch ($campo_lectura)
        {
            case 'energia':
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&red='.$_SESSION['id_red'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'DELETE',
                  CURLOPT_HTTPHEADER => array(
                      'token:'.$token),
                ));
                break;
            case 'ie1q':
               curl_setopt_array($curl, array(
                 CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&red='.$_SESSION['id_red'].'&flag_reactiva=1',
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_ENCODING => '',
                 CURLOPT_MAXREDIRS => 10,
                 CURLOPT_TIMEOUT => 0,
                 CURLOPT_FOLLOWLOCATION => true,
                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                 CURLOPT_CUSTOMREQUEST => 'PUT',
                 CURLOPT_HTTPHEADER => array(
                     'token:'.$token),
               ));
               break;
            case 'ce4q':
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&red='.$_SESSION['id_red'].'&flag_reactiva=2',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_HTTPHEADER => array(
                        'token:'.$token),
                  ));
                break;
            default:
                return;
                break;
        }
        $response = curl_exec($curl);
        // Control del codigo de estado
        // de la peticion
         if (!curl_errno($curl)) {
                    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                      case 200:  # OK
                        break;
                      default:
                        $res = "ERROR";
                        $msg = 'Error en la peticion a la API code:'.$http_code;
                        return;
                    }
                  }
                curl_close($curl);

    }

    function elimina_sensor_sgclima($fila_sensor)
    {
        $direccion_api_externa = API_EXTERNA_SGCLIMA_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $nombre = $fila_sensor['nombre'];

        $ip_servidor = file_get_contents('http://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SGCLIMA_DIRECCION.'/eliminar_sensor_sgclima';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre='.$nombre.'&ip_servidor='.$ip_servidor.'&red='.$_SESSION['id_red'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'token:'.$token),
          ));

        $response = curl_exec($curl);
        // Control del codigo de estado
        // de la peticion
         if (!curl_errno($curl)) {
                    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                      case 200:  # OK
                        break;
                      default:
                        $res = "ERROR";
                        $msg = 'Error en la peticion a la API code:'.$http_code;
                        return;
                    }
                  }
                curl_close($curl);

    }

    function elimina_sensor_datadis($fila_sensor)
    {
        $direccion_api_externa = API_EXTERNA_SENSORES_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $nombre = $fila_sensor['nombre'];

        $ip_servidor = file_get_contents('http://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SENSORES_DIRECCION.'/datadis/elimina_sensor';
        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre_sensor='.$nombre_url.'&ip_servidor='.$ip_servidor.'&red='.$_SESSION['id_red'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'token:'.$token),
          ));

        $response = curl_exec($curl);
        // Control del codigo de estado
        // de la peticion
         if (!curl_errno($curl)) {
                    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                      case 200:  # OK
                        break;
                      default:
                        $res = "ERROR";
                        $msg = 'Error en la peticion a la API code:'.$http_code;
                        return False;
                    }
                  }
                curl_close($curl);
                return True;

    }

    function obtiene_token_api($direccion_api_externa)
    {
        // Primero se obtiene el token para autenticarse en la API
        $curl = curl_init();
        $url = $direccion_api_externa.'/login';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?usuario='.API_EXTERNA_SENSORES_USUARIO.'&password='.API_EXTERNA_SENSORES_PASS,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST'
        ));
        $response = curl_exec($curl);
        $data = json_decode($response,true);
        curl_close($curl);
        $token = $data['token'];
        return $token;
    }

?>
