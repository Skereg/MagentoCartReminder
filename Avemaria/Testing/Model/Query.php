<?php
namespace Avemaria\Testing\Model;

use Magento\Framework\App\ResourceConnection;

class Query
{
    protected ResourceConnection $resourceConnection;
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function selectQuery()
    {
        $tableName = $this->resourceConnection->getTableName('quote');
        $connection = $this->resourceConnection->getConnection();
        $is_active = '1';
        $items_count = '0';
        $select = $connection->select()
            ->from(
                ['c' => $tableName],
                ['updated_at', 'customer_email', 'customer_firstname', 'entity_id']
            )
            ->where(
                'c.is_active = :is_active'
            )->where(
                'c.items_count > :items_count'
            );
        $bind = ['is_active' => $is_active, 'items_count' => $items_count];
        return $connection->fetchAll($select, $bind);
    }

    public function getItems($id)
    {
        $tableName = $this->resourceConnection->getTableName('quote_item');
        $connection = $this->resourceConnection->getConnection();
        $quote_id = $id;
        $base_price = '0';
        $select = $connection->select()
            ->from(
                ['c' => $tableName],
                ['name']
            )
            ->where(
                'c.quote_id = :quote_id'
            )->where(
                'c.base_price > :base_price'
            );
        $bind = ['quote_id' => $quote_id, 'base_price' => $base_price];
        return $connection->fetchAll($select, $bind);
    }
}
