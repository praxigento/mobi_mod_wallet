<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOperation;
use Praxigento\Wallet\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallData extends \Praxigento\Core\Setup\Data\Base
{
    private function _addAccountingAssetsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeAsset::ENTITY_NAME),
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
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::ATTR_CODE, TypeOperation::ATTR_NOTE],
            [
                [
                    Cfg::CODE_TYPE_OPER_WALLET_TRANSFER,
                    'Transfer WALLET_ACTIVE asset between customers accounts (or customer & representative accounts).'
                ],
                [
                    Cfg::CODE_TYPE_OPER_WALLET_SALE,
                    'Pay by WALLET_ACTIVE asset for sale order.'
                ]
            ]
        );
    }

    protected function _setup()
    {
        $this->_addAccountingAssetsTypes();
        $this->_addAccountingOperationsTypes();
    }
}