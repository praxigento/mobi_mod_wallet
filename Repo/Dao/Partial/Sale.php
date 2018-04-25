<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Dao\Partial;

class Sale
    extends \Praxigento\Core\App\Repo\Dao
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, \Praxigento\Wallet\Repo\Data\Partial\Sale::class);
    }

    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param array|int|string $id
     * @return \Praxigento\Wallet\Repo\Data\Partial\Sale|false
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