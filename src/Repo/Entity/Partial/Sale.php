<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Entity\Partial;

class Sale
    extends \Praxigento\Core\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Wallet\Repo\Entity\Data\Partial\Sale::class);
    }

    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param array|int|string $id
     * @return \Praxigento\Wallet\Repo\Entity\Data\Partial\Sale|false
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