<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Setup;

use Praxigento\Wallet\Config as Cfg;

class UpgradeSchema
    implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /** @var \Praxigento\Wallet\Setup\UpgradeSchema\A\V0_1_1 */
    private $v0_1_1;

    public function __construct(
        \Praxigento\Wallet\Setup\UpgradeSchema\A\V0_1_1 $v0_1_1
    ) {
        $this->v0_1_1 = $v0_1_1;
    }

    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        if ($version == Cfg::MOD_VERSION_0_1_0) {
            $this->v0_1_1->exec($setup);
        }
        $setup->endSetup();
    }
}