<?php

namespace App\Models;

use App\Helpers\CustFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Produit extends Model
{
    use HasFactory;
    //protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id','categorie_id','name','description','published','ref','alias','max_price','min_price'];

    /*public function images()
    {
        return $this->hasMany(Image::class);
    }*/
    
    public function paniers(){
        return $this->hasMany('App\Models\Panier');
    }
   
   
    public function images(){
        return $this->hasMany('App\Models\Produit_img');
    }
   
    
    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

  
    public function categorie(){
        return $this->belongsTo('App\Models\Categorie','categorie_id');
    }
    public function marque(){
        return $this->belongsTo('App\Models\Marque','marque_id');
    }
   
    public function produit_stocks(){
        return $this->hasMany('App\Models\Produit_stock');
    }
   
    public function commandes(){
        return $this->belongsToMany('App\Models\commande','paniers','commande_id','commande_id');
    }
    protected $table='produits';
    protected $primaryKey='id';


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [

    ];

 //To generate a 15 character reference
    public function generateReference(){

        if(empty($this->attributes['ref'])){
            do{
                $token = CustFunc::getToken(Config::get('constants.values.reference'));
            }
            while (Produit::where('ref',$token)->first() instanceof Produit);
            $this->attributes['ref'] = $token;

            return true;
        }
        return false;
    }

    //To generate an alias for the object based on the name of that object.
    public function generateAlias($name){
        $append = Config::get('constants.values.zero');
        if(empty($this->attributes['alias'])){
            do{
                if($append == Config::get('constants.values.zero')){
                    $alias = CustFunc::toAscii($name);
                }else{
                    $alias = CustFunc::toAscii($name)."-".$append;
                }
                $append += Config::get('constants.values.one');
            }while(Produit::where('alias',$alias)->first() instanceof Produit);
            $this->attributes['alias'] = $alias;
        }
    }
}
