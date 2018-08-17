<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet;

class Config
    extends \Praxigento\Accounting\Config
{
    /** Code for quote/order/invoice totals to save part of eWallet payment */
    const CODE_TOTAL_PARTIAL = 'prxgt_wallet_partial';
    const CODE_TOTAL_PARTIAL_AMOUNT = self::CODE_TOTAL_PARTIAL . '_amount';
    const CODE_TOTAL_PARTIAL_AMOUNT_BASE = 'base_' . self::CODE_TOTAL_PARTIAL_AMOUNT;
    const CODE_TYPE_ASSET_WALLET = 'WALLET';
    /** Pay by WALLET asset for sale order. */
    const CODE_TYPE_OPER_WALLET_SALE = 'WALLET_SALE';
    /** Transfer WALLET asset between customers accounts (or customer & representative accounts). */
    const CODE_TYPE_OPER_WALLET_TRANSFER = 'WALLET_TRANSFER';
    const MODULE = 'Praxigento_Wallet';
    const MOD_VERSION_0_1_0 = '0.1.0';
}