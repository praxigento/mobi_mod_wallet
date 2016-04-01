<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOperation;
use Praxigento\Wallet\Config as Cfg;

class InstallData extends \Praxigento\Core\Setup\Data\Base
{
    private function _addAccountingAssetsTypes()
    {
        $this->_getConn()->insertArray(
            $this->_getTableName(TypeAsset::ENTITY_NAME),
            [TypeAsset::ATTR_CODE, TypeAsset::ATTR_NOTE],
            [
                [
                    Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE,
                    'Active funds in a customer wallet. Can be transferred to other customer, to external payment system or used to sale smth.'
                ],
                [
                    Cfg::CODE_TYPE_ASSET_WALLET_HOLD,
                    '\'On hold\' funds in a customer wallet. Can be converted to active funds only.'
                ]
            ]
        );
    }

    private function _addAccountingOperationsTypes()
    {
        $this->_getConn()->insertArray(
            $this->_getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::ATTR_CODE, TypeOperation::ATTR_NOTE],
            [
                [
                    Cfg::CODE_TYPE_OPER_WALLET_TRANSFER,
                    'Transfer WALLET_ACTIVE asset between customers accounts (or customer & representative accounts).'
                ]
            ]
        );
    }

    protected function _setup(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->_addAccountingAssetsTypes();
        $this->_addAccountingOperationsTypes();
    }
}