<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include_once($_SESSION["directorio"].'/comun/log/log.php');


class BaseDatos
{
    // Miembros de base de datos


    // Conexión con la base de datos
    public $conexion_base_datos;


	// Funciones de base de datos


    // Inicia la conexión con la base de datos (comprueba la versión de la base de datos)
    function conecta()
	{
        $log = dame_log();
        $log->debug("Tipo de base de datos: '".$this->tipo."'");

        $this->conexion_base_datos->conecta();
	}


    // Finaliza la conexión con la base de datos
    function desconecta()
	{
        $this->conexion_base_datos->desconecta();
	}


    // Ejecuta una operación en la base de datos
    function ejecuta_operacion($operacion)
	{
        return ($this->conexion_base_datos->ejecuta_operacion($operacion));
	}


    // Devuelve el id autoincremental de la última inserción
    function dame_id_autoincremental_ultima_insercion()
    {
        return ($this->conexion_base_datos->dame_id_autoincremental_ultima_insercion());
    }


    // Devuelve el número de filas afectadas de la última operación
    function dame_numero_filas_afectadas_ultima_operacion()
    {
        return ($this->conexion_base_datos->dame_numero_filas_afectadas_ultima_operacion());
    }


    // Ejecuta una consulta en la base de datos
    function ejecuta_consulta($consulta)
	{
        return ($this->conexion_base_datos->ejecuta_consulta($consulta));
	}


	// Convierte los caracteres especiales de una cadena
	function _($cadena)
	{
		return ($this->conexion_base_datos->_($cadena));
	}
}
?>