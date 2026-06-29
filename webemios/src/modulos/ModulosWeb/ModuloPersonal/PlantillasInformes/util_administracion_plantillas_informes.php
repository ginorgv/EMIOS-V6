<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    function dame_lista_tipos_elementos($tipo)
    {
        $idiomas = new Idiomas();
        $lista_tipos_elementos = "";
        $tipos_elemento = array(
            array(TIPO_ELEMENTO_PLANTILLA_INFORME_XXX, $idiomas->_("XXX"))
        );
        foreach ($tipos_elemento as $tipo_elemento)
        {
            $lista_tipos_elementos .= "<option value='".$tipo_elemento[0]."'";
			if ($tipo_elemento[0] == $tipo)
			{
				$lista_tipos_elementos .= " selected";
			}
			$lista_tipos_elementos .= ">".$tipo_elemento[1]."</option>";
        }
        return ($lista_tipos_elementos);
    }
?>