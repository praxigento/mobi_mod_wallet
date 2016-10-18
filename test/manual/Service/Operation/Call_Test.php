<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Service\Operation;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{

    public function test_addToWalletActive()
    {
        $asCustId = 'cid';
        $asValue = 'val';
        $asRef = 'ref';
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $dba \Praxigento\Core\Lib\Context\IDbAdapter */
        $dba = $obm->get(\Praxigento\Core\Lib\Context\IDbAdapter::class);
        $conn = $dba->getDefaultConnection();
        $conn->beginTransaction();
        try {
            /** @var  $call \Praxigento\Wallet\Service\IOperation */
            $call = $obm->get(\Praxigento\Wallet\Service\IOperation::class);
            $req = new Request\AddToWalletActive();
            $req->setOperationTypeId(1);
            $req->setTransData([
                [$asCustId => 1, $asValue => 23, $asRef => 3],
                [$asCustId => 2, $asValue => 32, $asRef => 2]
            ]);
            $req->setAsAmount($asValue);
            $req->setAsCustomerId($asCustId);
            $req->setAsRef($asRef);
            $response = $call->addToWalletActive($req);
            $this->assertTrue($response->isSucceed());
        } finally {
            $conn->rollback();
        }
    }

    public function test_payForSaleOrder()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $manTrans \Praxigento\Core\Transaction\Database\IManager */
        $manTrans = $obm->get(\Praxigento\Core\Transaction\Database\IManager::class);
        $def = $manTrans->begin();
        try {
            /** @var  $call \Praxigento\Wallet\Service\IOperation */
            $call = $obm->get(\Praxigento\Wallet\Service\IOperation::class);
            $req = new Request\PayForSaleOrder();
            $req->setCustomerId(10);
            $req->setOrderId(14);
            $req->setBaseAmountToPay(32.10);
            $res = $call->payForSaleOrder($req);
            $this->assertTrue($res->isSucceed());
            $manTrans->commit($def);
        } finally {
            $manTrans->end($def);
        }
    }

}