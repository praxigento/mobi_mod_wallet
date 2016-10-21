define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        console.log("Partial payment register is loading...");

        console.log("Pushing new payment method object into the renderer list.");
        debugger;
        rendererList.push({
            type: 'praxigento_partial',
            component: 'Praxigento_Wallet/js/view/payment/method/partial/renderer'
        });

        /** Add view logic here if needed */
        /** EOL */

        console.log("Partial payment register is loaded. Empty component is returned.");
        return Component.extend({});
    }
);