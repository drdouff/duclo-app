<?php

namespace Database\Seeders;

use App\Models\Mode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $objMode=new Mode();//1
        $objMode->name='orange';
        $objMode->logo='e-shop-api/public/img/logo-Mode-paiement/orangeMoney.png';
        $objMode->published = 1;
        $objMode->generateReference();
        $objMode->generateAlias($objMode->name);
        $objMode->save();

        $objMode=new Mode();//1
        $objMode->name='mtn';
        $objMode->logo='e-shop-api/public/img/logo-Mode-paiement/mtnMoney.png';
        $objMode->published = 1;
        $objMode->generateReference();
        $objMode->generateAlias($objMode->name);
        $objMode->save();
    
        $objMode=new Mode();//1
        $objMode->name='En espece';
        $objMode->published = 1;
        $objMode->generateReference();
        $objMode->generateAlias($objMode->name);
        $objMode->save();
    }
}
