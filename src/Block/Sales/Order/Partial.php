<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Block\Sales\Order;


class Partial
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\ISale */
    protected $_repoPartialSale;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Praxigento\Wallet\Repo\Entity\Partial\ISale $repoPartialSale,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_repoPartialSale = $repoPartialSale;
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
        $found = $this->_repoPartialSale->getById($orderId);
        if ($found) {
            $baseAmount = $found->getBasePartialAmount();
//            $baseAmount = $order->formatBasePrice($baseAmount);
            $amount = $found->getPartialAmount();
//            $amount = $order->formatPrice($amount);
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'praxigento_wallet',
                    'strong' => true,
                    'base_value' => $baseAmount,
                    'value' => $amount,
                    'label' => __('eWallet part'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total, 'praxigneto_wallet_partial');
        }
        return $this;
    }

}