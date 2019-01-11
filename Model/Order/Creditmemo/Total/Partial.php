<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Model\Order\Creditmemo\Total;

use Praxigento\Wallet\Config as Cfg;

/**
 * Model to collect partial payment totals in creditmemos.
 *
 * Find record in registry (if exists) then decrease grand total and save eWallet part in creditmemo model.
 */
class Partial
    extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    protected $daoPartialSale;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        array $data = []
    ) {
        parent::__construct($data);
        $this->daoPartialSale = $daoPartialSale;
    }

    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $orderId = $creditmemo->getOrderId();
        $found = $this->daoPartialSale->getById($orderId);
        if ($found) {
            /* all amounts are rated in order currencies (base & regular) */
            $partial = $found->getPartialAmount();
            $partialBase = $found->getBasePartialAmount();
            $grand = $creditmemo->getGrandTotal();
            $grandBase = $creditmemo->getBaseGrandTotal();
            $grandFixed = $grand - $partial;
            $grandFixedBase = $grandBase - $partialBase;
            $creditmemo->setGrandTotal($grandFixed);
            $creditmemo->setBaseGrandTotal($grandFixedBase);
            $creditmemo->setData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT, $partial);
            $creditmemo->setData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE, $partialBase);
        }
        return $this;
    }

}