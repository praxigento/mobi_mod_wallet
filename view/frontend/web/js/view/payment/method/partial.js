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
        'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method',
        'Magento_Paypal/js/view/payment/method-renderer/paypal-express',
        'Magento_AuthorizenetAcceptjs/js/view/payment/method-renderer/authorizenet-accept',
        'Magento_Braintree/js/view/payment/method-renderer/cc-form'
    ], function (
    ko, Component, uiTotals, uiPayDefault, uiPayPayPalPayflow, uiPayPayPalStandard, uiPayAuthNet, uiPayBraintree
    ) {
        'use strict';

        /* get quote from checkout configuration data */
        const quoteData = window.checkoutConfig.quoteData;
        const baseCurrency = quoteData['base_currency_code'];
        const baseGrandTotal = quoteData['base_grand_total'];
        /* get payment method configuration */
        /* see \Praxigento\Wallet\Model\Payment\Method\ConfigProvider::UI_CHECKOUT_WALLET */
        const paymentConfig = window.checkoutConfig.prxgtWalletPaymentCfg;
        /* see \Praxigento\Wallet\Model\Payment\Method\ConfigProvider\Data */
        const negativeBalanceEnabled = paymentConfig['negative_balance_enabled'];
        const partialPaymentMaxPercent = paymentConfig['partial_max_percent'];
        const customerAccountBalance = paymentConfig['customer_balance'];

        const initState = getAmount() > 0;

        /**
         * Extract partial payment amount from totals segment.
         * @returns {number}
         */
        function getAmount() {
            let result = 0;
            /* see \Praxigento\Wallet\Plugin\Magento\Quote\Model\Cart\CartTotalRepository::TOTAL_SEGMENT */
            if (uiTotals && uiTotals.getSegment('praxigento_wallet')) {
                result = uiTotals.getSegment('praxigento_wallet').value;
            }
            return Number(result);
        }

        function getMaxPercent() {
            return Number(Math.round(partialPaymentMaxPercent * 10000) / 100).toFixed(2);
        }

        function getCustomerBalance() {
            return Number(Math.round(customerAccountBalance * 100) / 100).toFixed(2);
        }

        function getAvailableAmount() {
            /* limit available amount by MAX percent of the baseGrandTotal */
            let result = baseGrandTotal * partialPaymentMaxPercent;
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
        const exportResult = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
                partialMaxPercent: ko.computed(getMaxPercent),
                baseCurrency: ko.observable(baseCurrency),
                availableAmount: ko.computed(getAvailableAmount),
                customerBalance: ko.computed(getCustomerBalance),
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
                const enabled = Boolean(paymentConfig['partial_enabled']);
                const amountAvailable = getAvailableAmount();
                /* hide if available amount equals to grand total - this is not partial */
                const enoughAmount = ((baseGrandTotal - amountAvailable) < 0.00001);
                return enabled && (amountAvailable > 0) && !enoughAmount;
            },

        });

        /* decorate default payment data ('checkmo' method), add partial payment state */
        const fnGetDataDefault = uiPayDefault.prototype.getData;
        uiPayDefault.prototype.getData = function () {
            /* put this UI Component into the local context */
            const uiPartial = exportResult;
            /* get original data from current object */
            const result = fnGetDataDefault.apply(this);
            /* compose partial payment state and add to payment data */
            const usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data = {use_partial: usePartial};
            return result;
        };

        /* decorate PayPal Payflow payment data, add partial payment state */
        const fnGetDataPayPalPayflow = uiPayPayPalPayflow.prototype.getData;
        uiPayPayPalPayflow.prototype.getData = function () {
            /* put this UI Component into the local context */
            const uiPartial = exportResult;
            /* get original data from current object */
            const result = fnGetDataPayPalPayflow.apply(this);
            /* compose partial payment state and add to payment data */
            const usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data.use_partial = usePartial;
            return result;
        };

        /* decorate PayPal Standard payment data, add partial payment state */
        const fnGetDataPayPalStandard = uiPayPayPalStandard.prototype.getData;
        uiPayPayPalStandard.prototype.getData = function () {
            /* put this UI Component into the local context */
            const uiPartial = exportResult;
            /* get original data from current object */
            const result = fnGetDataPayPalStandard.apply(this);
            /* compose partial payment state and add to payment data */
            const usePartial = uiPartial.prototype.isPartialChecked();
            if (result.additional_data === null) {
                result.additional_data = {};
            }
            result.additional_data.use_partial = usePartial;
            return result;
        };

        /* decorate Authorize.net payment data, add partial payment state */
        const fnGetDataAuthNet = uiPayAuthNet.prototype.getData;
        uiPayAuthNet.prototype.getData = function () {
            /* put this UI Component into the local context */
            const uiPartial = exportResult;
            /* get original data from current object */
            const result = fnGetDataAuthNet.apply(this);
            /* compose partial payment state and add to payment data */
            const usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data.use_partial = usePartial;
            return result;
        };

        /* decorate Braintree payment data, add partial payment state */
        const fnGetDataBraintree = uiPayBraintree.prototype.getData;
        uiPayBraintree.prototype.getData = function () {
            /* put this UI Component into the local context */
            const uiPartial = exportResult;
            /* get original data from current object */
            const result = fnGetDataBraintree.apply(this);
            /* compose partial payment state and add to payment data */
            const usePartial = uiPartial.prototype.isPartialChecked();
            result.additional_data.use_partial = usePartial;
            return result;
        };

        return exportResult;
    }
);
