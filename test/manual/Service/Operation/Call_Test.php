<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Service\Operation;



include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {

    public function test_addToWalletActive() {
        $asCustId = 'cid';
        $asValue = 'val';
        $asRef = 'ref';
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $dba \Praxigento\Core\Lib\Context\IDbAdapter */
        $dba = $obm->get(\Praxigento\Core\Lib\Context\IDbAdapter::class);
        $conn = $dba->getDefaultConnection();
        $conn->beginTransaction();
        try {
            /** @var  $call \Praxigento\Wallet\Lib\Service\IOperation */
            $call = $obm->get(\Praxigento\Wallet\Lib\Service\IOperation::class);
            $req = new Request\AddToWalletActive();
            $req->setOperationTypeId(1);
            $req->setTransData([
                [ $asCustId => 1, $asValue => 23, $asRef => 3 ],
                [ $asCustId => 2, $asValue => 32, $asRef => 2 ]
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

}