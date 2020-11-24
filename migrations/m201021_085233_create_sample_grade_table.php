<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sample_grade}}`.
 */
class m201021_085233_create_sample_grade_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // main table

        $table = '{{%sample_grade}}';
        $this->createTable($table, [
            'id' => $this->primaryKey(),
            // data
            'substance_id' => $this->integer(),
            'sort_order' => $this->integer()->defaultValue(1000),
            'name' => $this->string(40),
            'preparation_cost' => $this->integer()->defaultValue(null),
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

        $this->createIndex('i_sample_grade_substance', $table, ['substance_id']);
        $this->addForeignKey('fk_sample_grade_substance', $table, 'substance_id', '{{%sample_substance}}', 'id');

        // sub table

        $subtable = '{{%sample_grade_pricing}}';
        $this->createTable($subtable, [
            'id' => $this->primaryKey(),
            // data
            'grade_id' => $this->integer(),
            'test_kind_id' => $this->integer(),
            'cost' => $this->integer(),
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

        $this->createIndex('i_sample_grade_pricing_grade', $subtable, ['grade_id']);
        $this->addForeignKey('fk_sample_grade_pricing_grade', $subtable, 'grade_id', $table, 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // subtable

        $subtable = '{{%sample_grade_pricing}}';

        $this->dropForeignKey('fk_sample_grade_pricing_grade', $subtable);
        $this->dropIndex('i_sample_grade_pricing_grade', $subtable);

        $this->dropTable($subtable);

        // main table

        $table = '{{%sample_grade}}';

        $this->dropForeignKey('fk_sample_grade_substance', $table);
        $this->dropIndex('i_sample_grade_substance', $table);

        $this->dropTable($table);
    }
}
