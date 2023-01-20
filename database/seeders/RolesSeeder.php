<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {

        $objRole = new Role();
        $objRole->name = 'Administrateur';//1
        $objRole->published   = 1;
        $objRole->generateReference();
        $objRole->generateAlias($objRole->name);
        if(!$objRole->save())
        {
            $this->command->info("Fail Seeded Role: Administrateur");
        }else{
            $this->command->info("Seeded Role: ". $objRole->name);
        }

         
        $objRole2 = new Role();
       $objRole2->name = 'Client';//3
       $objRole2->published   = 1;
       $objRole2->generateReference();
       $objRole2->generateAlias($objRole2->name);
       if(!$objRole2->save())
       {
           $this->command->info("Fail Seeded Role: Client");
       }else{
           $this->command->info("Seeded Role: ". $objRole2->name);
       }

        
        

        
    }
}
