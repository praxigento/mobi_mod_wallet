/**
 * Display eWallet payment part for partial payment in shopping cart summary (right sided block).
 * URL: /checkout/cart/
 */
define([
    'Praxigento_Wallet/js/view/checkout/summary/partial'
], function (Component) {
    'use strict';

    var result = Component.extend({
        defaults: {
            template: 'Praxigento_Wallet/checkout/cart/totals/partial' // redefine template
        }
    });
    return result;
});