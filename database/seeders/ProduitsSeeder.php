<?php

namespace Database\Seeders;

use App\Models\ProduitImg;
use App\Models\Produit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Produiteeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Categorie : accessoires mode
         * Sous-categorie : femmes
         */
        $objProduit = new Produit();//1
        $objProduit->name = 'Jean slim Jack & Jones en coton stretch';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 9;
        $objProduit->type_Produit_id = 2;
        $objProduit->categorie_id = 30;
        $objProduit->marque_id = 8;
        $objProduit->max_price = '5000';
        $objProduit->min_price = '2000';
        $objProduit->description = '60% Coton, 28% Polyester, 10% Coton biologique, 2% Élasthanne - Bleu Denim - Coupe slim- Taille intermédiaire- Fermeture boutons + zip - Jean 5 poches';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/jean-slim-jack-et-jones-en-coton-bleu.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Jean slim Jack & Jones en coton stretch");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        /**
         * Categorie : accessoire de mode
         * Sous-categorie : hommes(tennis)
         */
        $objProduit = new Produit();//2
        $objProduit->name = 'Baskets Vans Old Skool à lacets plats en cuir noir';
        $objProduit->user_id = 5;
        $objProduit->boutique_id = 7;
        $objProduit->type_Produit_id = 2;
        //$objProduit->categorie_id = 7;
        $objProduit->categorie_id = 31;
        $objProduit->marque_id = 6;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2000';
        $objProduit->description = 'Noir - Dessus/tige: cuir et textile - Semelle extérieure: autres matériaux - Type de talon : plat - Fermeture blanc cassé - Empeigne noire et blanche avec motif damier';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/baskets-vans-old-school.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Baskets Vans Old Skool à lacets plats en cuir noir");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        /**
         * Categorie : acceesoire de mode
         * Sous-categorie : hommes (Chemises)
         */
        $objProduit = new Produit();//3
        $objProduit->name = 'Chemise coupe cintrée Tommy Jeans en coton stretch bleu ciel';
        $objProduit->user_id = 5;
        $objProduit->boutique_id = 7;
        $objProduit->type_Produit_id = 2;
        //$objProduit->categorie_id = 7;
        $objProduit->categorie_id = 31;
        $objProduit->marque_id = 9;
        $objProduit->max_price = '4000';
        $objProduit->min_price = '1500';
        $objProduit->description = 'Coton (98%), élasthanne (2%) - Bleu ciel - Coupe cintrée - Col américain à boutons blanc nacré cousus au fil blanc - Manches longues - Boutons blanc nacré cousus au fil blanc';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/chemise_coupe_cintree_tommy_hilfiger_en_coton_stretch_bleu_ciel.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chemise coupe cintrée Tommy Jeans en coton stretch bleu ciel");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        $objProduit = new Produit();//4
        $objProduit->name = 'Chemise manches courtes col français Levi\'s en coton mélangé bleu indigo';
        $objProduit->user_id = 5;
        $objProduit->boutique_id = 7;
        $objProduit->type_Produit_id = 2;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->marque_id = 7;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->description = 'Autres matériaux, Textile, Autres matériaux - Noir - Unies à gros trèfles contrastant - Première anatomique';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = "storage/Produit/chemise-manches-courtes-col-français-Levi's-en-coton-bleu-indigo.jpg";
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chemise manches courtes col français Levi's en coton mélangé bleu indigo");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        /**
         * Categorie : Accessoires de mode
         * Sous-categorie : hommes (babouche)
         */
        $objProduit = new Produit();//5
        $objProduit->name = 'Claquettes adidas noires à première forme anatomique';
        $objProduit->user_id = 5;
        $objProduit->boutique_id = 7;
        $objProduit->type_Produit_id = 2;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 31;
        $objProduit->marque_id = 5;
        $objProduit->max_price = '2000';
        $objProduit->min_price = '1000';
        $objProduit->description = 'Autres matériaux, Textile, Autres matériaux - Noir - Unies à gros trèfles contrastant - Première anatomique';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/claquettes-adidas-noires-a-premiere-forme-anatomique.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Claquettes adidas noires à première forme anatomique");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }


        /**
         * Categorie : Accessoires de mode
         * Sous-categorie : femmes
         */
        $objProduit = new Produit();//6
        $objProduit->name = 'Chapeaux de soleil chapeau de plage d\'été pour femmes';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 2;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/chapeaux-de-soleil-chapeau-de-plage-d-ete-pour-fem.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chapeaux de soleil chapeau de plage d'été pour femmes");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        $objProduit = new Produit();//7
        $objProduit->name = 'Echarpe femme foulard longue lace poudre châles 180x95cm';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 9;
        $objProduit->type_Produit_id = 2;
        $objProduit->marque_id = 5;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->max_price = '1500';
        $objProduit->min_price = '1000';
        $objProduit->description = 'ECHARPE - FOULARD - Gris foncé + rose clair.';
        $objProduit->published = 1;
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/echarpe-femme-foulard-longue-lace-poudre-chales-18.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Echarpe femme foulard longue lace poudre châles 180x95cm");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }




        /**
         * Categorie : accessoire
         * Sous-categorie : enfants (robe)
         */
        $objProduit = new Produit();//8
        $objProduit->name = 'Robe';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 8;
        $objProduit->type_Produit_id = 2;
        $objProduit->marque_id = 5;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 32;
        $objProduit->description = 'Robe pour enfant pur coton. Disponible pour enfant de 0 à 14 ans..';
        $objProduit->published = 1;
        $objProduit->max_price = '3500';
        $objProduit->min_price = '1500';        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/robe-child.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Robe");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        $objProduit = new Produit();//9
        $objProduit->name = 'Chapeau pour enfant';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 9;
        $objProduit->type_Produit_id = 2;
        $objProduit->marque_id = 5;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 32;
        $objProduit->description = 'Chapeau pour enfant pur coton.';
        $objProduit->published = 1;
        $objProduit->max_price = '2000';
        $objProduit->min_price = '1000';        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/chapeau-pour-enfant.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chapeau pour enfant");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }




        /**
         * Categorie : electronique
         * Sous-categorie : ordinateur
         */
        $objProduit = new Produit();//10
        $objProduit->name = 'Ordinateur';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 8;
        $objProduit->type_Produit_id = 2;
       // $objProduit->categorie_id = 4;
        $objProduit->categorie_id = 21;
        $objProduit->marque_id = 10;
        $objProduit->description = 'ordinateur de bureau avec equipements';
        $objProduit->published = 1;
        $objProduit->max_price = '3500';
        $objProduit->min_price = '1500';        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/23.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:ordinateur");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        $objProduit = new Produit();
        $objProduit->name = 'appareil audiovisuel';//11
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 8;
        $objProduit->type_Produit_id = 1;
        //$objProduit->categorie_id = 7;
        $objProduit->categorie_id = 23;
        $objProduit->marque_id = 10;
        $objProduit->description = 'appareil audiovisuel.';
        $objProduit->published = 1;
        $objProduit->max_price = '2000';
        $objProduit->min_price = '1000';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/14.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:appareil audiovisuel");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }

        $objProduit = new Produit();//12
        $objProduit->name = 'telephone';
        $objProduit->user_id = 6;
        $objProduit->boutique_id = 9;
        $objProduit->type_Produit_id = 1;
        //$objProduit->categorie_id = 7;
        $objProduit->categorie_id = 24;
        $objProduit->marque_id = 10;
        $objProduit->description = 'telephone.';
        $objProduit->published = 1;
        $objProduit->max_price = '2000';
        $objProduit->min_price = '1000';        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/2.png';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:telephone");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }



        /*********Produit de la boutique huawei******* */

        $objProduit = new Produit();//13
        $objProduit->name = 'Chapeaux de soleil ';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/chapeau2.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chapeaux de soleil chapeau de plage d'été pour femmes");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }


        $objProduit = new Produit();//14
        $objProduit->name = 'porte cle';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '1000';
        $objProduit->min_price = '500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/portecle.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:porte cle");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }




        $objProduit = new Produit();//15
        $objProduit->name = 'Pot de fleur';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/potdefleur.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:pot de fleur");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }




        $objProduit = new Produit();//16
        $objProduit->name = 'tableau de decoration';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/tableau.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:tableau de deco");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }



        $objProduit = new Produit();//17
        $objProduit->name = 'collier';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/collier.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:collier");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }



        $objProduit = new Produit();//18
        $objProduit->name = 'diamants';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/diamants.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:diamants");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }



        $objProduit = new Produit();//19
        $objProduit->name = 'bague';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/bague.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:bague");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }



        $objProduit = new Produit();//20
        $objProduit->name = 'farine';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/farine.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:farine");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }


        $objProduit = new Produit();//21
        $objProduit->name = 'tomate';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/tomate.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:tomate");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }


        $objProduit = new Produit();//22
        $objProduit->name = 'champagne';
        $objProduit->user_id = 6;
        $objProduit->marque_id = 5;
        $objProduit->boutique_id = 9 ;
        $objProduit->type_Produit_id = 1;
       // $objProduit->categorie_id = 7;
        $objProduit->categorie_id = 30;
        $objProduit->description = '100% haute qualité de papier naturel tissé de paille, léger, doux et ventilé. Taille unique, la plupart des gens, conception à large bord.';
        $objProduit->published = 1;
        $objProduit->max_price = '3000';
        $objProduit->min_price = '2500';
        $objProduit->generateReference();
        $objProduit->generateAlias($objProduit->name);
        if($objProduit->save()){
            $objImage = new ProduitImg();
            $objImage->name = 'storage/Produit/champagne.jpg';
            $objImage->Produit_id = $objProduit->id;
            $objImage->published = 1;
            $objImage->generateReference();
            $objImage->generateAlias($objImage->name);
            $objImage->Produit()->associate($objProduit);
            if(!$objImage->save())
            {
                $this->command->info("Fail Seeded Image:Chapeaux de soleil chapeau de plage d'été pour femmes");
            }else{
                $this->command->info("Seeded Image: ". $objImage->name);
            }

        }


    }
}
