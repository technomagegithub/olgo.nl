jQuery.fn.extend({
    scrollToMe: function(){
        var top = jQuery(this).offset().top - 100;
        jQuery('html,body').animate({scrollTop: top}, 500);
    },
    scrollToJustMe: function(){
        var top = jQuery(this).offset().top;
        jQuery('html,body').animate({scrollTop: top}, 500);
    }
});
/*!
 * jQuery.scrollTo
 * Copyright (c) 2007-2014 Ariel Flesler - aflesler<a>gmail<d>com | http://flesler.blogspot.com
 * Licensed under MIT
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 * @projectDescription Easy element scrolling using jQuery.
 * @author Ariel Flesler
 * @version 1.4.14
 */
;(function (define) {
    'use strict';

    define(['jquery'], function ($) {

        var $scrollTo = $.scrollTo = function( target, duration, settings ) {
            return $(window).scrollTo( target, duration, settings );
        };

        $scrollTo.defaults = {
            axis:'xy',
            duration: 0,
            limit:true
        };

        // Returns the element that needs to be animated to scroll the window.
        // Kept for backwards compatibility (specially for localScroll & serialScroll)
        $scrollTo.window = function( scope ) {
            return $(window)._scrollable();
        };

        // Hack, hack, hack :)
        // Returns the real elements to scroll (supports window/iframes, documents and regular nodes)
        $.fn._scrollable = function() {
            return this.map(function() {
                var elem = this,
                    isWin = !elem.nodeName || $.inArray( elem.nodeName.toLowerCase(), ['iframe','#document','html','body'] ) != -1;

                if (!isWin)
                    return elem;

                var doc = (elem.contentWindow || elem).document || elem.ownerDocument || elem;

                return /webkit/i.test(navigator.userAgent) || doc.compatMode == 'BackCompat' ?
                    doc.body :
                    doc.documentElement;
            });
        };

        $.fn.scrollTo = function( target, duration, settings ) {
            if (typeof duration == 'object') {
                settings = duration;
                duration = 0;
            }
            if (typeof settings == 'function')
                settings = { onAfter:settings };

            if (target == 'max')
                target = 9e9;

            settings = $.extend( {}, $scrollTo.defaults, settings );
            // Speed is still recognized for backwards compatibility
            duration = duration || settings.duration;
            // Make sure the settings are given right
            settings.queue = settings.queue && settings.axis.length > 1;

            if (settings.queue)
            // Let's keep the overall duration
                duration /= 2;
            settings.offset = both( settings.offset );
            settings.over = both( settings.over );

            return this._scrollable().each(function() {
                // Null target yields nothing, just like jQuery does
                if (target == null) return;

                var elem = this,
                    $elem = $(elem),
                    targ = target, toff, attr = {},
                    win = $elem.is('html,body');

                switch (typeof targ) {
                    // A number will pass the regex
                    case 'number':
                    case 'string':
                        if (/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)) {
                            targ = both( targ );
                            // We are done
                            break;
                        }
                        // Relative/Absolute selector, no break!
                        targ = win ? $(targ) : $(targ, this);
                        if (!targ.length) return;
                    case 'object':
                        // DOMElement / jQuery
                        if (targ.is || targ.style)
                        // Get the real position of the target
                            toff = (targ = $(targ)).offset();
                }

                var offset = $.isFunction(settings.offset) && settings.offset(elem, targ) || settings.offset;

                $.each( settings.axis.split(''), function( i, axis ) {
                    var Pos	= axis == 'x' ? 'Left' : 'Top',
                        pos = Pos.toLowerCase(),
                        key = 'scroll' + Pos,
                        old = elem[key],
                        max = $scrollTo.max(elem, axis);

                    if (toff) {// jQuery / DOMElement
                        attr[key] = toff[pos] + ( win ? 0 : old - $elem.offset()[pos] );

                        // If it's a dom element, reduce the margin
                        if (settings.margin) {
                            attr[key] -= parseInt(targ.css('margin'+Pos)) || 0;
                            attr[key] -= parseInt(targ.css('border'+Pos+'Width')) || 0;
                        }

                        attr[key] += offset[pos] || 0;

                        if(settings.over[pos])
                        // Scroll to a fraction of its width/height
                            attr[key] += targ[axis=='x'?'width':'height']() * settings.over[pos];
                    } else {
                        var val = targ[pos];
                        // Handle percentage values
                        attr[key] = val.slice && val.slice(-1) == '%' ?
                            parseFloat(val) / 100 * max
                            : val;
                    }

                    // Number or 'number'
                    if (settings.limit && /^\d+$/.test(attr[key]))
                    // Check the limits
                        attr[key] = attr[key] <= 0 ? 0 : Math.min( attr[key], max );

                    // Queueing axes
                    if (!i && settings.queue) {
                        // Don't waste time animating, if there's no need.
                        if (old != attr[key])
                        // Intermediate animation
                            animate( settings.onAfterFirst );
                        // Don't animate this axis again in the next iteration.
                        delete attr[key];
                    }
                });

                animate( settings.onAfter );

                function animate( callback ) {
                    $elem.animate( attr, duration, settings.easing, callback && function() {
                        callback.call(this, targ, settings);
                    });
                }
            }).end();
        };

        // Max scrolling position, works on quirks mode
        // It only fails (not too badly) on IE, quirks mode.
        $scrollTo.max = function( elem, axis ) {
            var Dim = axis == 'x' ? 'Width' : 'Height',
                scroll = 'scroll'+Dim;

            if (!$(elem).is('html,body'))
                return elem[scroll] - $(elem)[Dim.toLowerCase()]();

            var size = 'client' + Dim,
                html = elem.ownerDocument.documentElement,
                body = elem.ownerDocument.body;

            return Math.max( html[scroll], body[scroll] ) - Math.min( html[size]  , body[size]   );
        };

        function both( val ) {
            return $.isFunction(val) || $.isPlainObject(val) ? val : { top:val, left:val };
        }

        // AMD requirement
        return $scrollTo;
    })
}(typeof define === 'function' && define.amd ? define : function (deps, factory) {
        if (typeof module !== 'undefined' && module.exports) {
            // Node
            module.exports = factory(require('jquery'));
        } else {
            factory(jQuery);
        }
    }));

