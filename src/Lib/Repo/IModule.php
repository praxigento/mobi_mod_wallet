<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Lib\Repo;

interface IModule
{
    /**
     *  Decorator for \Praxigento\Accounting\Repo\IModule::getTypeAssetIdByCode
     *
     * @param string $assetTypeCode
     *
     * @return int
     */
    public function getTypeAssetIdByCode($assetTypeCode);
}