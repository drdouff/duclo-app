<?php

namespace App\Models;

use App\Helpers\CustFunc;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Ville extends Model
{
    use HasFactory;

   //  //protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'published','region_id'];

    public function users(){
        return $this->hasMany('App\Models\User');
    }
    public function commandes(){
        return $this->hasMany('App\Models\Commande');
    }
    public function region(){
        return $this->belongsTo('App\Models\Region','region_id');
    }
    protected $table='villes';
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
            while (Ville::where('ref',$token)->first() instanceof Ville);
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
            }while(Ville::where('alias',$alias)->first() instanceof Ville);
            $this->attributes['alias'] = $alias;
        }
    }
}
