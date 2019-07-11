<?php
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WantStatusSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('want_status')->insert([
            'id' => 1,
            'status_name' => "active"
        ]);

        DB::table('want_status')->insert([
            'id' => 2,
            'status_name' => "in review",
        ]);

        DB::table('want_status')->insert([
            'id' => 3,
            'status_name' => "in progress",
        ]);

        DB::table('want_status')->insert([
            'id' => 4,
            'status_name' => "complete",
        ]);

        DB::table('want_status')->insert([
            'id' => 5,
            'status_name' => "flagged",
        ]);
    }
}
