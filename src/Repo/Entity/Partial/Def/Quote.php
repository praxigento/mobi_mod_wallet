<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Entity\Partial\Def;

class Quote
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Wallet\Repo\Entity\Partial\IQuote
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Wallet\Data\Entity\Partial\Quote::class);
    }

}