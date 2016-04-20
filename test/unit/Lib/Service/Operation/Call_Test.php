<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Service\Operation;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Lib\Service\Account\Response\Get as AccountGetResponse;
use Praxigento\Accounting\Lib\Service\Account\Response\GetRepresentative as GetRepresentativeResponse;
use Praxigento\Accounting\Lib\Service\Operation\Response\Add as OperationAddResponse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  Call */
    private $call;
    /** @var  \Mockery\MockInterface */
    private $mCallAccount;
    /** @var  \Mockery\MockInterface */
    private $mCallOper;
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mRepoMod;
    /** @var  \Mockery\MockInterface */
    private $mToolbox;

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
        $this->mLogger = $this->_mockLogger();
        $this->mConn = $this->_mockConn();
        $this->mDba = $this->_mockResourceConnection($this->mConn);
        $this->mToolbox = $this->_mock(\Praxigento\Core\Lib\IToolbox::class);
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Lib\Service\IAccount::class);
        $this->mCallOper = $this->_mock(\Praxigento\Accounting\Lib\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Wallet\Lib\Repo\IModule::class);
        $this->call = new Call(
            $this->mLogger,
            $this->mDba,
            $this->mToolbox,
            $this->mCallAccount,
            $this->mCallOper,
            $this->mRepoMod
        );
    }

    public function test_addToWalletActive()
    {
        /** === Test Data === */
        $DATE_APPLIED = '2016-02-24 10:12:23';
        $DATE_PERFORMED = '2016-02-25 12:10:23';
        $OPER_TYPE_CODE = 'code';
        $AS_AMOUNT = 'amount';
        $AS_CUST_ID = 'custId';
        $AS_REF = 'ref';
        $ASSET_TYPE_ID = 2;
        $CUST_ID_REPRES = 10;
        $OPER_ID = 16;
        $TRANS = [
            [$AS_CUST_ID => $CUST_ID_REPRES, $AS_AMOUNT => -32.23, $AS_REF => 45],
            [$AS_CUST_ID => 41, $AS_AMOUNT => 32.23, $AS_REF => 54]
        ];
        /** === Setup Mocks === */
        $this->mLogger->shouldReceive('info');
        $this->mLogger->shouldReceive('debug');

        // $assetTypeId = $this->_repoMod->getTypeAssetIdByCode(Config::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $this->mRepoMod
            ->shouldReceive('getTypeAssetIdByCode')
            ->andReturn($ASSET_TYPE_ID);
        // function _getAccountRepresentativeId($assetTypeId)
        // $respAccRepres = $this->_callAccount->getRepresentative($reqAccRepres);
        $mResp = new GetRepresentativeResponse();
        $this->mCallAccount
            ->shouldReceive('getRepresentative')
            ->andReturn($mResp);
        $mResp->setData(Account::ATTR_CUST_ID, $CUST_ID_REPRES);
        // $respGetAccount = $this->_callAccount->get($reqGetAccount);
        $mResGet = new AccountGetResponse();
        $this->mCallAccount
            ->shouldReceive('get')
            ->andReturn($mResGet);
        // $accId = $respGetAccount->getData(Account::ATTR_ID);
        $mResGet->setData(Account::ATTR_ID, 54);
        // $respOperAdd = $this->_callOper->add($reqOperAdd);
        $mRespAdd = new OperationAddResponse();
        $this->mCallOper
            ->shouldReceive('add')
            ->andReturn($mRespAdd);
        // $operId = $respOperAdd->getOperationId();
        $mRespAdd->setOperationId($OPER_ID);

        /** === Call and asserts  === */
        $req = new Request\AddToWalletActive();
        $req->setDateApplied($DATE_APPLIED);
        $req->setDatePerformed($DATE_PERFORMED);
        $req->setOperationTypeCode($OPER_TYPE_CODE);
        $req->setTransData($TRANS);
        $req->setAsAmount($AS_AMOUNT);
        $req->setAsCustomerId($AS_CUST_ID);
        $req->setAsRef($AS_REF);
        $resp = $this->call->addToWalletActive($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($OPER_ID, $resp->getOperationId());
    }

}