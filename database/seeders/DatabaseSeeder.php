<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PaysSeeder::class,
            RegionsSeeder::class,
            VillesSeeder::class,
            
            RolesSeeder::class,
            UserSeeder::class,
            ModesSeeder::class,
            CategoriesSeeder::class,
            MarquesSeeder::class,
           
            StatutCmdesSeeder::class,
            StatutTransactionsSeeder::class,
            StatutLivraisonsSeeder::class,
           
           
            ProduitsSeeder::class,
            ProduitImgsSeeder::class
           
          
         


        ]);
    }
}
