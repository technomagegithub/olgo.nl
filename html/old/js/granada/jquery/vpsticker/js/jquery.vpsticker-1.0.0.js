/*	
 *	jQuery vpsticker 1.0.0
 *	Demo's and documentation:
 *	vpsticker.frebsite.nl
 *	
 *	Copyright (c) 2012 Fred Heusschen
 *	www.frebsite.nl
 *
 *	Dual licensed under the MIT and GPL licenses.
 *	http://en.wikipedia.org/wiki/MIT_License
 *	http://en.wikipedia.org/wiki/GNU_General_Public_License
 */


(function($) {
	if ($.fn.vpSticker) return;

	$.fn.vpSticker = function(o) {
		if (this.length == 0) {
			debug(true, 'No element found for "'+this.selector+'".');
			return this;
		}
		if (this.length > 1) {
			return this.each(function() {
				$(this).vpSticker(o);
			});
		}


		var $vps = this,
			$con = $vps.parent();

		$con.css('position', 'relative');
		$vps.css('position', 'absolute');

		var opts = $.extend(true, {}, $.fn.vpSticker.defaults, o),
			posi = null,
			$bod = ($.browser.webkit) ? $('body') : $('html'),
			$win = $(window),
			sOpt = {},
			cOpt = {};

		sOpt.top = (typeof opts.top == 'number') ? opts.top : $vps.position().top;

		if (typeof opts.padding != 'object') {
			opts.padding = {
				'top': opts.padding,
				'bottom': opts.padding
			};
		}

		$vps.bind('updateWindowHeight.vps', function() {
			sOpt.height = $vps.outerHeight();
			cOpt.height = $con.height();
			cOpt.top = $con.offset().top;
			cOpt.bottom = cOpt.top + cOpt.height;
		});
		if (typeof opts.bottom == 'number') {
			$vps.bind('updateWindowHeight.vps', function() {
				sOpt.top = $win.height() - sOpt.height - opts.bottom;
				debug(opts.debug, 'new "top" calculated: '+sOpt.top);
			});
		} else {
			opts.bottom = false;
		}


		$vps.bind('updateScrollPosition.vps', function() {

			//	get scrollTop
			var st = $bod.scrollTop();

			//	container-top beneden favi-top
			if (st < cOpt.top - sOpt.top + opts.padding.top) {
				if (posi != 'abstop') {
					posi = 'abstop';
					$vps.css({
						'position': 'absolute',
						'top': opts.padding.top,
						'bottom': 'auto'
					});
				}

			//	container-top boven favi-top
			} else {

				//	container-bottom beneden favi-bottom
				if (st > cOpt.bottom - sOpt.height - sOpt.top - opts.padding.bottom) {
					if (posi != 'absbot') {
						posi = 'absbot';
						$vps.css({
							'position': 'absolute',
							'top': 'auto',
							'bottom': opts.padding.bottom
						});
					}

				//	container-bottom boven favi-bottom
				} else {
					if (posi != 'fixtop') {
						posi = 'fixtop';
						$vps.css({
							'position': 'fixed',
							'top': sOpt.top,
							'bottom': 'auto'
						});
					}
				}
			}

		});

		$win.bind('scroll.vps', function() {
			$vps.trigger('updateScrollPosition');
		});

		$win.bind('resize.vps', function() {
			posi = null;
			$vps.trigger('updateWindowHeight.vps');
			$vps.trigger('updateScrollPosition.vps');
		}).trigger('resize.vps');


		return $vps;
	};


	//	public
	$.fn.vpSticker.defaults = {
		'debug': false,
		'bottom': null,
		'padding': 0
	};


	//	private
	function debug(d, m) {
		if (d) {
			if (window.console && window.console.log) {
				window.console.log('vpSticker: '+m);
			}
		}
	}

})(jQuery);