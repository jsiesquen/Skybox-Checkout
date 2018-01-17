/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery'
], function ($) {
    $(document).ready(function () {
        $(".skybox-price-set").each(function () {
            var productId = $(this).data("product-id");
            var urlProductPrices = $(this).data("url-template");
            if (urlProductPrices !== '')
            {
                $("div[data-product-id='" + productId + "']").each(function (index) {
                    $(this).load(urlProductPrices);
                });
            }
        });
    });
});
