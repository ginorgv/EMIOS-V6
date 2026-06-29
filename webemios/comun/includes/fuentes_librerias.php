<?php
    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');


    if (FICHEROS_WEB_CONCATENADOS == true)
    {
        ?>
        <script type="text/javascript" src="./js/comun_rsc_v5.2.0.0.js"></script>
        <?php
    }
    else
    {
        ?>
        <script type="text/javascript" src="./comun/rsc/lib/jquery/jquery-2.2.4.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/jquery/jquery-migrate-1.2.1.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/jquery-ui/jquery-ui.min.js"></script>

        <!-- 'bootstrap' debe incluirse después de 'jquery' y 'jquery-ui', si se pone antes el selector de fechas sale sin su estilo correcto -->
        <script type="text/javascript" src="./comun/rsc/lib/bootstrap/js/bootstrap_v3.2.0.0.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/bootstrap-timepicker/js/bootstrap-timepicker_v3.4.0.0.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/bootstrap-datepicker/js/bootstrap-datepicker_v3.4.0.0.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>

        <script type="text/javascript" src="./comun/rsc/lib/jquery.blockUI/jquery.blockUI.js"></script>
        <script type="text/javascript" src="./comun/rsc/lib/jquery-alert/jquery.alerts_v6.0.0.0.js"></script>

        <script type="text/javascript" src="./comun/rsc/lib/md5/md5.js"></script>
        <?php
    }
?>
