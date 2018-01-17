/**
 * Copyright © 2017 SkyBOX Checkout, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'skybox/clean': 'Skybox_Checkout/js/jquery/cart/clean',
            'skybox/international': 'Skybox_Checkout/js/jquery/cart/international',
            'skybox/main': 'Skybox_Checkout/js/jquery/cart/main',
            'skybox/multicalculate': 'Skybox_Checkout/js/jquery/cart/multicalculate',
            'skybox/reset': 'Skybox_Checkout/js/jquery/cart/reset'
        },
    },
    paths: {
        'skybox/fancybox': 'Skybox_Checkout/js/jquery/fancybox/jquery.fancybox',
    },
    shim: {
        'skybox/fancybox': {
            deps: ['jquery']
        },
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Skybox_Checkout/js/view/checkout/abstract-total-mixin': true
            }
        }
    }
};
