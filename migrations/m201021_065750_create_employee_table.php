<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee}}`.
 */
class m201021_065750_create_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = '{{%employee}}';
        $this->createTable($table, [
            'id' => $this->primaryKey(),
            // data
            'nick_name' => $this->string(16),
            'full_name' => $this->string(),
            'position' => $this->string(32)->defaultValue(null),
            'user_id' => $this->integer()->defaultValue(null),
            'sort_order' => $this->integer()->defaultValue(1000),
            // moderation
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            // soft-delete
            'is_deleted' => $this->boolean()->defaultValue(false),
            'deleted_at' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
        ]);

        $this->createIndex('i_employee_sorting', $table, ['sort_order']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = '{{%employee}}';
        $this->dropIndex('i_employee_sorting', $table);
        $this->dropTable($table);
    }
}
