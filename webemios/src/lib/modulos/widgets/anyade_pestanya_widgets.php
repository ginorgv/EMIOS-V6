<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanya_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PESTANYA_WIDGETS, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST["nombre"];
    $posicion_pestanya_anterior = $_POST["posicion_pestanya_anterior"];
    $actualizacion_periodica_rotatoria = $_POST["actualizacion_periodica_rotatoria"];
    $numeros_columnas_filas_widgets = $_POST["numeros_columnas_filas_widgets"];
    $titulos_filas_widgets = $_POST["titulos_filas_widgets"];
    $ajustar_altura_widgets = $_POST["ajustar_altura_widgets"];
    $parametros_apariencia_pestanya = $_POST["parametros_apariencia_pestanya"];
    $parametros_apariencia_widgets = $_POST["parametros_apariencia_widgets"];
    $parametros_opciones_pantalla_completa = $_POST["parametros_opciones_pantalla_completa"];
    $id_usuario = $_POST["id_usuario"];
    $modulo = $_POST["modulo"];
    $id_pestanya_anterior = $_POST["id_pestanya_anterior"];

    // Flag de duplicado
    $duplicar_pestanya = ($id_pestanya_anterior != ID_NINGUNO);

    // Usuario 'destino'
    if ($duplicar_pestanya == true)
    {
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            $id_usuario = $_SESSION["id_usuario"];
        }
        else
        {
            if ($id_usuario == ID_NINGUNO)
            {
                $id_usuario = $_SESSION["id_usuario"];
            }
        }
    }
    else
    {
        $id_usuario = $_SESSION["id_usuario"];
    }

    // Se comprueba si existe una pestaña del usuario con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM pestanyas_widgets
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (usuario = '".$bd_red->_($id_usuario)."')
            AND (modulo = '".$bd_red->_($modulo)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una pestaña de widgets con el mismo nombre");
    }
    else
    {
        // Se añade la pestaña de widgets
        if ($duplicar_pestanya == false)
        {
            // Posición auxiliar (se añade una posición 0.1 mayor que la de la pestaña anterior y después se 'normalizan' las posiciones de 1 en 1)
            $posicion_auxiliar = $posicion_pestanya_anterior + 0.1;

            // Se añade la pestaña de widgets
            $operacion_insercion = "
                INSERT INTO pestanyas_widgets (
                    nombre,
                    red,
                    usuario,
                    modulo,
                    posicion,
                    actualizacion_periodica_rotatoria,
                    numeros_columnas_filas_widgets,
                    titulos_filas_widgets,
                    ajustar_altura_widgets,
                    parametros_apariencia_pestanya,
                    parametros_apariencia_widgets,
                    parametros_opciones_pantalla_completa
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($_SESSION["id_usuario"])."',
                    '".$bd_red->_($modulo)."',
                    '".$bd_red->_($posicion_auxiliar)."',
                    '".$bd_red->_($actualizacion_periodica_rotatoria)."',
                    '".$bd_red->_($numeros_columnas_filas_widgets)."',
                    '".$bd_red->_($titulos_filas_widgets)."',
                    '".$bd_red->_($ajustar_altura_widgets)."',
                    '".$bd_red->_($parametros_apariencia_pestanya)."',
                    '".$bd_red->_($parametros_apariencia_widgets)."',
                    '".$bd_red->_($parametros_opciones_pantalla_completa)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                $res = "OK";
                $msg = $idiomas->_("Pestaña de widgets añadida correctamente");

                // Se recupera el id de la pestaña añadida
                $id_pestanya = $bd_red->dame_id_autoincremental_ultima_insercion();
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
        else
        {
            // Se añade la pestaña de widgets
            $operacion_insercion = "
                INSERT INTO pestanyas_widgets (
                    red,
                    usuario,
                    modulo,
                    nombre,
                    posicion,
                    actualizacion_periodica_rotatoria,
                    numeros_columnas_filas_widgets,
                    titulos_filas_widgets,
                    ajustar_altura_widgets,
                    parametros_apariencia_pestanya,
                    parametros_apariencia_widgets,
                    parametros_opciones_pantalla_completa
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($modulo)."',
                    '".$bd_red->_($nombre)."',
                    '".$bd_red->_(POSICION_PESTANYA_ULTIMA)."',
                    '".$bd_red->_($actualizacion_periodica_rotatoria)."',
                    '".$bd_red->_($numeros_columnas_filas_widgets)."',
                    '".$bd_red->_($titulos_filas_widgets)."',
                    '".$bd_red->_($ajustar_altura_widgets)."',
                    '".$bd_red->_($parametros_apariencia_pestanya)."',
                    '".$bd_red->_($parametros_apariencia_widgets)."',
                    '".$bd_red->_($parametros_opciones_pantalla_completa)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recupera el id de la pestaña añadida
                $id_pestanya = $bd_red->dame_id_autoincremental_ultima_insercion();

                // Se duplican los widgets
                $numero_widgets_no_duplicados = duplica_widgets_pestanya_widgets_usuario($id_pestanya_anterior, $id_pestanya, $id_usuario);

                $res = "OK";
                $msg = $idiomas->_("Pestaña de widgets duplicada correctamente");
                if ($numero_widgets_no_duplicados > 0)
                {
                    $msg .= "\n(".$idiomas->_("número de widgets no duplicados").": ".$numero_widgets_no_duplicados.")";
                }
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }

        // Actualiza las posiciones de las pestañas de widgets
        actualiza_posiciones_pestanyas_widgets_modulo($modulo);
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_pestanya" => $id_pestanya))
    );
?>
