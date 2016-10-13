<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model;


class Payment
    extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'praxigento_wallet';
    protected $_code = self::CODE;

    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;

}