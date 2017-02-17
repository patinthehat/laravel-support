## LaravelSupport
---

Various support classes for Laravel 5+.

---

#### Classes

#####ExtendedSeeder
ExtendedSeeder is an extended version of the `Seeder` class and provides easy foreign key check enable/disable and table truncating.

Sample Usage:

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
        $this->cleanup();
    }
}
```

---
#### License
LaravelSupport is available under the [MIT License](LICENSE).