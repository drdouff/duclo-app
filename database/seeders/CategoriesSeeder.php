<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $objCategorie=new Categorie();//1
        $objCategorie->name='Objets de collection et art';
        $objCategorie->icon= 'floor_lamp';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
  
     

        $objCategorie=new Categorie();//2
        $objCategorie->name='Maison & Jardin';
        $objCategorie->icon='house';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       

        $objCategorie=new Categorie();//3
        $objCategorie->name='Articles de sport';
        $objCategorie->icon='sports_soccer';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       
    

        $objCategorie=new Categorie();//4
        $objCategorie->name='Electronique';
        $objCategorie->icon='desktop_windows';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       

        $objCategorie=new Categorie();//5
        $objCategorie->name='Pièces et accessoires automobiles';
        $objCategorie->icon='build';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       

        $objCategorie=new Categorie();//6
        $objCategorie->name='Jouets et loisirs';
        $objCategorie->icon='child_care';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
        

        
        $objCategorie=new Categorie();//7
        $objCategorie->name='Accessoire de Mode';
        $objCategorie->icon='watch';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
        
        $objCategorie=new Categorie();//8
        $objCategorie->name='Instruments de musique et équipement';
        $objCategorie->icon='music_note';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       

        $objCategorie=new Categorie();//9
        $objCategorie->name='Autres catégories';
        $objCategorie->icon='palette';
        $objCategorie->published = 1;
        $objCategorie->generateReference();
        $objCategorie->generateAlias($objCategorie->name);
        $objCategorie->save();
       


        /*
         * Categorie : Objets de collection et art
         */

        $objSousCategorie=new Categorie();//10
        $objSousCategorie->name='Objets de collection';
        $objSousCategorie->icon='group_work';
        $objSousCategorie->Categorie_id=1;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//11
        $objSousCategorie->name='Antiques';
        $objSousCategorie->icon='mode_fan';
        $objSousCategorie->Categorie_id=1;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//12 
        $objSousCategorie->name='Sports memorabilia';
        $objSousCategorie->icon='sports_basketball';
       $objSousCategorie->Categorie_id=1;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//13
        $objSousCategorie->name='Art';
        $objSousCategorie->icon='mode_fan';
         $objSousCategorie->Categorie_id=1;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();



        /*
         * Categorie : Maison & Jardin
         */

        $objSousCategorie=new Categorie();//14
        $objSousCategorie->name='Cour, jardin et extérieur';
        $objSousCategorie->icon='yard';
        $objSousCategorie->Categorie_id=2;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//15
        $objSousCategorie->name='Artisanat';
        $objSousCategorie->icon='pie_chart';
        $objSousCategorie->Categorie_id=2;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//16
        $objSousCategorie->name='amélioration de l habitat';
        $objSousCategorie->icon='chalet';
        $objSousCategorie->Categorie_id=2;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//17
        $objSousCategorie->name='Fournitures pour animaux';
        $objSousCategorie->icon='cruelty_free';
        $objSousCategorie->Categorie_id=2;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

      
        
        /*
         * Categorie : Articles de sport
         */

        $objSousCategorie=new Categorie();//18
        $objSousCategorie->name='Sports de plein air';
        $objSousCategorie->icon='stadium';
        $objSousCategorie->Categorie_id=3;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//19
        $objSousCategorie->name='Sports d équipe';
        $objSousCategorie->icon='sports_kabaddi';
        $objSousCategorie->Categorie_id=3;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//20
        $objSousCategorie->name='Exercice et forme physique';
        $objSousCategorie->icon='sports_gymnastics';
        $objSousCategorie->Categorie_id=3;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        
        /*
         * Categorie :Electronique
         */

        $objSousCategorie=new Categorie();//21
        $objSousCategorie->name='Ordinateurs et tablettes ';
        $objSousCategorie->icon='devices';
        $objSousCategorie->Categorie_id=4;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//22
        $objSousCategorie->name='Appareil photo et photo ';
        $objSousCategorie->icon='photo_camera';

        $objSousCategorie->Categorie_id=4;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();


        $objSousCategorie=new Categorie();//23
        $objSousCategorie->name='Télévision, audio et vidéosurveillance';
        $objSousCategorie->icon='camera_outdoor';
        $objSousCategorie->Categorie_id=4;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//24
        $objSousCategorie->name='Telephone';
        $objSousCategorie->icon='phone_iphone';
        $objSousCategorie->Categorie_id=4;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        /*
         * Pièces et accessoires automobiles
         */

        $objSousCategorie=new Categorie();//25
        $objSousCategorie->name='Appareils GPS et de sécurité';
        $objSousCategorie->icon='share_localisation';
        $objSousCategorie->Categorie_id=5;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//26
        $objSousCategorie->name='Pièces et accessoires de trottinette ';
        $objSousCategorie->Categorie_id=5;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        
        /*
         * Jouets et loisirs
         */

        $objSousCategorie=new Categorie();//27
        $objSousCategorie->name='Jouets pour enfants';
        $objSousCategorie->icon='mode_fan';
        $objSousCategorie->Categorie_id=6;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//28
        $objSousCategorie->name='Pièces et accessoires de trottinette ';
        $objSousCategorie->icon='mode_fan';
        $objSousCategorie->Categorie_id=6;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//29
        $objSousCategorie->name='Poupées et ours';
        $objSousCategorie->icon='mode_fan';
        $objSousCategorie->Categorie_id=6;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();


         /*
         * accessoires de modes
         */
        $objSousCategorie=new Categorie();//30
        $objSousCategorie->name='Femmes';
        $objSousCategorie->icon='woman';

        $objSousCategorie->Categorie_id=7;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();
    
        $objSousCategorie=new Categorie();//31
        $objSousCategorie->name='Hommes';
        $objSousCategorie->icon='man';

        $objSousCategorie->Categorie_id=7;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//32
        $objSousCategorie->name='enfants';
        $objSousCategorie->icon='child';

        $objSousCategorie->Categorie_id=7;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        $objSousCategorie=new Categorie();//33
        $objSousCategorie->name='chaussures';
        $objSousCategorie->icon='ice_skating';
        $objSousCategorie->Categorie_id=7;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();



         /*
         * Instruments de musique et équipement
         */
        $objSousCategorie=new Categorie();//34
        $objSousCategorie->name='Piano';
        $objSousCategorie->icon='piano';
        $objSousCategorie->Categorie_id=8;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();


        $objSousCategorie=new Categorie();//35
        $objSousCategorie->name='Équipement audio professionnel';
        $objSousCategorie->icon='headset_mic';
        $objSousCategorie->Categorie_id=8;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();

        /*
         * Autres Categories
         */
        $objSousCategorie=new Categorie();//36
        $objSousCategorie->name='Jeux vidéo et consoles';
        $objSousCategorie->icon='stadia_controller';
        $objSousCategorie->Categorie_id=8;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();


        $objSousCategorie=new Categorie();//37
        $objSousCategorie->name='Santé & Beauté Bébé';
        $objSousCategorie->icon='health_and_safety';
        $objSousCategorie->Categorie_id=8;
        $objSousCategorie->published = 1;
        $objSousCategorie->generateReference();
        $objSousCategorie->generateAlias($objSousCategorie->name);
        $objSousCategorie->save();
    
    }
}
