/**
 * UI Component: eWallet payment method on checkout (radio button).
 */
define([
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Praxigento_Wallet/js/view/payment/method/partial'
    ], function (ko, Component, uiPartial) {
        'use strict';

        /* get Checkout configuration data (see \Praxigento\Wallet\Model\Checkout\ConfigProvider) */
        var quoteData = window.checkoutConfig.quoteData;
        var baseGrandTotal = quoteData['base_grand_total'];
        /* see \Praxigento\Wallet\Api\Data\Config\Payment\Method*/
        var paymentConfig = window.checkoutConfig.praxigentoWallet;
        if(!paymentConfig) {
            /* TODO: remove after development */
            paymentConfig={};
            paymentConfig['negative_balance_enabled'] = true;
        }
        var negativeBalanceEnabled = paymentConfig['negative_balance_enabled'];

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
                /* compose compex condition */
                var result = !isPartialChecked && isAmountEnough && isMaxPercent100;
                return result;
            }),

        });

        return result;
    }
);