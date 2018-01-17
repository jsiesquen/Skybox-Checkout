/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
requirejs(['jquery', 'skybox/fancybox'], function ($) {
    var widthPage = 0;
    var heightPage = 0;
    var merchant = '';
    var urlStore = '';

    var main = {
        __init: function () {
            widthPage = window.outerWidth;
            heightPage = window.outerHeight;
            merchant = MERCHANT_ID_SKYBOX;
            urlStore = URL_BASE_MAGENTO;
        },
        dataMain: function () {
            merchant = MERCHANT_ID_SKYBOX;
            return {
                cartDataURL: CART_DATA_URL_SKYBOX,
                IsRegistrationDisabled: IS_REGISTRATION_DISABLED_SKYBOX,
                currentUri: window.location.href
            };
        },
        load: function () {
            $(document).ready(function () {
                $(document).on('click', '.skx_banner_image_car', function () {
                    main.goToCart();
                });
                $(document).on('click', '.skx_position_option_country', function () {
                    main.goToLocation(main.dataMain());
                });
                $(document).on('click', '.skx_banner_image_account', function () {
                    main.goToInitializeSession(main.dataMain());
                });
                $(document).on('click', '.skx_banner_image_tracking', function () {
                    main.goToTrackingLocation(main.dataMain());
                });
                $(document).on('click', '#link_choise_country', function () {
                    main.goToCart();
                });
            });
        },
        showPopup: function (name, t, url, w, h) {
            $.fancybox({
                href: url,
                closeBtn: true,
                width: w,
                height: h,
                type: 'iframe',
                iframe: {
                    scrolling: 'auto',
                    preload: true
                },
                loop: false,
                padding: 0,
                helpers: {
                    overlay: {
                        css: {
                            'background': 'none'
                        }
                    }
                }
            });
        },
        goToCart: function () {
            document.location = urlStore + '/checkout/cart/';
        },
        goToInitializeSession: function (data) {
            if (data.IsRegistrationDisabled == 1) {
                main.goToTrackingLocation(data)
            } else {
                main.goToLoginLocation(data)
            }
        },
        goToLoginLocation: function (data) {
            var idCart = "";
            var datos = data.cartDataURL;
            var actualUri = data.currentUri + "?LoadFrame=1";
            var url = SKYBOX_URL
                + "APILoginCustomer.aspx?" + datos + "&merchant=" + merchant
                + "&idCart=" + idCart + "&ReLoad=1&uri=" + actualUri;
            main.showPopup('initSession', '', url, (widthPage - 50 ), ((heightPage < 800) ? heightPage - 50 : 800));
        },
        goToLocation: function (data) {
            var datos = data.cartDataURL;
            var process_url = urlStore + "/skbcheckout/process";
            var return_url = document.URL;
            var url = SKYBOX_URL + "Webforms/PublicSite/ReSync.aspx?" + datos + "&process_url=" + process_url + "&return_url=" + return_url;
            main.showPopup('selectLocation', '', url, 540, 640);
        },
        goToTrackingLocation: function (data) {
            var idCart = "";
            var datos = data.cartDataURL;
            var url = SKYBOX_URL + "Webforms/PublicSite/Tracking.aspx?" + datos + "&idCart=" + idCart;
            main.showPopup('tracking', '', url, (widthPage - 50 ), ((heightPage < 800) ? heightPage - 50 : 800));
        },
        loadIframe: function (data) {
            var actualUri = data.currentUri;
            var flgLoadIframe = "0";
            if (flgLoadIframe == "1") {
                if (actualUri.indexOf("pages/checkout") == -1) {
                    main.goToInitializeSession();
                }
            }
        }
    };

    main.__init();
    main.load();
});
