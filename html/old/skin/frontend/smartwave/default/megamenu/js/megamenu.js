jQuery(document).ready(function($){
    // **********************************************************************// 
    // ! Main Navigation plugin
    // **********************************************************************//

    $.fn.et_menu = function ( options ) {
        var methods = {
            showChildren: function(el,marValue) {				
				if(marValue != 0){
					el.fadeIn(settings.fadeTime).css({
						display: 'list-item',
						listStyle: 'none',
						marginLeft: marValue + 'px'
					}).find('li').css({listStyle: 'none'});	
				} else {
					el.fadeIn(settings.fadeTime).css({
						display: 'list-item',
						listStyle: 'none'
					}).find('li').css({listStyle: 'none'});
				}
            },
            calculateColumns: function(el) {
                // calculate columns count
                var columnsCount = el.find('.container > ul > li.menu-item-has-children').length;
                var dropdownWidth = el.find('.container > ul > li').outerWidth();
                var padding = 20;
                if(columnsCount > 1) {
                    dropdownWidth = dropdownWidth*columnsCount + padding;
                    el.css({
                        'width':dropdownWidth
                    });
                }

                // calculate right offset of the  dropdown
                var headerWidth = $('.menu-wrapper').outerWidth();
                var headerLeft = $('.menu-wrapper').offset().left;
                var dropdownOffset = el.offset().left - headerLeft;
                var dropdownRight = headerWidth - (dropdownOffset + dropdownWidth);

                if(dropdownRight < 0) {
                    el.css({
                        'left':'auto',
                        'right':0
                    });
                } 
            },
            loadParIcon: function() {
                var navitem =  $('.nav-category-list > li');
                var plusIcon = '+';
                var openerHTML = '<div class="cat-open-this">' + plusIcon + '</div>';
                $('.cat-open-this').remove();
                navitem.has('.nav-sublist-dropdown').prepend(openerHTML);
            }
        };

        var settings = $.extend({
            type: "default", 
            animTime: 250,
            openByClick: true,
            fadeTime: 100,  
            parIcon: false  
        }, options );

        if(settings.parIcon) {
            methods.loadParIcon();
        }
        this.find('>li').hover(function (){                        
			$(this).addClass('open');
			var dropdown = $(this).find('> .nav-sublist-dropdown');
			if ($(this).hasClass('menu-full-width') && SW_MENU_POPUP_WIDTH) {
				dropdown.css('width', SW_MENU_POPUP_WIDTH);
			}
			if ($(this).hasClass('menu-static-width'))
			{
				var menuWidth = $(this).find('.nav-sublist-dropdown').width();
				var wrapperOffset = $(this).parents().find('.menu-wrapper').offset();
				var thisOffset = $(this).find('>a').offset();
				var marValue = 0;
				if ((thisOffset.left + menuWidth - 25) > (wrapperOffset.left + $(this).parents().find('.menu-wrapper').width()))
				{
					marValue = wrapperOffset.left + $('.menu-wrapper').width() - thisOffset.left - menuWidth;
				}
			}
			methods.showChildren(dropdown,marValue);

			if(settings.type == 'columns') {
				methods.calculateColumns(dropdown);
			}
            
            if (settings.parIcon) {
                if($(this).has('.cat-open-this')) {
                    $(this).find('.cat-open-this').html('&ndash;');
                }
            }
        }, function () {            
			$(this).removeClass('open');
			$(this).find('> .nav-sublist-dropdown').fadeOut(settings.fadeTime);                
            if (settings.parIcon) {
                if($(this).has('.cat-open-this')) {
                    $(this).find('.cat-open-this').html('&#43;');
                }
            }            
        });

        return this;
    }

    // First Type of column Menu
    $('.main-nav .menu').et_menu({
        type: "default"
    });

    $('.fixed-header .menu').et_menu({
        openByClick: false
    });
    

    $('.nav-category-list').et_menu({
        fadeTime: 5,
        parIcon: true
    });
    


    function et_equalize_height(elements, removeHeight) {
        var heights = [];

        if(removeHeight) {
            elements.attr('style', '');
        }

        elements.each(function(){
            heights.push($(this).height());
        });

        var maxHeight = Math.max.apply( Math, heights );
        if($(window).width() > 767) {
            elements.height(maxHeight);
        }
    }

    $(window).resize(function(){
        //et_equalize_height($('.product-category'), true);
    });

     // **********************************************************************// 
    // ! Mobile navigation
    // **********************************************************************// 

    var navList = $('.mobile-nav div > ul');
    var etOpener = '<span class="open-child button">(open)</span>';
    navList.addClass('sw-mobile-menu');
    
    navList.find('li:has(ul)',this).each(function() {
        $(this).prepend(etOpener);
    })
    
    navList.find('.open-child').click(function(){
        if ($(this).parent().hasClass('over')) {
            $(this).parent().removeClass('over').find('>ul').slideUp(200);
        }else{
            $(this).parent().parent().find('>li.over').removeClass('over').find('>ul').slideUp(200);
            $(this).parent().addClass('over').find('>ul').slideDown(200);
        }
    });    
    $('.menu-icon, .close-mobile-nav').click(function(event) {
        if(!$('body').hasClass('mobile-nav-shown')) {
            $('body').addClass('mobile-nav-shown', function() {
                // Hide search input on click
                setTimeout(function(){
                    $(document).one("click",function(e) {
                        var target = e.target;
                        if (!$(target).is('.mobile-nav') && !$(target).parents().is('.mobile-nav')) {
                                    $('body').removeClass('mobile-nav-shown');
                        }
                    });  
                }, 111);
            });
        } else{
            $('body').removeClass('mobile-nav-shown');
        }
    });
    
    // **********************************************************************// 
    // ! Mobile navigation
    // **********************************************************************// 

    var cat_navList = $('.nav-category-mobile-list > ul');
    var cat_plusIcon =  '&#43;';
    var cat_minusIcon = '&ndash;';
    var cat_etOpener = '<div class="open-child">' + cat_plusIcon + '</div>';
    
    cat_navList.addClass('sw-mobile-menu');
    
    cat_navList.find('li:has(ul)',this).each(function() {
        $(this).prepend(cat_etOpener);
    })
    
    cat_navList.find('.open-child').click(function(){
        if ($(this).parent().hasClass('over')) {            
            $(this).parent().removeClass('over').find('>ul').slideUp(100);            
        }else{
            $(this).parent().parent().find('>li.over').removeClass('over').find('>ul').slideUp(200);            
            $(this).parent().addClass('over').find('>ul').slideDown(100);                 
        }                                                             
        cat_navList.find('.open-child').html(cat_plusIcon);
        cat_navList.find('.over').find('>.open-child').html(cat_minusIcon);
    });

	// **********************************************************************// 
    // ! Category List Accordion
    // **********************************************************************// 

    var cat_navAccor = $('.nav-category-accordion > ul');
    
    cat_navAccor.find('li:has(ul)',this).each(function() {
        $(this).prepend(cat_etOpener);
    })
    
    cat_navAccor.find('.open-child').click(function(){
        if ($(this).parent().hasClass('over')) {            
            $(this).parent().removeClass('over').find('>ul').slideUp(100);            
        }else{
            $(this).parent().parent().find('>li.over').removeClass('over').find('>ul').slideUp(200);            
            $(this).parent().addClass('over').find('>ul').slideDown(100);                 
        }                                                             
        cat_navAccor.find('.open-child').html(cat_plusIcon);
        cat_navAccor.find('.over').find('>.open-child').html(cat_minusIcon);
    });

    // **********************************************************************//
    // ! Hidden Top Panel
    // **********************************************************************//
    $(document).ready(function(){
    });

    // **********************************************************************// 
    // ! Images lightbox
    // **********************************************************************//

    // **********************************************************************// 
    // ! Fixed header
    // **********************************************************************// 
    
    $(window).scroll(function(){
        //if (!$('body').hasClass('fixNav-enabled')) {return false; }
        var fixedHeader = $('.fixed-header-area');
        var scrollTop = $(this).scrollTop();
        var headerHeight = $('.header-container').height() + $('.header-wrapper').height() + 20;
        
        if(scrollTop > headerHeight){
            if(!fixedHeader.hasClass('fixed-already')) {
                fixedHeader.stop().addClass('fixed-already');
            }
        }else{
            if(fixedHeader.hasClass('fixed-already')) {
                fixedHeader.stop().removeClass('fixed-already');
            }
        }
    });

}); // document ready

