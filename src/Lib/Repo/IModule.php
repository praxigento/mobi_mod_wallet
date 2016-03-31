<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Lib\Repo;

interface IModule  extends \Praxigento\Core\Lib\Repo\IModule {
    /**
     *  Decorator for \Praxigento\Accounting\Lib\Repo\IModule::getTypeAssetIdByCode
     *
     * @param string $assetTypeCode
     *
     * @return int
     */
    public function getTypeAssetIdByCode($assetTypeCode);
}