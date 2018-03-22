<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Setup;

use Praxigento\Wallet\Repo\Data\Log\Sale as ELogSale;
use Praxigento\Wallet\Repo\Data\Partial\Quote as EPartialQuote;
use Praxigento\Wallet\Repo\Data\Partial\Sale as EPartialSale;

class InstallSchema extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Wallet';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Partial / Sale */
        $demEntity = $demPackage->get('package/Partial/entity/SaleQuote');
        $this->toolDem->createEntity(EPartialQuote::ENTITY_NAME, $demEntity);

        /* Partial / Sale */
        $demEntity = $demPackage->get('package/Partial/entity/SaleOrder');
        $this->toolDem->createEntity(EPartialSale::ENTITY_NAME, $demEntity);

        /* Log / Sale */
        $demEntity = $demPackage->get('package/Log/entity/SaleOrder');
        $this->toolDem->createEntity(ELogSale::ENTITY_NAME, $demEntity);

    }


}