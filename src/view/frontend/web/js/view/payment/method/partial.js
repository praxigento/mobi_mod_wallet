/**
 * UI Component: partial payment checkbox on checkout.
 */
define([
        'ko',
        'uiComponent'
    ], function (ko, Component) {
        'use strict';

        /* see \Praxigento\Wallet\Api\Data\Config\Payment\Method */
        var paymentConfig = window.checkoutConfig.praxigentoWallet;

        var uiComp = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
            },

            isPartialEanbled: function () {
                var result = paymentConfig['partial_enabled'];
                return result;
            },

            getMaxPercent: function () {
                var percent = paymentConfig['partial_max_percent'];
                var result = Number(Math.round(percent * 10000) / 100).toFixed(2);
                return result;
            }
        });

        return uiComp;
    }
);