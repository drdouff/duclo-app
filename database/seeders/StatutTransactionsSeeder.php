<?php

namespace Database\Seeders;

use App\Models\StatutTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatutTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $objStatutTrans=new StatutTransaction();
        $objStatutTrans->name='initie';
        $objStatutTrans->published = 1;
        $objStatutTrans->generateReference();
        $objStatutTrans->generateAlias($objStatutTrans->name);
        $objStatutTrans->save();

        $objStatutTrans=new StatutTransaction();
        $objStatutTrans->name='echoue';
        $objStatutTrans->published = 1;
        $objStatutTrans->generateReference();
        $objStatutTrans->generateAlias($objStatutTrans->name);
        $objStatutTrans->save();

        $objStatutTrans=new StatutTransaction();
        $objStatutTrans->name='reussie';
        $objStatutTrans->published = 1;
        $objStatutTrans->generateReference();
        $objStatutTrans->generateAlias($objStatutTrans->name);
        $objStatutTrans->save();
    }
}
