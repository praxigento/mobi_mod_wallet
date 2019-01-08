<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Setup\UpgradeData\A;


use Praxigento\Accounting\Repo\Data\Type\Operation as TypeOperation;
use Praxigento\Wallet\Config as Cfg;

/**
 * Add operation type for refunds.
 */
class V0_1_2
{
    private function addAccountingOperationsTypes(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable(TypeOperation::ENTITY_NAME);

        $conn->insertArray(
            $table,
            [TypeOperation::A_CODE, TypeOperation::A_NOTE],
            [
                [
                    Cfg::CODE_TYPE_OPER_WALLET_REFUND,
                    'Refund e-wallet funds on sale order cancellation.'
                ]
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function exec($setup)
    {
        $this->addAccountingOperationsTypes($setup);
    }
}