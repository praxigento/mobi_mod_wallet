<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Service\Operation;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AccountGetRequest;
use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Service\Operation\Request;
use Praxigento\Wallet\Service\Operation\Response;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Wallet\Service\IOperation
{

    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Api\Service\Operation */
    protected $_callOper;
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    protected $_repoEAcc;
    /** @var \Praxigento\Wallet\Repo\Entity\Log\Sale */
    protected $_repoELogSale;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    protected $_repoETypeAsset;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Operation */
    protected $_repoETypeOper;
    /** @var \Praxigento\Core\Api\Helper\Date */
    protected $_toolDate;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Api\Service\Account\Get $callAccount,
        \Praxigento\Accounting\Api\Service\Operation $callOper,
        \Praxigento\Accounting\Repo\Entity\Account $repoEAccount,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoETypeAsset,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoETypeOper,
        \Praxigento\Wallet\Repo\Entity\Log\Sale $repoELogSale
    ) {
        parent::__construct($logger, $manObj);
        $this->_toolDate = $hlpDate;
        $this->_callAccount = $callAccount;
        $this->_callOper = $callOper;
        $this->_repoEAcc = $repoEAccount;
        $this->_repoETypeAsset = $repoETypeAsset;
        $this->_repoETypeOper = $repoETypeOper;
        $this->_repoELogSale = $repoELogSale;
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
        $req = new AccountGetRequest();
        $req->setIsRepresentative(TRUE);
        $req->setAssetTypeId($assetTypeId);
        $resp = $this->_callAccount->exec($req);
        $result = $resp->get(Account::ATTR_ID);
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
        $this->logger->info("'Add to Wallet Active' operation is started.");
        /* prepare additional data */
        $datePerformed = is_null($datePerformed) ? $this->_toolDate->getUtcNowForDb() : $datePerformed;
        $dateApplied = is_null($dateApplied) ? $datePerformed : $dateApplied;
        /* get asset type ID */
        $assetTypeId = $this->_repoETypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_WALLET);
        /* get representative customer ID */
        $represAccId = $this->_getRepresentativeAccId($assetTypeId);
        /* save operation */
        $reqOperAdd = new \Praxigento\Accounting\Api\Service\Operation\Request();
        $reqOperAdd->setOperationTypeCode($operTypeCode);
        $reqOperAdd->setDatePerformed($datePerformed);
        $reqOperAdd->setAsTransRef($asRef);
        $trans = [];
        $reqGetAccount = new AccountGetRequest();
        $reqGetAccount->setAssetTypeId($assetTypeId);
        foreach ($transData as $item) {
            $custId = $item[$asCustId];
            $value = $item[$asAmount];
            if ($value > 0) {
                /* get WALLET_ACTIVE account ID for customer */
                $reqGetAccount->setCustomerId($custId);
                $respGetAccount = $this->_callAccount->exec($reqGetAccount);
                $accId = $respGetAccount->get(Account::ATTR_ID);
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
                $this->logger->debug("Transaction ($value) for customer #$custId (acc #$accId) is added to operation with type '$operTypeCode'.");
            } else {
                $this->logger->debug("Transaction for customer #$custId is '$value'. Transaction is not included in operation with type '$operTypeCode'.");
            }
        }
        $reqOperAdd->setTransactions($trans);
        $respOperAdd = $this->_callOper->exec($reqOperAdd);
        $operId = $respOperAdd->getOperationId();
        $this->logger->debug("New operation (type id '$operTypeCode') is added with id=$operId .");
        $result->set($respOperAdd->get());
        $result->markSucceed();
        $this->logger->info("'Add to Wallet Active' operation is completed.");
        return $result;
    }

    public function payForSaleOrder(Request\PayForSaleOrder $req)
    {
        $result = new Response\PayForSaleOrder();
        /* extract request params */
        $custId = $req->getCustomerId();
        $value = $req->getBaseAmountToPay();
        $saleOrderId = $req->getOrderId();
        /* collect data */
        $reqGet = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $reqGet->setCustomerId($custId);
        $reqGet->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET);
        $respGet = $this->_callAccount->exec($reqGet);
        $accIdDebit = $respGet->getId();
        $assetTypeId = $respGet->getAssetTypeId();
        $reqGetRepres = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $reqGetRepres->setIsRepresentative(TRUE);
        $reqGetRepres->setAssetTypeId($assetTypeId);
        $respGetRepres = $this->_callAccount->exec($reqGetRepres);
        $accIdCredit = $respGetRepres->getId();
        /* compose transaction data */
        $transaction = new \Praxigento\Accounting\Repo\Entity\Data\Transaction();
        $transaction->setDebitAccId($accIdDebit);
        $transaction->setCreditAccId($accIdCredit);
        $transaction->setValue($value);
        /* create operation using service call */
        $reqAddOper = new \Praxigento\Accounting\Api\Service\Operation\Request();
        $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_SALE);
        $reqAddOper->setTransactions([$transaction]);
        $reqAddOper->setCustomerId($custId);
        $respAddOper = $this->_callOper->exec($reqAddOper);
        $operId = $respAddOper->getOperationId();
        $result->setOperationId($operId);
//        /* log sale order operation */
//        $log = new \Praxigento\Wallet\Repo\Entity\Data\Log\Sale();
//        $log->setOperationRef($operId);
//        $log->setSaleOrderRef($saleOrderId);
        if ($respAddOper->isSucceed()) {
            $result->markSucceed();
        }
        return $result;
    }
}