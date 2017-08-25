<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Service\Operation;

use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Accounting\Service\Account\Response\Get as AccountGetResponse;
use Praxigento\Accounting\Service\Account\Response\GetRepresentative as GetRepresentativeResponse;
use Praxigento\Accounting\Service\Operation\Response\Add as OperationAddResponse;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
    /** @var  \Mockery\MockInterface */
    private $mCallAccount;
    /** @var  \Mockery\MockInterface */
    private $mCallOper;
    /** @var  \Mockery\MockInterface */
    private $mRepoMod;
    /** @var  \Mockery\MockInterface */
    private $mToolDate;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOper = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Wallet\Repo\IModule::class);
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
            $this->mToolDate,
            $this->mCallAccount,
            $this->mCallOper,
            $this->mRepoMod
        );
    }

    public function test_addToWalletActive()
    {
        /** === Test Data === */
        $dateApplied = '2016-02-24 10:12:23';
        $datePerformed = '2016-02-25 12:10:23';
        $operTypeCode = 'code';
        $asAmount = 'amount';
        $asCustId = 'custId';
        $asRef = 'ref';
        $assetTypeId = 2;
        $custIdRepres = 10;
        $operId = 16;
        $trans = [
            [$asCustId => $custIdRepres, $asAmount => -32.23, $asRef => 45],
            [$asCustId => 41, $asAmount => 32.23, $asRef => 54]
        ];
        /** === Setup Mocks === */
        // $assetTypeId = $this->_repoMod->getTypeAssetIdByCode(Config::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $this->mRepoMod
            ->shouldReceive('getTypeAssetIdByCode')
            ->andReturn($assetTypeId);
        // function _getAccountRepresentativeId($assetTypeId)
        // $respAccRepres = $this->_callAccount->getRepresentative($reqAccRepres);
        $mResp = new GetRepresentativeResponse();
        $this->mCallAccount
            ->shouldReceive('getRepresentative')
            ->andReturn($mResp);
        $mResp->set(Account::ATTR_CUST_ID, $custIdRepres);
        // $respGetAccount = $this->_callAccount->get($reqGetAccount);
        $mResGet = new AccountGetResponse();
        $this->mCallAccount
            ->shouldReceive('get')
            ->andReturn($mResGet);
        // $accId = $respGetAccount->get(Account::ATTR_ID);
        $mResGet->set(Account::ATTR_ID, 54);
        // $respOperAdd = $this->_callOper->add($reqOperAdd);
        $mRespAdd = new OperationAddResponse();
        $this->mCallOper
            ->shouldReceive('add')
            ->andReturn($mRespAdd);
        // $operId = $respOperAdd->getOperationId();
        $mRespAdd->setOperationId($operId);
        /** === Call and asserts  === */
        $req = new Request\AddToWalletActive();
        $req->setDateApplied($dateApplied);
        $req->setDatePerformed($datePerformed);
        $req->setOperationTypeCode($operTypeCode);
        $req->setTransData($trans);
        $req->setAsAmount($asAmount);
        $req->setAsCustomerId($asCustId);
        $req->setAsRef($asRef);
        $resp = $this->obj->addToWalletActive($req);
        $this->assertTrue($resp->isSucceed());
        $this->assertEquals($operId, $resp->getOperationId());
    }

}