<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sample_substance}}`.
 */
class m201021_072253_create_sample_substance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // main table

        $table = '{{%sample_substance}}';
        $this->createTable($table, [
            'id' => $this->primaryKey(),
            // data
            'category_id' => $this->integer(),
            'name' => $this->string(40),
            'preparation_service' => $this->boolean()->defaultValue(true),
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

        $this->createIndex('i_sample_substance_category', $table, ['category_id']);
        $this->addForeignKey('fk_sample_substance_category', $table, 'category_id', '{{%sample_category}}', 'id');

        // sub table

        $subtable = '{{%sample_substance_test}}';
        $this->createTable($subtable, [
            'id' => $this->primaryKey(),
            // data
            'substance_id' => $this->integer(),
            'test_kind_id' => $this->smallInteger(),
        ]);

        $this->createIndex('i_sample_substance_test_substance', $subtable, ['substance_id']);
        $this->addForeignKey('fk_sample_substance_test_substance', $subtable, 'substance_id', $table, 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // subtable

        $subtable = '{{%sample_substance_test}}';

        $this->dropForeignKey('fk_sample_substance_test_substance', $subtable);
        $this->dropIndex('i_sample_substance_test_substance', $subtable);

        $this->dropTable($subtable);

        // main table

        $table = '{{%sample_substance}}';

        $this->dropForeignKey('fk_sample_substance_category', $table);
        $this->dropIndex('i_sample_substance_category', $table);

        $this->dropTable($table);
    }
}
