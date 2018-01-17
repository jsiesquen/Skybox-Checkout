/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Skybox_Checkout/checkout/summary/fee'
            },
            totals: quote.getTotals(),
            // isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function () {
                if (typeof IS_SKYBOX_VISIBLE !== "undefined") {
                    return true;
                }
                return false;
            },
            isConceptDisplayed: function (num) {
                var price = 0;
                if (this.totals()) {
                    var name = "checkout_total_" + num;
                    var segment = totals.getSegment(name);
                    if (segment) {
                        price = segment.value;
                    }
                }
                if (price) {
                    return true;
                }
                return false;
            },
            getValue: function (num) {
                var price = 0;
                if (this.totals()) {
                    var name = "checkout_total_" + num;
                    price = totals.getSegment(name).value;
                    console.log("Concept: " + name + " : " + price);
                }
                return this.getFormattedPrice(price);
            },
            getTitle: function (num) {
                var title = this.rawData(num);
                return title;
            },
            rawData: function (num) {
                var data = SKYBOX_DYNAMIC_LABELS;
                // console.log(data);
                if (typeof data === "undefined") {
                    data = {
                        0: "Shipping",
                        1: "Insurance",
                        2: "Duties"
                    };
                }

                return data[num];
            },
            getDynamicLabels: function () {
                var data = window.checkoutConfig.totalsData.total_segments;
                var result = [];
                for (var i = 0; i < data.length; i++) {
                    var item = data[i].code;
                    // console.log(item);
                    var myRegExp = item.indexOf("skybox_fee_");
                    if (myRegExp >= 0) {
                        result.push(data[i]);
                    }
                }
                // console.log(result);
                return result;
            }
        });
    }
);
