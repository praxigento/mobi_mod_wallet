<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{

    protected function _setup(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Wallet';
        $decoded = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
    }

}