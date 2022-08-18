<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RequestableDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(RequestableTableSeeder::class);
        $this->call(CreateFormTableSeeder::class);
    }
}
