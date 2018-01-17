/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
requirejs(['jquery'], function ($) {
    $(document).ready(function () {
        var height = SKYBOX_INT_HEIGHT;
        var isMobile = /Mobi/.test(navigator.userAgent);
        console.log("isMobile ::: " + isMobile);
        if (isMobile) {
            height = jQuery(window).height();
            height = height * 5;
            console.log("height ::: " + height);
        }
        jQuery('#iframe_skybox_checkout').attr('height', height);

        setTimeout(function () {
            document.getElementsByClassName("cssload-container-iframe")[0].style.display = "none";
        }, 3800);
    });
});
