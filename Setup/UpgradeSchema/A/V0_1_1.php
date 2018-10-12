<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Setup\UpgradeSchema\A;

use Praxigento\Accounting\Repo\Data\Transaction as EAccTran;
use Praxigento\Wallet\Repo\Data\Partial\Quote as EPartQuote;
use Praxigento\Wallet\Repo\Data\Partial\Sale as EPartSale;

class V0_1_1
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Praxigento\Core\Data $demPackage
     */
    public function exec($setup, $demPackage = null)
    {
        $this->upgradeTblQuote($setup);
        $this->upgradeTblSale($setup);
    }

    /**
     * Upgrade table "prxgt_wallet_partial_quote". Add currencies fields.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function upgradeTblQuote($setup)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable(EPartQuote::ENTITY_NAME);

        /* add columns */
        $conn->addColumn(
            $table,
            EPartQuote::A_BASE_CURRENCY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 3,
                'nullable' => false,
                'after' => EPartQuote::A_BASE_PARTIAL_AMOUNT,
                'comment' => 'Currency code for base amount (stock/warehouse currency).'
            ]
        );
        $conn->addColumn(
            $table,
            EPartQuote::A_CURRENCY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 3,
                'nullable' => false,
                'after' => EPartQuote::A_PARTIAL_AMOUNT,
                'comment' => 'Currency code for amount (payment??? currency).'
            ]
        );
    }

    /**
     * Upgrade table "prxgt_wallet_partial_sale".
     * Add currencies fields and transaction reference.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function upgradeTblSale($setup)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable(EPartSale::ENTITY_NAME);

        /* add columns */
        $conn->addColumn(
            $table,
            EPartSale::A_TRANS_REF,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => null,
                'unsigned' => true,
                'nullable' => false,
                'after' => EPartSale::A_SALE_ORDER_REF,
                'comment' => 'Transaction Reference.'
            ]
        );
        $conn->addColumn(
            $table,
            EPartSale::A_BASE_CURRENCY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 3,
                'nullable' => false,
                'after' => EPartSale::A_BASE_PARTIAL_AMOUNT,
                'comment' => 'Currency code for base amount (stock/warehouse currency).'
            ]
        );
        $conn->addColumn(
            $table,
            EPartSale::A_CURRENCY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 3,
                'nullable' => false,
                'after' => EPartSale::A_PARTIAL_AMOUNT,
                'comment' => 'Currency code for amount (payment??? currency).'
            ]
        );

        /* add foreign keys */
        $tbl = $setup->getTable(EPartSale::ENTITY_NAME);
        $tblRef = $setup->getTable(EAccTran::ENTITY_NAME);
        $fkName = $setup->getFkName($tbl, $tblRef, EPartSale::A_TRANS_REF, EAccTran::A_ID);
        $conn->addForeignKey($fkName, $tbl, EPartSale::A_TRANS_REF, $tblRef, EAccTran::A_ID);
    }
}