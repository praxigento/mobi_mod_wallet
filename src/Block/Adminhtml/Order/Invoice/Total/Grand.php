<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total;

class Grand
    extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    const CODE = 'praxigento_wallet_partial_grand';
    const CODE_GRAND_INCL = 'grand_total_incl';
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\ISale */
    protected $repoPartialSale;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Praxigento\Wallet\Repo\Entity\Partial\ISale $repoPartialSale,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->repoPartialSale = $repoPartialSale;
    }


    /**
     * Initialize partial payment related totals (if exist).
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $parent */
        $parent = $this->getParentBlock();
        $totalPartial = $parent
            ->getTotal(\Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total\Partial::CODE);
        if ($totalPartial) {
            $totalGrandFixed = $parent
                ->getTotal(self::CODE_GRAND_INCL);
            $partialBase = $totalPartial->getData('base_value');
            $partial = $totalPartial->getData('value');
            $grandFixedBase = $totalGrandFixed->getData('base_value');
            $grandFixed = $totalGrandFixed->getData('value');
            $baseAmount = $grandFixedBase - $partialBase;
            $amount = $grandFixed - $partial;
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => self::CODE,
                    'strong' => true,
                    'base_value' => $baseAmount,
                    'value' => $amount,
                    'label' => __('Grand Total (Incl.Tax, Excl.eWallet)'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total);
        }
        return $this;
    }

}