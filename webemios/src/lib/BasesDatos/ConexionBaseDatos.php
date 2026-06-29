<?php
session_start();

include_once($_SESSION["directorio"].'/comun/log/log.php');

// Includes de las clases derivadas de ConexionBaseDatos
include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ConexionBaseDatosMySql.php');


// Tipos de bases de datos
define("TIPO_BASE_DATOS_MYSQL", "MYSQL");


class ConexionBaseDatos
{
    // Funciones estáticas de base datos


    // Crea una conexión a base de datos del tipo especificado
    static function crea_conexion_base_datos($id, $tipo, $params, $comprobar_limite_numero_filas_consultas)
    {
        switch ($tipo)
        {
            case TIPO_BASE_DATOS_MYSQL:
            {
                return (new ConexionBaseDatosMysql($id, $params, $comprobar_limite_numero_filas_consultas));
            }
            default:
            {
				throw new Exception("Tipo de base de datos desconocido: '".$tipo."'");
            }
		}
	}


    // Miembros de conexión de base de datos
    public $id;
    public $params;

    public $conectado;
    public $conexion;

    public $comprobar_limite_numero_filas_consultas;


    // Funciones de conexión de base de datos


    // Constructor
    function __construct($id, $params, $comprobar_limite_numero_filas_consultas)
	{
        $this->id = $id;
        $this->params = $params;
        $this->comprobar_limite_numero_filas_consultas = $comprobar_limite_numero_filas_consultas;

        $this->conectado = false;
        $this->conexion = NULL;
	}


    // Inicia la conexión con la base de datos (comprueba la versión de la base de datos)
    function conecta()
    {
    }


    // Finaliza la conexión con la base de datos
    function desconecta()
    {
    }


    // Comprueba la versión de la base de datos
    function comprueba_version()
    {
    }


    // Ejecuta una operación en la base de datos
    function ejecuta_operacion($operacion)
	{
        return (false);
	}


    // Devuelve el id autoincremental de la última inserción
    function dame_id_autoincremental_ultima_insercion()
    {
        return (false);
    }


    // Devuelve el número de filas afectadas de la última operación
    function dame_numero_filas_afectadas_ultima_operacion()
    {
        return (false);
    }


    // Ejecuta una consulta en la base de datos
    function ejecuta_consulta($consulta)
	{
       return (false);
	}


    // Convierte los caracteres especiales de una cadena
	function _($cadena)
	{
		return ($cadena);
	}
}
?>