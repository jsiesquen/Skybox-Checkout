/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    customerData.invalidate(['cart']);
});
