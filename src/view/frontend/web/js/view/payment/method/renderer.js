/**
 * UI Component: eWallet payment on checkout (radio button).
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
        var negativeBalanceEnabled = paymentConfig['negative_balance_enabled'];

        var result = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/form'
            },

            /**
             * Display/hide payment method section on the page.
             */
            isVisible: ko.computed(function () {
                var isPartialChecked = uiPartial.prototype.isPartialChecked();
                var customerBalance = uiPartial().customerBalance();
                var isAmountEnough = (baseGrandTotal - customerBalance ) <= 0;
                var result = !isPartialChecked && (isAmountEnough || negativeBalanceEnabled);
                return result;
            }),

        });

        return result;
    }
);