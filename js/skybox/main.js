(function($) {
    // var SKYBOX_URL = 'https://www.skyboxcheckout.com/';
    var widthPage = 0;
    var heightPage = 0;
// var currentUri = '';
    var merchant = '';
    var urlStore = '';

    var main =  {
        __init : function(){
            widthPage = window.outerWidth; //$(window).width();
            heightPage = window.outerHeight ; //$(window).height();
            //currentUri = window.location.href;
            merchant = MERCHANT_ID_SKYBOX;
            //cartDataURL = CART_DATA_URL_SKYBOX;
            // urlStore = window.location.protocol+'//'+window.location.host;
            urlStore = URL_BASE_MAGENTO;
        },
        dataMain : function(){
            merchant = MERCHANT_ID_SKYBOX;
            return {
                cartDataURL : CART_DATA_URL_SKYBOX,
                IsRegistrationDisabled : IS_REGISTRATION_DISABLED_SKYBOX,
                currentUri : window.location.href
            };
        },
        load : function(){
            $(document).ready(function () {
                // console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Ready document');
                // console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get load');
                $(document).on('click', '.skx_banner_image_car', function(){
                    main.goToCart();
                });
                $(document).on('click', '.skx_position_option_country', function(){
                    main.goToLocation(main.dataMain());
                });
                $(document).on('click', '.skx_banner_image_account', function(){
                    // console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: get dataMain: ');
                    console.dir(main.dataMain());
                    main.goToInitializeSession(main.dataMain());
                });
                $(document).on('click', '.skx_banner_image_tracking', function(){
                    main.goToTrackingLocation(main.dataMain());
                });
                $(document).on('click', '#link_choise_country', function(){
                    main.goToCart();
                });
                //main.loadIframe(data);
            });
        },
        showPopup : function (name, t, url, w, h){
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get showPopup');
            console.info( 'URL: ' + url );
            $.fancybox({
                href : url,
                closeBtn : true,
                width : w,
                height: (h+28),
                type : 'iframe',
                iframe : {
                    scrolling : 'auto',
                    preload   : true
                },
                loop : false,
                afterClose: function(current) {
                    console.info( 'Current: ' + current.href );
                    // var actualUri = window.location.href;
                    // indexParams = actualUri.indexOf('?');
                    // top.location = actualUri.substring(0, indexParams);
                },
                padding: 0,
                helpers : {
                    overlay : {
                        css : {
                            'background' : 'none'
                        }
                    }
                }
            });
        },
        goToCart: function() {
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get goToCart');
            document.location = urlStore + '/checkout/cart/';
        },
        goToInitializeSession: function(data){
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get goToInitializeSession');
            if(data.IsRegistrationDisabled === 1){
                main.goToTrackingLocation(data)
            }else{
                main.goToLoginLocation(data)
            }
        },
        goToLoginLocation: function(data){
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get goToLoginLocation');
            var idCart = "";
            var datos = data.cartDataURL;
            var actualUri = data.currentUri + "?LoadFrame=1";
            var url = SKYBOX_URL
                + "APILoginCustomer.aspx?" + datos + "&merchant=" + merchant
                + "&idCart=" + idCart + "&ReLoad=1&uri=" + actualUri;
            main.showPopup('initSession', '', url, (widthPage - 50 ), ((heightPage < 800) ? heightPage - 50 : 800));
        },
        goToLocation: function(data) {
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get goToLocation');
            var datos = data.cartDataURL;
            var process_url =  urlStore + "/skbcheckout/process"; // cambiar esta url
            var return_url = document.URL;
            // var url = SKYBOX_URL + "Webforms/PublicSite/ReSync.aspx?" + datos + "&process_url=" + process_url + "&return_url=" + return_url; //Falta una pÃ¡gina de /skbcheckout/process en shopify
            var url = SKYBOX_URL + "Webforms/PublicSite/ReSync.aspx?" + datos + "&process_url=" + process_url + "&return_url=" + return_url;
            //console.log(url);
            //showPopup('selectLocation', 'Select your location', url, 400, 370);
            main.showPopup('selectLocation', '', url, 540, 640);
        },
        goToTrackingLocation: function(data) {
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get goToTrackingLocation');
            var idCart = "";
            var datos = data.cartDataURL;
            //var url = "http://www.skyboxcheckout.com/Tracking.aspx?" + datos;
            var url = SKYBOX_URL + "Webforms/PublicSite/Tracking.aspx?" + datos + "&idCart=" + idCart;
            //console.log(url);
            main.showPopup('tracking', '', url, (widthPage - 50 ), ((heightPage < 800) ? heightPage - 50 : 800));
        },
        loadIframe: function(data) {
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>> Log: Get loadIframe');
            var actualUri = data.currentUri;
            var flgLoadIframe = "0";
            if (flgLoadIframe === "1") {
                if (actualUri.indexOf("pages/checkout") === -1) { //facil se confunde con la pÃ¡gina de "success" (pages/checkout-success)
                    main.goToInitializeSession();
                }
            }
        }
    };

    main.__init();
    main.load();
})(jQuery);