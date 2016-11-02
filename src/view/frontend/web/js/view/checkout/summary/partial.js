define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";

        var uTotal = totals;
        var qTotals = quote.getTotals();
        debugger;

        return Component.extend({
            defaults: {
                title: 'eWallet part',
                template: 'Praxigento_Wallet/checkout/summary/partial'
            },

            totals: quote.getTotals(),

            isDisplayed: function () {
                return this.isFullMode();
            },

            isDisplayed: function () {
                return this.isFullMode();
            },

            getBaseValue: function () {
                var price = 0;
                debugger;
                if (this.totals()) {
                    price = totals.getSegment('praxigento_wallet').value;
                }
                var result = priceUtils.formatPrice(price, quote.getBasePriceFormat());
                return result;
            },

            getValue: function () {
                var price = 0;
                // debugger;
                if (this.totals()) {
                    // price = totals.getSegment('praxigento_wallet').value;
                }
                return this.getFormattedPrice(price);
            },

            // getBaseValue: function () {
            //     var price = 0;
            //     if (this.totals()) {
            //         price = this.totals().base_fee;
            //     }
            //     return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            // }
        });
    }
);