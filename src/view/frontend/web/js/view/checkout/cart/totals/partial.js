define([
    'Praxigento_Wallet/js/view/checkout/summary/partial'
], function (Component) {
    'use strict';

    debugger;

    return Component.extend({
        defaults: {
            title: 'eWallet part',
            template: 'Praxigento_Wallet/checkout/cart/totals/partial' // redefine template
        },

        isDisplayed: function () {
            return true;
        }
    });
});