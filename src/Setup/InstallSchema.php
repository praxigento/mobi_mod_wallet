<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Praxigento\Wallet\Data\Entity\Log\Sale as ELogSale;
use Praxigento\Wallet\Data\Entity\Partial\Quote as EPartialQuote;
use Praxigento\Wallet\Data\Entity\Partial\Sale as EPartialSale;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Wallet';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Partial / Sale */
        $entityAlias = EPartialQuote::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Partial/entity/SaleQuote');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Partial / Sale */
        $entityAlias = EPartialSale::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Partial/entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Log / Sale */
        $entityAlias = ELogSale::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/Log/entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

    }


}