function dame_texto_pie_pagina(texto_adicional) {
    var texto_fijo = $(".texto-fijo-pie-pagina").text();
    var texto_pie_pagina = "<a class='texto-fijo-pie-pagina elemento-no-seleccionable'>" + texto_fijo + "</a>";
    if ((texto_adicional != null) && (texto_adicional != "")) {
        texto_pie_pagina += "<span class='texto-adicional-pie-pagina elemento-no-seleccionable'> [" + texto_adicional + "]</span>";
    }
    return (texto_pie_pagina);
}


function actualiza_texto_pie_pagina(texto_adicional) {
    var texto_pie_pagina = dame_texto_pie_pagina(texto_adicional);
    $('#texto-pie-pagina').html(texto_pie_pagina);
}