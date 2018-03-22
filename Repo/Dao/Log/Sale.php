<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Dao\Log;

class Sale
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Wallet\Repo\Data\Log\Sale::class);
    }

}