<?php
include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloAdministracion/ModuloAdministracion.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloMonitorizacion/ModuloMonitorizacion.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloPersonal/ModuloPersonal.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloRed/ModuloRed.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloLocalizaciones/ModuloLocalizaciones.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloSensores/ModuloSensores.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloActuadores/ModuloActuadores.php");
include_once($_SESSION["directorio"]."/src/modulos/ModulosWeb/ModuloSmartmeter/ModuloSmartmeter.php");


// Devuelve el modulo web
function dame_modulo_web($modulo)
{
    switch ($modulo)
    {
        case MODULO_ADMINISTRACION:
        {
            $modulo_web = new ModuloAdministracion();
            break;
        }
        case MODULO_MONITORIZACION:
        {
            $modulo_web = new ModuloMonitorizacion();
            break;
        }
        case MODULO_PERSONAL:
        {
            $modulo_web = new ModuloPersonal();
            break;
        }
        case MODULO_RED:
        {
            $modulo_web = new ModuloRed();
            break;
        }
        case MODULO_LOCALIZACIONES:
        {
            $modulo_web = new ModuloLocalizaciones();
            break;
        }
        case MODULO_SENSORES:
        {
            $modulo_web = new ModuloSensores();
            break;
        }
        case MODULO_ACTUADORES:
        {
            $modulo_web = new ModuloActuadores();
            break;
        }
        case MODULO_SMARTMETER:
        {
            $modulo_web = new ModuloSmartmeter();
            break;
        }
        case MODULO_PROYECTOS:
        {
            $modulo_web = new ModuloProyectos();
            break;
        }
        default:
        {
            throw new Exception("Módulo web desconocido: '".$modulo."'");
        }
    }

    return ($modulo_web);
}


// Devuelve las secciones del módulo especificado
function dame_secciones_modulo($modulo)
{
    switch ($modulo)
    {
        case MODULO_ADMINISTRACION:
        {
            $secciones = ModuloAdministracion::dame_secciones();
            break;
        }
        case MODULO_MONITORIZACION:
        {
            $secciones = ModuloMonitorizacion::dame_secciones();
            break;
        }
        case MODULO_PERSONAL:
        {
            $secciones = ModuloPersonal::dame_secciones();
            break;
        }
        case MODULO_RED:
        {
            $secciones = ModuloRed::dame_secciones();
            break;
        }
        case MODULO_LOCALIZACIONES:
        {
            $secciones = ModuloLocalizaciones::dame_secciones();
            break;
        }
        case MODULO_SENSORES:
        {
            $secciones = ModuloSensores::dame_secciones();
            break;
        }
        case MODULO_ACTUADORES:
        {
            $secciones = ModuloActuadores::dame_secciones();
            break;
        }
        case MODULO_SMARTMETER:
        {
            $secciones = ModuloSmartmeter::dame_secciones();
            break;
        }
        case MODULO_PROYECTOS:
        {
            $secciones = ModuloProyectos::dame_secciones();
            break;
        }
        default:
        {
            throw new Exception("Módulo web desconocido: '".$modulo."'");
        }
    }

    return ($secciones);
}


function dame_todos_modulos_ordenados()
{
    $todos_modulos_ordenados = array(
        MODULO_ADMINISTRACION,
        MODULO_MONITORIZACION,
        MODULO_PERSONAL,
        MODULO_RED,
        MODULO_LOCALIZACIONES,
        MODULO_SENSORES,
        MODULO_ACTUADORES,
        MODULO_SMARTMETER,
        MODULO_PROYECTOS);
    return ($todos_modulos_ordenados);
}


function dame_id_ordenacion_lista_modulo($modulo)
{
    $todos_modulos_ordenados = dame_todos_modulos_ordenados();
    $id_ordenacion_lista = array_search($modulo, $todos_modulos_ordenados);
    return ($id_ordenacion_lista);
}


function dame_modulos_ordenados($modulos)
{
    $todos_modulos_ordenados = dame_todos_modulos_ordenados();
    $modulos_ordenados = array();
    foreach ($todos_modulos_ordenados as $modulo_ordenado)
    {
        if (in_array($modulo_ordenado, $modulos) == true)
        {
            array_push($modulos_ordenados, $modulo_ordenado);
        }
    }
    return ($modulos_ordenados);
}


function dame_secciones_modulo_ordenadas($secciones, $modulo)
{
    $todas_secciones_ordenadas = dame_secciones_modulo($modulo);
    $secciones_ordenados = array();
    foreach ($todas_secciones_ordenadas as $seccion_ordenada)
    {
        if (in_array($seccion_ordenada, $secciones) == true)
        {
            array_push($secciones_ordenados, $seccion_ordenada);
        }
    }
    return ($secciones_ordenados);
}
?>