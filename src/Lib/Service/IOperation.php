<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Service;

use Praxigento\Wallet\Lib\Service\Operation\Request;
use Praxigento\Wallet\Lib\Service\Operation\Response;

interface IOperation {
    /**
     * Add new operation with WALLET_ACTIVE asset.
     *
     * @param Request\AddToWalletActive $req
     *
     * @return Response\AddToWalletActive
     */
    public function addToWalletActive(Request\AddToWalletActive $req);


}