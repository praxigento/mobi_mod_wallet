<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Praxigento\Wallet\Plugin\ParadoxLabs\Authnetcim\Gateway\Validator;


/**
 * Prevent validation failure on empty properties for credit cards.
 */
class CreditCard
{
    public function aroundValidate(
        \ParadoxLabs\Authnetcim\Gateway\Validator\CreditCard $subject,
        \Closure $proceed,
        $validationSubject
    ) {
        if (
            is_array($validationSubject) &&
            isset($validationSubject['payment'])
        ) {
            /** @var \Magento\Payment\Model\Info $payment */
            $payment = $validationSubject['payment'];
            $ccNum = $payment->getData('cc_number');
            // skip validation if credit card number is empty
            // (see ./vendor/paradoxlabs/authnetcim/Gateway/Validator/CreditCard.php:49)
            if (empty($ccNum)) {
                $payment->setAdditionalInformation('acceptjs_value', true);
            }
            $result = $proceed($validationSubject);
        } else {
            $result = $proceed($validationSubject);
        }
        return $result;
    }
}
