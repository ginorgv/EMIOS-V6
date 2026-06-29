<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/modulos/ModulosWeb/ModuloWeb.php');


	class ModuloWebEmios extends ModuloWeb
	{
        //
        // Funciones de secciones
        //


        function dame_secciones_usuario()
        {
            $secciones_usuario = $_SESSION["modulos"][$this->id]["secciones"];
            return ($secciones_usuario);
        }


        static function dame_lista_secciones($secciones, $secciones_seleccionadas)
        {
            $secciones_modulo = static::dame_secciones();

            $info_secciones = array();
            foreach ($secciones_modulo AS $seccion_modulo)
            {
                if (($secciones === NULL) || (in_array($seccion_modulo, $secciones) == true))
                {
                    array_push($info_secciones, array(
                        "id" => $seccion_modulo,
                        "nombre" => static::dame_descripcion_seccion($seccion_modulo)));
                }
            }

            $id_ordenacion_lista = 1;
            foreach ($info_secciones as $info_seccion)
            {
                $lista .= "<option value='".$info_seccion['id']."' sort_id='".$id_ordenacion_lista."'";
                if (in_array($info_seccion['id'], $secciones_seleccionadas) == true)
                {
                    $lista .= " selected";
                }
                $lista .= ">".$info_seccion['nombre']."</option>";
                $id_ordenacion_lista += 1;
            }

            return ($lista);
        }
	}
?>
