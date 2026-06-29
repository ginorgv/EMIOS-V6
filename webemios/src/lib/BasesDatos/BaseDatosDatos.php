<?php
session_start();

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatos.php');

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ConexionBaseDatos.php');
include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


class BaseDatosDatos extends BaseDatos
{
    // Miembros estáticos de la clase
    static public $base_datos = NULL;


    // Devuelve la base de datos (si está creada y si no la crea)
    static function dame_base_datos()
    {
        if (BaseDatosDatos::$base_datos === NULL)
        {
            BaseDatosDatos::$base_datos = new BaseDatosDatos();
        }
        return (BaseDatosDatos::$base_datos);
    }


    // Constructor
    function __construct()
	{
        $this->tipo = TIPO_BASE_DATOS_DATOS;

        // Datos de conexión de base de datos de red (fichero .ini)
        $entradas_ini = dame_entradas_ini();
        $params = array();
        $params["direccion_ip"] = $entradas_ini[INI_IP_BASE_DATOS_DATOS];
        $params["puerto"] = $entradas_ini[INI_PUERTO_BASE_DATOS_DATOS];
        $params["base_datos"] = $entradas_ini[INI_NOMBRE_BASE_DATOS_DATOS];
        $params["usuario"] = $entradas_ini[INI_USUARIO_BASE_DATOS_DATOS];
        $params["contrasenya"] = $entradas_ini[INI_CONTRASENYA_BASE_DATOS_DATOS];
        $params["version"] = VERSION_BASE_DATOS_DATOS;

        $this->conexion_base_datos = ConexionBaseDatos::crea_conexion_base_datos("Datos", TIPO_BASE_DATOS_MYSQL, $params, true);
        $this->conecta();
	}


    // Destructor
	function __destruct()
	{
        $this->desconecta();
	}
}
?>