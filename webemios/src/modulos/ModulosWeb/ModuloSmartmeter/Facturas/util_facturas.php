<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    //
    // Funciones de tipos de facturas
    //


    // Devuelve el nombre de la tabla de las validaciones de facturas
    function dame_nombre_tabla_validaciones_facturas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($nombre_tabla);
    }


    //
    // Funciones auxiliares
    //


    function dame_administracion_validaciones_facturas()
    {
        $administracion_validaciones_facturas = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR);
        return ($administracion_validaciones_facturas);
    }
?>
