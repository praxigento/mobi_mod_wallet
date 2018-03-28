<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Setup;

use Praxigento\Accounting\Repo\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Data\Type\Operation as TypeOperation;
use Praxigento\Wallet\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallData extends \Praxigento\Core\App\Setup\Data\Base
{
    protected function _setup()
    {
        $this->addAccountingAssetsTypes();
        $this->addAccountingOperationsTypes();
    }

    private function addAccountingAssetsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeAsset::ENTITY_NAME),
            [
                TypeAsset::A_CODE,
                TypeAsset::A_NOTE,
                TypeAsset::A_IS_TRANSFERABLE
            ], [
                [
                    Cfg::CODE_TYPE_ASSET_WALLET,
                    'Active funds in a customer wallet. Can be transferred to other customer, to external payment system or used to sale smth.',
                    true
                ]
            ]
        );
    }

    private function addAccountingOperationsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::A_CODE, TypeOperation::A_NOTE],
            [
                [
                    Cfg::CODE_TYPE_OPER_WALLET_TRANSFER,
                    'Transfer WALLET_ACTIVE asset between customers accounts (or customer & system accounts).'
                ],
                [
                    Cfg::CODE_TYPE_OPER_WALLET_SALE,
                    'Pay by WALLET_ACTIVE asset for sale order.'
                ]
            ]
        );
    }
}