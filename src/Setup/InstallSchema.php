<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Praxigento\Wallet\Data\Entity\Log\Sale as ELogSale;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Log / Sale */
        $entityAlias = ELogSale::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Log/entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

    }


}