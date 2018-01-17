/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery'
], function ($) {
    $(document).ready(function () {
        var url = URL_BASE_MAGENTO + 'skbcheckout/process/reset';
        $.ajax({
            url: url,
            type: 'POST',
            data: {}
        }).done(function (data, textStatus, xhr) {
            console.log("success");
        }).fail(function () {
            console.log('error');
        }).always(function (data) {
            console.log("complete");
        });
    });
});
