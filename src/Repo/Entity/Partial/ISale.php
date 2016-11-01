<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Entity\Partial;

interface ISale
    extends \Praxigento\Core\Repo\IEntity
{

    /**
     * @param array|int|string $id
     * @return \Praxigento\Wallet\Data\Entity\Partial\Sale|false
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id);


}