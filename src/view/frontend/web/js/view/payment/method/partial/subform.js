define([
        'uiElement'
    ], function (elem) {
        'use strict';

        console.log("Partial subform is loading...");

        var subformComponent = elem.extend({
            defaults: {
                template: 'Praxigento_Wallet/payment/method/subform',
                is_available: true
            },

            isAvailable: function () {
                return this.is_available;
            },

            initContainer: function (parent) {
                console.log("Partial payment subform is initiated.");
                this._super();
                return this;
            }
        });

        return subformComponent;

    }
);