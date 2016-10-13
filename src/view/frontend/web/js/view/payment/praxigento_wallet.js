define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        debugger;

        rendererList.push(
            {
                type: 'praxigento_wallet',
                component: 'Praxigento_Wallet/js/view/payment/method-renderer/praxigento_wallet'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);