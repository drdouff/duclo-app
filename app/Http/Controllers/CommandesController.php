<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommandeRequest;
use App\Mail\commandeSendByCustomer;
use App\Mail\commandeSendToAdmin;
use App\Mail\commandeSendToGestionnaire;
use App\Mail\SendDeliverymanMail;
use App\Models\Commande;
use App\Models\Mode;
use App\Models\Panier;
use App\Models\Produit;
use App\Models\Produit_combinaison;
use App\Models\Role;
use App\Models\StatutCmde;
use App\Models\StatutLivraison;
use App\Models\StatutTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Ville;
use App\Mail\OrderMailToCustomer;
use Exception;
//use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Mailjet\LaravelMailjet\Facades\Mailjet;
use Barryvdh\DomPDF\Facade\PDF as PDF;
use App\Mail\FactureMail;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;


class CommandeApiController extends Controller
{

    public function listAllcommandes(Request $request){

        $user=Auth::user();
       $objRole = Role::where('id', '=', $user->role_id)->first();

        if(in_array($objRole->alias, array('gestionnaire','administrateur'))) {

            $cmde=Commande::where('published', '=', 1)->get();

            $listCmde=collect(); $allcmdes=collect();

            foreach($cmde as $c){

                $cmd=Commande::find($c->id)->produits()->get();
                $listCmde->push($cmd);
            }

            if(!empty($listCmde)){

                foreach($listCmde as $lists){
                    foreach($lists as $list){
                        // if($list->user_id== Auth::user()->id){

                            $cmd=Commande::where('id','=',$list->pivot->commande_id)->with('paniers')->get();

                            $allcmdes->push($cmd);

                        //}
                    }
                }

            }
            $data = [
                'commande' => $allcmdes
            ];
            $this->_response['message'] = 'liste des commandes';
            $this->_response['data'] = $data;
            $this->_response['success'] = true;
            return response()->json($this->_response);
        }
        else if(in_array($objRole->alias, array('vendeur'))) {

            $cmde=Commande::where('published', '=', 1)->get();



            $listCmde=collect(); $allcmdes=collect();

            foreach($cmde as $c){
                $us=User::where('published', '=', 1)->where('id','=',$c->boutique_id)->with('vendeur')->first();
              //  return response()->json($us);

                $cmd=Commande::find($c->id)->produits()->get();
              //  return response()->json($cmd);

                $listCmde->push($cmd);
            }
           // return response()->json($listCmde);
            if(!empty($listCmde)){
                // foreach($listCmde->reverse() as $lists){

                foreach($listCmde as $lists){

                    foreach($lists as $list){

                        if($list->user_id== Auth::user()->id){
                          // return response()->json($list);
                                $cmd=Commande::where('id','=',$list->pivot->commande_id)->with('paniers')->get();

                                $allcmdes->push($cmd);
                           // return response()->json($allcmdes);
                            //}
                        }
                    }

                }
            }
            return response()->json($allcmdes);
            $data = [
                'commande' => $allcmdes
            ];
        $this->_response['message'] = 'liste des commandes';
        $this->_response['data'] = $data;
        $this->_response['success'] = true;
        return response()->json($this->_response);

       }
      else{
        $this->_response['message'] = 'vous n avez pas d"authorisation pour acceder';
      }
       // DB::commit();



    }

