/**
 * UI Component: eWallet payment on checkout.
 */
define([
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Praxigento_Wallet/js/view/payment/method/partial'
    ], function (ko, Component, uiPartial) {
        'use strict';

        /* see \Praxigento\Wallet\Api\Data\Config\Payment\Method*/
        var paymentConfig = window.checkoutConfig.praxigentoWallet;

        var uiComp = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/form'
            },

            isEnabled: ko.computed(function () {
                var isPartialChecked = uiPartial.prototype.isPartialChecked();
                var result = !isPartialChecked;
                return result;
            }),

            // initContainer: function (parent) {
            //     console.log("Internal Money  payment renderer is initiated.");
            //     this._super();
            //     return this;
            // },
            //
            // initObservable: function () {
            //
            //     this._super()
            //         .observe([
            //             'transactionResult'
            //         ]);
            //     return this;
            // },

            // getBillingAddressFormName: function () {
            //     // debugger;
            //     return 'billing-address-form-' + this.item.method;
            // },
            //
            // getCode: function () {
            //     return 'praxigento_wallet';
            // },

            // getData: function () {
            //     return {
            //         'method': this.item.method,
            //         'additional_data': {
            //             'transaction_result': this.transactionResult()
            //         }
            //     };
            // },

            // isEnabled: function () {
            //     var result = paymentConfig['enabled'];
            //     return result;
            // }

        });

        return uiComp;
    }
);