<?php

namespace Database\Seeders;

use App\Models\Categorie_Boutique;
use App\Models\Boutique_cat_type;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         /******************
        User :Client 1
         ***************************/
        $objUser = new User();//1
        $objUser->firstname = 'Client1';
        $objUser->lastname = 'mama';
        $objUser->email = 'client1@domain.cm';
        $objUser->phone = '+237697847117';
        $objUser->password = Hash::make('12345678');
        $objUser->lien_whatsapp='https://wa.me/message/MPNWXJ27S2Y4H1';
        $objUser->role_id = 2;
        $objUser->ville_id = 3;
        $objUser->published   = 1;
        $objUser->generateReference();
        $objUser->generateAlias($objUser->firstname);
        if(!$objUser->save())
        {
            $this->command->info("Fail Seeded user: Client1");
        }else{
            $this->command->info("Seeded user: ". $objUser->firstname);
        }

        /******************
        User :Client 2
         ***************************/
        $objUser = new User();
        $objUser->firstname = 'Client2';//2
        $objUser->lastname = 'mimi';
        $objUser->email = 'client2@domain.cm';
        $objUser->phone = '+237697812272';//Rufus
        $objUser->password = Hash::make('12345678');
        $objUser->lien_whatsapp='https://wa.me/message/MPNWXJ27S2Y4H1';
        $objUser->role_id = 2;
        $objUser->ville_id = 3;
        $objUser->published   = 1;
        $objUser->generateReference();
        $objUser->generateAlias($objUser->firstname);
        if(!$objUser->save())
        {
            $this->command->info("Fail Seeded user: Client2");
        }else{
            $this->command->info("Seeded user: ". $objUser->firstname);
        }

        $objUser = new User();
        $objUser->firstname = 'Administrateur';//3
        $objUser->lastname = 'Administrateur';
        $objUser->email = 'tegomovarmand@gmail.com';
        $objUser->phone = '+237690296578';
        $objUser->password = Hash::make('12345678');
        $objUser->role_id = 1;
        $objUser->ville_id = 1;
        $objUser->published   = 1;
        $objUser->generateReference();
        $objUser->generateAlias($objUser->firstname);
        if(!$objUser->save())
        {
            $this->command->info("Fail Seeded user: Admin");
        }else{
            $this->command->info("Seeded user: ". $objUser->firstname);
        }

        



    }
}
