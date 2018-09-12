<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Block\Payment\Method\Wallet;


class Info
    extends \Magento\Payment\Block\Info
{
    /** @var \Magento\Directory\Model\Currency */
    private $currency;

    public function __construct(
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->currency = $currency;
        parent::__construct($context, $data);
    }

    public function getSpecificInformation()
    {
        $data = [];
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getInfo();
        $order = $payment->getOrder();

        /* transaction ID */
        $transId = $payment->getLastTransId();
        $phrase = __('Transaction ID');
        $label = $phrase->getText();
        $data[$label] = $transId;

        /* amount */
        $amountPaid = $payment->getBaseAmountPaid();
        $curCode = $order->getBaseCurrencyCode();
        $curr = $this->currency->load($curCode);
        $symbol = $curr->getCurrencySymbol();
        $opts = [
            'symbol' => $symbol,
            'precision' => 2
        ];
        $formatted = $curr->format($amountPaid, $opts, false);

        $phrase = __('Amount');
        $label = $phrase->getText();
        $data[$label] = $formatted;

        $result = $data;
        return $result;
    }

}