//granada tabs
if (!window.Varien)
    var Varien = new Object();
Varien.GTabs = Class.create();
Varien.GTabs.prototype = {
    initialize: function(selector) {
        var self=this;
        $$(selector+'>li a').each(this.initTab.bind(this));
    },

    initTab: function(el) {
        el.href = 'javascript:void(0)';
        if ($(el.parentNode).hasClassName('active')) {
            this.showContent(el);
        }
        el.observe('click', this.showContent.bind(this, el));
    },

    showContent: function(a) {
        var li = $(a.parentNode), ul = $(li.parentNode);
        ul.getElementsBySelector('li', 'ol').each(function(el){
            var contents = $(el.id+'_contents');
            if (el==li) {
                el.addClassName('active');
                contents.show();
            } else {
                el.removeClassName('active');
                contents.hide();
            }
        });
    }
}
function contentProdImages() {
    jQuery('.list-image-effect').each(function () {
        var slider = jQuery(this);
        var index = 0;
        var autoSlide;
        var imageLink = slider.find('.product-image');
        var imagesList = imageLink.data('images-list');
        imagesList = imagesList.split(",");
        var arrowsHTML = '<div class="small-slider-arrow arrow-left button button-custom">left</div><div class="small-slider-arrow arrow-right button button-custom">right</div>';
        if (imagesList.length > 1) {
            slider.prepend(arrowsHTML);            
            slider.find('.arrow-left').click(function (event) {
                if (index > 0) {
                    index--;
                } else {
                    index = imagesList.length - 1;
                }
                imageLink.find('img').attr('src', imagesList[index]);
                slider.find('.current-index').text(index + 1);
            });
            slider.find('.arrow-right').click(function (event) {
                if (index < imagesList.length - 1) {
                    index++;
                } else {
                    index = 0;
                }
                imageLink.find('img').attr('src', imagesList[index]);
                slider.find('.current-index').text(index + 1);
            });
        }
    });
}

