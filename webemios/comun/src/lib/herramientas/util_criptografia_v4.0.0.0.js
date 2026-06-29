var codifica_caracter_peticion_php = function(caracter) {
    var caracter_codificado = "";
    if (caracter == 0) {
        caracter_codificado = '`';
    }
    else {
        caracter_codificado = String.fromCharCode(0x20 + caracter);
    }
    return (caracter_codificado);
};

var codifica_cadena_peticion_php = function(cadena) {
	cadena = unescape(encodeURIComponent(cadena));
	var contador = 0;
	var cadena_codificada = [];
	for (;;) {
		var longitud_cadena = cadena.length - contador;
		if (longitud_cadena > 45) {
			longitud_cadena = 45;
		}
		cadena_codificada.push(codifica_caracter_peticion_php(longitud_cadena));
		var subcadena = cadena.substring(contador, contador + longitud_cadena);
		subcadena += String.fromCharCode(0) + String.fromCharCode(0);
		for (var i = 0; i < longitud_cadena; i += 3) {
            cadena_codificada.push(codifica_caracter_peticion_php(subcadena.charCodeAt(i) >> 2));
			cadena_codificada.push(codifica_caracter_peticion_php(((subcadena.charCodeAt(i) & 3) << 4) + (subcadena.charCodeAt(i + 1) >> 4)));
			cadena_codificada.push(codifica_caracter_peticion_php(((subcadena.charCodeAt(i + 1) & 15) << 2) + (subcadena.charCodeAt(i + 2) >> 6)));
			cadena_codificada.push(codifica_caracter_peticion_php(subcadena.charCodeAt(i + 2) & 0x3f));
		}
		contador += longitud_cadena;
		if (contador == cadena.length) {
			break;
        }
		cadena_codificada.push('\n');
	}
    cadena_codificada = cadena_codificada.join('');
	return (cadena_codificada);
};




