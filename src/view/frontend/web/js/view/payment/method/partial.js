/**
 * UI Component: partial payment checkbox on checkout.
 *
 * Decorates getData() functions for other methods' UiComponents.
 */
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Braintree/js/view/payment/method-renderer/hosted-fields'
    ], function (ko, Component, uiTotals, uiPaymentDefault, uiPaymentBraintree) {
        'use strict';
        /* get Checkout configuration data (see \Praxigento\Wallet\Model\Checkout\ConfigProvider) */
        var quoteData = window.checkoutConfig.quoteData;
        var baseCurrency = quoteData['base_currency_code'];
        var baseGrandTotal = quoteData['base_grand_total'];
        /* eWallet payment method config (see \Praxigento\Wallet\Api\Data\Config\Payment\Method) */
        var paymentConfig = window.checkoutConfig.praxigentoWallet;
        var negativeBalanceEnabled = paymentConfig['negative_balance_enabled'];
        var partialPaymentMaxPercent = paymentConfig['partial_max_percent'];
        var customerData = window.customerData;
        // \Praxigento\Wallet\Model\Checkout\ConfigProvider::CFG_CUST_...
        var customerAccountBalance = customerData['prxgtWalletBalance'];
        var initState = getAmount() > 0;

        /**
         * Extract partial payment amount from totals segment.
         * @returns {number}
         */
        function getAmount() {
            var result = 0;
            /* see \Praxigento\Wallet\Plugin\Quote\Model\Cart\CartTotalRepository::TOTAL_SEGMENT */
            if (uiTotals && uiTotals.getSegment('praxigento_wallet')) {
                result = uiTotals.getSegment('praxigento_wallet').value;
            }
            return Number(result);
        }

        function getMaxPercent() {
            var result = Number(Math.round(partialPaymentMaxPercent * 10000) / 100).toFixed(2);
            return result;
        }

        function getCustomerBalance() {
            var result = Number(Math.round(customerAccountBalance * 100) / 100).toFixed(2);
            return result;
        }

        function getAvailableAmount() {
            /* limit available amount by MAX percent of the baseGrandTotal */
            var result = baseGrandTotal * partialPaymentMaxPercent;
            if (customerAccountBalance <= result) {
                if (!negativeBalanceEnabled) {
                    /* limit available amount by customer balance */
                    result = customerAccountBalance;
                }
            }
            result = Number(Math.round(result * 100) / 100).toFixed(2);
            return result;
        }

        /**
         * This UiComponent. Should be placed before 'getData()' decorators.
         */
        var exportResult = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
                partialMaxPercent: ko.observable(getMaxPercent()),
                baseCurrency: ko.observable(baseCurrency),
                availableAmount: ko.observable(getAvailableAmount()),
                customerBalance: ko.observable(getCustomerBalance()),
            },

            /**
             * This property is used in the child uiComp 'Praxigento_Wallet/js/view/payment/method/renderer.js'
             */
            isPartialChecked: ko.observable(initState),

            /**
             * Switch visibility for partial checkbox node. Checkbox is available
             *  - if partial payment is available
             *  - AND customer balance is
             *
             * @returns {boolean}
             */
            isVisible: function () {
                var enabled = Boolean(paymentConfig['partial_enabled']);
                var amountAvailable = getAvailableAmount();
                /* hide if available amount equals to grand total - this is not partial */
                var enoughAmount = ((baseGrandTotal - amountAvailable) < 0.00001);
                var result = enabled && (amountAvailable > 0) && !enoughAmount;
                return result;
            },

        });

        /* decorate default payment data ('checkmo' method), add partial payment state */
        var fnGetDataDefault = uiPaymentDefault.prototype.getData;
        uiPaymentDefault.prototype.getData = function () {
            /* put this UI Component into the local context */
            var uiPartial = exportResult;
            /* get original data from current object */
            var result = fnGetDataDefault.apply(this);
            /* compose partial payment state and add to payment data */
            var usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data = {use_partial: usePartial};
            return result;
        }

        /* decorate Braintree payment data, add partial payment state */
        var fnGetDataBraintree = uiPaymentBraintree.prototype.getData;
        uiPaymentBraintree.prototype.getData = function () {
            /* put this UI Component into the local context */
            var uiPartial = exportResult;
            /* get original data from current object */
            var result = fnGetDataBraintree.apply(this);
            /* compose partial payment state and add to payment data */
            var usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data.use_partial = usePartial;
            return result;
        }

        return exportResult;
    }
);