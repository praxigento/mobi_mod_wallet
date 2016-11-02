/**
 * UI Component partial payment checkout.
 */
define([
        'knockout',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
    ], function (ko, Component, quote) {
        'use strict';

        var q = quote;
        debugger;

        var uiComp = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
            },

            isVisible: function () {
                return true;
            }
        });

        return uiComp;
    }
);