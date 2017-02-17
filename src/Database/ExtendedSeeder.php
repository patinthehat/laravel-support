<?php
namespace LaravelSupport\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class ExtendedSeeder extends Seeder
{

    /**
     * Store whether foreign key checks have been disabled
     * @var boolean
     */
    protected $foreignKeyCheckDisabled = false;

    /**
     * Store the db connection type
     * @var string
     */
    protected $connection = "";

    public function __construct()
    {
        $this->foreignKeyCheckDisabled = false;
        $this->connection = strtolower(env('DB_CONNECTION'));
    }

    /**
     * Checks to see if the current connection is a mysql connection
     * @return boolean
     */
    protected function isMySqlConnection()
    {
        return ($this->connection == 'mysql');
    }

    /**
     * Checks to see if the current connection is an sqlite connection
     * @return boolean
     */
    protected function isSqliteConnection()
    {
        return ($this->connection == 'sqlite');
    }

    /**
     * Disable foreign key checks
     * @return void
     */
    protected function disableForeignKeyChecks()
    {
        if ($this->isMySqlConnection()) {
            $this->foreignKeyCheckDisabled = true;
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($this->isSqliteConnection()) {
            $this->foreignKeyCheckDisabled = true;
            DB::statement('PRAGMA foreign_keys = OFF;');
        }
    }

    /**
     * Enable foreign key checks
     * @return void
     */
    protected function enableForeignKeyChecks()
    {
        if ($this->isMySqlConnection()) {
            $this->foreignKeyCheckDisabled = false;
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($this->isSqliteConnection()) {
            $this->foreignKeyCheckDisabled = false;
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    /**
     * Deletes all data from a table.
     *
     * @param string $tableName
     * @return boolean
     */
    protected function deleteAllTableEntries($tableName)
    {
        if (trim($tableName) == '')
            return false;

        if ($this->isMySqlConnection()) {
            // MySQL
            DB::table($tableName)->truncate();
        } elseif ($this->isSqliteConnection()) {
            // SQLite
            DB::statement('DELETE FROM ' . $tableName);
        } else {
            // PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE ' . $tableName . ' CASCADE');
        }

        return true;
    }

    /**
     * Initializes the seeder.
     *
     * @param string $tableName
     * @param boolean $disableForeignKeyChecks
     * @param boolean $deleteAllTableEntries
     * @return void
     */
    public function init($tableName, $disableForeignKeyChecks = true, $deleteAllTableEntries = true)
    {
        if (strlen(trim($tableName)) == 0)
            throw new \Exception('Table name is required when initializing the ExtendedSeeder.');

        if ($disableForeignKeyChecks)
            $this->disableForeignKeyChecks();

        if ($deleteAllTableEntries)
            $this->deleteAllTableEntries($tableName);
    }

    /**
     * Clean up the seeder instance before destruction.
     *
     * @return void
     */
    public function cleanup()
    {
        if ($this->foreignKeyCheckDisabled)
            $this->enableForeignKeyChecks();
    }
}