<?php
	session_start();


    function dame_icono_fila_con_errores()
	{
        $idiomas = new Idiomas();

        $icono_fila_con_errores = "<i class='icon-warning-sign color-rojo'>".
            "<texto class='elemento-oculto'>".htmlspecialchars($idiomas->_("fila con errores"), ENT_QUOTES)."</texto></i>";
        return ($icono_fila_con_errores);
    }


    function dame_icono_dato_erroneo()
	{
        $idiomas = new Idiomas();

        $icono_dato_erroneo = "<i class='icon-question-sign color-rojo'>".
            "<texto class='elemento-oculto'>".htmlspecialchars($idiomas->_("dato erróneo"), ENT_QUOTES)."</texto></i>";
        return ($icono_dato_erroneo);
    }
?>
