/**
 * Add a URL parameter (or modify if already exists)
 * @param {string} url
 * @param {string} param the key to set
 * @param {string} value
 */
var spcc_update_query_arg = function (url, param, value) {
	param = encodeURIComponent(param);
	var r = "([&?]|&amp;)" + param + "\\b(?:=(?:[^&#]*))*";
	var a = document.createElement('a');
	var regex = new RegExp(r);
	var str = param + (value ? "=" + encodeURIComponent(value) : "");
	a.href = url;
	var q = a.search.replace(regex, "$1" + str);
	if (q === a.search) {
		a.search += (a.search ? "&" : "") + str;
	} else {
		a.search = q;
	}
	return a.href;
};

/**
 * Remove query string parameter
 *
 * @param url
 * @param parameter
 * @returns {string}
 */
function spcc_remove_query_arg(url, parameter) {
	var urlParts = url.split('?');

	if (urlParts.length >= 2) {
		// Get first part, and remove from array
		var urlBase = urlParts.shift();

		// Join it back up
		var queryString = urlParts.join('?');

		var prefix = encodeURIComponent(parameter) + '=';
		var parts = queryString.split(/[&;]/g);

		// Reverse iteration as may be destructive
		for (var i = parts.length; i-- > 0;) {
			// Idiom for string.startsWith
			if (parts[i].lastIndexOf(prefix, 0) !== -1) {
				parts.splice(i, 1);
			}
		}

		url = urlBase + '?' + parts.join('&');
	}

	return url;
}

/**
 * Update push state
 * @param params
 */
function spcc_update_pushstate(params) {

	console.log(params);

	var currentUrl = window.location.href;
	var parts = currentUrl.split('?');
	currentUrl = parts[0];
	for (var key in params) {
		if(!params[key] || params[key] === '') {
			currentUrl = spcc_remove_query_arg(currentUrl, key);
		} else {
			currentUrl = spcc_update_query_arg(currentUrl, key, params[key]);
		}
	}
	window.history.pushState({path: currentUrl}, '', currentUrl);
}

/**
 * Parses url
 * https://stpetecatalyst.com/test-cc/?search=&datefrom=&dateto=&filter=0&category=3618&view=list&sort_by=date?search=&datefrom=&dateto=&filter=0&category=3620&view=list&sort_by=date
 * @param url
 * @returns {Array}
 */
function spcc_parse_query(url) {

	var queryString;
	var parts = url.split('?');
	if (parts.length === 2) {
		queryString = parts[1];
	} else {
		return [];
	}
	var query = {};
	var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
	for (var i = 0; i < pairs.length; i++) {
		var pair = pairs[i].split('=');
		query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
	}
	return query;
}


/**
 * The events date filters functionality
 */
(function ($) {

	var dateFormat = "mm-dd-yy";

	window.init_spcc_date_picker = function () {
		if (!jQuery().datepicker) {
			return;
		}

		var from = $("#datefrom").datepicker({
				dateFormat: dateFormat,
				defaultDate: "+1w",
				changeMonth: true,
				prevText: '<i class="spcc-icon-angle-left"></i>',
				nextText: '<i class="spcc-icon-angle-right"></i>',
			}).on("change", function () {
				to.datepicker("option", "minDate", getDate(this));
			}),
			to = $("#dateto").datepicker({
				dateFormat: dateFormat,
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 1,
				prevText: '<i class="spcc-icon-angle-left"></i>',
				nextText: '<i class="spcc-icon-angle-right"></i>',
			}).on("change", function () {
				from.datepicker("option", "maxDate", getDate(this));
			});

		function getDate(element) {
			var date;
			try {
				date = $.datepicker.parseDate(dateFormat, element.value);
			} catch (error) {
				date = null;
			}

			return date;
		}
	};


	window.init_spcc_calendar = function () {
		if (!jQuery().datepicker) {
			return;
		}
		$('#calendar').datepicker({
			dateFormat: dateFormat,
			prevText: '<i class="spcc-icon-angle-left"></i>',
			nextText: '<i class="spcc-icon-angle-right"></i>',
			onSelect: function (date) {
				var $field = $('#datefrom');
				$field.val(date);
				$field.closest('form').submit();
			}
		});
	};


	window.init_spcc_scripts = function () {
		window.init_spcc_date_picker();
		window.init_spcc_calendar();
		$('.cc-events-container').find('br').remove();
	};


	window.init_spcc_scripts();

})(jQuery);


/**
 * The events Quick View functionality
 */
