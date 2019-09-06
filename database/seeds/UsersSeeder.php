<?php

use Illuminate\Database\Seeder;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        App\Users::truncate();

        $faker = \Faker\Factory::create();
        $password = Hash::make('Woooo');

        // And now, let's create a few Users in our database:
        for ($i = 0; $i < 1; $i++) {
            App\Users::create([
                'username' => $faker->sentence,
                'email' => $faker->sentence,
                'password' => $password
            ]);
        }
    }
}
