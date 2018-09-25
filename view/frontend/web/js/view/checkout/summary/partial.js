/**
 * Display eWallet payment part for partial payment in checkout summary (right sided block).
 * URL: /checkout/#payment
 */
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function (Component, uiTotals, uiPriceUtils) {
        "use strict";

        /* save totals uiComponent to local context */
        var totals = uiTotals;
        /* shortcuts to global objects */
        var basePriceFormat = window.checkoutConfig.basePriceFormat;

        /**
         * Extract partial payment amount from totals segment.
         * @returns {number}
         */
        function getAmount() {
            var result = 0;
            /* see \Praxigento\Wallet\Plugin\Magento\Quote\Model\Cart\CartTotalRepository::TOTAL_SEGMENT */
            if (totals && totals.getSegment('praxigento_wallet')) {
                result = totals.getSegment('praxigento_wallet').value;
            }
            return Number(result);
        }

        var result = Component.extend({
            defaults: {
                title: 'Paid by eWallet',
                template: 'Praxigento_Wallet/checkout/summary/partial'
            },

            /**
             * Switch visibility for summary node.
             *
             * @returns {boolean}
             */
            isVisible: function () {
                var value = getAmount();
                var result = (value > 0);
                return result;
            },

            /**
             * Return formatted amount for partial payment amount (base currency).
             *
             * @returns {String|*}
             */
            getBaseValue: function () {
                var price = getAmount();
                var result = uiPriceUtils.formatPrice(price, basePriceFormat);
                return result;
            },

        });

        return result;
    }
);