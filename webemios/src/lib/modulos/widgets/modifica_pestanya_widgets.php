<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/CuadriculaWidgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanya_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PESTANYA_WIDGETS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_pestanya = $_POST["id_pestanya"];
    $nombre = $_POST["nombre"];
    $posicion_pestanya_anterior = $_POST["posicion_pestanya_anterior"];
    $actualizacion_periodica_rotatoria = $_POST["actualizacion_periodica_rotatoria"];
    $numeros_columnas_filas_widgets = $_POST["numeros_columnas_filas_widgets"];
    $titulos_filas_widgets = $_POST["titulos_filas_widgets"];
    $ajustar_altura_widgets = $_POST["ajustar_altura_widgets"];
    $parametros_apariencia_pestanya = $_POST["parametros_apariencia_pestanya"];
    $parametros_apariencia_widgets = $_POST["parametros_apariencia_widgets"];
    $parametros_opciones_pantalla_completa = $_POST["parametros_opciones_pantalla_completa"];
    $ids_widgets = $_POST["ids_widgets"];
    $modulo = $_POST["modulo"];

    // Parámetros auxiliares
    $imagen_fondo_apariencia_pestanya_anterior = $_POST['imagen_fondo_apariencia_pestanya_anterior'];
    $imagen_fondo_apariencia_pestanya = $_POST['imagen_fondo_apariencia_pestanya'];

    // Se comprueba si existe otra pestaña de widgets del usuario con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM pestanyas_widgets
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
            AND (modulo = '".$bd_red->_($modulo)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_pestanya)."')";
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
        // Si antes había imagen de mapa local y ahora no, se elimina la imagen anterior
        if (($imagen_fondo_apariencia_pestanya_anterior == VALOR_SI) && ($imagen_fondo_apariencia_pestanya == VALOR_NO))
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, $id_pestanya);
        }

        // Se recupera la posición anterior de la pestaña de widgets
        $consulta_posicion = "
            SELECT posicion
            FROM pestanyas_widgets
            WHERE
                id = '".$bd_red->_($id_pestanya)."'";
        $res_posicion = $bd_red->ejecuta_consulta($consulta_posicion);
        if (($res_posicion == false) || ($res_posicion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$res_posicion."'");
        }
        $fila_posicion = $res_posicion->dame_siguiente_fila();
        $posicion_anterior = $fila_posicion['posicion'];

        // Posición auxiliar (se añade una posición 0.1 mayor que la de la pestaña anterior y después se 'normalizan' las posiciones de 1 en 1)
        $posicion_auxiliar = $posicion_pestanya_anterior + 0.1;

        // Se modifica la pestaña de widgets
        $operacion_modificacion = "
            UPDATE pestanyas_widgets
            SET
                nombre = '".$bd_red->_($nombre)."',
                posicion = '".$bd_red->_($posicion_auxiliar)."',
                actualizacion_periodica_rotatoria = '".$bd_red->_($actualizacion_periodica_rotatoria)."',
                numeros_columnas_filas_widgets = '".$bd_red->_($numeros_columnas_filas_widgets)."',
                titulos_filas_widgets = '".$bd_red->_($titulos_filas_widgets)."',
                ajustar_altura_widgets = '".$bd_red->_($ajustar_altura_widgets)."',
                parametros_apariencia_pestanya = '".$bd_red->_($parametros_apariencia_pestanya)."',
                parametros_apariencia_widgets = '".$bd_red->_($parametros_apariencia_widgets)."',
                parametros_opciones_pantalla_completa = '".$bd_red->_($parametros_opciones_pantalla_completa)."'
            WHERE
                id = '".$bd_red->_($id_pestanya)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }

        // Actualiza las posiciones de las pestañas de widgets
        actualiza_posiciones_pestanyas_widgets_modulo($modulo);

        // Actualización de las posiciones de los widgets
        if ($_POST["ids_widgets"] != "")
        {
            $posicion_widget = 1;
            foreach ($ids_widgets as $id_widget)
            {
                $operacion_modificacion_widget = "
                    UPDATE widgets
                    SET
                        posicion = '".$bd_red->_($posicion_widget)."'
                    WHERE
                        id = '".$bd_red->_($id_widget)."'";
                $res_modificacion_widget = $bd_red->ejecuta_operacion($operacion_modificacion_widget);
                if ($res_modificacion_widget == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_widget."'");
                }
                $posicion_widget++;
            }
        }

        $res = "OK";
        $msg = $idiomas->_("Pestaña de widgets modificada correctamente");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
