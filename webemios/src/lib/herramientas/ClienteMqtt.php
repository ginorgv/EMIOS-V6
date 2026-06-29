<?php
    session_start();

	include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/rsc/lib/mqtt/phpMQTT.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	class ClienteMqtt
	{
        // Miembros de ClienteMqtt

        
        public $log;
		public $php_mqtt;


        // Métodos


		// Constructor
		function __construct($ip, $puerto, $id_cliente)
		{
			$this->log = dame_log();
            $this->log->debug("[".$_SESSION["id_usuario"]."] ".
				"IP: '".$ip.
				"', puerto: '".$puerto.
				"', id_cliente: '".$id_cliente."'");

			$this->php_mqtt = new phpMQTT($ip, $puerto, $id_cliente);
		}


        // Conecta
        function conecta()
        {
            $res = $this->php_mqtt->connect();
            return ($res);
        }

        function conecta_auth($user, $password)
        {
            $res = $this->php_mqtt->connect(true, NULL, $user, $password);
            return ($res);
        }


        // Desconecta
        function desconecta()
        {
            $res = $this->php_mqtt->close();
            return ($res);
        }


        // Publica
        function publica($asunto, $datos, $qos)
        {
            $this->log->debug("[".$_SESSION["id_usuario"]."] ".
				"Asunto: '".$asunto.
				"', datos: '".$datos.
				"', qos: '".$qos."'");

            // Nota: Se desconecta y conecta antes de enviar un mensaje porque si se envían varios mensajes seguidos en ocasiones sólo llega el primero
            $this->desconecta();
            $this->conecta();

            $this->php_mqtt->publish($asunto, $datos, $qos);
        }
	}
?>
