<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Praxigento\Wallet\Plugin\Magento\Paypal\Model\Api;

use Praxigento\Wallet\Config as Cfg;

/**
 * Correct amounts for PayPal Standard payment method when partial payment is used.
 */
class Nvp
{
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote
    ) {
        $this->daoGeneric = $daoGeneric;
        $this->daoPartialQuote = $daoPartialQuote;
    }

    public function beforeCall(
        \Magento\Paypal\Model\Api\Nvp $subject,
        $methodName,
        array $request
    ) {
        $saleInvNum = $subject->getData('inv_num');
        if ($saleInvNum) {
            /* get quote id by sale inventory number */
            $quoteId = $this->getQuoteId($saleInvNum);
            if ($quoteId) {
                /* get partial payment if exists */
                $entity = $this->daoPartialQuote->getById($quoteId);
                if ($entity && $entity->getBasePartialAmount()) {
                    /* decrease PayPal amounts to partial payment amount */
                    $partial = $entity->getBasePartialAmount();
                    if ($methodName == \Magento\Paypal\Model\Api\Nvp::SET_EXPRESS_CHECKOUT) {
                        $request = $this->calcSetExpressCheckout($partial, $request);
                    } elseif ($methodName == \Magento\Paypal\Model\Api\Nvp::DO_EXPRESS_CHECKOUT_PAYMENT) {
                        $request = $this->calcDoExpressCheckoutPayment($partial, $request);
                    }
                }
            }
        }
        return [$methodName, $request];
    }

    private function calcDoExpressCheckoutPayment($partial, $api)
    {
        /* save payment amount (it is already decreased) then decrease items/shipping/tax */
        $pay = $api['AMT'] ?? 0;
        $shipping = $api['SHIPPINGAMT'] ?? 0;
        $tax = $api['TAXAMT'] ?? 0;
        $api = $this->calcSetExpressCheckout($partial, $api);
        /* restore payment amount */
        $api['AMT'] = $pay;
        /* decrease sale items amounts */
        $tail = $partial - $shipping - $tax;
        foreach ($api as $key => $value) {
            if (substr($key, 0, 5) === 'L_AMT') {
                if ($value < $tail) {
                    $tail -= $value;
                    $api[$key] = "0.00";
                } else {
                    $api[$key] = number_format(($value - $tail), 2, '.', '');
                    break;
                }
            }
        }
        return $api;
    }

    private function calcSetExpressCheckout($partial, $api)
    {
        /* extract API request data */
        $pay = $api['AMT'] ?? 0;
        $items = $api['ITEMAMT'] ?? 0;
        $shipping = $api['SHIPPINGAMT'] ?? 0;
        $tax = $api['TAXAMT'] ?? 0;

        /* decrease total amount and other amounts */
        $pay = number_format(($pay - $partial), 2, '.', '');
        $tail = $partial;
        /* decrease shipping at first */
        if ($shipping > 0) {
            if ($shipping < $tail) {
                $tail -= $shipping;
                $shipping = "0.00";
            } else {
                $shipping = number_format(($shipping - $tail), 2, '.', '');
                $tail = 0;
            }
        }
        /* ... then decrease tax */
        if ($tail && ($tax > 0)) {
            if ($tax < $tail) {
                $tail -= $tax;
                $tax = "0.00";
            } else {
                $tax = number_format(($tax - $tail), 2, '.', '');
                $tail = 0;
            }
        }
        /* ... then decrease items */
        if ($tail) {
            $items = number_format(($items - $tail), 2, '.', '');
        }

        /* update API request data */
        $api['AMT'] = $pay;
        $api['ITEMAMT'] = $items;
        $api['SHIPPINGAMT'] = $shipping;
        $api['TAXAMT'] = $tax;
        return $api;
    }

    private function getQuoteId($saleInvNum)
    {
        $result = false;
        $entity = Cfg::ENTITY_MAGE_QUOTE;
        $cols = [Cfg::E_QUOTE_A_ENTITY_ID];
        $conn = $this->daoGeneric->getConnection();
        $quoted = $conn->quote($saleInvNum);
        $where = Cfg::E_QUOTE_A_RESERVED_ORDER_ID . "=$quoted";
        $rs = $this->daoGeneric->getEntities($entity, $cols, $where);
        if (is_array($rs)) {
            $item = reset($rs);
            if (isset($item[Cfg::E_QUOTE_A_ENTITY_ID])) {
                $result = $item[Cfg::E_QUOTE_A_ENTITY_ID];
            }
        }
        return $result;
    }
}
