<?php
/**
 * Facade for current module for dependent modules repos.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Repo\Def;

use Praxigento\Core\Repo\Def\Base;
use Praxigento\Wallet\Lib\Repo\IModule;

class Module extends Base implements IModule
{
    /** @var  \Praxigento\Accounting\Lib\Repo\IModule */
    protected $_repoAccounting;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Lib\Repo\IModule $repoAccounting

    ) {
        parent::__construct($resource);
        $this->_repoAccounting = $repoAccounting;
    }

    public function getTypeAssetIdByCode($assetTypeCode)
    {
        $result = $this->_repoAccounting->getTypeAssetIdByCode($assetTypeCode);
        return $result;
    }

}