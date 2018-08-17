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
    /** @var \Praxigento\Wallet\Setup\UpgradeSchema\A\V010\Upgrade */
    private $aV010Upgrade;
    /** @var \Praxigento\Core\App\Setup\Dem\Tool */
    private $toolDem;

    public function __construct(
        \Praxigento\Core\App\Setup\Dem\Tool $toolDem,
        \Praxigento\Wallet\Setup\UpgradeSchema\A\V010\Upgrade $aV010Upgrade
    ) {
        $this->toolDem = $toolDem;
        $this->aV010Upgrade = $aV010Upgrade;
    }

    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        if ($version == Cfg::MOD_VERSION_0_1_0) {
            $this->aV010Upgrade->exec($setup);
        }
        $setup->endSetup();
    }
}