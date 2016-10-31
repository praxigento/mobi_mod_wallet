<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Repo\Entity\Partial;

interface IQuote
    extends \Praxigento\Core\Repo\IEntity
{
    /**
     * @param int $id
     * @return \Praxigento\Wallet\Data\Entity\Partial\Quote|false
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id);
}