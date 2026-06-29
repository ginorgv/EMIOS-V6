// http://stackoverflow.com/questions/11128939/feature-detection-of-foreignobject-in-svg
function dame_libreria_dom_to_image_soportada() {
    if (libreria_dom_to_image_soportada == null) {
        libreria_dom_to_image_soportada = document.implementation.hasFeature("www.http://w3.org/TR/SVG11/feature#Extensibility", "1.1");
        if (libreria_dom_to_image_soportada == true) {
            // Nota: Aunque se detecta el 'feature', 'domtoimage' no funciona en el navegador 'Edge' (por ahora, ir comprobando en sucesivas versiones)
            var navegador_edge = /Edge/.test(navigator.userAgent);
            if (navegador_edge == true) {
                libreria_dom_to_image_soportada = false;
            }
        }
    }
    return (libreria_dom_to_image_soportada);
}