(function ($) {
	$(document).on('click', '.spcc-action-qw', function (e) {
		e.preventDefault();

		var eventID = $(this).data('id');

		var modalWrap = $(document).find('.spcc-quick-view');
		if (!modalWrap.length) {
			$('body').append('<div class="remodal spcc-quick-view"><button data-remodal-action="close" class="remodal-close"></button><div class="spcc-quick-view-body"></div></div>');
			modalWrap = $(document).find('.spcc-quick-view');
		}
		var modalInst = modalWrap.remodal();

		$.ajax({
			url: SPCC.ajax_url + '?action=spcc_render_quickview&event_id=' + eventID,
			type: 'GET',
			cache: false,
			beforeSend: function () {
				modalWrap.find('.spcc-quick-view-body').html('');
				modalWrap.LoadingOverlay('show');
				modalInst.open();
			},
			success: function (response) {
				modalWrap.find('.spcc-quick-view-body').html(response);
				modalWrap.LoadingOverlay('hide');
			},
		});

	});
})(jQuery);


/**
 * The calendar modal
 */
(function($){

	$(document).on('click', '.spcc-event-calendar--button', function(e){
		e.preventDefault();
		var modal = $('#calendarModal').remodal();
		modal.open()
	})

})(jQuery);


(function($){

	$(document).on('click', '.spc-share-icons--icon--mail a', function (e) {
		e.preventDefault();
		inst = $('[data-remodal-id=emailSignup]').remodal({
			closeOnOutsideClick: false,
			hashTracking: false,
		});
		inst.open();
	});

	$(document).on('submit', '#share-with-friend', function () {

		var $self = $(this);
		var $btn = $(this).find('.btn-submit');
		var old_text = $btn.text();
		var data = $(this).serialize();

		$.ajax({
			type: 'POST',
			url: SPCC.ajax_url + '?action=spc_share_post_via_email&nonce=' + SPCC.nonce,
			data: data,
			cache: false,
			beforeSend: function () {
				$btn.text('Loading...');
			},
			success: function (response) {
				if (response.success) {
					$self.get(0).reset();
					alert(response.data.message);
					if (null !== inst) {
						inst.close();
					}
				} else {
					alert(response.data.message);
				}
			},
			error: function () {
				alert('HTTP Error.');
			},
			complete: function () {
				$btn.text(old_text);
			}
		});

		return false;
	});

})(jQuery);


/// Lazy load
(function ($) {

	// Ajaxify pagination
	$(document).on('click', '.spcc-view-links a, .spcc-sort-links a, .spcc-nav-links a', function (e) {
		e.preventDefault();
		var url = $(this).attr('href');
		if (!url) {
			return;
		}
		$.ajax({
			type: 'GET',
			cache: false,
			url: url,
			beforeSend: function() {
				$('body').LoadingOverlay('show');
			},
			success: function (response) {
				var cont = $('.spcc-events-container');
				var part = $(response).find('.spcc-events-container').html();
				cont.addClass('spcc-loading');
				cont.html(part);
				window.init_spcc_scripts();
				window.init_spcc_map();
				var params = spcc_parse_query(url);
				spcc_update_pushstate(params);
				setTimeout(function () {
					cont.removeClass('spcc-loading');
				}, 100);
			},
			complete: function() {
				$('body').LoadingOverlay('hide');
			},
			error: function() {
				$('body').LoadingOverlay('hide');
			},
		})

	});


	// Ajaxify filters form
	$(document).on('submit', '#spcc-events-filters-form', function () {

		var data = $(this).serializeArray();
		var url = window.location.href;
		for (var i in data) {
			if (i === 'pagenum') {
				continue;
			}
			//console.log(data[i]['name'] +'---------'+ data[i]['value']);
			url = spcc_update_query_arg(url, data[i]['name'], data[i]['value']);
		}

		$.ajax({
			type: 'GET',
			cache: false,
			url: url,
			beforeSend: function() {
				$('body').LoadingOverlay('show');
			},
			success: function (response) {
				var cont = $('.spcc-events-container');
				var part = $(response).find('.spcc-events-container').html();
				cont.addClass('spcc-loading');
				cont.html(part);
				window.init_spcc_scripts();
				window.init_spcc_map();
				var params = spcc_parse_query(url);
				spcc_update_pushstate(params);
				setTimeout(function () {
					cont.removeClass('spcc-loading');
				}, 100);
			},
			complete: function() {
				$('body').LoadingOverlay('hide');
			},
			error: function() {
				$('body').LoadingOverlay('hide');
			},
		});

		return false;

	});

	// Email subscription box
	$(document).on('submit', '#spcc-subscribe', function(){
		var $self = $(this);
		var data = $self.serialize();
		$.ajax({
			url: 'https://stpetecatalyst.com/wp-json/communitycalendar/v1/account/subscribe',
			data: data,
			type: 'POST',
			beforeSend: function() {
				$('body').LoadingOverlay('show');
			},
			success: function(response){
				if(response.code === 200) {
					$self.get(0).reset();
					alert(response.message);
				} else {
					alert(response.errors[0]);
				}
				$('body').LoadingOverlay('hide');
			},
			error: function() {
				alert('HTTP Error.');
				$('body').LoadingOverlay('hide');
			},
			complete: function() {
				$('body').LoadingOverlay('hide');
			}
		});
		return false;
	});


})(jQuery);