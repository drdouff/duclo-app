<?php

namespace App\Http\Controllers;

use App\Mail\AccountMessageCreatedToAdmin;
use App\Mail\customerMail;
use App\Models\Categorie_Boutique;
use App\Models\Boutique_cat_type;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Models\Ville;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersApiController extends Controller
{
    /**Create user */
    public function createUser(Request $request)
    {
        $this->_fnErrorCode = 1;
        $validator = Validator::make($request->all(), [
            'firstname' => 'nullable',
            'lastname' => 'nullable',
            'email' => 'required|email|max:255',
            'phone' => 'required',
            'ville' => 'required',
            'role' => 'required',
            'lien_whatsapp'=>'nullable',
            'vendeur'=> 'nullable',
            'boutique'=> 'nullable',
            'password'=>'required|min:'.Config::get('constants.size.min.password').'|max:'.Config::get('constants.size.max.password'),

        ]);


        if ($validator->fails())
        {
            if (!empty($validator->errors()->all()))
            {
                foreach ($validator->errors()->all() as $error)
                {
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUser = User::where('email', '=', $request->get('email'))->first();
       

        if(!empty($objUser))
        {
            $this->_errorCode               = 3;
            $this->_response['message'][]   = "Le mail est dèjà utilisé";
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUserPhone = User::where('phone', '=', $request->get('phone'))->first();
        if(!empty($objUserPhone)){
            $this->_errorCode = 4;
            $this->_response['message'][] = "Le numero de téléphone existe déjà.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $objUser = Auth::user();
        
        $mail_reponse = "";

        if(!empty($objUser)){

            $objAuthRole = Role::where('id', '=', $objUser->role_id)->first();
            
            if(empty($objAuthRole)){
                $this->_errorCode = 12;
                $this->_response['message'][] = "L'utilisateur n'a pas de rôle.";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            if($request->has('role')){

                $objRole = Role::where('ref', '=', $request->get('role'))->first();
              
                
                if(empty($objRole)){
                    $this->_errorCode = 13;
                    $this->_response['message'][] = "Aucun rôle n'a été renseigné.";
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
                $objVille = Ville::where('id', '=', intval($request->get('ville')))->first();
                if(empty($objVille)){
                    $this->_errorCode = 14;
                    $this->_response['message'][] = "La ville n'existe pas";
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
                $objRoleBoutique = Role::where('alias', '=', 'entreprise')->first();
                $objRoleVendeur = Role::where('alias', '=', 'vendeur')->first();
                $objvendeur=User::where('published',1)->where('role_id', '=', $objRoleVendeur->id)->where('ref','=',$request->get('vendeur'))->first();
                
                if ($request->has('vendeur')) {
                    if(empty($objvendeur)){
                        $this->_errorCode = 15;
                        $this->_response['message'][] = "Le vendeur n'existe pas";
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }
                }
                $objBoutique = User::where('published', 1)->where('role_id', '=', $objRoleBoutique->id)->where('ref', '=', $request->get('boutique'))->first();
               
                if ($request->has('boutique')) {
                    
                    
                    if (empty($objBoutique)) {
                        $this->_errorCode = 16;
                        $this->_response['message'][] = "La boutique n'existe pas";
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }
                }
                
                if(in_array($objAuthRole->alias, array('administrateur','gestionnaire','vendeur','gestionnaire-boutique'))){
                    if(in_array($objRole->alias, array('livreur'))){
                        $user_connect = $objUser;

                        try
                        {

                            $objUser = new User();
                            $objUser->firstname = $request->get('firstname');
                            if($request->has('lastname')) {
                                $objUser->lastname = $request->get('lastname');
                            }
                            $objUser->phone = $request->get('phone');
                            $objUser->email = $request->get('email');

                            $objUser->password = Hash::make($request->get('password'));
                            $objUser->published = 1;
                            $objUser->generateReference();
                            $objUser->generateAlias($request->get('firstname'));
                            //$objUser->generateCode();
                            $objUser->ville()->associate($objVille);
                            $objUser->role()->associate($objRole);
                            $objUser->user()->associate($user_connect);
                            $objUser->vendeur()->associate($objvendeur);
                            $objUser->boutique()->associate($objBoutique);
                          
                            $objUser->save();

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 17;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();

                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }

                    }
                }

                if(in_array($objAuthRole->alias, array('administrateur','gestionnaire','vendeur'))){
                    if(in_array($objRole->alias, array('gestionnaire-boutique'))){
                        $user_connect = $objUser;
                        try{

                            $objUser = new User();
                            $objUser->firstname = $request->get('firstname');
                            if($request->has('lastname')) {
                                $objUser->lastname = $request->get('lastname');
                            }
                            $objUser->phone = $request->get('phone');
                            $objUser->email = $request->get('email');

                            $objUser->password = Hash::make($request->get('password'));
                            $objUser->published = 1;
                            $objUser->generateReference();
                            $objUser->generateAlias($request->get('firstname'));
                            //$objUser->generateCode();
                            $objUser->ville()->associate($objVille);
                            $objUser->role()->associate($objRole);
                            $objUser->user()->associate($user_connect);
                            $objUser->vendeur()->associate($objvendeur);
                            $objUser->boutique()->associate($objBoutique);

                            $objUser->save();

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 18;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();

                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }

                    }




                }
                if(in_array($objAuthRole->alias, array('administrateur','gestionnaire'))) {

                    if(in_array($objRole->alias, array('vendeur'))){
                        $user_connect = $objUser;

                        try{

                            $objUser = new User();
                            $objUser->firstname = $request->get('firstname');
                            if($request->has('lastname')) {
                                $objUser->lastname = $request->get('lastname');
                            }
                            $objUser->phone = $request->get('phone');
                            $objUser->email = $request->get('email');

                            $objUser->password = Hash::make($request->get('password'));
                            $objUser->published = 1;
                            $objUser->generateReference();
                            $objUser->generateAlias($request->get('firstname'));
                            //$objUser->generateCode();
                            $objUser->ville()->associate($objVille);
                            $objUser->role()->associate($objRole);
                            $objUser->user()->associate($user_connect);
                            $objUser->save();

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 19;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();

                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }



                    }

                }
                if(in_array($objAuthRole->alias, array('administrateur'))){
                    if(in_array($objRole->alias, array('gestionnaire','administrateur'))) {
                        $user_connect = $objUser;
                        try {

                            $objUser = new User();
                            $objUser->firstname = $request->get('firstname');
                            if($request->has("lastname")){$objUser->lastname = $request->get('lastname');}
                            $objUser->phone = $request->get('phone');
                            $objUser->email = $request->get('email');
                            $objUser->password = Hash::make($request->get('password'));
                            $objUser->published = 1;
                            $objUser->generateReference();
                            $objUser->generateAlias($request->get('firstname'));
                            //$objUser->generateCode();
                            $objUser->user()->associate($user_connect);
                            $objUser->role()->associate($objRole);
                            $objUser->ville()->associate($objVille);
                            $objUser->save();

                        }catch (Exception $objException){
                            DB::rollback();
                            $this->_errorCode = 20;
                            if(in_array($this->_env, ['local', 'development'])){
                            }
                            $this->_response['message'] = $objException->getMessage();

                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json( $this->_response );
                        }
                    }

                }else{
                    $this->_errorCode = 21;
                    $this->_response['message'][] ="vous nest pas habilite";
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
            }

        }
        //dd($objUser);
        // Commit the queries!
        DB::commit();
        //Format d'affichage de message
        $toReturn = [
            'mail_reponse' => $mail_reponse,
            'objet' => $objUser
        ];

        $this->_response['message'] = 'Votre compte a été créé avec succès!';
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }




    //Detail user
    public function detailUser(Request $request){
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref_user' => 'required'
        ]);

        if ($validator->fails()){
            if (!empty($validator->errors()->all())){
                foreach ($validator->errors()->all() as $error){
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
        $objRole= Role::where('alias', '=', 'vendeur')->first();

        $objuser = User::where('ref', $request->get('ref_user'))->first();
        if(empty($objuser)) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "L'utilisateur n'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try {
            if($objRole->id==$objuser->role_id){
                $objRoleBoutiq = Role::where('alias', '=', 'entreprise')->first();
                $objDetailUserVendeur = User::where('ref','=',$objuser->ref)->with('role','ville','user')->first();

                $boutiques=User::where('published',1)->where('role_id',$objRoleBoutiq->id)->where('vendeur_id',$objuser->id)
                    ->get();
                $objDetailUser=[
                    $objDetailUserVendeur,
                    "boutiques"=>$boutiques

                ];

            }else{
                $objDetailUser = User::where('ref','=',$objuser->ref)->with('role','ville','user','vendeur','boutiques_vendeur')->first();

            }

        } catch (Exception $objException) {
            $this->_errorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::commit();
        $toReturn = [
            'objet' => $objDetailUser
        ];

        $this->_response['message'] = "Detail de l'utilisateur ";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }



    //liste des administrateurs du systemes
    public function getAllAdmin(){
        $this->_fnErrorCode = 1;


        $objUser=Auth::user();

        // role de l'utilisateur connecté
        $objAuthRole = $objUser->role;
        if (!in_array($objAuthRole->alias,array("administrateur"))) {
            $this->_errorCode = 2;
            $this->_response['message'][] = "Vous ne disposez pas du rôle necessaire!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }



        $objRole = Role::where('alias', '=', 'administrateur')->first();
        if(empty($objRole)){
            $this->_errorCode = 3;
            $this->_response['message'][] = "Le rôle administrateur  n'exite pas";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        try{

            $allAdmin = User::where('role_id','=',$objRole->id)->with('role','ville','user')->get();


        }catch(Exception $objException) {
            $this->_errorCode = 2;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $toReturn = [
            'objet' => $allAdmin
        ];

        $this->_response['message'] = "Liste des administrateurs.";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }


   
    


    
    
    // update user
    public function updateUser(Request $request)
    {
        $this->_fnErrorCode = 1;
        $validator = Validator::make($request->all(), [
            'ref_user'=>'string|required',
            'firstname' => 'nullable',
            'lastname' => 'nullable',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable',
            'lien_whatsapp'=>'nullable',
            'ville' => 'nullable',
            'role' => 'nullable',
            'password'=>'nullable|min:'.Config::get('constants.size.min.password').'|max:'.Config::get('constants.size.max.password'),
        ]);

        if ($validator->fails()){
            if (!empty($validator->errors()->all())){
                foreach ($validator->errors()->all() as $error){
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 3;
            $this->_response['message'][] = "Cette action nécéssite une connexion.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objAuthRole = Role::where("id", $objUser->role_id)->first();
        if(empty($objAuthRole)){
            DB::rollback();
            $this->_errorCode = 4;
            $this->_response['message'][] = "Le user n'a pas de rôle.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        // Start transaction!
        DB::beginTransaction();

        $objUpdateUser = User::where('ref', '=', $request->get('ref_user'))->first();
        if(empty($objUpdateUser)){
            DB::rollback();
            $this->_errorCode = 5;
            $this->_response['message'][] = "L'utilisateur n'existe pas";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        if($request->has('firstname') && $request->get('firstname')!=""){

            try {
                $objUpdateUser->update(["firstname" => $request->get('firstname')]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 6;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }

        if($request->has('lastname') && $request->get('lastname')!=""){

            try {
                $objUpdateUser->update(["lastname" => $request->get('lastname')]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 7;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }
        if($request->has('phone') && $request->get('phone')!=""){

            try {
                $objUpdateUser->update(["phone" => $request->get('phone')]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 8;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }



        if($request->has('ville') && $request->get('ville')!=""){

            try {
                $objUpdateUser->update(["ville_id" => intval($request->get('ville'))]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 9;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            } 
        }



        if($request->has('password') && $request->get('password')!=""){

            try {
                $objUpdateUser->update(["password" => Hash::make($request->get('password'))]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 11;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }
        }

        if($request->has('role') && $request->get('role')!=""){
            $objUserRole = Role::where('ref', '=', $request->get('role'))->first();
            if(empty($objUserRole)){
                DB::rollback();
                $this->_errorCode = 12;
                $this->_response['message'][] = "Le 'Rôle' n'existe pas";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            try{
                $objUpdateUser->update(["role_id" => $objUserRole->id]);
            }catch (Exception $objException){
                DB::rollback();
                $this->_errorCode = 13;
                if(in_array($this->_env, ['local', 'development'])){
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json( $this->_response );
            }
        }
        if($request->has('email') && $request->get('email')!=""){

            try {
                $objUpdateUser->update(["email" => $request->get('email')]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 14;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }

        
        if($request->has('quartier') && $request->get('quartier')!=""){

            try {
                $objUpdateUser->update(["quartier" => $request->get('quartier')]);

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 21;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }





        if($request->has('lien_whatsapp') && $request->get('lien_whatsapp')!=""){

                    try {
                        $objUpdateUser->update(["lien_whatsapp" => $request->get('lien_whatsapp')]);

                    } catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 25;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

        }
        
        $objU=user::where('id','=',$objUpdateUser->id)->with('role','ville','boutique','vendeur','user')->first();

        $region=Region::where('id','=',$objU->ville->region_id)->with('pays')->first();


        // Commit the queries!
        DB::commit();

        //Format d'affichage de message
        $toReturn = [
            'objet' => $objU,
            'pays'=> $region->pays
        ];

        $this->_response['message'] = 'Modification réussi!';
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    public function deleteUser(Request $request)
    {
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref_user'=>'required'
        ]);

        if ($validator->fails()){
            if (!empty($validator->errors()->all())){
                foreach ($validator->errors()->all() as $error){
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 3;
            $this->_response['message'][] = "Cette action nécéssite une connexion.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objAuthRole = Role::where('id', '=', $objUser->role_id)->first();
        if(empty($objAuthRole)){
            $this->_errorCode = 4;
            $this->_response['message'][] = "L'utilisateur n'a pas de rôle.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $auth_01 = array("administrateur");
        if($request->has("ref_user")){

            if(in_array($objAuthRole->alias, $auth_01)){

                $objDelUser = User::where("ref", $request->get("ref_user"))->first();

                try {
                    // $objDelUser =  User::where("ref", $request->get("ref_user"))->delete();
                    $objDelUser->update(["published" => 0]);

                } catch (Exception $objException) {
                    $this->_errorCode = 5;
                    if (in_array($this->_env, ['local', 'development'])) {
                        $this->_response['message'] = $objException->getMessage();
                    }
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }

            }else{
                DB::rollback();
                $this->_errorCode = 6;
                $this->_response['message'][] = "Vous n'étes pas habilié.";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }else{
            $this->_errorCode = 7;
            $this->_response['message'][] = "User n'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
        /* $toReturn = [
           'objet' => $objDelUser
        ];*/

        $this->_response['message'] = "L'utilisateur a été supprimé!";
        // $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }


    public function activateUser(Request $request)
    {
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref_user'=>'required'
        ]);

        if ($validator->fails()){
            if (!empty($validator->errors()->all())){
                foreach ($validator->errors()->all() as $error){
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 3;
            $this->_response['message'][] = "Cette action nécéssite une connexion.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objAuthRole = $objUser->role;
        if (!in_array($objAuthRole->alias,array("gestionnaire","administrateur","vendeur"))) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "Vous ne disposez pas du rôle necessaire!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $objActUser = User::where("ref", $request->get("ref_user"))->first();
        $role=$objActUser->role;

        if(empty($objActUser)) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "l'utilisateur n/'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        if (in_array($objAuthRole->alias,array("vendeur"))) {
            if (!in_array($role->alias,array("gestionnaire-boutique","entreprise","coursier"))) {
                try {

                    $objActUser->update(["published" => 1]);

                } catch (Exception $objException) {
                    $this->_errorCode = 5;
                    if (in_array($this->_env, ['local', 'development'])) {
                        $this->_response['message'] = $objException->getMessage();
                    }
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
            }
        }
        if (in_array($objAuthRole->alias,array("gestionnaire"))) {
            if (!in_array($role->alias,array("administrateur"))) {
                try {

                    $objActUser->update(["published" => 1]);

                } catch (Exception $objException) {
                    $this->_errorCode = 5;
                    if (in_array($this->_env, ['local', 'development'])) {
                        $this->_response['message'] = $objException->getMessage();
                    }
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
            }
        }

        if (in_array($objAuthRole->alias,array("administrateur"))) {

                try {

                    $objActUser->update(["published" => 1]);

                } catch (Exception $objException) {
                    $this->_errorCode = 5;
                    if (in_array($this->_env, ['local', 'development'])) {
                        $this->_response['message'] = $objException->getMessage();
                    }
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }

        }





        $toReturn = [
           'objet' => $objActUser
        ];

        $this->_response['message'] = "L'utilisateur a été activé!";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

}
