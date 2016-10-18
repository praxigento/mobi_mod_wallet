<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Entity\Log\Def;


class Sale
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Wallet\Repo\Entity\Log\ISale
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Wallet\Data\Entity\Log\Sale::class);
    }

}