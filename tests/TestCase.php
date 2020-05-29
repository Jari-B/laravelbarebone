<?php

namespace Tests;

use App\Models\Users\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $globalUser;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --seed --env=testing');

        $this->enableForeignKeys();
        $this->globalUser = factory(User::class)->create();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:reset --env=testing');
        parent::tearDown();
    }

    /**
     * Clears Laravel Cache.
     */
    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear'];
        foreach ($commands as $command) {
            \Illuminate\Support\Facades\Artisan::call($command);
        }
    }

    /**
     * Enables foreign keys.
     *
     * @return void
     */
    public function enableForeignKeys()
    {
        if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            DB::statement(DB::raw('PRAGMA foreign_keys=1'));
        }
    }
}
