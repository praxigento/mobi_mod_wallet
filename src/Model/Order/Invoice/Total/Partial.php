<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model\Order\Invoice\Total;

use Praxigento\Wallet\Config as Cfg;

/**
 * Collect partial payment totals in invoices.
 *
 * Find record in registry (if exists) then decrease grand total and save eWallet part in invoice.
 */
class Partial
    extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\Def\Sale */
    protected $repoPartialSale;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\Def\Sale $repoPartialSale,
        array $data = []
    ) {
        parent::__construct($data);
        $this->repoPartialSale = $repoPartialSale;
    }

    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        parent::collect($invoice);
        $orderId = $invoice->getOrderId();
        $found = $this->repoPartialSale->getById($orderId);
        if ($found) {
            $partial = $found->getPartialAmount();
            $partialBase = $found->getBasePartialAmount();
            $grand = $invoice->getGrandTotal();
            $grandBase = $invoice->getBaseGrandTotal();
            $grandFixed = $grand - $partial;
            $grandFixedBase = $grandBase - $partialBase;
            $invoice->setGrandTotal($grandFixed);
            $invoice->setBaseGrandTotal($grandFixedBase);
            $invoice->set(Cfg::CODE_TOTAL_PARTIAL_AMOUNT, $partial);
            $invoice->set(Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE, $partialBase);
        }
        return $this;
    }

}