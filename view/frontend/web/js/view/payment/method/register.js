define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        /* payment method code see in \Praxigento\Wallet\Model\Payment\Method\ConfigProvider::CODE_WALLET */
        rendererList.push({
            type: 'prxgt_wallet_pay',
            component: 'Praxigento_Wallet/js/view/payment/method/renderer'
        });

        /** Add view logic here if needed */
        var result = Component.extend({});
        return result;
    }
);