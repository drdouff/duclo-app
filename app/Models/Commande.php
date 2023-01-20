<?php

namespace App\Models;

use App\Helpers\CustFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Commande extends Model
{
    use HasFactory;
    //protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_client_id','ville_id','statut_cmd_id','statut_livraison_id'
      ,'lieu_livraison','montant_total','signature_client','published','ref','alias'];

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
    public function paniers(){
        return $this->hasMany('App\Models\panier');
    }

    public function produits(){
        return $this->belongsToMany('App\Models\Produit','paniers','commande_id','produit_id');
    }
  

    public function user_client(){
        return $this->belongsTo('App\Models\User','user_client_id');
    }
  
    public function ville(){
        return $this->belongsTo('App\Models\Ville','ville_id');
    }
    public function statut_cmd(){
        return $this->belongsTo('App\Models\Statut_cmde','statut_cmd_id');
    }
    public function statut_livraison(){
        return $this->belongsTo('App\Models\Statut_livraison','statut_livraison_id');
    }
    public function user_vendeur(){
        return $this->belongsTo('App\Models\User','user_vendeur_id');
    }
    protected $table='commandes';
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
            while (Commande::where('ref',$token)->first() instanceof Commande);
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
            }while(Commande::where('alias',$alias)->first() instanceof Commande);
            $this->attributes['alias'] = $alias;
        }
    }
}
