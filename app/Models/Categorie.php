<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Helpers\CustFunc;

class Categorie extends Model
{
    use HasFactory;

     //protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','icon','image','published','ref','alias'];
    protected $table='categories';
    protected $primaryKey='id';


    public function produits(){
        return $this->hasMany('App\Models\Produit');
    }

    public function sous_categories(){
        return $this->hasMany('App\Models\Categorie');
    }

    public function categorie_parent(){
        return $this->belongsTo('App\Models\Categorie','categorie_id');
    }

 
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
            while (Categorie::where('ref',$token)->first() instanceof Categorie);
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
            }while(Categorie::where('alias',$alias)->first() instanceof Categorie);
            $this->attributes['alias'] = $alias;
        }
    }
}
