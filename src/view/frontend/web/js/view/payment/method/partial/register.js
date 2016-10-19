define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'praxigento_partial',
                component: 'Praxigento_Wallet/js/view/payment/method/partial/renderer'
            }
        );

        /** Add view logic here if needed */

        return Component.extend({});
    }
);