<?php
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    if (FICHEROS_WEB_CONCATENADOS == true)
    {
        ?>
        <script type="text/javascript" src="./js/web_rsc_v5.4.0.0_R2.js"></script>
        <?php
    }
    else
    {
        ?>
        <script type="text/javascript" src="./rsc/lib/autosize/dist/autosize.js"></script>

        <script type="text/javascript" src="./rsc/lib/canvg/StackBlur.js"></script>
        <script type="text/javascript" src="./rsc/lib/canvg/rgbcolor.js"></script>
        <script type="text/javascript" src="./rsc/lib/canvg/canvg.js"></script>

        <script type="text/javascript" src="./rsc/lib/chosen/chosen.jquery_v3.2.0.0.js"></script>

        <script type="text/javascript" src="./rsc/lib/d3js/d3.js"></script>
        <script type="text/javascript" src="./rsc/lib/d3js/plugins/heatmap/heatmap_v5.4.0.0_R2.js"></script>
        <script type="text/javascript" src="./rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/d3js/plugins/windhistory/windhistory_v5.2.0.0.js"></script>

        <script type="text/javascript" src="./rsc/lib/dom-to-image/dom-to-image.js"></script>
        <script type="text/javascript" src="./rsc/lib/html2canvas/html2canvas.js"></script>

        <script type="text/javascript" src="./rsc/lib/jqplot/jquery.jqplot_v4.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.highlighter_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.cursor_v5.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.dateAxisRenderer_v3.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.canvasAxisTickRenderer_v5.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.pointLabels.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.canvasOverlay_v5.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.barRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.meterGaugeRenderer.min.js"></script>
        <script type="text/javascript" src="./rsc/lib/jqplot/plugins/jqplot.pieRenderer_v3.2.0.0.js"></script>

        <script type="text/javascript" src="./rsc/lib/justgage/raphael-2.1.4_v5.0.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/justgage/justgage_v5.0.0.0.js"></script>

        <script type="text/javascript" src="./rsc/lib/jquery.multiselect2side/js/jquery.multiselect2side_v4.0.0.0.js" ></script>

        <script type="text/javascript" src="./rsc/lib/leaflet/dist/leaflet-src_v5.2.0.0.js"></script>
        <script type="text/javascript" src="./rsc/lib/leaflet/plugins/leaflet-providers.js"></script>
        <script type="text/javascript" src="./rsc/lib/leaflet/plugins/markercluster/leaflet.markercluster.js"></script>
        <script type="text/javascript" src="./rsc/lib/leaflet/plugins/heatmap/heatmap.js"></script>
        <script type="text/javascript" src="./rsc/lib/leaflet/plugins/heatmap/leaflet-heatmap.js"></script>

        <script type="text/javascript" src="./rsc/lib/SVG.toDataURL/base64.js"></script>
        <script type="text/javascript" src="./rsc/lib/SVG.toDataURL/svg_todataurl.js"></script>
        <?php
    }
?>
