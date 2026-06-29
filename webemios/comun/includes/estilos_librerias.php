<?php
    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');


    if (FICHEROS_WEB_CONCATENADOS == true)
    {
        ?>
        <link rel="stylesheet" href="./comun/rsc/lib/font-awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="./css/comun_rsc_v5.2.0.0.css" />
        <?php
    }
    else
    {
        ?>
        <link rel="stylesheet" href="./comun/rsc/lib/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="./comun/rsc/lib/bootstrap/css/bootstrap-colored.css" />
        <link rel="stylesheet" href="./comun/rsc/lib/bootstrap-datepicker/css/datepicker.css" />
        <link rel="stylesheet" href="./comun/rsc/lib/bootstrap-timepicker/css/bootstrap-timepicker.css" />
        <link rel="stylesheet" href="./comun/rsc/lib/font-awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="./comun/rsc/lib/jquery-alert/jquery.alerts.css" />
        <?php
    }
?>
