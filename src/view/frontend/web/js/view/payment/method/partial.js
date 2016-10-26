define([
        'knockout',
        'uiComponent',
    ], function (ko, Component) {
        'use strict';

        console.log("Common subform for partial payment is loading...");

        var uiComp = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/partial',
            }


        });

        return uiComp;
    }
);