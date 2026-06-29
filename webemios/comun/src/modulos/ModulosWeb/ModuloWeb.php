<?php
session_start();

include_once($_SESSION["directorio"].'/comun/log/log.php');
include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');


// Clase abstracta de módulo web
class ModuloWeb
{
    // Miembros de modulo web
    public $log;
	public $idiomas;

    public $id;
    public $nombre;


    // Funciones de modulo web


	function __construct($id, $nombre)
	{
        $this->log = dame_log();
		$this->idiomas = new Idiomas();

        $this->id = $id;
        $this->nombre = $nombre;
	}


    function dame_menu_secciones($parametros_extra)
	{
        $html = "
            <div id='cabecera-menu-secciones'>".$this->idiomas->_($this->nombre)."</div>
            <div id='contenido-menu-secciones'>
                <nav>";
        $secciones_menu = $this->dame_secciones_menu(NULL);
        foreach ($secciones_menu AS $seccion_menu)
        {
            $enlace_seccion_menu = $this->dame_enlace_seccion_menu($seccion_menu, $parametros_extra);
            $html .= "<p><a id='seccion-".$seccion_menu["id"]."' class='menu-secciones' href='".$enlace_seccion_menu."' style='display: none;'>".
                $seccion_menu["descripcion"]."</a></p>";
        }
        $html .= "<p><div id='margen-inferior-menu-secciones'</div></p>";
        $html .= "
                </nav>
            </div>";

        // Nota: Se devuelven también las secciones 'visibles'
        // (inicialmente se muestra el menú con las secciones ocultas y luego se muestran sólo las secciones visibles)
        $secciones_menu_visibles = $this->dame_secciones_menu($parametros_extra);

        // Se devuelve el resultado
        $res = array(
            "html" => $html,
            "secciones_menu" => $secciones_menu_visibles);
        return ($res);
	}


    function dame_secciones_menu($parametros_extra)
    {
        $secciones_usuario = $this->dame_secciones_usuario();
        $secciones_modulo = $this->dame_secciones($parametros_extra);

        $secciones_menu = array();
        foreach ($secciones_modulo AS $seccion_modulo)
        {
            if (($secciones_usuario === NULL) || (in_array($seccion_modulo, $secciones_usuario) == true))
            {
                $seccion_menu = array(
                    "id" => $seccion_modulo,
                    "descripcion" => $this->dame_descripcion_seccion($seccion_modulo));
                array_push($secciones_menu, $seccion_menu);
            }
        }
        return ($secciones_menu);
    }


    function dame_enlaces_secciones_menu($secciones_menu, $parametros_extra)
    {
        $enlaces_secciones_menu = array();
        foreach ($secciones_menu AS $seccion_menu)
        {
            $enlace_seccion_menu = $this->dame_enlace_seccion_menu($seccion_menu, $parametros_extra);
            array_push($enlaces_secciones_menu, $enlace_seccion_menu);
        }
        return ($enlaces_secciones_menu);
    }


    function dame_enlace_seccion_menu($seccion_menu, $parametros_extra)
    {
        $cadena_parametros_extra_enlace_seccion = $this->dame_cadena_parametros_extra_enlace_seccion($seccion_menu["id"], $parametros_extra);
        $enlace_seccion_menu .= "#".$this->id."#".$seccion_menu["id"].$cadena_parametros_extra_enlace_seccion;
        return ($enlace_seccion_menu);
    }


    function dame_secciones_usuario()
    {
        return (NULL);
    }


    // http://stackoverflow.com/questions/13174343/overriding-static-methods-in-php
    static function dame_seccion_defecto($secciones)
    {
        $secciones_defecto = static::dame_secciones(NULL);
        $seccion_defecto_encontrada = false;
        foreach ($secciones_defecto as $seccion_defecto)
        {
            if ($secciones === NULL)
            {
                $seccion_defecto_encontrada = true;
                break;
            }
            else
            {
                if (in_array($seccion_defecto, $secciones) == true)
                {
                    $seccion_defecto_encontrada = true;
                    break;
                }
            }
        }
        if ($seccion_defecto_encontrada == true)
        {
            return ($seccion_defecto);
        }
        else
        {
            throw new Exception("No hay ninguna sección asignada al usuario");
        }
    }


    //
    // Funciones virtuales a sobreescribir
    //


    static function dame_secciones($parametros_extra)
    {
        throw new Exception("Esta función no está definida en la clase base");
    }


    static function dame_descripcion_seccion($seccion)
    {
        throw new Exception("Esta función no está definida en la clase base");
    }


    static function dame_cadena_parametros_extra_enlace_seccion($seccion, $parametros_extra)
    {
        return ("");
    }


    function dame_contenido_seccion($seccion, $parametros_extra)
	{
        throw new Exception("Esta función no está definida en la clase base");
	}
}
?>
