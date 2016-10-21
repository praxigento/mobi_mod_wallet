define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        console.log("Full payment register is loading...");

        rendererList.push(
            {
                type: 'praxigento_wallet',
                component: 'Praxigento_Wallet/js/view/payment/method/full/renderer'
            }
        );

        /** Add view logic here if needed */

        console.log("Full payment register is loaded. Empty component is returned.");

        return Component.extend({});
    }
);