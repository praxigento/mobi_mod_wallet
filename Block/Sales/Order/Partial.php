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
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    protected $daoPartialSale;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->daoPartialSale = $daoPartialSale;
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
        $found = $this->daoPartialSale->getById($orderId);
        if ($found) {
            $amntBaseWallet = $found->getBasePartialAmount();
            $amntWallet = $found->getPartialAmount();
            $totalWallet = new \Magento\Framework\DataObject(
                [
                    'code' => 'praxigento_wallet',
                    'strong' => true,
                    'base_value' => $amntBaseWallet,
                    'value' => $amntWallet,
                    'label' => __('Paid by eWallet'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($totalWallet);
            /* add balance */
            $totalGrandIncl = $parent->getTotal('grand_total_incl');
            if ($totalGrandIncl) {
                $amntBaseGrandIncl = $totalGrandIncl->getData('base_value');
                $amntGrandIncl = $totalGrandIncl->getData('value');
                $amntBaseLeft = $amntBaseGrandIncl - $amntBaseWallet;
                $amntLeft = $amntGrandIncl - $amntWallet;
                if ($amntBaseLeft || $amntLeft) {
                    $totalLeft = new \Magento\Framework\DataObject(
                        [
                            'code' => 'praxigento_wallet_left',
                            'strong' => true,
                            'base_value' => $amntBaseLeft,
                            'value' => $amntLeft,
                            'label' => __('Balance'),
                            'area' => 'footer',
                            'is_formated' => false
                        ]
                    );
                    $parent->addTotal($totalLeft);
                }
            }
            /* MOBI-497: fix 'due' amount */
            $totalDue = $parent->getTotal('due');
            if ($totalDue) {
                $amntBaseDue = $totalDue->getData('base_value');
                $amntDue = $totalDue->getData('value');
                $dueFixedBase = $amntBaseDue - $amntBaseWallet;
                $dueFixed = $amntDue - $amntWallet;
                $totalDue->setData('base_value', $dueFixedBase);
                $totalDue->setData('value', $dueFixed);
            }
        }
        return $this;
    }

}