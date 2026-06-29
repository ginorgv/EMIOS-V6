<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatos.php');

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ConexionBaseDatos.php');
include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


class BaseDatosRed extends BaseDatos
{
    // Miembro estático de la clase
    static public $base_datos = NULL;


    // Devuelve la base de datos (si está creada y si no la crea)
    static function dame_base_datos()
    {
        if (BaseDatosRed::$base_datos === NULL)
        {
            BaseDatosRed::$base_datos = new BaseDatosRed();
        }
        return (BaseDatosRed::$base_datos);
    }


    // Constructor
	function __construct()
	{
        $this->tipo = TIPO_BASE_DATOS_RED;

        // Datos de conexión de base de datos de red (fichero .ini)
        $entradas_ini = dame_entradas_ini();
        $params = array();
        $params["direccion_ip"] = $entradas_ini[INI_IP_BASE_DATOS_RED];
        $params["puerto"] = $entradas_ini[INI_PUERTO_BASE_DATOS_RED];
        $params["base_datos"] = $entradas_ini[INI_NOMBRE_BASE_DATOS_RED];
        $params["usuario"] = $entradas_ini[INI_USUARIO_BASE_DATOS_RED];
        $params["contrasenya"] = $entradas_ini[INI_CONTRASENYA_BASE_DATOS_RED];
        $params["version"] = VERSION_BASE_DATOS_RED;

        $this->conexion_base_datos = ConexionBaseDatos::crea_conexion_base_datos("Red", TIPO_BASE_DATOS_MYSQL, $params, false);
        $this->conecta();
	}


    // Destructor
	function __destruct()
	{
        $this->desconecta();
	}
}
?>