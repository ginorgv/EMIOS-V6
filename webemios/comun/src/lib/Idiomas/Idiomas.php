<?php
	class Idiomas
	{
		public $idioma_defecto = "es_ES";
		public $idioma;


        // Devuelve la cadena traducida al idioma correspondiente (o la cadena original si no existe la traducción)
		function _($cadena_original)
		{
			session_start();

			$idioma = $_SESSION["idioma"];
			if ($idioma == '')
			{
				$idioma = $this->idioma_defecto;
			}

            // Si el idioma es el idioma por defecto, la cadena traducida es la misma que la original
            if ($idioma == $this->idioma_defecto)
            {
                $cadena_traducida = $cadena_original;
            }
            else
            {
                // Se recupera la cadena traducida del idioma actual:
                // - Si no existe se añade un prefijo a la cadena para indicar que no existe la traducción
                $cadena_traducida = $_SESSION["cadenas_idiomas"]->{$cadena_original}->{$idioma};
                if ($cadena_traducida == '')
                {
                    // Se añade un prefijo a la cadena para indicar que no existe la traducción
                    if ($idioma != $this->idioma_defecto)
                    {
                        $cadena_traducida = "_".$cadena_original;
                    }
                }
            }

			return (htmlspecialchars($cadena_traducida, ENT_QUOTES));
		}
	}
?>