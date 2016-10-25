define([
        'knockout',
        'uiComponent',
    ], function (ko, Component) {
        'use strict';

        console.log("Common subform for all payment methods is loading...");

        var uiComp = Component.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/header',
            }


        });

        return uiComp;
    }
);