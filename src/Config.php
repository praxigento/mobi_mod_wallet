<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet;

class Config
{
    const CODE_TYPE_ASSET_WALLET_ACTIVE = 'WALLET_ACTIVE';
    const CODE_TYPE_ASSET_WALLET_HOLD = 'WALLET_HOLD';
    /** Transfer WALLET_ACTIVE asset between customers accounts (or customer & representative accounts) */
    const CODE_TYPE_OPER_WALLET_TRANSFER = 'WALLET_TRANSFER';
    const MODULE = 'Praxigento_Wallet';
}