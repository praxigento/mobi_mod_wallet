<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet;

class Config
{
    const CODE_PAYMENT_METHOD = 'praxigento_wallet';
    /** Code for quote/order total to save part of eWallet payment */
    const CODE_TOTAL_PARTIAL = \Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE;
    const CODE_TYPE_ASSET_WALLET_ACTIVE = 'WALLET_ACTIVE';
    const CODE_TYPE_ASSET_WALLET_HOLD = 'WALLET_HOLD';
    /** Pay by WALLET_ACTIVE asset for sale order. */
    const CODE_TYPE_OPER_WALLET_SALE = 'WALLET_SALE';
    /** Transfer WALLET_ACTIVE asset between customers accounts (or customer & representative accounts). */
    const CODE_TYPE_OPER_WALLET_TRANSFER = 'WALLET_TRANSFER';
    const MODULE = 'Praxigento_Wallet';
}