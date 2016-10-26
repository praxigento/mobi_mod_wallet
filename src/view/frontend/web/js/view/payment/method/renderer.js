define([
        'ko',
        'Magento_Checkout/js/view/payment/default'
    ], function (ko, Component) {
        'use strict';

        console.log("Internal Money  payment renderer is loading...");

        return Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/form'
            },

            initContainer: function (parent) {
                console.log("Internal Money  payment renderer is initiated.");
                this._super();
                return this;
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            /**
             * @return {String}
             */
            getBillingAddressFormName: function () {
                // debugger;
                return 'billing-address-form-' + this.item.method;
            },

            getCode: function () {
                return 'praxigento_wallet';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },

            isAvailable: function () {
                // debugger;
                return true;
            }

        });
    }
);