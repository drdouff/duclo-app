<?php

namespace Database\Seeders;

use App\Models\StatutCmde;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatutCmdesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $objStatutCmd=new StatutCmde();//1
        $objStatutCmd->name='paiement en attente';
        $objStatutCmd->published = 1;
        $objStatutCmd->generateReference();
        $objStatutCmd->generateAlias($objStatutCmd->name);
        $objStatutCmd->save();

        $objStatutCmd=new StatutCmde();//2
        $objStatutCmd->name='paiement echoue';
        $objStatutCmd->published = 1;
        $objStatutCmd->generateReference();
        $objStatutCmd->generateAlias($objStatutCmd->name);
        $objStatutCmd->save();

        $objStatutCmd=new StatutCmde();//3
        $objStatutCmd->name='paye';
        $objStatutCmd->published = 1;
        $objStatutCmd->generateReference();
        $objStatutCmd->generateAlias($objStatutCmd->name);
        $objStatutCmd->save();
    }
}
