/**
 * UI Component: partial payment checkbox on checkout.
 */
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/totals'
    ], function (ko, Component, uiTotals) {
        'use strict';
        /* save totals uiComponent to local context */
        // var totals = uiTotals;
        /* see globals in 'Magento_Checkout/js/model/quote.js' */
        // var quoteData = window.checkoutConfig.quoteData;
        // var totalsData = window.checkoutConfig.totalsData;
        /* eWallet payment method config (see \Praxigento\Wallet\Api\Data\Config\Payment\Method) */
        var paymentConfig = window.checkoutConfig.praxigentoWallet;
        var initState = getAmount() > 0;

        /**
         * Extract partial payment amount from totals segment.
         * @returns {number}
         */
        function getAmount() {
            var result = 0;
            /* see \Praxigento\Wallet\Decorate\Quote\Model\Cart\CartTotalRepository::TOTAL_SEGMENT */
            if (uiTotals && uiTotals.getSegment('praxigento_wallet')) {
                result = uiTotals.getSegment('praxigento_wallet').value;
            }
            return Number(result);
        }

        function getMaxPercent() {
            var percent = paymentConfig['partial_max_percent'];
            var result = Number(Math.round(percent * 10000) / 100).toFixed(2);
            return result;
        }

        var result = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
                partialMaxPercent: ko.observable(getMaxPercent()),
            },

            /**
             * This property is used in the child uiComp 'Praxigento_Wallet/js/view/payment/method/renderer.js'
             */
            isPartialChecked: ko.observable(initState),

            /**
             * Switch visibility for partial checkbox node.
             *
             * @returns {boolean}
             */
            isVisible: function () {
                var result = Boolean(paymentConfig['partial_enabled']);
                return result;
            },

        });

        return result;
    }
);