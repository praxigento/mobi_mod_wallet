define([
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/renderer-list',
        'uiElement',
        'uiRegistry'
    ], function (Component, rendererList, elem, uiRegistry) {
        'use strict';

        console.log("Partial payment renderer is loading...");

        var uiReg = uiRegistry;


        var mageJsComponent = Component.extend({
            initContainer: function (parent) {
                console.log("Partial payment renderer is initiated.");
                this._super();
                return this;
            }
        });

        return mageJsComponent;

    }
);