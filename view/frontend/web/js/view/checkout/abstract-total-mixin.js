/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function (Component, quote, priceUtils, totals, stepNavigator) {
        'use strict';

        var mixin = {
            getFormattedPrice: function (price) {
                if (typeof IS_SKYBOX_VISIBLE !== "undefined") {
                    if (IS_SKYBOX_VISIBLE === "1") {
                        var currency = SKYBOX_CURRENCY;
                        if (currency) {
                            var result = currency + price;
                            return result;
                        }
                    }
                }
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
