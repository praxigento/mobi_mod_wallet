<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Service;

use Praxigento\Wallet\Service\Operation\Request;
use Praxigento\Wallet\Service\Operation\Response;

interface IOperation
{
    /**
     * Add new operation with WALLET_ACTIVE asset.
     *
     * @param Request\AddToWalletActive $req
     *
     * @return Response\AddToWalletActive
     */
    public function addToWalletActive(Request\AddToWalletActive $req);

    /**
     * Create eWallet payment for sale order.
     *
     * @param Request\PayForSaleOrder $req
     * @return Response\PayForSaleOrder
     */
    public function payForSaleOrder(Request\PayForSaleOrder $req);


}