<?php

namespace LaravelSupport\Database;

use Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;
use LaravelSupport\Support\TableColumnReference;

abstract class ExtendedMigration extends Migration
{
    /**
     * Foreign key definitions.
     * Valid definitions:
     *    table.column => [table2.column2, 'cascade'|'restrict'|null, ...],
     *    table.column => table2.column2,
     *    table.column_id => null,
     * @var array
     */
    protected $foreignKeyDefinitions = [];

    /**
     * Automatically create foreign keys that are defined in $foreignKeyDefinitions.
     * @var boolean
     */
    protected $autoCreateDefinedKeys = true;

    /**
     * Automatically delete foreign keys that are defined in $foreignKeyDefinitions.
     * @var boolean
     */
    protected $autoDeleteDefinedKeys = true;


    /**
     * Create all foreign keys that are defined in the $foreignKeyDefintions
     * array, and support array, string and null values.
     * @return void
     */
    public function createForeignKeysUsingDefinitions()
    {
        foreach($this->foreignKeyDefintions as $localRef => $data) {
            if (is_array($data))
                $this->createForeignKey($localRef, $data[0], $data[1], $data[2]);

            if (is_string($data))
                $this->createForeignKey($localRef, $data, null, null);

            if (is_null($data)) {
                $local = TableColumnReference::parse($localRef);
                $foreign = TableColumnReference::guessForeignReference($local->column);
                $this->createForeignKey($local, $foreign, null, null);
            }
        }
    }

    /**
     * Deletes all foreign keys that are defined in the $foreignKeyDefintions array.
     * @return void
     */
    public function deleteForeignKeysUsingDefinitions()
    {
        foreach($this->foreignKeyDefintions as $localRef => $data) {
            $this->deleteForeignKey($localRef);
        }
    }

    /**
     * Create a foreign key constraint. References are strings in the form "tablename.columnname",
     * or are instances of TableColumnReference.
     * @param string|TableColumnReference $localReference
     * @param string|TableColumnReference $foreignReference
     * @param string|null $onDelete
     * @param string|null $onUpdate
     * @return boolean
     */
    public function createForeignKey($localReference, $foreignReference, $onDelete = null, $onUpdate = null)
    {
        if ($localReference instanceof TableColumnReference) {
            $local = $localReference;
        } else {
            $local = TableColumnReference::parse($localReference);
        }

        if ($foreignReference instanceof TableColumnReference) {
            $foreign = $foreignReference;
        } else {
            $foreign = TableColumnReference::parse($foreignReference);
        }

        if ($local->isValid() && $foreign->isValid()) {
            Schema::table($local->table, function(Blueprint $table) use ($local,$foreign,$onDelete,$onUpdate) {
                $fk = $table->foreign($local->column)->references($foreign->column)->on($foreign->table);
                if (!is_null($onDelete))
                    $fk->onDelete($onDelete);
                if (!is_null($onUpdate))
                    $fk->onUpdate($onUpdate);
            });
            return true;
        }
        return false;
    }

    /**
     * Delete a foreign key constraint. References are strings in the form "tablename.columnname",
     * or are instances of TableColumnReference.
     * @param string $reference
     * @return boolean
     */
    public function deleteForeignKey($reference)
    {
        if ($reference instanceof TableColumnReference) {
            $ref = $reference;
        } else {
            $ref = TableColumnReference::parse($reference);
        }

        if ($ref->isValid()) {
            Schema::table($ref->table, function(Blueprint $table) use ($ref) {
                $table->dropForeign($ref->table."_".$ref->column."_foreign");
            });
            return true;
        }
        return false;
    }


    public function up()
    {
        if ($this->autoCreateDefinedKeys) {
            $this->createForeignKeysUsingDefinitions();
        }
    }

    public function down()
    {
        if ($this->autoDeleteDefinedKeys) {
            $this->deleteForeignKeysUsingDefinitions();
        }
    }
}
