<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Intercept\Checkout\Block\Checkout;

/**
 * Add "Partial Payment" subform to payment methods components.
 */
class LayoutProcessor
{
    public function __construct()
    {
        $q = 4;
    }


    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        /** TODO: add Data Object here */
        if (
            $result &&
            isset($result['components']) &&
            isset($result['components']['checkout']) &&
            isset($result['components']['checkout']['children']) &&
            isset($result['components']['checkout']['children']['steps']) &&
            isset($result['components']['checkout']['children']['steps']['children']) &&
            isset($result['components']['checkout']['children']['steps']['children']['billing-step']) &&
            isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children'])
        ) {
            $billingChildren = &$result['components']['checkout']['children']['steps']['children']['billing-step']['children'];
            $billingChildren['prxgtPartial'] = [];
            $partial = &$billingChildren['prxgtPartial'];
//            $partial['children'] = [];
//            $partial['component'] = 'Praxigento_Wallet/js/view/payment/method/partial/subform';
//            $partial['config'] = 'conf';

        }
        return $result;
    }
}