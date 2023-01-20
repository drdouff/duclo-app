<?php

namespace App\Models;

use App\Helpers\CustFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Transaction extends Model
{
    use HasFactory;
    //protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['commande_id','mode_id','statut_trans_id','montant','paie_phone','taspay_transaction'
        ,'total_payment','image','published','ref','alias'];


    public function commande(){
        return $this->belongsTo('App\Models\Commande','commande_id');
    }
    public function mode(){
        return $this->belongsTo('App\Models\Mode','mode_id');
    }
    public function statut_trans(){
        return $this->belongsTo('App\Models\Statut_transaction','statut_trans_id');
    }

    protected $table='transactions';
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
            while (Transaction::where('ref',$token)->first() instanceof Transaction);
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
            }while(Transaction::where('alias',$alias)->first() instanceof Transaction);
            $this->attributes['alias'] = $alias;
        }
    }
}
