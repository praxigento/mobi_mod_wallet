define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push({
            type: 'praxigento_wallet_method',
            component: 'Praxigento_Wallet/js/view/payment/method/renderer'
        });

        /** Add view logic here if needed */
        var result = Component.extend({});
        return result;
    }
);