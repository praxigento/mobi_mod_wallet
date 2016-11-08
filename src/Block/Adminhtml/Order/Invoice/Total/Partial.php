<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total;

class Partial
    extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    const CODE = 'praxigento_wallet_partial';
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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $parent->getOrder();
        $orderId = $order->getId();
        $found = $this->repoPartialSale->getById($orderId);
        if ($found) {
            /* add partial total */
            $baseAmount = $found->getBasePartialAmount();
            $amount = $found->getPartialAmount();
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => self::CODE,
                    'strong' => true,
                    'base_value' => $baseAmount,
                    'value' => $amount,
                    'label' => __('eWallet part'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total);
        }
        return $this;
    }

}