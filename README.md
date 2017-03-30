## LaravelSupport
---

Various support classes for Laravel 5+.

---
####Installation

Install with composer:

`composer require patinthehat/laravel-support`


---
#### Classes

#####ExtendedSeeder
ExtendedSeeder is an extended version of the `Seeder` class and provides easy foreign key check enable/disable and table truncating.  It also allows for easy access to [Faker](https://github.com/fzaninotto/Faker).

######Methods
 - `getFaker()` - returns an instance of Faker\Factory (see [Faker](https://github.com/fzaninotto/Faker)).
 - `init($tableName, $disableForeignKeyChecks = true, $deleteAllTableEntries = true)` - call at the beginning of `run()`.
 - `cleanup()` - call at the end of `run()`.
 

######Sample Usage:

```php
use App\Support\ExtendedSeeder;
use App\User;

class UserTableSeeder extends ExtendedSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //disable foreign key checks, delete all existing table entries
        $this->init('users', true, true); 
        //seed the table
        $text = $this->getFaker()->text();
        $this->cleanup();
    }
}
```

---
#####ExtendedMigration
ExtendedMigration is an extended version of the `Migration` class and provides easy foreign key creation/deletion.

######Methods
 --
 
######Sample Usage:

```php
use LaravelSupport\Database\ExtendedMigration;

class CreateForeignKeys extends ExtendedMigration
{
    //define the FKs
    protected $foreignKeyDefinitions = [
        'info.author_id' => ['authors.id', 'cascade', 'cascade'],
        'info.book_id' => ['books.id', null, null],
        'table2.test_id' => 'tests.id',
        'myinfo.publisher_id' => null, //creates FK on 'publishers.id'
    ];
    
    //automatically create/delete FKs
    protected $autoCreateDefinedKeys = true;
    protected $autoDeleteDefinedKeys = true;
}
```

---
#### License
LaravelSupport is available under the [MIT License](LICENSE).