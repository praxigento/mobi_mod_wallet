<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Entity\Partial\Def;

class Quote
    extends \Praxigento\Core\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Wallet\Data\Entity\Partial\Quote::class);
    }

    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Wallet\Data\Entity\Partial\Quote|false
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    public function updateById($id, $data)
    {
        $result = parent::updateById($id, $data);
        return $result;
    }

}