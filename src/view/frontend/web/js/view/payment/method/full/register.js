define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'praxigento_wallet',
                component: 'Praxigento_Wallet/js/view/payment/method/full/renderer'
            }
        );

        /** Add view logic here if needed */

        return Component.extend({});
    }
);