function accordionProcess() {
    var $ = jQuery;
    var accordionList = $('.accordion-format');
    var plusIcon = '+';
    var minusIcon = '&ndash;';

    var etOpener = '<div class="open-this">' + plusIcon + '</div>';
    accordionList.find('li:has(ul)',this).each(function() {
        $(this).find('>a').prepend(etOpener);
    });

    if (jQuery('.active-item').length > 0) {
        jQuery('.active-item').parents('li.accord-item').addClass('active-parent');
    }
    if (jQuery('.active-parent').length > 0) {
        jQuery('.active-parent').find('>a .open-this').html(minusIcon).closest('.active-parent').addClass('opened').find('>ul.children').show();
    } else {
        if(!accordionList.hasClass('home-sidebar-accordion'))
            accordionList.find('>li').first().find('> a .open-this').html(minusIcon).closest('li').addClass('opened').find('>ul.children').show();
    }

    accordionList.find('.open-this').click(function(event){
        event.preventDefault();
        if ($(this).closest('li').hasClass('opened')) {
            $(this).html(plusIcon).closest('li').removeClass('opened').find('>ul').slideUp(200);
        }else{
            $(this).closest('li').parent().find('>li.opened>a>.open-this').html(plusIcon);
            $(this).closest('li').parent().find('>li.opened').removeClass('opened').find('>ul').slideUp(200);
            $(this).html(minusIcon).closest('li').addClass('opened').find('>ul').slideDown(200);
        }
    });
}
//full screen image(width)
function sw_section_width() {
    var $ = jQuery;
    $('.sw_section').each(function(){
        $(this).css({
            'left': - ($(window).width() - $('.main-container.container').width())/2,
            'width': $(window).width(),
            'visibility': 'visible'
        });
    });
}
function sw_section_full() {
    var $ = jQuery;
    $('.sw_section_full').each(function(){
        $(this).css({
            'width': $(window).width(),
            'height': $(window).height(),
            'visibility': 'visible'
        });
    });
}
function sw_section_full_footer(){
    var $ = jQuery;
    $('.sw_section_footer').each(function(){
        $(this).css({
            'margin-left': - ($(window).width() - $('.footer .container').width())/2,
            'width': $(window).width(),
            'visibility': 'visible'
        });
    });
}
jQuery.fn.fullWindow = function($){
    var $ = jQuery;
    var $b = ( $.browser.webkit ) ? $( 'body' ) : $( 'html' ),
        $p = $('.sw_vertical'),
        $slideCounts = $p.length,
        $w = $(window),
        $buttonContainer = '<div class="sw_vertical_buttons"></div>';
    $p.each(function(){
        var index = $p.index(this);
        $(this).attr('id','sw_vertical_'+index);
        $(this).append($buttonContainer);
        if (index > 0) {
            var prevIndex = index - 1;
            var prev = '<a class="vertical-prev vertical-slide-button button button-custom button-custom-active button-up" href="#'+'sw_vertical_'+prevIndex+'">prev</a>';
            $(this).find('.sw_vertical_buttons').append(prev);
        }
        if(index < $slideCounts - 1) {
            var nextIndex = index + 1;
            var next = '<a class="vertical-next vertical-slide-button button button-custom button-custom-active button-down" href="#'+'sw_vertical_'+nextIndex+'">next</a>';
            $(this).find('.sw_vertical_buttons').append(next);
        }
        var $n = $('.sw_vertical_buttons', this);
        $n.vpSticker({
            bottom:0,
            top:0,
            padding: {
                top: 150,
                bottom: 5
            }
        });
    });
    var $n = $('.sw_vertical_buttons a');
    $n.on('click', function() {
        $b.animate({
            'scrollTop': $( $( this ).attr( 'href' ) ).offset().top
        }, {
            duration: 300,
            easing: 'swing'
        });
        return false;
    });
}
jQuery.fn.vslider = function(){
    var $ = jQuery;
    var $b = ( $.browser.webkit ) ? $( 'body' ) : $( 'html' ),
        $p = $('.sw_vertical_nav'),
        $slideCounts = $p.length,
        $w = $(window),
        $buttonContainer = '<div class="sw_vertical_navigation"></div>';
    $(this).append($buttonContainer);
    $p.each(function(){
        var index = $p.index(this);
        $(this).attr('id','sw_vertical_nav_'+index);
        var navigation = '<a class="sw-vertical-navigation" href="#'+'sw_vertical_nav_'+index+'">'+index+'</a>';
        if(index == 0) {
           navigation = '<a class="sw-vertical-navigation cur_index" href="#'+'sw_vertical_nav_'+index+'">'+index+'</a>';
        }
        $('.sw_vertical_navigation').append(navigation);
    });
    var $n = $('.sw-vertical-navigation');
    $n.on('click', function() {
        var $ncur = $(this);
        $b.animate({
            'scrollTop': $( $( this ).attr( 'href' ) ).offset().top
        }, {
            duration: 300,
            easing: 'swing',
            complete: function(){
                $('.sw_vertical_navigation .cur_index').removeClass('cur_index');
                $ncur.addClass('cur_index');
            }
        });
        return false;
    });
    var lastId,
        $vbc = $('.sw_vertical_navigation'),
        last = $('.sw_vertical_nav').last().offset().top + $('.sw_vertical_nav').last().height(),
        $menuItems = $n.map(function(){
            var item = $($(this).attr("href"));
            if(item.length){return item;}
        });
    $(window).scroll(function(){
        var fromTop = $(this).scrollTop();
        var cur = $menuItems.map(function(){
            if($(this).offset().top - 1 < fromTop)
                return this;
        });
        cur = cur[cur.length - 1];
        var id = cur && cur.length ? cur[0].id : "";
        if (lastId !== id){
            lastId = id;
            $n.removeClass('cur_index').end();
            $n.filter("[href=#"+id+"]").addClass('cur_index');
        }
        if(fromTop + $(this).height() - last > $(this).height()*0.5 - $('.sw_vertical_navigation').height()*0.5){
            $('.sw_vertical_navigation').css('display','none');
        } else {
            $('.sw_vertical_navigation').css('display','block');
        }
    });
}

