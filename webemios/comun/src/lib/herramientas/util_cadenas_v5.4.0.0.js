// http://forwebonly.com/capitalize-the-first-letter-of-a-string-in-javascript-the-fast-way/
String.prototype.capitalize = function() {
    return (this.charAt(0).toUpperCase() + this.slice(1));
};


String.prototype.uncapitalize = function() {
    return (this.charAt(0).toLowerCase() + this.slice(1));
};


// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
if (!String.prototype.endsWith) {
	String.prototype.endsWith = function(search, this_len) {
		if (this_len === undefined || this_len > this.length) {
			this_len = this.length;
		}
		return this.substring(this_len - search.length, this_len) === search;
	};
}


// http://stackoverflow.com/questions/1144783/replacing-all-occurrences-of-a-string-in-javascript
function escapeRegExp(string) {
    return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}


function replaceAll(string, find, replace) {
    return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}


// http://stackoverflow.com/questions/1787322/htmlspecialchars-equivalent-in-javascript
function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


function escapeHtmlXml(text) {
    var map = {
      '&': '%amp;',
      '<': '%lt;',
      '>': '%gt;',
      '"': '%quot;',
      "'": '%#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


function unescapeHtml(text) {
    return String(text)
        .replace(/&amp;/g, '&')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");
}


// https://gist.github.com/oxguy3/18d92821fe931945c86f
function unescapeHtmlXml(text) {
    return String(text)
        .replace(/%amp;/g, '&')
        .replace(/%lt;/g, '<')
        .replace(/%gt;/g, '>')
        .replace(/%quot;/g, '"')
        .replace(/%#039;/g, "'");
}


// http://stackoverflow.com/questions/30867172/code-not-running-in-ie-11-works-fine-in-chrome
if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position) {
        position = position || 0;
        return this.indexOf(searchString, position) === position;
    };
}


// Elimina los espacios en blanco
function elimina_espacios(cadena) {
    return String(cadena)
        .replace(/ /g, "");
}




