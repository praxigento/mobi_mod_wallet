<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Block\Sales\Order;

/**
 * Block to display partial totals for invoice in adminhtml.
 */
class Partial
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\ISale */
    protected $repoPartialSale;

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
            $baseAmount = $found->getBasePartialAmount();
            $amount = $found->getPartialAmount();
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'praxigento_wallet',
                    'strong' => true,
                    'base_value' => $baseAmount,
                    'value' => $amount,
                    'label' => __('Paid by eWallet'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total);
            /* MOBI-497: fix 'due' amount */
            $totalDue = $parent->getTotal('due');
            if ($totalDue) {
                $due = $totalDue->getData('value');
                $dueBase = $totalDue->getData('base_value');
                $dueFixed = $due - $amount;
                $dueFixedBase = $dueBase - $baseAmount;
                $totalDue->setData('value', $dueFixed);
                $totalDue->setData('base_value', $dueFixedBase);
            }
        }
        return $this;
    }

}