jQuery(function($){
    $('div.product-view p.no-rating a, div.product-view .rating-links a').click(function(){
        $('.product-tab ul li').removeClass('active');
        $('#tab_review_tabbed').addClass('active');
        $('.product-tab .tab-content').hide();
        $('#tab_review_tabbed_contents').show();
        $('#tab_review_tabbed').scrollToMe();
        return false;
    });
    $('.dropdown-toggle').on('click', function(event){
        event.preventDefault();
    });
    $doc = $(document);
//    search input
    var GE = {
        searchBox: function() {
            $doc.on('click', '[data-toggle-active]', function(event) {
                event.preventDefault();
                var $this = $(this),
                    selector = $this.attr('data-toggle-active'),
                    $selector = $(selector);

                $selector.toggleClass('active');
                var focus = $this.attr('data-focus');
                if(focus) {
                    $(focus).focus();
                }
            });
        }
    }
    GE.searchBox();
//    dropdown
    var GD = {
        init: function() {
            $doc.on('hover','[data-gd-toggle-dropdown]', function(event){
               event.preventDefault();
                var $this=$(this),
                    selector = $this.attr('data-gd-toggle-dropdown'),
                    $selector = $(selector);
                $selector.toggleClass('open');
            });
        }
    }
    GD.init();
});
jQuery(document).ready(function($){
	$('.sticky-language > a').click(function(event){
		event.preventDefault();
	});
	$('.sticky-language').hover(function(){
			$(this).addClass('open');
		},
		function(){
			$(this).removeClass('open');
	});
	$('.sticky-currency > a').click(function(event){
	   event.preventDefault();
	});
	$('.sticky-currency').hover(function(){
	   $(this).addClass('open');
	},
	function(){
	   $(this).removeClass('open');
	});
    contentProdImages();
    accordionProcess();
    sw_section_width();
    sw_section_full();
    sw_section_full_footer();

    if($(window).width() > 767) {
        $('.parallax-section').each(function(){
            var speed = 0.1;
            if($(this).data('parallax-speed') != '') {
                speed = $(this).data('parallax-speed');
            }
            $(this).parallax('50%', speed);
        });
    }
    $('.show-case').each(function(){
        $xpos = $(this).attr('data-posx');
        $ypos = $(this).attr('data-posy');
        $product_position = $(this).attr('data-productpos');
        $(this).attr('style','top:'+$ypos+'px;left:'+$xpos+'px;');
        $btw = 10;
        $(this).find('.pop-product').addClass($product_position);
    });
    // **********************************************************************//
    // ! "Top" button
    // **********************************************************************//

    var scroll_timer;
    var displayed = false;
    var $message = $('.back-to-top');

    $(window).scroll(function () {
        window.clearTimeout(scroll_timer);
        scroll_timer = window.setTimeout(function () {
            if(jQuery(window).scrollTop() <= 100)
            {
                displayed = false;
                $message.removeClass('btt-shown');
            }
            else if(displayed == false)
            {
                displayed = true;
                $message.stop(true, true).addClass('btt-shown').click(function () { $message.removeClass('btt-shown'); });
            }
        }, 200);
    });

    $('.back-to-top').click(function(e) {
        $('html, body').animate({scrollTop:0}, 200);
        return false;
    });

    $(window).resize(function(){
        sw_section_width();
        sw_section_full();
        sw_section_full_footer();
    });
	/********** Fullscreen Slider ************/
    var s_width = $(window).innerWidth();
    var s_height = $(window).innerHeight();
    var s_ratio = s_width/s_height;
    var v_width=320;
    var v_height=240;
    var v_ratio = v_width/v_height;
    $(".full-screen-slider div.item").css("position","relative");
    $(".full-screen-slider div.item").css("overflow","hidden");
    $(".full-screen-slider div.item").width(s_width);
    $(".full-screen-slider div.item").height(s_height);
    $(".full-screen-slider div.item > video").css("position","absolute");
    $(".full-screen-slider div.item > video").bind("loadedmetadata",function(){
        v_width = this.videoWidth;
        v_height = this.videoHeight;
        v_ratio = v_width/v_height;
        if(s_ratio>=v_ratio){
            $(this).width(s_width);
            $(this).height("");
            $(this).css("left","0px");
            $(this).css("top",(s_height-s_width/v_width*v_height)/2+"px");
        }else{
            $(this).width("");
            $(this).height(s_height);
            $(this).css("left",(s_width-s_height/v_height*v_width)/2+"px");
            $(this).css("top","0px");
        }
        $(this).get(0).play();
    });
    $(window).resize(function(){
        s_width = $(window).innerWidth();
        s_height = $(window).innerHeight();
        s_ratio = s_width/s_height;
        $(".full-screen-slider div.item").width(s_width);
        $(".full-screen-slider div.item").height(s_height);
        $(".full-screen-slider div.item > video").each(function(){
            if(s_ratio>=v_ratio){
                $(this).width(s_width);
                $(this).height("");
                $(this).css("left","0px");
                $(this).css("top",(s_height-s_width/v_width*v_height)/2+"px");
            }else{
                $(this).width("");
                $(this).height(s_height);
                $(this).css("left",(s_width-s_height/v_height*v_width)/2+"px");
                $(this).css("top","0px");
            }
        });
    });
    // **********************************************************************//
    // ! Counter
    // **********************************************************************//
    var counters = $('.animated-counter');

    counters.each(function(){
        $(this).waypoint(function(){
            animateCounter($(this));
        }, { offset: '100%' });
    });

    // **********************************************************************//
    // ! Progress bars
    // **********************************************************************//

    var progressBars = $('.progress-bars');
    if (progressBars.length) {
        progressBars.waypoint(function() {
            i = 0;
            $(this).find('.progress-bar').each(function () {
                i++;

                var el = $(this);
                var width = $(this).data('width');
                setTimeout(function(){
                    el.find('div').animate({
                        'width' : width + '%'
                    },400);
                    el.find('span').css({
                        'opacity' : 1
                    });
                    el.find('.animated-counter-wrapper').animate({
                        'left': width + '%'
                    },400);
                },i*300, "easeOutCirc");

            });
        }, { offset: '85%' });
    }

    // **********************************************************************//
    // ! Animated Counters
    // **********************************************************************//

    function animateCounter(el) {
        var initVal = parseInt(el.text());
        var finalVal = el.data('value');
        if(finalVal <= initVal) return;
        var intervalTime = 1;
        var time = 400;
        var step = parseInt((finalVal - initVal)/time.toFixed());
        if(step < 1) {
            step = 1;
            time = finalVal - initVal;
        }
        var firstAdd = (finalVal - initVal)/step - time;
        var counter = parseInt((firstAdd*step).toFixed()) + initVal;
        var i = 0;
        var interval = setInterval(function(){
            i++;
            counter = counter + step;
            el.text(counter);
            if(i == time) {
                clearInterval(interval);
            }
        }, intervalTime);
    }
});

