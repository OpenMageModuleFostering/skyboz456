/**************************** CONFIGURABLE PRODUCT **************************/

if (typeof Product != 'undefined') {

    /*
     * Note: We disabled this jQuery function, because we need to support IE older versions.
     */

    function jqueryButton() {
        (function ($) {
            var $jq = jQuery.noConflict();
            $jq(".skx_content_button").each(function () {
                //Tipped.create($jq(this).find(".skx_content_button")[0], $jq(this).find(".skx_content_button_detail")[0], { skin: 'light', hook: 'lefttop' });
                Tipped.create($jq(".skx_button_content")[0], $jq(".skx_content_button_detail")[0], {
                    skin: 'light',
                    hook: 'lefttop'
                });
                $jq(this).mouseover(function () {
                    $jq(this).find(".t_Tooltip").show();
                });
                $jq('.skx_button_content').show();
            });
        })(jQuery);
    }

    function prototypeButton() {

        $$('.skx_content_button').each(function () {


            //Tipped.create($$('.skx_button_content')[0], $$('".skx_content_button_detail')[0], { skin: 'light', hook: 'lefttop' });
            Tipped.create($$('.skx_button_content'), $$('".skx_content_button_detail'), {
                skin: 'light',
                hook: 'lefttop'
            });

            $$('.skx_content_button').observe('mouseover', function () {

                Prototype.Selector.find($$('.skx_content_button'), '.t_Tooltip', 0).show();
            });
        });
    }

    var SkyboxOptionsPrice = Class.create(Product.OptionsPrice, {

        makeAjaxCall: function () {
            document.getElementsByClassName('price-info')[0].innerHTML = "<img src='https://cdn.shopify.com/s/files/1/1976/9429/t/1/assets/ajax-loader.gif?10083406412996855781'/>";
            console.log("Price: " + this.myProductPrice);
            var request = $('product_addtocart_form').serialize();

            new Ajax.Request(this.url, {
                parameters: {product_id: this.productId, price: this.myProductPrice, request: request},
                onSuccess: function (transport) {
                    var response = transport.responseText;
                    //$$('.skx_content_button')[0].innerHTML = response;

                    var price_box = $$('.price-box')[0];
                    var price_box_bundle = $$('.price-box-bundle')[0];
                    var price_info = $$('.price-info')[0]; // Magento 1.9.x

                    if (price_box) {
                        //console.log('price-box');
                        //$$('.price-box')[0].innerHTML = response;
                    }
                    if (price_box_bundle) {
                        //console.log('price-box-bundle');
                        //$$('.price-box-bundle')[0].innerHTML = response;
                    }

                    if (price_info) {
                        //console.log('price-info');
                        $$('.price-info')[0].innerHTML = response;
                    }

                    jqueryButton();
                    //prototypeButton();
                }.bind(this)
            });
        },

        reload: function ($super) {
            $super();

            var price;
            var formattedPrice;
            var optionPrices = this.getOptionPrices();
            var nonTaxable = optionPrices[1];
            var optionOldPrice = optionPrices[2];
            var priceInclTax = optionPrices[3];
            optionPrices = optionPrices[0];

            // ...

            var _productPrice;
            var _plusDisposition;
            var _minusDisposition;
            var _priceInclTax;

            _productPrice = this.productPrice;
            _plusDisposition = this.plusDisposition;
            _minusDisposition = this.minusDisposition;

            _priceInclTax = priceInclTax;

            // ...

            //price = optionOldPrice+parseFloat(_productPrice);
            price = optionPrices + parseFloat(_productPrice);
            _priceInclTax += parseFloat(_productPrice) * (100 + this.currentTax) / 100;

            var tax = price * (this.currentTax / 100);
            var excl = price;
            // var incl = excl + tax;
            var incl = excl;
            var finalPrice = parseFloat(incl);

            /*console.log('Price incl tax: ' + _priceInclTax);
            console.log('Origin Tax: ' + this.currentTax);
            console.log('Tax: ' + tax);
            console.log('price reload: ' + price);
            console.log('Price + Tax: ' + incl);*/

            this.url = SKYBOX_OPTIONS_PRICE_URL;
            this.myProductPrice = finalPrice;
            this.makeAjaxCall();
        }

    });

    Product.OptionsPrice = SkyboxOptionsPrice;
}


