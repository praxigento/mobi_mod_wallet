define([
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/renderer-list',
        'uiElement'
    ], function (Component, rendererList, elem) {
        'use strict';

        console.log("Partial payment renderer is loading...");

        debugger;

        var mageJsComponent = Component.extend({
            initContainer: function (parent) {
                console.log("Partial payment renderer is initiated.");
                this._super();
                debugger;
                return this;
            }
        });

        // var mageJsComponent = function (config, node) {
        //     console.log(config);
        //     console.log(node);
        //     //alert(config);
        // };

        return mageJsComponent;


        // return Component.extend({
        //     defaults: {
        //         template: 'Praxigento_Wallet/payment/method/partial',
        //         transactionResult: ''
        //     },
        //
        //     initObservable: function () {
        //
        //         this._super()
        //             .observe([
        //                 'transactionResult'
        //             ]);
        //         return this;
        //     },
        //
        //     getCode: function () {
        //         return 'praxigento_partial';
        //     },
        //
        //     getData: function () {
        //         return {
        //             'method': this.item.method,
        //             'additional_data': {
        //                 'transaction_result': this.transactionResult()
        //             }
        //         };
        //     },
        //
        //     isAvailable: function () {
        //         // return quote.totals().grand_total <= 0;
        //         return true;
        //     }
        //     // getTransactionResults: function () {
        //     //     return _.map(window.checkoutConfig.payment.sample_gateway.transactionResults, function (value, key) {
        //     //         return {
        //     //             'value': key,
        //     //             'transaction_result': value
        //     //         }
        //     //     });
        //     // }
        // });
    }
);