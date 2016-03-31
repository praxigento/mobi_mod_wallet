<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Repo\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Module_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {
    /** @var  \Mockery\MockInterface */
    private $mRepoBasic;
    /** @var  \Mockery\MockInterface */
    private $mRepoAcc;
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  Module */
    private $repo;

    protected function setUp() {
        parent::setUp();
        $this->mConn = $this->_mockConnection();
        $this->mDba = $this->_mockDba($this->mConn);
        $this->mRepoBasic = $this->_mockRepoBasic($this->mDba);
        $this->mRepoAcc = $this->_mock(\Praxigento\Accounting\Lib\Repo\IModule::class);
        $this->repo = new Module(
            $this->mRepoBasic,
            $this->mRepoAcc
        );
    }

    public function test_getTypeAssetIdByCode() {
        /** === Test Data === */
        $CODE = 'code';
        $ID = 2;

        /** === Setup Mocks === */

        // $result = $this->_repoAccounting->getTypeAssetIdByCode($assetTypeCode);
        $this->mRepoAcc
            ->shouldReceive('getTypeAssetIdByCode')
            ->andReturn($ID);

        /** === Call and asserts  === */
        $resp = $this->repo->getTypeAssetIdByCode($CODE);
        $this->assertEquals($ID, $resp);
    }

}