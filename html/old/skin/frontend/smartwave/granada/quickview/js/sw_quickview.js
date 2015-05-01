jQuery.noConflict();



//base function    
var myhref;
//get IE version
function ieVersion(){
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer'){
        var ua = navigator.userAgent;
        var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
            rv = parseFloat( RegExp.$1 );
    }
    return rv;
}

//read href attr in a tag
function readHref(){
    var mypath = arguments[0];
    var patt = /\/[^\/]{0,}$/ig;
    if(mypath[mypath.length-1]=="/"){
        mypath = mypath.substring(0,mypath.length-1);
        return (mypath.match(patt)+"/");
    }
    return mypath.match(patt);
}

//string trim
function strTrim(){
    return arguments[0].replace(/^\s+|\s+$/g,"");
}
function loadUrls() {
    var mypath = 'quickview/index/view';
    if (EM.Quickview.BASE_URL.indexOf('index.php') == -1){
        mypath = 'index.php/' + mypath;
    }
    var baseUrl = EM.Quickview.BASE_URL + mypath;

    jQuery('.sw-product-quickview').each(function(){
		reloadurl = baseUrl + "/id/" + jQuery(this).attr('href');
        jQuery(this).attr('href', reloadurl);
    });
}
//main image loading
function loadQvImg(){
    
}
//image zoom
function loadZoom(el) {
    var current = this.currentItem;
    el.find(".owl-item").removeClass('qv-active-cur').eq(current).addClass('qv-active-cur');
    jQuery('.zoomContainer').remove();
    setTimeout(function(){
        el.find(".qv-active-cur .item img.main-zoom-img").elevateZoom({zoomContainer:'.qv-active-cur',borderSize:0,easing:1,easingDuration:2000,cursor:'crosshair', zoomWindowFadeIn:500, zoomWindowFadeOut:500,imageCrossfade: true, zoomType:'inner'});
    }, 1000);
}
function _qsJnit(){ 
    loadUrls();       
    
    jQuery('.sw-product-quickview').on('click', function(){
        if (jQuery(this).parents('.products-list').length) {
            //list mode
            jQuery(this).closest('.product-image-wrapper').addClass('loading-state');
            jQuery(this).closest('.product-image-wrapper').append('<div class="sw-qv-loading"></div>');
        } else {
            jQuery(this).closest('.item-area').addClass('loading-state');
            jQuery(this).closest('.item-area').append('<div class="sw-qv-loading"></div>');
        }
    });
}
jQuery(function($) {
    //end base function
    _qsJnit();
    $('.sw-product-quickview').fancybox({
        'type'              : 'iframe',
        'autoSize'          : false,
        'autoScale'         : false,
        'transitionIn'      : 'none',
        'transitionOut'     : 'none',
        'scrolling'         : 'auto',
        'padding'           : 0,
        'margin'            : 0,
        'autoDimensions'    : false,
        'maxWidth'          : '90%',
        'width'             : EM.Quickview.QS_FRM_WIDTH,
        'maxHeight'         : '90%',
        'centerOnScroll'    : true,
        'height'            : EM.Quickview.QS_FRM_HEIGHT,
        'loadingIcon'       : false,
        'afterLoad'         : function() {
            jQuery('#fancybox-content').height('auto');
            jQuery('.fancybox-overlay').addClass('loading-success');
        },
        'afterShow'        : function() {
            loadQvImg();
        },
        'helpers'             : {
            title   : null,
            overlay : {
                locked  : false
            }
        }
    });
});