/*
 Plugin: jQuery Parallax
 Version 1.1.3
 Author: Ian Lunn
 Twitter: @IanLunn
 Author URL: http://www.ianlunn.co.uk/
 Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

 Dual licensed under the MIT and GPL licenses:
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html
 */

(function( $ ){
    var $window = $(window);
    var windowHeight = $window.height();

    $window.resize(function () {
        windowHeight = $window.height();
    });

    $.fn.parallax = function(xpos, speedFactor, outerHeight) {
        var $this = $(this);
        var getHeight;
        var firstTop;
        var paddingTop = 0;

        //get the starting position of each element to have parallax applied to it
        $this.each(function(){
            firstTop = $this.offset().top;
        });

        if (outerHeight) {
            getHeight = function(jqo) {
                return jqo.outerHeight(true);
            };
        } else {
            getHeight = function(jqo) {
                return jqo.height();
            };
        }

        // setup defaults if arguments aren't specified
        if (arguments.length < 1 || xpos === null) xpos = "50%";
        if (arguments.length < 2 || speedFactor === null) speedFactor = 0.1;
        if (arguments.length < 3 || outerHeight === null) outerHeight = true;

        // function to be called whenever the window is scrolled or resized
        function update(){
            var pos = $window.scrollTop();

            $this.each(function(){
                var $element = $(this);
                var top = $element.offset().top;
                var height = getHeight($element);
                var viewportBottom = pos + windowHeight;

                // Check if totally above or totally below viewport
                if (top + height < pos || top > viewportBottom) {
                    return;
                }


                $this.css('backgroundPosition', xpos + " " + Math.round((top - viewportBottom) * speedFactor) + "px");
            });
        }

        $window.bind('scroll', update).resize(update);
        update();
    };
})(jQuery);
