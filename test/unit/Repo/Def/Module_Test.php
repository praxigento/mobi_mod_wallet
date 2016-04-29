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
    private $mRepoTypeAsset;
    /** @var  Module */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mConn = $this->_mockConn();
        $mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoTypeAsset = $this->_mock(\Praxigento\Accounting\Repo\Entity\Type\IAsset::class);
        $this->obj = new Module(
            $mResource,
            $this->mRepoTypeAsset
        );
    }

    public function test_getTypeAssetIdByCode()
    {
        /** === Test Data === */
        $CODE = 'code';
        $ID = 2;
        /** === Setup Mocks === */
        // $result = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        $this->mRepoTypeAsset
            ->shouldReceive('getIdByCode')
            ->andReturn($ID);
        /** === Call and asserts  === */
        $resp = $this->obj->getTypeAssetIdByCode($CODE);
        $this->assertEquals($ID, $resp);
    }

}