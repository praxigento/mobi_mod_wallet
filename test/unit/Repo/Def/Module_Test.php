<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Def;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRepoAcc;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Module */
    private $repo;

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Test is deprecated after M1 & M2 merge is done.');
        $this->mConn = $this->_mockDba();
        $this->mDba = $this->_mockResourceConnection($this->mConn);
        $this->mRepoGeneric = $this->_mockRepoGeneric($this->mDba);
        $this->mRepoAcc = $this->_mock(\Praxigento\Accounting\Repo\IModule::class);
        $this->repo = new Module(
            $this->mRepoGeneric,
            $this->mRepoAcc
        );
    }

    public function test_getTypeAssetIdByCode()
    {
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