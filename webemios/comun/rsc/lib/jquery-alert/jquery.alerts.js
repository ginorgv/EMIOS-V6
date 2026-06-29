// jQuery Alert Dialogs Plugin
//
// Usage:
//		jInfo( message, [title, callback] )
//		jHelp( message, [title, callback] )
//		jAlert( message, [title, callback] )
//		jConfirm( message, [title, callback] )
//		jPrompt( message, [value, title, callback] )
//
(function($) {
	$.alerts = {
		// These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time

		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .5,                 // transparency level of overlay (50%)
		overlayColor: '#000',               // base color of overlay (black)
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		okButton: "Ok",        				// text for the Ok button
		cancelButton: "Cancel", 			// text for the Cancel button
        yesButton: "Yes",                   // text for the Yes button
        noButton: "No",                     // text for the No button


		// Public methods

		info: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Información");
			}

            $.alerts.okButton = TLNT.Idiomas._("Aceptar");
			$.alerts._show(title, message, null, 'jInfo', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de información por defecto
			$("#popup_title").css({'background-color': '#5486b7'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jInfo_popup');
		},

        help: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Ayuda");
			}

            $.alerts.okButton = TLNT.Idiomas._("Aceptar");
			$.alerts._show(title, message, null, 'jHelp', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de ayuda por defecto
			$("#popup_title").css({'background-color': '#2F8AD3'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jHelp_popup');


			// Configuración específica para la ayuda de fórmulas de las tarifas de cierre
			if (title == "Ayuda de fórmulas") {
				title = TLNT.Idiomas._(title);
				$("#popup_container").addClass('jHelp_tarifa_cierre');
				$("#popup_content").addClass('jHelp_tarifa_cierre_content');
				$("#popup_container").css("top", "5%");
			}

		},

		alert: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Aviso");
			}
            $.alerts.okButton = TLNT.Idiomas._("Aceptar");
			$.alerts._show(title, message, null, 'jAlert', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de aviso
			$("#popup_title").css({'background-color': '#B94A48'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jAlert_popup');
		},

        confirmAcceptCancelInfo: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Confirmación");
			}
            $.alerts.okButton = TLNT.Idiomas._("Aceptar");
			$.alerts.cancelButton = TLNT.Idiomas._("Cancelar");
			$.alerts._show(title, message, null, 'jConfirmInfo', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de confirmación
			$("#popup_title").css({'background-color': '#5486b7'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jConfirmInfo_popup');
		},

		confirmYesNoInfo: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Confirmación");
			}
            $.alerts.okButton = TLNT.Idiomas._("Sí");
			$.alerts.cancelButton = TLNT.Idiomas._("No");
			$.alerts._show(title, message, null, 'jConfirmInfo', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de confirmación
			$("#popup_title").css({'background-color': '#5486b7'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jConfirmInfo_popup');
		},

		confirmAcceptCancelAlert: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Confirmación");
			}
            $.alerts.okButton = TLNT.Idiomas._("Aceptar");
			$.alerts.cancelButton = TLNT.Idiomas._("Cancelar");
			$.alerts._show(title, message, null, 'jConfirmAlert', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de confirmación
			$("#popup_title").css({'background-color': '#B94A48'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jConfirmAlert_popup');
		},

        confirmYesNoAlert: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Confirmación");
			}
            $.alerts.okButton = TLNT.Idiomas._("Sí");
			$.alerts.cancelButton = TLNT.Idiomas._("No");
			$.alerts._show(title, message, null, 'jConfirmAlert', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de confirmación
			$("#popup_title").css({'background-color': '#B94A48'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jConfirmAlert_popup');
		},

		confirm: function(message, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Confirmación");
			}
			$.alerts._show(title, message, null, 'jConfirm', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de confirmación
			$("#popup_title").css({'background-color': '#B94A48'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jConfirm_popup');
		},

		prompt: function(message, value, title, callback) {
			if (title == null) {
				title = TLNT.Idiomas._("Mensaje");
			}
			$.alerts._show(title, message, value, 'jPrompt', function(result) {
				if (callback) callback(result);
			});

			// Estilo de ventana de 'prompt'
			$("#popup_title").css({'background-color': '#B94A48'});

            // Estilos para definir en CSS externos
            $("#popup_container").addClass('jQuery_alert_popup');
            $("#popup_container").addClass('jPrompt_popup');
		},


		// Private methods

		_show: function(title, msg, value, type, callback) {
			$.alerts._hide();
			$.alerts._overlay('show');

            icon = "";
			switch (type) {
				case 'jInfo':
					icon = 'icon-info-sign';
					break;
                case 'jHelp':
					icon = 'icon-question-sign';
					break;
				case 'jAlert':
				case 'jConfirmAlert':
					icon = 'icon-warning-sign';
					break;
				case 'jConfirmInfo':
					icon = 'icon-question-sign';
					break;
				case 'jPrompt':
                    icon = 'icon-question-sign';
					break;
			};

			$("BODY").append(
				'<div id="popup_container">' +
					'<h1 id="popup_title"></h1>' +
					'<div id="popup_content" class="row-fluid" style="margin-top: 0.5em">' +
						'<i class="span1 ' + icon + ' icon-2x color-gris" style="margin-left: 0.5em"></i>' +
						'<div id="popup_message" class="span10" style="text-align: center"></div>' +
					'</div>' +
				'</div>');

			// IE6 Fix
			//var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed';
			var pos = 'fixed';

			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});

			$("#popup_title").text(title);

			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			$("#popup_message").html($("#popup_message").text().replace(/\n/g, '<br/>'));

			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth(),
				maxWidth: $("#popup_container").outerWidth()
			});

			$.alerts._reposition();
			$.alerts._maintainPosition(true);

			switch (type) {
				case 'jInfo':
                    $("#popup_content").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-info" type="button" value="' + $.alerts.okButton + '" id="popup_ok" /></div>');
                    $("#popup_ok").click(function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress(function(e) {
						if (e.keyCode == 13 || e.keyCode == 27) $("#popup_ok").trigger('click');
					});
					break;
                case 'jHelp':
                    $("#popup_content").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-info" type="button" value="' + $.alerts.okButton + '" id="popup_ok" /></div>');
                    $("#popup_ok").click(function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress(function(e) {
						if (e.keyCode == 13 || e.keyCode == 27) $("#popup_ok").trigger('click');
					});
					break;
				case 'jAlert':
                    $("#popup_content").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-danger" type="button" value="' + $.alerts.okButton + '" id="popup_ok" /></div>');
                    $("#popup_ok").click(function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress(function(e) {
						if (e.keyCode == 13 || e.keyCode == 27) $("#popup_ok").trigger('click');
					});
					break;
				case 'jConfirmInfo':
					$("#popup_content").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-info" type="button" value="' + $.alerts.okButton + '" id="popup_ok"/> <input class="btn" type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click(function() {
						$.alerts._hide();
						if (callback) callback(true);
					});
					$("#popup_cancel").click(function() {
						$.alerts._hide();
						if (callback) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress(function(e) {
						if (e.keyCode == 13) $("#popup_ok").trigger('click');
						if (e.keyCode == 27) $("#popup_cancel").trigger('click');
					});
					break;
				case 'jConfirmAlert':
					$("#popup_content").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-danger" type="button" value="' + $.alerts.okButton + '" id="popup_ok"/> <input class="btn" type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click(function() {
						$.alerts._hide();
						if (callback) callback(true);
					});
					$("#popup_cancel").click(function() {
						$.alerts._hide();
						if (callback) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress(function(e) {
						if (e.keyCode == 13) $("#popup_ok").trigger('click');
						if (e.keyCode == 27) $("#popup_cancel").trigger('click');
					});
					break;
				case 'jPrompt':
                    promptInputType = 'text';
                    // TODO: Para tipos de texto tipo 'password'
                    //promptInputType = 'password';

					$("#popup_content").after(
						'<div id="popup_prompt" class="row-fluid" style="margin-top: 0.5em">' +
							'<input type="' + promptInputType + '" class="offset2 span9" id="popup_prompt_input">' +
						'</div>');

					$("#popup_prompt").after('<div id="popup_panel" style="margin-bottom: 0.5em"><input class="btn btn-danger" type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input class="btn" type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');

					$("#popup_ok").click(function() {
						var val = $("#popup_prompt_input").val();
						$.alerts._hide();
						if (callback) callback(val);
					});
					$("#popup_cancel").click(function() {
						$.alerts._hide();
						if (callback) callback(null);
					});
					$("#popup_prompt_input, #popup_ok, #popup_cancel").keypress( function(e) {
						if (e.keyCode == 13) $("#popup_ok").trigger('click');
						if (e.keyCode == 27) $("#popup_cancel").trigger('click');
					});
					if (value) {
						$("#popup_prompt_input").val(value);
					}
					$("#popup_prompt").focus().select();
					break;
			}

			// Make draggable
			if ($.alerts.draggable) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				}
				catch(e) {
					/* requires jQuery UI draggables */
				}
			}
		},

		_hide: function() {
			$("#popup_container").remove();
			$("#popup_overlay").unbind('click');
			$.alerts._overlay('hide');
			// Fallback: eliminar cualquier overlay residual
			var overlay = document.getElementById('popup_overlay');
			while (overlay) {
				overlay.parentNode.removeChild(overlay);
				overlay = document.getElementById('popup_overlay');
			}
			$.alerts._maintainPosition(false);
		},

		_overlay: function(status) {
			switch (status) {
				case 'show':
					$.alerts._overlay('hide');
					$("BODY").append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'fixed',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: '100%',
						background: $.alerts.overlayColor,
						opacity: $.alerts.overlayOpacity
					});
					// Si se hace click en el overlay y no se cierra, forzar cierre
					$("#popup_overlay").click(function(e) {
						if ($("#popup_container").length > 0) {
							$('#popup_ok').trigger('click');
						} else {
							$("#popup_overlay").remove();
						}
					});
					break;
				case 'hide':
					$("#popup_overlay").remove();
					break;
			}
		},

		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
			if (top < 0) top = 0;
			if (left < 0) left = 0;

			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
		},

		_maintainPosition: function(status) {
			if( $.alerts.repositionOnResize ) {
				switch (status) {
					case true:
						$(window).bind('resize', $.alerts._reposition);
						break;
					case false:
						$(window).unbind('resize', $.alerts._reposition);
						break;
				}
			}
		}
	};

	// Shortcut functions
	jInfo = function(message, title, callback) {
		$.alerts.info(message, title, callback);
	};

    jHelp = function(message, title, callback) {
		$.alerts.help(message, title, callback);
	};

	jAlert = function(message, title, callback) {
		$.alerts.alert(message, title, callback);
	};

	jConfirmAcceptCancelInfo = function(message, title, callback) {
		$.alerts.confirmAcceptCancelInfo(message, title, callback);
	};

    jConfirmYesNoInfo = function(message, title, callback) {
		$.alerts.confirmYesNoInfo(message, title, callback);
	};

	jConfirmAcceptCancelAlert = function(message, title, callback) {
		$.alerts.confirmAcceptCancelAlert(message, title, callback);
	};

    jConfirmYesNoAlert = function(message, title, callback) {
		$.alerts.confirmYesNoAlert(message, title, callback);
	};

	jPrompt = function(message, value, title, callback) {
		$.alerts.prompt(message, value, title, callback);
	};
}) (jQuery);
