<?php

use Illuminate\Database\Seeder;
use Gwaps4nlp\Core\Models\Role;

class RoleSuperAdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'label' => 'Super-Administrator',
            'slug' => 'super-admin'
        ]);      
    }
  
}
