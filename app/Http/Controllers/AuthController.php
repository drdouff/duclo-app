<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AccountMessageCreatedToAdmin;
use App\Mail\customerMail;
use App\Mail\ForgotPasswordMail;

use App\Models\Pays;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Ville;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class AuthApiController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] expires_at
     */

    public function register(Request $request)
    {
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'nullable',
            'email' => 'required|email',
           // 'role' => 'nullable',
            'phone' => 'required',
            'ville' => 'required',
            'password'=>'required|min:'.Config::get('constants.size.min.password').'|max:'.Config::get('constants.size.max.password'),
            'lien_whatsapp'=> 'required'

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

        $objUserEmail = User::where('email', '=', $request->get('email'))->first();
        if(!empty($objUserEmail))
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
        //$reponse = "";
        $mail_reponse = "";

        // Start transaction!
      
        if(empty($objUser)) {

            /// if(!$request->has('role')){

            $objRole = Role::where('alias', '=', 'client')->first();
            if (empty($objRole)) {
                DB::rollback();
                $this->_errorCode = 5;
                $this->_response['message'][] = "Le rôle 'Client' n'existe pas";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            $objVille = Ville::where('id', '=', intval($request->get('ville')))->first();
            if (empty($objVille)) {
                DB::rollback();
                $this->_errorCode = 6;
                $this->_response['message'][] = "La ville n'existe pas";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            try {

                $objUser = new User();
                $objUser->firstname = $request->get('firstname');
                if ($request->has('lastname')) {
                    $objUser->lastname = $request->get('lastname');
                }
                $objUser->email = $request->get('email');
                $objUser->password = Hash::make($request->get('password'));
                $objUser->phone = $request->get('phone');
                $objUser->lien_whatsapp = $request->get('lien_whatsapp');
                $objUser->published = 1;
                if ($request->has('ville')) {
                    $objUser->ville()->associate($objVille);
                } else {
                    $this->_errorCode = 7;
                    $this->_response['message'][] = "Veuillez entrer une ville.";
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }
                $objUser->generateReference();
                $objUser->generateAlias($request->get('firstname'));
                $objUser->role()->associate($objRole);
                $objUser->save();

            } catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 8;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();

                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            $data = [
                'user' => $objUser
            ];

        }
            // Commit the queries!
            DB::commit();
            //Format d'affichage de message
            $toReturn = [
                //'mail_reponse' => $mail_reponse,
                'objet' => $objUser
            ];

        $this->_response['message'] = 'Votre compte a été créé avec succès!';
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);


    }

    public function login(Request $request)
    {

        $this->_fnErrorCode = "01";
        $validator = Validator::make($request->all(), [
            'email'=>'required|email|max:'.Config::get('constants.size.max.email').'|exists:users,email',
            'password'=>'required|min:'.Config::get('constants.size.min.password').'|max:'.Config::get('constants.size.max.password')

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


        $objUser = User::where('email', $request->get('email'))
            ->first();
        if(empty($objUser) || !$objUser->isPublished())
        {
            $this->_errorCode               = 3;
            $this->_response['message'][]   = trans('auth.denied');
            $this->_response['error_code']   = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $arrPasswordValidation = $objUser->validatePassword($request->get('password'));
        if($arrPasswordValidation['success'] == false)
        {
            $this->_errorCode               = 4;
            $this->_response['message'][]   = trans('messages.login.fail.default');
            $this->_response['error_code']   = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try
        {
            $objToken = $objUser->createToken('PersonalAccessToken');
        }
        catch(Exception $objException)
        {

            $this->_errorCode             = 5;
            if(in_array($this->_env, ['local', 'development']))
            {
                $this->_response['message'][]   = $objException->getMessage();
            }
            $this->_response['message'][]   = trans('messages.token.fail.generate');
            $this->_response['error_code']   = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objU=user::where('id','=',$objUser->id)->with('role','ville','boutique','user','vendeur')->first();

        $region=Region::where('id','=',$objU->ville->region_id)->with('pays')->first();




        $toReturn = [
            'token'=>$objToken->accessToken,
            'ref_connected_user'=>$objUser->ref,
            'token_type'=>'Bearer',
            'infos' => $objU,
            'pays'=>$region->pays
        ];
       // return response()->json($toReturn);
        $this->_response['data']    = $toReturn;
        $this->_response['success'] = true;

        return response()->json($this->_response);
    }


    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $objUser= Auth::user();
        $this->_fnErrorCode = "01";

        if(empty($objUser))
        {
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            $this->_response['message'][]   = Lang::get('messages.error-occured.default');
            return response()->json($this->_response);

        }

        $request->user()->token()->revoke();

        $arrResult[] = [
            'message'=>Lang::get('logged-out')
        ];
        $this->_response['success'] = true;
        $this->_response['data'] = [
            'result'=>$arrResult
        ];

        return response()->json($this->_response);
    }

    public function accountActivation(Request $request)
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

        $objUser = User::where('ref','=',$request->get("ref_user"))->first();
        if(empty($objUser)){
            $this->_errorCode = 3;
            $this->_response['message'][] = "L'objet User n'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try {

            $objUser->update(['published' => 1]);

        } catch (Exception $objException) {
            $this->_errorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $toReturn = [
            'objet' => $objUser
        ];

        $this->_response['message'] = "Votre compte a été activé!";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

   /* public function forgotPassword(Request $request)
    {
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'email'=>'email|required'
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

        $objUser = User::where('email', $request->get('email'))->first();
        if(empty($objUser)) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "utilisateur inconnu !";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try {
            Mail::to($objUser->email)->send(new ForgotPasswordMail($objUser));
            $mail_reponse = 'Email a été envoyé au client '.$objUser->firstname;
        }catch (Exception $objException) {
            DB::rollBack();
            $this->_errorCode = 4;
            if(in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $toReturn = [
            'mail_reponse' => $mail_reponse,
            'objet' => $objUser
        ];
        $this->_response['message'] = 'Vérifier votre boite mail';
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);

    }*/

    public function changePassword(Request $request){
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref'=>'string|required',
            'old_password'=>'string|required',
            'new_password'=>'string|required'
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

        $objUser = User::where('ref', $request->get('ref'))->first();
        if(empty($objUser)) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "utilisateur inconnu !";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if(empty($objRole)) {
            $this->_errorCode = 4;
            $this->_response['message'][] = "Vous n'êtes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $arrPasswordValidation = $objUser->validatePassword($request->get('old_password'));
        if($arrPasswordValidation['success'] == false)
        {
            $this->_errorCode               = 5;
            $this->_response['message'][]   = trans('messages.pass.fail.default');
            $this->_response['error_code']   = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $password = Hash::make($request->get('new_password'));

        try{
            $objUser->update(['password' => $password]);
        }
        catch(Exception $objException) {
            $this->_errorCode = 6;
            if(in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'][] = $objException->getMessage();
            $this->_response['message'][] = trans('messages.token.fail.generate');
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try{
            $objToken = $objUser->createToken('PersonalAccessToken');
        }catch(Exception $objException) {
            $this->_errorCode = 7;
            if(in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'][] = $objException->getMessage();
            $this->_response['message'][] = trans('messages.token.fail.generate');
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $toReturn = [
            'token'=>$objToken->accessToken,
            'ref_connected_user'=>$objUser->ref,
            'objet' => $objUser
        ];

        $this->_response['message'] = "Le nouveau mot de passe vient d'être créé. Vous pouvez vous connecter!";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }


}
