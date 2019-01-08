<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Setup;

use Praxigento\Wallet\Config as Cfg;

class UpgradeData
    implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /** @var \Praxigento\Wallet\Setup\UpgradeData\A\V0_1_2 */
    private $v0_1_2;

    public function __construct(
        \Praxigento\Wallet\Setup\UpgradeData\A\V0_1_2 $v0_1_2
    ) {
        $this->v0_1_2 = $v0_1_2;
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        if ($version == Cfg::MOD_VERSION_0_1_1) {
            /* Add operation type for refunds. */
            $this->v0_1_2->exec($setup);
        }
        $setup->endSetup();
    }


}