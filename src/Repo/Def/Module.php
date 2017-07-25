<?php
/**
 * Facade for current module for dependent modules repos.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Def;

use Praxigento\Core\Repo\Def\Db;
use Praxigento\Wallet\Repo\IModule;

class Module extends Db implements IModule
{
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Def\Asset */
    protected $_repoTypeAsset;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Entity\Type\Def\Asset $repoTypeAsset

    ) {
        parent::__construct($resource);
        $this->_repoTypeAsset = $repoTypeAsset;
    }

    /** @inheritdoc */
    public function getTypeAssetIdByCode($assetTypeCode)
    {
        $result = $this->_repoTypeAsset->getIdByCode($assetTypeCode);
        return $result;
    }

}