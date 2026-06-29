<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

	include_once($_SESSION["directorio"].'/comun/rsc/lib/log4php/src/main/php/Logger.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	function dame_log($nombre_fichero_log = NOMBRE_FICHERO_LOG)
	{
        // Se utiliza el último fichero de log (así se utiliza el mismo para todo el script PHP)
        if (!isset($GLOBALS["nombre_fichero_log"]))
        {
            $GLOBALS["nombre_fichero_log"] = $nombre_fichero_log;
        }
        else
        {
            $nombre_fichero_log = $GLOBALS["nombre_fichero_log"];
        }

        // Web: http://logging.apache.org/log4php/docs/appenders.html

        // Se carga el fichero de configuración y se recupera el logger
		Logger::configure($_SESSION["directorio"].'/comun/log/config.xml');
		$logger = Logger::getRootLogger();

        // Se establece el nombre del fichero de log ('standard')
        $standard_appender = $logger->getAppender('standard_appender');
        if ($standard_appender != NULL)
        {
            if (get_class($standard_appender) == "LoggerAppenderRollingFile")
            {
                $standard_appender->setFile($_SESSION["directorio"]."/log/".$nombre_fichero_log.".log", true);
                $standard_appender->setMaxFileSize(TAMANYO_MAXIMO_FICHERO_LOG);
                $standard_appender->setMaxBackupIndex(NUMERO_FICHEROS_LOG);

                // Nota: Modificar aquí si se quiere el nivel de log para pruebas
                // - 'DEBUG'
                // - 'INFO'
                // $nivel_log = "DEBUG";
                $nivel_log = NIVEL_LOG;
                $standard_appender->setThreshold($nivel_log);
            }
        }

        // Se establece el nombre del fichero de log ('warning')
        $warning_appender = $logger->getAppender('warning_appender');
        if ($warning_appender != NULL)
        {
            if (get_class($warning_appender) == "LoggerAppenderRollingFile")
            {
                $warning_appender->setFile($_SESSION["directorio"]."/log/".'warning_'.$nombre_fichero_log.".log", true);
                $warning_appender->setMaxFileSize(TAMANYO_MAXIMO_FICHERO_LOG);
                $warning_appender->setMaxBackupIndex(NUMERO_FICHEROS_LOG);

                $nivel_log = "WARN";
                $warning_appender->setThreshold($nivel_log);
            }
        }

		return ($logger);
	}
?>
