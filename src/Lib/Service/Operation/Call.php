<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Lib\Service\Operation;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as AccountGetRequest;
use Praxigento\Accounting\Lib\Service\Account\Request\GetRepresentative as AccountGetRepresentativeRequest;
use Praxigento\Accounting\Lib\Service\Operation\Request\Add as OperationAddRequest;
use Praxigento\Wallet\Config;
use Praxigento\Wallet\Lib\Service\IOperation;
use Praxigento\Wallet\Lib\Service\Operation\Request;
use Praxigento\Wallet\Lib\Service\Operation\Response;

class Call extends \Praxigento\Core\Service\Base\Call implements IOperation
{

    /** @var  \Praxigento\Accounting\Lib\Service\IAccount */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Lib\Service\IOperation */
    protected $_callOper;
    /** @var \Praxigento\Wallet\Lib\Repo\IModule */
    protected $_repoMod;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Accounting\Lib\Service\IAccount $callAccount,
        \Praxigento\Accounting\Lib\Service\IOperation $callOper,
        \Praxigento\Wallet\Lib\Repo\IModule $repoMod
    ) {
        parent::__construct($logger);
        $this->_toolDate = $toolDate;
        $this->_callAccount = $callAccount;
        $this->_callOper = $callOper;
        $this->_repoMod = $repoMod;
    }

    /**
     * Get account ID for representative customer.
     *
     * @param int $assetTypeId
     *
     * @return int
     */
    private function _getRepresentativeAccId($assetTypeId)
    {
        $req = new AccountGetRepresentativeRequest();
        $req->setAssetTypeId($assetTypeId);
        $resp = $this->_callAccount->getRepresentative($req);
        $result = $resp->getData(Account::ATTR_ID);
        return $result;
    }

    public function addToWalletActive(Request\AddToWalletActive $req)
    {
        $result = new Response\AddToWalletActive();
        $dateApplied = $req->getDateApplied();
        $datePerformed = $req->getDatePerformed();
        $operTypeCode = $req->getOperationTypeCode();
        $transData = $req->getTransData();
        $asAmount = $req->getAsAmount();
        $asCustId = $req->getAsCustomerId();
        $asRef = $req->getAsRef();
        $this->_logger->info("'Add to Wallet Active' operation is started.");
        /* prepare additional data */
        $datePerformed = is_null($datePerformed) ? $this->_toolDate->getUtcNowForDb() : $datePerformed;
        $dateApplied = is_null($dateApplied) ? $datePerformed : $dateApplied;
        /* get asset type ID */
        $assetTypeId = $this->_repoMod->getTypeAssetIdByCode(Config::CODE_TYPE_ASSET_WALLET_ACTIVE);
        /* get representative customer ID */
        $represAccId = $this->_getRepresentativeAccId($assetTypeId);
        /* save operation */
        $reqOperAdd = new OperationAddRequest();
        $reqOperAdd->setOperationTypeCode($operTypeCode);
        $reqOperAdd->setDatePerformed($datePerformed);
        $reqOperAdd->setAsTransRef($asRef);
        $trans = [];
        $reqGetAccount = new AccountGetRequest();
        $reqGetAccount->setCreateNewAccountIfMissed();
        $reqGetAccount->setAssetTypeId($assetTypeId);
        foreach ($transData as $item) {
            $custId = $item[$asCustId];
            $value = $item[$asAmount];
            if ($value > 0) {
                /* get WALLET_ACTIVE account ID for customer */
                $reqGetAccount->setCustomerId($custId);
                $respGetAccount = $this->_callAccount->get($reqGetAccount);
                $accId = $respGetAccount->getData(Account::ATTR_ID);
                $one = [
                    Transaction::ATTR_DEBIT_ACC_ID => $represAccId,
                    Transaction::ATTR_CREDIT_ACC_ID => $accId,
                    Transaction::ATTR_DATE_APPLIED => $dateApplied,
                    Transaction::ATTR_VALUE => $value
                ];
                if (!is_null($asRef) && isset($item[$asRef])) {
                    $one[$asRef] = $item[$asRef];
                }
                $trans[] = $one;
                $this->_logger->debug("Transaction ($value) for customer #$custId (acc #$accId) is added to operation with type '$operTypeCode'.");
            } else {
                $this->_logger->debug("Transaction for customer #$custId is '$value'. Transaction is not included in operation with type '$operTypeCode'.");
            }
        }
        $reqOperAdd->setTransactions($trans);
        $respOperAdd = $this->_callOper->add($reqOperAdd);
        $operId = $respOperAdd->getOperationId();
        $this->_logger->debug("New operation (type id '$operTypeCode') is added with id=$operId .");
        $result->setData($respOperAdd->getData());
        $result->markSucceed();
        $this->_logger->info("'Add to Wallet Active' operation is completed.");
        return $result;
    }

}