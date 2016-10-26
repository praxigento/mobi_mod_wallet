<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Model\Quote\Address\Total;


class Partial
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        return $this;
    }

}