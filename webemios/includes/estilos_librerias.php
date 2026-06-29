<?php
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    if (FICHEROS_WEB_CONCATENADOS == true)
    {
        ?>
        <link type="text/css" rel="stylesheet" href="./css/web_rsc.php"/>

        <link rel="stylesheet" href="./css/web_rsc_v5.2.0.0.css" />
        <?php
    }
    else
    {
        ?>
        <link type="text/css" rel="stylesheet" href="./rsc/lib/d3js/plugins/heatmap/heatmap.php"/>
        <link type="text/css" rel="stylesheet" href="./rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol.php"/>
        <link type="text/css" rel="stylesheet" href="./rsc/lib/d3js/plugins/windhistory/windhistory.php" />

        <link rel="stylesheet" href="./rsc/lib/chosen/chosen.css" />
        <link rel="stylesheet" href="./rsc/lib/jqplot/jquery.jqplot.css" />
        <link rel="stylesheet" href="./rsc/lib/jquery.multiselect2side/css/jquery.multiselect2side.css" />
        <link rel="stylesheet" href="./rsc/lib/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="./rsc/lib/leaflet/plugins/markercluster/MarkerCluster.css" />
        <link rel="stylesheet" href="./rsc/lib/leaflet/plugins/markercluster/MarkerCluster.Default.css" />
        <?php
    }
?>
