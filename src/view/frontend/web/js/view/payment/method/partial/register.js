define(
    [
        'underscore',
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/view/payment'
    ],
    function (_, ko, $, Component, rendererList, quote, payment) {
        'use strict';

        console.log("Partial payment register is loading...");

        rendererList.push({
            type: 'praxigento_partial',
            component: 'Praxigento_Wallet/js/view/payment/method/partial/renderer'
        });

        /**
         * Create new Knockout handler and environment for it.
         */
        var vmBillingStep;

        // ko.bindingHandlers.prxgtPartialSubformHandler = {
        //     init: function prxgtPartialSubformHandlerInit(element, valueAccesor) {
        //         vmBillingStep = valueAccesor;
        //         debugger;
        //     },
        //
        //     update: function prxgtPartialSubformHandlerUpdate(element, valueAccesor) {
        //         var local = vmBillingStep;
        //         var uicomp = local();
        //         debugger;
        //         local.subscribe(function (newValue) {
        //
        //         });
        //         debugger;
        //     },
        // }

        // var nodePayment = $('#payment')[0];
        // var vmPayment = ko.dataFor(this);
        // debugger;
        // ko.extenders.prxgtPartialSubform = function (target, opts) {
        //     target.subscribe(function extenPaymentMethod($method) {
        //         var parent = target;
        //         var code = $method.method;
        //         console.log("'" + code + "' method is selected.");
        //         var selector = ".payment-method._active > .payment-method-content";
        //         var nodeMethodSelected = $(selector);
        //         var nodeSubform = $('#prxgtPartialSubform');
        //         nodeMethodSelected.prepend(nodeSubform);
        //         debugger;
        //     });
        //     return target;
        // }
        //
        // quote.paymentMethod = quote.paymentMethod.extend({prxgtPartialSubform: ''});

        /** Add view logic here if needed */
        rendererList.each(function ($item) {
            var type = $item.type;
            var component = $item.component;
            console.log("Payment method: '" + type + "' (" + component + ");");
        });

        /** EOL */

        console.log("Partial payment register is loaded. Empty component is returned.");
        return Component.extend({});
    }
);