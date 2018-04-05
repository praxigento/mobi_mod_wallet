/**
 * UI Component: eWallet payment method on checkout (radio button).
 */
define([
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Praxigento_Wallet/js/view/payment/method/partial'
    ], function (ko, Component, uiPartial) {
        'use strict';

        /* get quote from checkout configuration data */
        var quoteData = window.checkoutConfig.quoteData;
        var baseGrandTotal = quoteData['base_grand_total'];
        var baseCurrency = quoteData['base_currency_code'];
        /* get payment method configuration */
        /* see \Praxigento\Wallet\Model\Payment\Method\ConfigProvider::UI_CHECKOUT_WALLET */
        var paymentConfig = window.checkoutConfig.prxgtWalletPaymentCfg;
        /* see \Praxigento\Wallet\Model\Payment\Method\ConfigProvider\Data */
        var negativeBalanceEnabled = paymentConfig['negative_balance_enabled'];
        var customerBalance = paymentConfig['customer_balance'];

        var result = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/form'
            },

            /**
             * Display/hide payment method section on the page.
             */
            isVisible: ko.computed(function () {
                /* hide full method if partial is enabled */
                var isPartialChecked = uiPartial.prototype.isPartialChecked();
                /* available balance should be not less then base grand total or negative balance is allowed */
                var customerBalance = uiPartial().customerBalance();
                var isAmountEnough = ((baseGrandTotal - customerBalance ) <= 0) || negativeBalanceEnabled;
                /* MAX % should be equal to 100% */
                var maxPercent = uiPartial().partialMaxPercent();
                var isMaxPercent100 = Math.abs(maxPercent - 100) < 0.0001;
                /* compose complex condition */
                var result = !isPartialChecked && isAmountEnough && isMaxPercent100;
                return result;
            }),

            balance: ko.observable(customerBalance),
            currency: ko.observable(baseCurrency)

        });

        return result;
    }
);