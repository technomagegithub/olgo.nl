var ajaxcart_timer;
var ajaxcart_sec;
jQuery.noConflict();
	function setAjaxData(data,iframe,type){
        var id = data.product_id;
		if(data.status == 'ERROR'){
            jQuery('#success-message-container').append('<div class="ajax-cart-fail"><div>' + data.message + '</div></div>');
		}else{
            if(jQuery('.header-icon-cart')){
                jQuery('.header-icon-cart').replaceWith(data.icon_cart);
            }
            if(jQuery('.header-minicart')){
                jQuery('.header-minicart').replaceWith(data.header_cart);
            }
            if(jQuery('.top-minicart')){
                jQuery('.top-minicart').replaceWith(data.top_cart);
            }
			
	        jQuery.fancybox.close();
			if(type!='item'){
                jQuery('#after-loading-success-message').fadeIn(200);
                jQuery('#success-message').html(data.message);
                //ajaxcart_sec = jQuery('#after-loading-success-message .timer').text();
                ajaxcart_sec = init_timer;
                ajaxcart_timer = setInterval(function(){
                    jQuery('#after-loading-success-message .timer').html(jQuery('#after-loading-success-message .timer').text()-1);
                },1000)
                setTimeout(function(){
                    jQuery('#after-loading-success-message').fadeOut(200);
                    clearTimeout(ajaxcart_timer);
                    setTimeout(function(){
                        jQuery('#after-loading-success-message .timer').html(ajaxcart_sec);
                    }, 1000);
                },ajaxcart_sec*1000);
			}
        }
        setTimeout(function(){
        }, 3000);
	}
	function setLocationAjax(url,id,type){
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
		url = url.replace("checkout/cart","ajaxcart/index");
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
        
		jQuery('#loading-mask').show();
		try {
			jQuery.ajax( {
				url : url,
				dataType : 'json',
				success : function(data) {
					jQuery('#loading-mask').hide();
         			setAjaxData(data,false,type);
				}
			});
		} catch (e) {
		}
	}

    function showOptions(id){
		initFancybox();
        jQuery('#fancybox'+id).trigger('click');
    }
	
	function initFancybox(){
		jQuery.noConflict();
		jQuery(document).ready(function(){
		jQuery('.fancybox').fancybox({
                hideOnContentClick : true,
                width: 450,
                padding: 0,
                autoDimensions: false,
                type : 'iframe',
                showTitle: false,
                scrolling: 'auto',
                'afterLoad'         : function() {                                    
                    jQuery('#fancybox-content').height('auto'); 
                    jQuery('.fancybox-overlay').addClass('loading-success');
                },
                'afterShow'        : function() { 
					if(typeof spConfig !== 'undefined')
						swatchesConfig = new Product.ConfigurableSwatches(spConfig);
	            },
                'helpers'             : {
                    overlay : {
                        locked  : false
                    }
                }
			}
		);
		});   	
	}
	function ajaxCompare(url,id){
	    url = url.replace("catalog/product_compare/add","ajaxcart/whishlist/compare");
		if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
		jQuery('#loading-mask').show();
	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
                jQuery('#loading-mask').hide();

//                jQuery('.sw-cart-state').append('<div class="ajax-compare-fail"><div>' + data.message + '</div></div>');
                if(jQuery('.btn-compare')){
                    jQuery('.btn-compare').replaceWith(data.toolbar_compare);
                }
                jQuery('#after-loading-cw-message').fadeIn(200);
                jQuery('#cw-success-message').html(data.message);
		    }
	    });
    }
    function ajaxCompareView(url,id){
        url = url.replace("catalog/product_compare/add","ajaxcart/whishlist/compare");
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
        url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
        if(jQuery('body').hasClass('quickview-index-view')){
            window.parent.jQuery('#loading-mask').show();
        }else{
            jQuery('#loading-mask').show();
        }
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            success : function(data) {
                if(jQuery('body').hasClass('quickview-index-view')){
                    window.parent.jQuery('#loading-mask').hide();
                }else{
                    jQuery('#loading-mask').hide();
                }
                if(data.status == 'ERROR'){
//                    alert(data.message);
                    jQuery('#messages_product_view').append('<div class="sw-cart-state"><div class="ajax-cart-fail"><div>'+data.message+'</div></div></div>');
                }else{
//                    alert(data.message);
                    jQuery('#messages_product_view').append('<div class="sw-cart-state"><div class="ajax-cart-success"><div>'+data.message+'</div></div></div>');
                    if(jQuery('.block-compare').length){
                        jQuery('.block-compare').replaceWith(data.sidebar);
                    }else{
                        if(jQuery('.col-right').length){
                            jQuery('.col-right').prepend(data.sidebar);
                        }
                    }
                }
                setTimeout(function(){
                    jQuery('.sw-cart-state').remove();
                }, 10000);
            }
        });
    }
    function ajaxWishlist(url,id){
	    url = url.replace("wishlist/index","ajaxcart/whishlist");
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
        if(jQuery('body').hasClass('quickview-index-view')){
            window.parent.jQuery('#loading-mask').show();
        }else{
            jQuery('#loading-mask').show();
        }
	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
                if(jQuery('body').hasClass('quickview-index-view')){
                    window.parent.jQuery('#loading-mask').hide();
                }else{
                    jQuery('#loading-mask').hide();
                }
                jQuery('#after-loading-cw-message').fadeIn(200);
                jQuery('#cw-success-message').html(data.message);
            }
	    });
    }
    function ajaxWishlistView(url,id){
        url = url.replace("wishlist/index","ajaxcart/whishlist");
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
        url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
        if(jQuery('body').hasClass('quickview-index-view')){
            window.parent.jQuery('#loading-mask').show();
        }else{
            jQuery('#loading-mask').show();
        }
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            success : function(data) {
                if(jQuery('body').hasClass('quickview-index-view')){
                    window.parent.jQuery('#loading-mask').hide();
                }else{
                    jQuery('#loading-mask').hide();
                }
                if(data.status == 'ERROR'){
//                    alert(data.message);
                    jQuery('#messages_product_view').append('<div class="sw-cart-state"><div class="ajax-cart-fail"><div>'+data.message+'</div></div></div>');
                }else{
                    //alert(data.message);
                    jQuery('#messages_product_view').append('<div class="sw-cart-state"><div class="ajax-cart-success"><div>'+data.message+'</div></div></div>');
                    if(jQuery('.top-bar > .container > .top-links > .links')){
                        jQuery('.top-bar > .container > .top-links > .links').replaceWith(data.toplink);
                    }
                }
                setTimeout(function(){
                    jQuery('.sw-cart-state').remove();
                }, 10000);
            }
        });
    }
    function deleteAction(deleteUrl,itemId,msg){
	    var result =  confirm(msg);
	    if(result==true){
		    setLocationAjax(deleteUrl,itemId,'item')
	    }else{
		    return false;
	    }
    }