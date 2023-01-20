<?php

namespace Database\Seeders;

use App\Models\StatutLivraison;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatutLivraisonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $objStatutLivraison=new StatutLivraison();//1
        $objStatutLivraison->name='En attente';
        $objStatutLivraison->published = 1;
        $objStatutLivraison->generateReference();
        $objStatutLivraison->generateAlias($objStatutLivraison->name);
        $objStatutLivraison->save();

        $objStatutLivraison=new StatutLivraison();//2
        $objStatutLivraison->name='annule';
        $objStatutLivraison->published = 1;
        $objStatutLivraison->generateReference();
        $objStatutLivraison->generateAlias($objStatutLivraison->name);
        $objStatutLivraison->save();

        $objStatutLivraison=new StatutLivraison();//3
        $objStatutLivraison->name='Livre';
        $objStatutLivraison->published = 1;
        $objStatutLivraison->generateReference();
        $objStatutLivraison->generateAlias($objStatutLivraison->name);
        $objStatutLivraison->save();
    }
}