    public function listcommande(Request $request){
       $this->_fnErrorCode = 1;
        $validator = Validator::make($request->all(), [
            'ref_user' => 'string|required',

        ]);

        if ($validator->fails()) {
            if (!empty($validator->errors()->all())) {
                foreach ($validator->errors()->all() as $error) {
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 3;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        //$objRole = Role::where('id', '=', $objUser->role_id)->first();


        $user = User::where('ref','=',$request->get('ref_user'))->first();

        if(empty($user)) {
            $this->_errorCode = 6;
            $this->_response['message'][] = "La boutique  n'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
       $userRole=$user->role;


        if (in_array($userRole->alias,array("client"))) {
            $listcommande=Commande::where('published','=',1)->where('user_client_id','=',$user->id)
            ->with('user_client','ville','StatutLivraison','statut_cmd','paniers','produits','produits.images','produits_combinaisons','produits_combinaisons.produit.images','boutique','user_vendeur')->get();

        }
        if (in_array($userRole->alias,array("entreprise"))) {
            $listcommande=Commande::where('published','=',1)->where('boutique_id','=',$user->id)
            ->with('user_client','ville','StatutLivraison','statut_cmd','paniers','produits','produits.images','produits_combinaisons','produits_combinaisons.produit.images','boutique','user_vendeur')->get();



        }
        if (in_array($userRole->alias,array("gestionnaire-boutique"))) {

            $boutique=$user->gestionnaire_boutique;

            $listcommande=Commande::where('published','=',1)->where('boutique_id','=',$boutique->id)
            ->with('user_client','ville','StatutLivraison','statut_cmd','paniers','produits','produits.images','produits_combinaisons','produits_combinaisons.produit.images','boutique','user_vendeur')->get();



        }

        $data = [
            'commandes' => $listcommande

        ];



        $this->_response['message']    = 'liste des commandes';
        $this->_response['data']    = $data;
        $this->_response['success'] = true;
        return response()->json($this->_response);


    }


    //listing du stattut des commandes

    public function getAllStatutCmdes()
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 2;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        $statuts = StatutCmde::where('published','=',1)->get();

        $data = [
            'statut_commande' => $statuts
        ];

        $this->_response['message']    = 'liste des statuts commande';
        $this->_response['data']    = $data;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    //listing du stattut des commandes

    public function getAllStatutLivraisons()
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 2;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        $statuts = StatutLivraison::where('published','=',1)->get();

        $data = [
            'StatutLivraison' => $statuts
        ];

        $this->_response['message']    = 'liste des statuts livraisons';
        $this->_response['data']    = $data;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    public function createCommande(Request $request)
    {
        $this->_errorCode = 1;

        $validator = Validator::make($request->all(), [
            'ville' => 'string|required',
            'lieu_livraison'=> 'string|required',
            'produits'=> 'required',
            'mode'=> 'string|required',
            'apiKey'=> 'string|required',
            'secretKey'=> 'string|required',
            'phone'=> 'string|required',
           'boutique' => 'string|required',
           'montant_total' => 'string|required'

        ]);
        if ($validator->fails()) {
            if (!empty($validator->errors()->all())) {
                foreach ($validator->errors()->all() as $error) {
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
            $this->_response['message'][] = 'Cette action nécéssite une connexion.';
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //On vérifie le rôle client
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if($objRole->alias != 'client') {
            $this->_errorCode = 4;
            $this->_response['message'][] = "Vous n'étes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objVille=Ville::where('id','=',intval($request->get('ville')))->first();
        if(empty($objVille)) {
            $this->_errorCode = 5;
            $this->_response['message'][] = "La ville n'existe pas!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objMode=Mode::where('ref','=',$request->get('mode'))->first();
        if(empty($objMode)) {
            $this->_errorCode = 6;
            $this->_response['message'][] = "Le mode de paiement n'existe pas!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $objBoutique=User::where('ref','=',$request->get('boutique'))->first();
        if(empty($objBoutique)) {
            $this->_errorCode = 8;
            $this->_response['message'][] = "La boutique n'existe pas!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }



        $phone = "";
        if(strlen($request->get('phone')) == 12) {

            if(preg_match('/^(237)(\d{3})(\d{3})(\d{3})$/', $request->get('phone'), $matches)) {

                if($objMode->name == 'mtn') {

                    $phone = $matches[1].$matches[2].$matches[3].$matches[4];

                }

                if($objMode->name == 'orange') {

                    $phone = $matches[2].$matches[3].$matches[4];

                }
            }

        }elseif(strlen($request->get('phone')) == 9) {

            if(preg_match('/^(6)(\d{2})(\d{3})(\d{3})$/', $request->get('phone'), $matches)) {

                if($objMode->name == 'mtn'){

                    $phone = "237".$matches[1].$matches[2].$matches[3].$matches[4];

                }

                if($objMode->name == 'orange'){

                    $phone = $matches[1].$matches[2].$matches[3].$matches[4];

                }
            }
        }

        //-----------------------------------------------------------------------------------------------
        //Statut commande : paiement en attente
        //-----------------------------------------------------------------------------------------------
        $objStatutCmde = StatutCmde::where('name', '=', 'paiement en attente')->first();
        if (empty($objStatutCmde)) {
            $this->_errorCode =9;
            $this->_response['message'][] = "Le statut de la commande n'existe pas!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //-----------------------------------------------------------------------------------------------
        //Statut livraison : En attente
        //-----------------------------------------------------------------------------------------------
        $objStatutLivraison = StatutLivraison::where('name', '=', 'En attente')->first();
        if (empty($objStatutLivraison)) {
            $this->_errorCode = 10;
            $this->_response['message'][] = "Le statut livraison n'existe pas!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::beginTransaction();
        $resultOperateur = (object)[];
        $resultTransaction = (object)[];
        $objTransaction = (object)[];
        $objCommande = (object)[];
        $mail_reponse = "";
        //-----------------------------------------------------------------------------------------------
        //Création de la commande
        //-----------------------------------------------------------------------------------------------

        try {

            $objCommande = new Commande();
            $objCommande->published = 1;
            $objCommande->lieu_livraison = $request->get("lieu_livraison");
            $objCommande->montant_total = $request->get("montant_total");
            $objCommande->generateReference();
            $objCommande->generateAlias("Commande".$objCommande->id);
            $objCommande->user_client()->associate($objUser);
            $objCommande->StatutLivraison()->associate($objStatutLivraison);
            $objCommande->statut_cmd()->associate($objStatutCmde);
            $objCommande->ville()->associate($objVille);
            $objCommande->boutique()->associate($objBoutique);
            $objCommande->user_vendeur()->associate($objBoutique->vendeur);
            $objCommande->save();

        } catch (Exception $objException) {
            DB::rollback();
            $this->_errorCode = 11;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $listproduit = json_decode($request->get('produits'),true);

        $colProduct = collect();
        $montantCmde = 0;
        $listGestionnaire = array();
        $listcreateGestionnaire = array();


        if (is_array($listproduit)) {

            foreach ($listproduit as $elmt){
                
                if($elmt['type']==1){
                    $produit=Produit::where('ref', '=', $elmt['produit'])->where('published','=',1)->first();
                    if(empty($produit)){
                        DB::rollBack();
                        $this->_errorCode = 14;
                        $this->_response['message'][] = "Le produit n'existe pas!";
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);

                    }

                    try{

                        $objPanier = new Panier();
                        $objPanier->quantite = $elmt['quantite'];
                        $objPanier->prix_total = $elmt['prix_unit']*$elmt['quantite'];
                        $objPanier->published = 1;
                        $objPanier->generateReference();
                        $objPanier->generateAlias("Commande".$objCommande->id);
                        $objPanier->commande()->associate($objCommande);
                        $objPanier->produit()->associate($produit);
                        $objPanier->save();

                    }catch(Exception $objException) {
                        DB::rollback();
                       // dd(($objPanier));
                        $this->_errorCode = 14;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }
                    $montantCmde = ($objPanier->prix_total) + $montantCmde;

                    $colProduct->push(array(
                        'product'=>$produit,
                        'product_unit_price' => $produit->min_price,
                        'panier' => $objPanier
                    ));
                    $objUserGestionnaire = User::where('id','=',$produit->user->id)->where('published',1)->first();

                    $CreateobjUserGestionnaire = User::where('id','=',$objUserGestionnaire->user_id)->first();

                    if(!in_array($objUserGestionnaire->email, $listGestionnaire)) {
                        $listGestionnaire[] = $objUserGestionnaire->email;

                    }
                    if(!in_array($CreateobjUserGestionnaire->email, $listcreateGestionnaire)) {
                        $listcreateGestionnaire[] = $CreateobjUserGestionnaire->email;
                    }
                }
            }

        }


        //---------------------------------------------------------------------------------
        // Shurtcode d'envoi de mail au client
        //-------------------------------------------------------------------------------
        $dataCustomer = [
            'customer'=> $objUser,
            'commande'=> $objCommande,
            'products'=> $colProduct,
            'montant_commande'=> $montantCmde
        ];

        /*
        * Shurtcode d'envoi de mail au client
        */

        /*
        try {

            Mail::to($objUser->email)
            ->send(new commandeSendByCustomer($dataCustomer));

        }catch (Exception $objException) {

            DB::rollBack();
            $this->_errorCode = 16;
            if(in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
        */


        /*
         * Shurtcode d'envoi de mail au gestionnaire
         */
        /*
            foreach($listGestionnaire as $userGestionnaire) {

            $objUserGestionnaire = User::where('email','=',$userGestionnaire)->first();

            $data = [
                'gestionnaire'=> $objUserGestionnaire,
                'customer'=> $objUser,
                'commande'=> $objCommande,
                'products'=> $colProduct,
                'montant_commande'=> $montantCmde
            ];



            try {

                Mail::to($objUserGestionnaire->email)
                ->send(new commandeSendToGestionnaire($data));

            }catch (Exception $objException) {

                DB::rollBack();
                $this->_errorCode = 17;
                if(in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }
     }*/


     $objUserAdmin = User::where('role_id','=',1)->first();

     /*   $adminData = [
            'customer'=> $objUser,
            'commande'=> $objCommande,
            'products'=> $colProduct,
            'montant_commande'=> $montantCmde
        ];

       // Shurtcode d'envoi de mail à l'admin

        try {

            Mail::to($objUserAdmin->email)
            ->send(new commandeSendToAdmin($adminData));

        }catch (Exception $objException) {

            DB::rollBack();
            $this->_errorCode = 18;
            if(in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }*/


            $mail_reponse = 'Email has been sent to gestionnaire, admin and customer';



        if($objMode->name !='En espece'){
            $objStatut_trans = StatutTransaction::where('name','=','initie')->first();
            if(empty($objStatut_trans)) {
                DB::rollBack();
                $this->_errorCode = 19;
                $this->_response['message'][] = "L'objet statut transaction n'existe pas!";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }
            try {

                $objTransaction = new Transaction();
                $objTransaction->paie_phone = $phone;
                $objTransaction->montant = $montantCmde;
                $objTransaction->total_payment = $montantCmde;
                $objTransaction->commande()->associate($objCommande);
                $objTransaction->statut_trans()->associate($objStatut_trans);
                $objTransaction->mode()->associate($objMode);
                $objTransaction->published = 1;
                $objTransaction->generateReference();
                $objTransaction->generateAlias("transaction".$objTransaction->id);
                $objTransaction->save();

            }catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 20;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }



            //-----------------------------------------------------------------------------------------------
            //Initiation d'un paiement à TasPay
            //-----------------------------------------------------------------------------------------------
            $postfields = array(
                'phone' => $objTransaction->paie_phone,
                'montant' => $objTransaction->montant,
                'transactionkey' => $objTransaction->ref,
                'apiKey' => $request->get("apiKey"),
                'secretKey' => $request->get("secretKey"),
                'methode_paiement' => $objMode->name
            );

            try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/marchand/transaction/create');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $result = json_decode(curl_exec($ch), true);

                //return response()->json($result);
            }catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 21;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            if($result != null){

                $resultOperateur = $result['data']['operateur'];

                if($resultOperateur['name'] == "orange") {

                    $resultTransaction = $result['data']['transaction'];

                    try {

                        $objTransaction->update(['taspay_transaction' => $resultTransaction['ref']]);


                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 22;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }


                }elseif($resultOperateur['name'] == "mtn") {

                    $resultTransaction = $result['data']['transaction'];

                    try {
                        $objTransaction->update(['taspay_transaction' => $resultTransaction['ref']]);
                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 23;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }


                }else {
                    DB::rollback();
                    $this->_errorCode = 24;
                    $this->_response['message'][] = 'Absence de paramètre de paiement.';
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }

            }else{
                DB::rollback();
                $this->_errorCode = 25;
                $this->_response['message'][] = 'Aucune donnée retournée par'.$objMode->name;
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }


            // Commit the queries!
            DB::commit();
            $toReturn = [
                'mail_reponse' => $mail_reponse,
                'objet' => $objCommande,
                'taspay_transaction'=> $resultTransaction,
                'operateur' => $resultOperateur,
                'transaction' => $objTransaction
            ];
        }
        else{
            DB::commit();
            $toReturn = [
                'mail_reponse' => $mail_reponse,
                'objet' => $objCommande

            ];


        }


        $this->_response['message'] = "La commande est créée avec succès et le paiement est initié.";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }



    //Fonction qui permet de checker une transaction par le moyen Orange Money
    public function checkTransactionOrange(Request $request)
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 2;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        $validator = Validator::make($request->all(), [
            'ref_transaction' => 'string|required'
        ]);

        if($validator->fails()){
            if (!empty($validator->errors()->all())){
                foreach ($validator->errors()->all() as $error){
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        $objTransaction = Transaction::where('ref','=',$request->get('ref_transaction'))->first();


        $objStatutTransaction = StatutTransaction::where('id', '=', $objTransaction->statut_trans_id)->first();
        DB::beginTransaction();

        $resultTransaction = (object)[];
        $objCommande = (object)[];
        $message = "";
        $responseMail = "";

        if($objStatutTransaction->name == "reussie") {
            $message = "La transaction est déjà reussie!";
        }

        if($objStatutTransaction->name == "echouer") {
            $message = "La transaction est déjà échouée!";
        }


        if($objStatutTransaction->name == "initie") {
            //------------------------------------------------------------------------------------------------------
            //Check de paiement Orange
            //------------------------------------------------------------------------------------------------------
            $postfields = array(
                'ref_transaction' => $objTransaction->taspay_transaction
            );

            try{

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/orange/payment/status/check');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $result = json_decode(curl_exec($ch), true);



            }catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 2;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }
            if(isset($result['data']['objet'])){
                $resultTransaction = $result['data']['objet'];

                if($resultTransaction['transaction_status'] == 'INITIATED') {
                    $message = "Votre paiement est inité chez Orange!";
                }

                if($resultTransaction['transaction_status'] == 'PENDING') {
                    $message = "Le paiement est en progression chez Orange.";
                }

                if($resultTransaction['transaction_status'] == 'SUCCESS') {

                    //-----------------------------------------------------------------------------------------------
                    //StatutTransaction : reussie
                    //-----------------------------------------------------------------------------------------------
                    $objStatutTransaction = StatutTransaction::where('name', '=', 'reussie')->first();

                    try {
                        $objTransaction->update(['statut_trans_id' => $objStatutTransaction->id]);
                    }catch(Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 3;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

                    $objStatutCmde = StatutCmde::where('name', '=', 'paye')->first();
                    if(empty($objStatutCmde)) {
                        DB::rollback();
                        $this->_errorCode = 4;
                        $this->_response['message'][] = "Le statut de la commande n'existe pas!";
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

                    $objCommande = Commande::where('id','=',$objTransaction->commande_id)->first();

        //return response()->json($objCommande);

                    try {
                        $objCommande->update(['statut_cmd_id' => $objStatutCmde->id]);
                    }catch(Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 5;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }



                    /**
                     * Code pour générer la facture au client en attente
                     */
                    $objTransaction->downloadPDF($objTransaction->id);

                    $message = "Transaction reussie!";

                }

                if($resultTransaction['transaction_status'] == 'FAILED') {
                    //-----------------------------------------------------------------------------------------------
                    //StatutTransaction : echoue
                    //-----------------------------------------------------------------------------------------------
                    $objStatutTransaction = StatutTransaction::where('name', '=', 'echoue')->first();

                    try {
                        $objTransaction->update(['statut_trans_id' => $objStatutTransaction->id]);
                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 6;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

                    $message = "Transaction échouée!";

                }

                if($resultTransaction['transaction_status'] == 'EXPIRED') {

                    $message = "Le paiement est expiré!";

                }
            }
        }

       /* DB::commit();
        $toReturn = [
            'commande' => $objCommande,
            'transaction' => $objTransaction,
            'taspay_info' => $resultTransaction
        ];
        $this->_response['message'] = $message;
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);*/
    }


        //Fonction qui permet de checker une transaction reussie par le moyen Mtn Money
    public function checkTransactionMtn(Request $request)
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 2;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        $validator = Validator::make($request->all(), [
            'ref_transaction' => 'string|required'
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

        $objTransaction = Transaction::where('ref','=',$request->get('ref_transaction'))->first();

        $objStatutTransaction = StatutTransaction::where('id', '=', $objTransaction->statut_trans_id)->first();

        DB::beginTransaction();

        $resultTransaction = (object)[];
        $message = "";

        if($objStatutTransaction->name == "reussie") {
            $message = "La transaction est déjà reussie!";
        }

        if($objStatutTransaction->name == "echouer") {
            $message = "La transaction est déjà échouée!";
        }

        if($objStatutTransaction->name == "initie") {
            //-----------------------------------------------------------------------------------------------
            //Check de paiement Mtn
            //-----------------------------------------------------------------------------------------------
            $postfields = array(
                'ref_transaction' => $objTransaction->taspay_transaction
            );

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/mtn/payment/status/check');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $result = json_decode(curl_exec($ch), true);

            }catch (Exception $objException) {
                DB::rollback();
                $this->_errorCode = 2;
                if (in_array($this->_env, ['local', 'development'])) {
                }
                $this->_response['message'] = $objException->getMessage();
                $this->_response['error_code'] = $this->prepareErrorCode();
            }


            if($result != null) {

                $resultTransaction = $result['data']['objet'];

                if($resultTransaction['transaction_status'] == 'PENDING') {

                    $message = "Le paiement est en cours chez MTN.";

                }

                if($resultTransaction['transaction_status'] == 'SUCCESSFUL') {
                    //-----------------------------------------------------------------------------------------------
                    //StatutTransaction : reussie
                    //-----------------------------------------------------------------------------------------------
                    $objStatutTransaction = StatutTransaction::where('name', '=', 'reussie')->first();

                    try {
                        $objTransaction->update(['statut_trans_id' => $objStatutTransaction->id]);
                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 3;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                    }

                    $objStatutCmde = StatutCmde::where('name', '=', 'paye')->first();
                    if(empty($objStatutCmde)) {
                        DB::rollback();
                        $this->_errorCode = 4;
                        $this->_response['message'][] = "Le statut de la commande n'existe pas!";
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

                    $objCommande = Commande::where('id','=',$objTransaction->commande_id)->first();

                    try {
                        $objCommande->update(['statut_cmd_id' => $objStatutCmde->id]);
                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 5;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                    }


                    $objTransaction->downloadPDF($objTransaction->id);


                    $message = "Transaction reussie!";

                }



                if($resultTransaction['transaction_status'] == 'FAILED') {
                    //-----------------------------------------------------------------------------------------------
                    //StatutTransaction : echoue
                    //-----------------------------------------------------------------------------------------------
                    $objStatutTransaction = StatutTransaction::where('name', '=', 'echouer')->first();

                    try {
                        $objTransaction->update(['statut_trans_id' => $objStatutTransaction->id]);
                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 6;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                    }

                    $message = "Transaction échouée!";

                }

            }else{
                DB::rollback();
                $this->_errorCode = 4;
                $this->_response['message'][] = "Aucune donnée retournée par MTN!";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        }


       DB::commit();
        $toReturn = [
            'commande' => $objCommande,
            'transaction' => $objTransaction,
            'taspay_info' => $resultTransaction
        ];
        $this->_response['message'] = $message;
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    public function DetailCommande(Request $request)
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 2;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        $validator = Validator::make($request->all(), [
            'ref_commande' => 'string|required'
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

        $objCommande = Commande::where('ref','=',$request->get('ref_commande'))->first();
        if(empty($objCommande)){
            $this->_errorCode = 3;
            $this->_response['message'][] = 'La commande n\'existe pas.';
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        try {

            $objOrderPanier = Panier::where('commande_id','=',$objCommande->id)->with('produit','produit.images','produit_combinaison','produit_combinaison.produit','produit_combinaison.produit.images')->get();

        }catch (Exception $objException) {
            $this->_fnErrorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }


        try{

            $allTransactions = Transaction::where('commande_id','=',$objCommande->id)->with('mode','statut_trans')->get();

        }catch (Exception $objException) {
            $this->_fnErrorCode = 5;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $prixTotalPanier = 0;
        $montant_restant_a_payer = 0;

        if($objOrderPanier->isNotEmpty()) {

            $montant = 0;
            foreach($objOrderPanier as $panier) {

                $prixTotalPanier = intval($panier->prix_total) + $montant;

            }

            if($allTransactions->isNotEmpty()) {

                $montantTotalTransaction = 0;
                foreach($allTransactions as $item) {

                    if($item->statut_trans->name == 'reussie') {

                        $montantTotalTransaction = intval($item->montant) + $montantTotalTransaction;
                    }

                }


                if($prixTotalPanier == $montantTotalTransaction) {
                    $montant_restant_a_payer = $prixTotalPanier - $montantTotalTransaction;
                }

            }else {
                $montant_restant_a_payer = $prixTotalPanier;
            }

        }

        DB::commit();
        $toReturn = [
            'all_paniers' => $objOrderPanier,
            'transaction' => $allTransactions,
            'montant_restant_a_payer' => $montant_restant_a_payer
        ];
        $this->_response['message'] = 'Détail d\'une Commande.';
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);

    }

    //annuler la commande
    public function DeleteCommande(Request $request){
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref_commande' => 'string|required'
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
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 3;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }

        //On vérifie que l'utilisateur est bien admin
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if($objRole->alias != 'client') {
            $this->_errorCode = 4;
            $this->_response['message'][] = "Vous n'êtes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //get car la commande se trouve dans le panier et les transactions
        $objCommande=commande::where('ref', '=', $request->get('ref_commande'))->get();


        if(!empty($objCommande)){

                try{
                    foreach($objCommande as $item){


                        $objPanier=Panier::where('commande_id','=',$item->id)->get();

                        foreach($objPanier as $panier){

                            $panier->update(["published" => 0]);


                        }

                     $objCommande->first()->update(["published" => 0]);



                    }
                }
                catch(Exception $objException){
                DB::rollback();
                $this->_errorCode = 5;
                if(in_array($this->_env, ['local', 'development'])){
                    $this->_response['message'] = $objException->getMessage();
                }
                $this->_response['error_code'] = $this->prepareErrorCode();
                }

        }else {
            $this->_errorCode = 6;
            $this->_response['message'][] = "Le commande n'existe pas.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
        $toReturn = [
        ];
        $this->_response['message'] = "La commande a été supprimée!";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);

    }

    //Fonction qui permet de recupérer la liste des commandes d'un customer
    public function customerOrderslist()
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 2;
            $this->_response['message'][] = 'Cette action nécéssite une connexion.';
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //On vérifie le rôle client
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if($objRole->alias != "client") {
            $this->_errorCode = 3;
            $this->_response['message'][] = "Vous n'étes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::beginTransaction();

        try {

            $objAllCommandes = Commande::where('user_client_id', $objUser->id)->with('user_client','statut_cmd','StatutLivraison','ville','user_livreur','user_gestionnaire')
                ->orderBy('created_at', 'desc')
                ->get();


        } catch (Exception $objException) {
            DB::rollback();
            $this->_errorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $collCommandes = collect();
        foreach($objAllCommandes as $commande){

            /**Check commande en attente de paiement*/
            if($commande->statut_cmd->name == 'paiement en attente') {

                /**Recup la transaction initiée */
                $objTransaction = Transaction::where('commande_id','=',$commande->id)->where('statut_trans_id','=',1)->with('mode')->first();

                if(!empty($objTransaction)) {

                    $commande = Commande::where('id','=',$commande->id)->first();

                    /**Vérif du mode de paiement */
                    if($objTransaction->mode->name == 'orange') {

                        $postfields = array(
                            'ref_transaction' => $objTransaction->taspay_transaction
                        );

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/orange/payment/status/check');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            $result = json_decode(curl_exec($ch), true);

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 5;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();
                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }

                        //dd($result);
                       if(isset($result['data']['objet'])){
                        $resultTransaction = $result['data']['objet'];

                        if($resultTransaction['transaction_status'] == 'FAILED') {
                            //-----------------------------------------------------------------------------------------------
                            //Statut_trans : echouer
                            //-----------------------------------------------------------------------------------------------
                            try {
                                $objTransaction->update(['statut_trans_id' => 2]);
                            }catch (Exception $objException) {
                                DB::rollback();
                                $this->_errorCode = 6;
                                if (in_array($this->_env, ['local', 'development'])) {
                                }
                                $this->_response['message'] = $objException->getMessage();
                                $this->_response['error_code'] = $this->prepareErrorCode();
                                return response()->json($this->_response);
                            }

                        }

                        if($resultTransaction['transaction_status'] == 'SUCCESS') {
                            //-----------------------------------------------------------------------------------------------
                            //Statut_trans : reussie
                            //-----------------------------------------------------------------------------------------------

                            try {
                                $objTransaction->update(['statut_trans_id' => 3]);
                            }catch (Exception $objException) {
                                DB::rollback();
                                $this->_errorCode = 7;
                                if (in_array($this->_env, ['local', 'development'])) {
                                }
                                $this->_response['message'] = $objException->getMessage();
                                $this->_response['error_code'] = $this->prepareErrorCode();
                                return response()->json($this->_response);
                            }

                            //-----------------------------------------------------------------------------------------------
                            //Statut_cmd : paye
                            //-----------------------------------------------------------------------------------------------

                            try {
                                $commande->update(['statut_cmd_id' => 3]);
                            }catch (Exception $objException) {
                                DB::rollback();
                                $this->_errorCode = 8;
                                if (in_array($this->_env, ['local', 'development'])) {
                                }
                                $this->_response['message'] = $objException->getMessage();
                                $this->_response['error_code'] = $this->prepareErrorCode();
                                return response()->json($this->_response);
                            }


                        }
                    }

                    }

                    if($objTransaction->mode->name == 'mtn') {

                        $postfields = array(
                            'ref_transaction' => $objTransaction->taspay_transaction
                        );

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/mtn/payment/status/check');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            $result = json_decode(curl_exec($ch), true);

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 9;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();
                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }
                        if(isset($result['data']['objet'])){
                            $resultTransaction = $result['data']['objet'];

                            if($resultTransaction['transaction_status'] == 'FAILED') {
                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : echouer
                                //-----------------------------------------------------------------------------------------------
                                try {
                                    $objTransaction->update(['statut_trans_id' => 2]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 10;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }
                            }


                            if($resultTransaction['transaction_status'] == 'SUCCESSFUL') {

                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : reussie
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $objTransaction->update(['statut_trans_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 11;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                                //-----------------------------------------------------------------------------------------------
                                //Statut_cmd : paye
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $commande->update(['statut_cmd_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 12;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                            }
                        }
                    }
                    /*if($objTransaction->mode->name == 'En espece') {


                        try{
                                $objTransaction->update(['statut_trans_id' => 3]);
                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 10;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();
                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }

                    }*/


                }

            }

            $allPaniers = Panier::where('commande_id','=',$commande->id)->with('produit_combinaison', 'produit_combinaison.produit','produit_combinaison.produit.images')->get();

            /**Recupère le montant de la commande passée */
            $montanCommande = Panier::where('commande_id','=',$commande->id)->sum('prix_total');

            $montant_restant_a_payer = 0;

            $total_paiement = 0;

            $allTransactions = Transaction::where('commande_id','=',$commande->id)->with('statut_trans','mode')->get();
            $commande = Commande::where('id','=',$commande->id)->with('statut_cmd','StatutLivraison')->first();

            $objTransaction = Transaction::where('commande_id','=',$commande->id)->where('statut_trans_id','=',3)->first();
            if (!empty($objTransaction)) {
                $total_paiement = $objTransaction->total_payment;
            }
            $montant_restant_a_payer = $montanCommande - intval($total_paiement);
            $collCommandes->push(array(
                'commande'=> $commande,
                'montant_commande'=> $montanCommande,
                'all_panier'=> $allPaniers,
                'transaction'=> $allTransactions,
                'reste_a_payer' => $montant_restant_a_payer
            ));

        }

        //return response()->json($collCommandes);

        DB::commit();
        $toReturn = [
            'objet' => $collCommandes
        ];
        $this->_response['message'] = "Liste des Commandes d'un client.";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    //Fonction qui permet au Gestionnaire| Admin de voir la liste des commandes des customers
    public function Orderslist()
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 2;
            $this->_response['message'][] = 'Cette action nécéssite une connexion.';
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //On vérifie le rôle gestionnaire
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if(!in_array($objRole->alias,array('gestionnaire','administrateur'))) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "Vous n'étes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::beginTransaction();

        try {

            $objAllCommandes = Commande::with('user_client','statut_cmd','ville','user_livreur','user_gestionnaire')
                ->orderBy('created_at', 'desc')
                ->get();

        } catch (Exception $objException) {
            DB::rollback();
            $this->_errorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $collCommandes = collect();
        foreach($objAllCommandes as $commande){

            /**Check commande en attente */
            if($commande->statut_cmd->name == 'paiement en attente') {

                /**Recup la transaction initiée */
                $objTransaction = Transaction::where('commande_id','=',$commande->id)->where('statut_trans_id','=',1)->with('mode')->first();
                if(!empty($objTransaction)) {

                    $commande = Commande::where('id','=',$commande->id)->first();

                    /**Vérif du mode de paiement */
                    if($objTransaction->mode->name == 'orange') {

                        $postfields = array(
                            'ref_transaction' => $objTransaction->taspay_transaction
                        );

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/orange/payment/status/check');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            $result = json_decode(curl_exec($ch), true);

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 5;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();
                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }

                        //$objTransaction = Transaction::where('id','=',$objTransaction->id)->first();
                        if(isset($result['data']['objet'])){
                            $resultTransaction = $result['data']['objet'];

                            if($resultTransaction['transaction_status'] == 'FAILED') {
                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : echouer
                                //-----------------------------------------------------------------------------------------------
                                try {
                                    $objTransaction->update(['statut_trans_id' => 2]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 6;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                            }

                            if($resultTransaction['transaction_status'] == 'SUCCESS') {
                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : reussie
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $objTransaction->update(['statut_trans_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 7;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                                //-----------------------------------------------------------------------------------------------
                                //Statut_cmd : paye
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $commande->update(['statut_cmd_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 8;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }


                            }
                        }
                    }

                    if($objTransaction->mode->name == 'mtn') {

                        $postfields = array(
                            'ref_transaction' => $objTransaction->taspay_transaction
                        );

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://taspay.team-solutions.net/api/api/mtn/payment/status/check');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            $result = json_decode(curl_exec($ch), true);

                        }catch (Exception $objException) {
                            DB::rollback();
                            $this->_errorCode = 9;
                            if (in_array($this->_env, ['local', 'development'])) {
                            }
                            $this->_response['message'] = $objException->getMessage();
                            $this->_response['error_code'] = $this->prepareErrorCode();
                            return response()->json($this->_response);
                        }
                        if(isset($result['data']['objet'])){
                            $resultTransaction = $result['data']['objet'];

                            if($resultTransaction['transaction_status'] == 'FAILED') {
                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : echouer
                                //-----------------------------------------------------------------------------------------------
                                try {
                                    $objTransaction->update(['statut_trans_id' => 2]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 10;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }
                            }


                            if($resultTransaction['transaction_status'] == 'SUCCESSFUL') {

                                //-----------------------------------------------------------------------------------------------
                                //Statut_trans : reussie
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $objTransaction->update(['statut_trans_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 11;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                                //-----------------------------------------------------------------------------------------------
                                //Statut_cmd : paye
                                //-----------------------------------------------------------------------------------------------

                                try {
                                    $commande->update(['statut_cmd_id' => 3]);
                                }catch (Exception $objException) {
                                    DB::rollback();
                                    $this->_errorCode = 12;
                                    if (in_array($this->_env, ['local', 'development'])) {
                                    }
                                    $this->_response['message'] = $objException->getMessage();
                                    $this->_response['error_code'] = $this->prepareErrorCode();
                                    return response()->json($this->_response);
                                }

                            }
                        }

                    }

                }

            }

            $allPaniers = Panier::where('commande_id','=',$commande->id)->with('produit_combinaison','produit_combinaison.produit','produit_combinaison.produit.images')
                ->get();

            /**Recupère le montant de la commande passée */
            $montanCommande = Panier::where('commande_id','=',$commande->id)->sum('prix_total');

            //$collDetail = collect();



            $total_paiement = 0;

            $allTransactions = Transaction::where('commande_id','=',$commande->id)->with('statut_trans','mode')->get();
            $objTransaction = Transaction::where('commande_id','=',$commande->id)->where('statut_trans_id','=',3)->first();

            if (!empty($objTransaction)) {
                $total_paiement = $objTransaction->total_payment;
            }

            $montant_restant_a_payer = $montanCommande - intval($total_paiement);
            $collCommandes->push(array(
                'commande'=> $commande,
                'montant_commande'=> $montanCommande,
                'all_panier'=> $allPaniers,
                'transaction'=> $allTransactions,
                'reste_a_payer' => $montant_restant_a_payer
            ));

        }


        //dd($collCommandes);

        DB::commit();
        $toReturn = [
            'objet' => $collCommandes
        ];
        $this->_response['message'] = "Liste global des Commandes.";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    //fonction qui assigne la commande au livreur
    public function ordersAssignToDeliveryman(Request $request){

        $this->_fnErrorCode = 1;

        //On vérifie que la commande est bien envoyé !
        $objListCommande = collect(json_decode($request->getContent(), true));
        if (empty($objListCommande)) {
            $this->_errorCode = 2;
            $this->_response['message'][] = "La liste des produits de la commande est vide!";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //On vérife que l'utilisateur est bien connecté
        $objUser = Auth::user();
        if (empty($objUser)) {
            $this->_errorCode = 3;
            $this->_response['message'][] = "Utilisateur non connecté";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        //On vérifie que l'utilisateur est bien gestionnaire
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if ($objRole->alias != 'gestionnaire') {
            $this->_errorCode = 4;
            $this->_response['message'][] = "Vous n'êtes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::beginTransaction();
        $mail_reponse = '';


        if($objListCommande->has('livreur')) {

            $objDeliveryMan = User::where('ref', '=', $objListCommande['livreur'])->first();
            if(empty($objDeliveryMan)) {
                DB::rollback();
                $this->_errorCode = 5;
                $this->_response['message'][] = "Le Livreur n'existe pas !";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

            if($objListCommande->has('commandes')) {

                foreach($objListCommande['commandes'] as $item) {

                    $objCommande = Commande::where('ref', '=',$item['commande'])->first();

                    try{

                        $objCommande->update(['user_gestionnaire_id' => $objUser->id]);

                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 6;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }

                    try{

                        $objCommande->update(['user_livreur_id' => $objDeliveryMan->id]);

                    }catch (Exception $objException) {
                        DB::rollback();
                        $this->_errorCode = 7;
                        if (in_array($this->_env, ['local', 'development'])) {
                        }
                        $this->_response['message'] = $objException->getMessage();
                        $this->_response['error_code'] = $this->prepareErrorCode();
                        return response()->json($this->_response);
                    }
                }

                /*
                * Shurtcode d'envoi de mail au livreur
                */

              /*  try {

                    Mail::to($objDeliveryMan->email)
                    ->send(new SendDeliverymanMail($objDeliveryMan));

                    $mail_reponse = 'Email has been sent to deliveryman';

                }catch (Exception $objException) {

                    DB::rollBack();
                    $this->_errorCode = 8;
                    if(in_array($this->_env, ['local', 'development'])) {
                    }
                    $this->_response['message'] = $objException->getMessage();
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }*/

            }else{

                DB::rollback();
                $this->_errorCode = 9;
                $this->_response['message'][] = "Veuillez renseigner la ou les commandes.";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);

            }

        }else{

            DB::rollback();
            $this->_errorCode = 10;
            $this->_response['message'][] = "Veuillez renseigner un livreur.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);

        }

        DB::commit();

        $toReturn = [
            'response_mail'=>$mail_reponse
        ];

        $this->_response['message'] = "Le livreur a été assigné avec succès ! ";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    //Fonction qui permet au livreur de voir la liste des commandes payées qui lui ont été assignées
    public function payOrderslist()
    {
        $this->_fnErrorCode = 1;

        $objUser = Auth::user();
        if(empty($objUser)){
            $this->_errorCode = 2;
			$this->_response['message'][] = 'Cette action nécéssite une connexion.';
			$this->_response['error_code'] = $this->prepareErrorCode();
			return response()->json($this->_response);
        }

        //On vérifie le rôle gestionnaire
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if(!in_array($objRole->alias,array('livreur'))) {
			$this->_errorCode = 3;
			$this->_response['message'][] = "Vous n'étes pas habilité à réaliser cette tâche.";
			$this->_response['error_code'] = $this->prepareErrorCode();
			return response()->json($this->_response);
		}

        try {

            $objAllCommandesPaid = Commande::with('user_client','statut_cmd','StatutLivraison','ville','user_gestionnaire')
            ->where('commandes.user_livreur_id', $objUser->id)
            ->where('commandes.statut_cmd_id', 4)
            ->orderBy('created_at', 'desc')
            ->get();

        } catch (Exception $objException) {
            DB::rollback();
            $this->_errorCode = 4;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        DB::commit();
        $toReturn = [
            'objet' => $objAllCommandesPaid
        ];
        $this->_response['message'] = "Liste des Commandes payées et assignées au livreur.";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }

    //signature du client
    public function signCustomer(Request $request){
        $this->_fnErrorCode = 1;

        $validator = Validator::make($request->all(), [
            'ref_commande' => 'string|required',
            'signature' => 'string|required'
        ]);

        //Vérification des paramètres
        if ($validator->fails()) {
            if (!empty($validator->errors()->all())) {
                foreach ($validator->errors()->all() as $error) {
                    $this->_response['message'][] = $error;
                }
            }
            $this->_errorCode = 2;
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }
        $objUser = Auth::user();
        if(empty($objUser)){
            if(in_array($this->_env, ['local', 'development'])){
                $this->_response['message'] = 'Cette action nécéssite une connexion.';
            }

            $this->_errorCode = 3;
            $this->_response['error_code']  = $this->prepareErrorCode();
            return response()->json( $this->_response );
        }
        //On vérifie que l'utilisateur est bien gestionnaire du centre
        $objRole = Role::where('id', '=', $objUser->role_id)->first();
        if($objRole->alias != 'livreur') {
            $this->_errorCode = 4;
            $this->_response['message'][] = "Vous n'étes pas habilité à réaliser cette tâche.";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        $objCommande = Commande::where('ref', '=', $request->get('ref_commande'))->first();
        if(empty($objCommande)) {
            $this->_errorCode = 5;
            $this->_response['message'][] = "La commande n'existe pas !";
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        try{
            $image = $request->get('signature');  // your base64 encoded
            $extension = explode('/', mime_content_type($request->get('signature')))[1];
            $image = str_replace('data:image/'.$extension.';base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = "signature_". date('D_M_Y_mhs') . '.'.$extension;

            if(Storage::disk('signature_client')->put($imageName, base64_decode($image))) {

                try{
                    //Mise à jour de la propriété statut_commande_id de la commande
                    //$objCommande->update(["signature_client" =>'E-shop-api/storage/app/public/images/signature/'.$imageName]);
                    $objCommande->update(["signature_client" =>'img/'.$imageName]);

                }catch(Exception $objException){
                    DB::rollback();
                    $this->_errorCode = 6;
                    if (in_array($this->_env, ['local', 'development'])) {
                        $this->_response['message'] = $objException->getMessage();
                    }
                    $this->_response['error_code'] = $this->prepareErrorCode();
                    return response()->json($this->_response);
                }

            }else {
                DB::rollback();
                $this->_errorCode = 7;
                $this->_response['message'][] = "Echec enregistrement de l'image !";
                $this->_response['error_code'] = $this->prepareErrorCode();
                return response()->json($this->_response);
            }

        } catch (Exception $objException) {
            DB::rollback();
            $this->_errorCode = 8;
            if (in_array($this->_env, ['local', 'development'])) {
                $this->_response['message'] = $objException->getMessage();
            }
            $this->_response['error_code'] = $this->prepareErrorCode();
            return response()->json($this->_response);
        }

        // Commit the queries!
        DB::commit();
        $toReturn = [
            "objet" => $objCommande
        ];
        $this->_response['message'] = "Commande livrée!";
        $this->_response['data'] = $toReturn;
        $this->_response['success'] = true;
        return response()->json($this->_response);
    }


   /* public function downloadPDF($id){

        $montant = 0;
        $montant_restant_a_payer = 0;

        $objTransaction = Transaction::findorFail($id);
        $objCommande = Commande::where('id','=',$objTransaction->commande_id)->first();
        $objCustomer = User::where('id','=',$objCommande->user_client_id)->first();

        //$objPanier = Panier::where('commande_id','=',$objCommande->id)->with('produit','produit.categorie','produit.subCategorie')->get();
        $objPanier = Panier::where('commande_id','=',$objCommande->id)->with('produit_combinaison','produit_combinaison.produit','produit_combinaison.produit.categorie','produit_combinaison.produit.!ùcategorie')
        ->get();

        $collDetail = collect();
        foreach($objPanier as $item) {
            $montant = intval($item->prix_total) + $montant;



            $collDetail->push(array(
                'panier' => $item,
                //'detail_panier' => $objDetailPanier
            ));

            //$item->produit->designation
            //dd($item->produit->categorie->name);
        }

        $montant_restant_a_payer = $montant - intval($objTransaction->total_payment);

        $data = [
            'commande' => $objCommande,
            'transaction' => $objTransaction,
            'all_paniers' => $collDetail,
            'montant_a_payer_panier' => $montant,
            'customer' => $objCustomer,
            'reste_a_payer' => $montant_restant_a_payer
        ];


        /**Laravel-dompdf pour générer un fichier pdf */
       /* $view = view('facture.facture', compact('data'))->render();

        PDF::loadHTML($view)
        ->setPaper('a4', 'portrait')
        ->setWarnings(false)
        ->save(public_path().'/facture_transaction_'.$objTransaction->id.'.pdf');//public_path().

        $filename = 'facture_transaction_'.$objTransaction->id.'.pdf';
        return response()->json($filename);

        try{

            Mail::to($objCustomer->email)
            ->send(new FactureMail($filename));

        } catch (Exception $objException) {

            DB::rollback();
            $this->_errorCode = 2;
            if (in_array($this->_env, ['local', 'development'])) {
            }
            $this->_response['message'] = $objException->getMessage();
            $this->_response['error_code'] = $this->prepareErrorCode();
        }


    }*/
}
