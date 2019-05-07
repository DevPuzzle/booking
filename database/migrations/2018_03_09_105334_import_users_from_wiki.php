<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class ImportUsersFromWiki extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('app.import_wiki_users')) {
            User::truncate();
            $now = Carbon::now('utc')->toDateTimeString();
            $wiki_csv_export = fopen(env('WIKI_CSV_PATH'), 'r');
            $wiki_data = [];
            while (($data = fgetcsv($wiki_csv_export)) !== FALSE) {
                $role = 'guide';
                if ($data[1] == 'admin') {
                    $role = 'administrator';
                } elseif ($data[1] == 'genmanager') {
                    $role = 'mngr';
                }
                $wiki_data [] = [
                    'name' => $data[4],
                    'username' => $data[1],
                    'email' => $data[2],
                    'role' => $role,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            fclose($wiki_csv_export);
            User::insert($wiki_data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        User::truncate();

    }
}
