define([
        'Magento_Checkout/js/view/payment/default'
    ], function (Component) {
        'use strict';

        console.log("Full payment renderer is loading...");

        return Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/full',
                transactionResult: ''
            },

            initContainer: function (parent) {
                console.log("Full payment renderer is initiated.");
                this._super();
                debugger;
                return this;
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
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
                // return quote.totals().grand_total <= 0;
                return true;
            }
            // getTransactionResults: function () {
            //     return _.map(window.checkoutConfig.payment.sample_gateway.transactionResults, function (value, key) {
            //         return {
            //             'value': key,
            //             'transaction_result': value
            //         }
            //     });
            // }
        });
    }
);