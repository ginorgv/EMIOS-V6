<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_PLANTILLA_INFORME, $_POST);

    $id_plantilla_informe = $_POST['id_plantilla_informe'];

	$fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
    $plantilla_informe = new PlantillaInforme($fila_plantilla_informe);
    switch ($_SESSION["perfil"])
    {
        case PERFIL_USUARIO_ESTANDAR:
        {
            $numero_columnas_tabla_plantillas_informes = NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_SIN_USUARIO;
            break;
        }
        default:
        {
            $numero_columnas_tabla_plantillas_informes = NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_CON_USUARIO;
            break;
        }
    }
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $plantilla_informe->dame_opciones_tabla(),
        "numero_columnas" => $numero_columnas_tabla_plantillas_informes);
    $fila = TablaDatos::dame_fila(
        $plantilla_informe->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
