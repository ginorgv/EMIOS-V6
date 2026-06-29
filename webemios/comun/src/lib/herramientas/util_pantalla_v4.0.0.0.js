// Función para obtener los píxeles del tamaño actual de letra
// (https://jsfiddle.net/SpYk3/fHgdv/)
(function($){$.getDefaultPx||($.extend({getDefaultPx:function(){var a=Array.prototype.slice.call(arguments),b=$("body"),d=1,e=!1,f=0,c=$("<div />").css({clear:"both",display:"block",height:"1em",position:"absolute",width:"1em","z-index":"-999"});for(x in a)switch(typeof a[x]){case "boolean":e=a[x];break;case "number":d=a[x];break;case "object":a[x]instanceof jQuery?a[x].length&&(b=a[x]):$(a[x]).prop("tagName")?b=$(a[x]):(a[x].element&&("string"==typeof a[x].element?$("body").find($(a[x].element)).length&&
(b=$(a[x].element)):"object"==typeof a[x].element&&(a[x].element instanceof jQuery?b=a[x].element:$(a[x].element).prop("tagName")&&(b=$(a[x].element)))),a[x].multiplier&&(d=parseFloat(a[x].multiplier)),a[x].asObject&&(e=a[x].asObject));break;case "string":$(a[x]).length&&(b=$(a[x]))}if(1==b.length){if("body"==b.prop("tagName").toLowerCase())return parseFloat($("body").css("font-size"))*d;b.append(c);a=c.height()*d;f=!e?a:{"0":a}}else f=!e?[]:{},b.each(function(a){$(this).append(c);f[a]=c.height()*
d});$(document).find(c).length&&c.remove();return f}}),$.fn.extend({getDefaultPx:function(a,b){return $.getDefaultPx($(this),a,b)}}))})(jQuery);


