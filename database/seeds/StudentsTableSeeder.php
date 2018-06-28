<?php

use Illuminate\Database\Seeder;

use App\Student;
// use Factory;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(App\Student::class, 20)->create()->each(function($st){
        //     $st->posts()->save(factory(App\Student::class)->make());
        // });
        factory(App\Student::class, 100)->create();
    }
}
