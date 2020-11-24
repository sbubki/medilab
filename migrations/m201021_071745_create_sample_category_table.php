<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sample_category}}`.
 */
class m201021_071745_create_sample_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = '{{%sample_category}}';
        $this->createTable($table, [
            'id' => $this->primaryKey(),
            // data
            'name' => $this->string(64),
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = '{{%sample_category}}';
        $this->dropTable($table);
    }
}
