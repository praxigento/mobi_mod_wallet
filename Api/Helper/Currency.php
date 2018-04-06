<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Api\Helper;

/**
 * Convert WALLET currency to store view currency and vice versa.
 */
interface Currency
{
    /**
     * Store currency code (USD).
     *
     * @param int $storeId
     * @return string
     */
    public function getStoreCurrency($storeId);

    /**
     * Wallet currency code (EUR).
     *
     * @return string
     */
    public function getWalletCurrency();

    /**
     * Convert store displayed currency to wallet stored currency (UI to DB).
     *
     * @param float $amount
     * @param int $storeId
     * @return float
     */
    public function storeToWallet($amount, $storeId);

    /**
     * Convert wallet stored currency to store displayed currency (DB to UI).
     *
     * @param float $amount
     * @param int $storeId
     * @return float
     */
    public function walletToStore($amount, $storeId);
}