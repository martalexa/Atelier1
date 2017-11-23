<?php
namespace presentapp\control;
use \presentapp\model\Liste as Liste;
use \presentapp\model\Item as Item;
use \presentapp\model\Createur as Createur;
use presentapp\auth\PresentAuthentification;
use presentapp\model\Message;
use presentapp\view\PresentView;


class PresentController extends \mf\control\AbstractController
{
    /* Méthode viewHome :
     *
     * Réalise la fonctionnalité : afficher la liste des cadeaux
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    // VUE INSCRIPTION
    public function viewSignUp($msg = null){

        $vue = new \presentapp\view\PresentView($msg);
        return $vue->render('renderViewSignUp');
    }

    // Show login form
    public function viewLogin($msg = null){
        $vue = new \presentapp\view\PresentView($msg);
        $vue->render('renderLogin');
    }

    public function viewItem(){

        $crea = $_SESSION['user_login'];
        $idCrea = Createur::select()->where('email', '=', $crea)->first();
        $id = $idCrea->id;


        $nada = Liste::select('id','=',$id)->first();
        $vue =  new \presentapp\view\PresentView($nada);
        $vue->render('renderViewListeItem');

    }

    public function viewAddItem($msg = null){
        $id['idListe'] = $this->request->get['idListe'];
        if($msg != ''){
            $id['msg'] = $msg;
        }
        $vue = new \presentapp\view\PresentView($id);

        
        $vue->render('renderViewAddItem');

    }

    public function viewListe($msg = null){

        $persCo = $_SESSION['user_login'];
        $requeteCrea = Createur::select()->where('email', '=', $persCo)->first();
        $idc = $requeteCrea->id;

        $requeteListe = Liste::select()->where('createur', '=', $idc)->get();

        $vue = new \presentapp\view\PresentView($requeteListe, $msg);
        $vue->render('renderViewListe');
    }

    public function viewaddListe($msg = null){
		//if($msg != ''){
			$message = $msg;
		/*}else{
            $message = null;
        }*/
        $vue = new \presentapp\view\PresentView($message);// DÉFINIR MESSAGE
        $vue->render('renderViewAddListe');
    }

    public function checkaddliste(){


        if(filter_has_var(INPUT_POST,'nomListe') AND filter_has_var(INPUT_POST,'dateFinale') AND filter_has_var(INPUT_POST,'description') AND filter_has_var(INPUT_POST,'reponse')){
			$dateEntrée = $_POST['dateFinale'];
						//annee    // mois				// Jour
			$regexDate = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/";  // VERIFIE LE FORMAT DE LA DATE
			
			if (preg_match($regexDate, $dateEntrée))

			{
				
				$today=getDate();
				
				$tJour=$today["mday"];
				$tJour--;	//Pour accepter la date d'ajourd'hui
				$tMois=$today["mon"];
				$tAnnee=$today["year"];
				$dateAuj = $tAnnee."-".$tMois."-".$tJour;
				
							//Date entrée 			//Aujourd'hui
				if(strtotime($dateEntrée) > strtotime($dateAuj)){
					$nomListe = filter_input(INPUT_POST,'nomListe',FILTER_SANITIZE_SPECIAL_CHARS);
					$dateFinal = filter_input(INPUT_POST,'dateFinale',FILTER_SANITIZE_SPECIAL_CHARS);
					$desc = filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS);
                    $reponse = filter_input(INPUT_POST,'reponse',FILTER_SANITIZE_SPECIAL_CHARS);


                //recuperation de l'id de la personne connecté
                                    $persCo = $_SESSION['user_login'];
                    $requeteCrea = Createur::select()->where('email', '=', $persCo)->first();
                    $testListe = $requeteCrea->listes()->where('nom','=',$nomListe)->first();

                    // on voie si la liste existe déja
                    if(isset($testListe) && $nomListe == $testListe->nom){


                        $this->viewListe();

                    } else{

                        $idc = $requeteCrea->id;

                        $l = new Liste();
                        $l->idpartage= uniqid();
                        $l->nom = $nomListe;
                        $l->date_final = $dateFinal;
                        $l->createur = $idc;
                        $l->description = $desc;
                        $l->pourmoi = $reponse;
                        $l->save();

                        $message = "<div class='alert alert-success col-12'>La liste a bien été ajouté</div>";
                        $this->viewListe($message);
                    }


				}
				else{
 					$message = "<div class='alert alert-danger col-12'>C'est un peu tard pour organiser un évènement, la date est déjà passée</div>";
            		$this->viewAddListe($message);	
				}
        	

    		}
			else{
				$message = "<div class='alert alert-danger col-12'>La date n'est pas conforme : AAAA-MM-JJ</div>";
            	$this->viewAddListe($message);	
				
			}
			
        } else {
            $message = "<div class='alert alert-danger col-12'>La liste n'a pas été ajouté</div>";
            $this->viewListe($message);
        }
    }

    public function viewSupprliste(){
        $idListe = $this->request->get['idListe'];

        try{
            Liste::where('id', '=', $idListe)->delete();
            Item::where('id_list', '=', $idListe)->delete();
            $message = "<div class='alert alert-success col-12'>La liste a bien été supprimée</div>";
            $this->viewListe($message);
        }catch(\Exception $e){
            $message = "<div class='alert alert-success col-12'>La liste n'a pas été supprimée</div>";
            $this->viewListe($message);
        }

        

        
    }

    public function addItem(){

		$regexTarif='/[^0-9\.\,]/';
		
        if(filter_has_var(INPUT_POST,'nom') AND filter_has_var(INPUT_POST,'description') AND filter_has_var(INPUT_POST,'tarif') AND filter_has_var(INPUT_POST,'urlImage')){
            
            $prix=$_POST["tarif"];
            $nom = filter_input(INPUT_POST,'nom',FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS);
            $tarif = filter_input(INPUT_POST,'tarif',FILTER_SANITIZE_SPECIAL_CHARS);
            $urlImage = filter_input(INPUT_POST,'urlImage',FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST,'url',FILTER_SANITIZE_SPECIAL_CHARS);
                
            
                

                if(preg_match($regexTarif, $prix)){
                    $message = "<div class='alert alert-danger col-12'>Le tarif doit être un nombre ou un chiffre</div>";
                    $this->viewAddItem($message);
                }else{

                    $tarifformatpoint = str_replace(',', '.', $tarif);
                    $tarifformat = number_format($tarifformatpoint, 2, '.', ' ');

                    $item=new Item();

                    if(isset($_POST['url'])){
                        $url = filter_input(INPUT_POST,'url',FILTER_SANITIZE_SPECIAL_CHARS);
                        $item->url=$url;
                    }

                    $idListe = $this->request->get['idListe'];
                    $requeteListe = Liste::select('id')->where('idPartage', '=', $idListe)->first();
                    
                    $item->nom=$nom;
                    $item->description = $description;
                    $item->urlImage = $urlImage;
                    $item->tarif=$tarifformat;
                    $item->id_list = $requeteListe['id'];
                    $item->save();


                    $message = "<div class='alert alert-success col-12'>L'item a bien été ajouté</div>";
                    $this->viewListeItem($message);
                }			
        }else{
            $message = "<div class='alert alert-danger col-12'>L'item n'a pas été ajouté</div>";
            $this->viewListeItem($message);
        }
    }



    public function logout(){
        $logout = new \mf\auth\Authentification();
        $logout->logout();

        $this->viewLogin();
    }

    public function check_login(){

        $vue = new \presentapp\view\PresentView('');

        if(isset($_POST['email'], $_POST['pw']) AND !empty($_POST['email']) AND !empty($_POST['pw'])){
            $user = filter_input(INPUT_POST,'email',FILTER_SANITIZE_STRING);
            $pass = filter_input(INPUT_POST,'pw',FILTER_SANITIZE_SPECIAL_CHARS);

            $connect = new PresentAuthentification();

            try{

                $connect->login($user,$pass);
                $this->viewListe();
                

            }catch(\Exception $e){
                $message = "<div class='alert alert-danger col-12'>Problème d'authentification, votre email n'existe pas ou a été mal entrée</div>";
                $this->viewLogin($message);
            }

        } else {

            $this->viewLogin();

        }
    }

    // CONTROL DE L'INSCRIPTION
    public function checkSignup(){

        $regex1='/[^a-zA-Z \-éèêëçäà]/';
		
		// Politique de MDP
      	$policyL = new \PasswordPolicy\Policy; // Lower
		$policyU = new \PasswordPolicy\Policy; // Upper						// Policy
		$policyD = new \PasswordPolicy\Policy; // Digit
		$policyS = new \PasswordPolicy\Policy; // symnbole
		$policyL->contains('lowercase', $policyL->atLeast(1));
		$policyU->contains('uppercase', $policyU->atLeast(1));
		$policyD->contains('digit', $policyD->atLeast(1));
		$policyS->contains('symbol', $policyS->atLeast(1));
		
        if(filter_has_var(INPUT_POST,'fullname') AND filter_has_var(INPUT_POST,'username') AND filter_has_var(INPUT_POST,'pw') AND filter_has_var(INPUT_POST,'pw') AND filter_has_var(INPUT_POST,'pw_repeat') AND filter_has_var(INPUT_POST, 'mail')){

            $email_a = $_POST["mail"];
            $prenom=$_POST["fullname"];
            $nom=$_POST["username"];
            $mdp=$_POST["pw"];
            $longueur= strlen($mdp);
            if(preg_match($regex1, $prenom)){
                $message = "<div class='alert alert-danger col-12'>Le prénom n'est pas au bon format</div>";
                $this->viewSignUp($message);
            }else{
                if(preg_match($regex1, $nom)){
                    $message = "<div class='alert alert-danger col-12'>Le nom n'est pas au bon format</div>";
                    $this->viewSignUp($message);
                }
                else{
                    if(filter_var($email_a, FILTER_VALIDATE_EMAIL)){

                        $fullname = filter_input(INPUT_POST,'fullname',FILTER_SANITIZE_STRING);
                        $username = filter_input(INPUT_POST,'username',FILTER_SANITIZE_STRING);
                        $pw = filter_input(INPUT_POST,'pw',FILTER_SANITIZE_SPECIAL_CHARS);
                        $pw_repeat = filter_input(INPUT_POST,'pw_repeat',FILTER_SANITIZE_SPECIAL_CHARS);

                        if($longueur < 8){  // Verif longueur mdp
                            $message = "<div class='alert alert-danger col-12'>Le mot de passe est trop court, 8 caractères minimum</div>";
                            $this->viewSignUp($message);
                        }else{
								
							$resultL = $policyL->test($mdp);  
							if($resultL->result){		// Verif minuscule
								$resultU = $policyU->test($mdp);
								if($resultU->result){		// Verif majuscule
									$resultD = $policyD->test($mdp);
									if($resultD->result){		// Verif Chiffre
										$resultS = $policyS->test($mdp);
										if($resultS->result){   	// Verif Symbole 
										  
											if($pw === $pw_repeat){

												$signUp = new PresentAuthentification();

												try{

													$signUp->createUser($username, $pw, $fullname,$email_a);

                                                    // Si tous se passe bien on renvoie sur les listes
                                                    
                                                    $message = "<div class='alert alert-success col-12'>Vous êtes maintenant authentifié</div>";
													$this->viewListe($message);

												}catch (\Exception $e){

                                                    // si la création du user à échouée
                                                    $message = "<div class='alert alert-danger col-12'>La création du compte a échouée</div>";
                                                    $this->viewSignUp($message);

												}
											}

											else {
                                                $message = "<div class='alert alert-danger col-12'>Les mots de passes ne sont pas les mêmes</div>";
                                                $this->viewSignUp($message);
											}
											}
										else{
                                            $message = "<div class='alert alert-danger col-12'>Vous devez mettre au moins un symbole dans votre mot de passe</div>";
                                            $this->viewSignUp($message);
                                            
										}
									}
									else{
                                        $message = "<div class='alert alert-danger col-12'>Vous devez mettre au moins un chiffre dans votre mot de passe</div>";
                                        $this->viewSignUp($message);
									}
								}
								else{
                                    $message = "<div class='alert alert-danger col-12'>Vous devez mettre au moins une majuscule dans votre mot de passe</div>";
                                    $this->viewSignUp($message);
								}
									
							}

							else{
                                $message = "<div class='alert alert-danger col-12'>Vous devez mettre au moins une minuscule dans votre mot de passe</div>";
                                $this->viewSignUp($message);
							}
						}
                    } else {
                        $message = "<div class='alert alert-danger col-12'>Saisisser une vrai adresse email</div>";
                        $this->viewSignUp($message);
                    }
                }
            }
        } else {
            $message = "<div class='alert alert-danger col-12'>Veuillez remplir tous les champs</div>";
            $this->viewSignUp($message);
        }
	}



    // AFFICHE LA LISTE DES ITEMS D'UNE LISTE
    public function viewListeItem($msg = null){

            $id = $this->request->get['idListe'];
            $l= Liste::where('idPartage','=',$id)->first();

            // ajout des donnée concernant les messages
            $message = Message::where('id_list','=',$id)->get();
            
            if(isset($l)){
                if($msg != ''){
                    $l['msg']=$msg;
                }
                // je sais pas comment tu utilise info du coup je me suis créer mon propre data -> message
                $vue = new \presentapp\view\PresentView($l,null,$message);

                $vue->render('renderViewListeItem');
            }else{
                $message = "<div class='alert alert-danger col-12'>La liste n'existe pas</div>";

                $this->viewListe($message);
            }            
    }

    public function viewSupprItem(){
        $idItem = $this->request->get['idItem'];
        $idListe = $this->request->get['idListe'];

        

        if(Item::where('id', '=', $idItem)->delete()){
            $message = "<div class='alert alert-success col-12'>Le cadeau a bien été supprimé</div>";
            $this->viewListeItem($message);
        }else{
            $message = "<div class='alert alert-danger col-12'>Le cadeau n'a pas été supprimé</div>";
            $this->viewListeItem($message);
        }
    }

    public function viewModifierItem(){
        $idItem = $this->request->get['idItem'];
        $idListe = $this->request->get['idListe'];

        $item = Item::where('id', '=', $idItem)->first();
        if(isset($item)){
            $item['idListe'] = $idListe;
            $vue = new \presentapp\view\PresentView($item);
            $vue->render('renderViewModifierItem');
        }else{
            $message = "<div class='alert alert-danger col-12'>Le cadeau n'existe pas</div>";
            $this->viewListeItem($message);
        } 
    }

    public function viewReserverItem(){
        $tab['idItem'] = $this->request->get['idItem'];
        $tab['idListe'] = $this->request->get['idListe'];

        $item = new Item();
        $nomItem = $item->select('nom')->where('id', '=', $tab['idItem'])->first();

        if(isset($nomItem)){
            $tab['nom'] = $nomItem;

            $vue = new \presentapp\view\PresentView($tab);
            $vue->render('renderViewReserverItem');
        }else{
            $message = "<div class='alert alert-danger col-12'>Le cadeau n'existe pas</div>";
            $this->viewListeItem($message);
        }

        
    }

    public function reserverItem(){
        $idItem = $this->request->get['idItem'];

        if(filter_has_var(INPUT_POST,'nom') AND filter_has_var(INPUT_POST,'message')){
            $message = filter_input(INPUT_POST,'message',FILTER_SANITIZE_STRING);
            $nom = filter_input(INPUT_POST,'nom',FILTER_SANITIZE_STRING);

            $item = new Item();
            $update = $item->where('id', '=', $idItem)->first();
            $update->status = 1;
            $update->message = $message;
            $update->reservePart = $nom;
            $update->save();

            $this->viewListeItem();
        }else{
            $this->viewReserverItem();
        }
    }

    public function modifierItemBDD(){
        if(filter_has_var(INPUT_POST,'nom') AND filter_has_var(INPUT_POST,'url') AND filter_has_var(INPUT_POST,'description') AND filter_has_var(INPUT_POST,'tarif') AND filter_has_var(INPUT_POST,'urlImage')){

            $nom = filter_input(INPUT_POST,'nom',FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS);
            $tarif = filter_input(INPUT_POST,'tarif',FILTER_SANITIZE_SPECIAL_CHARS);
            $urlImage = filter_input(INPUT_POST,'urlImage',FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST,'url',FILTER_SANITIZE_SPECIAL_CHARS);

            $tarifformat = str_replace(',', '.', $tarif);

            $item=new Item();

            /*if(isset($_POST['url'])){
                $url = filter_input(INPUT_POST,'url',FILTER_SANITIZE_SPECIAL_CHARS);
                $item->url=$url;
            }*/

            $idListe = $this->request->get['idListe'];
            $requeteListe = Liste::select('id')->where('idPartage', '=', $idListe)->first();

            $item->nom=$nom;
            $item->description = $description;
            $item->urlImage = $urlImage;
            $item->tarif=$tarifformat;
            $item->url = $url;
            $item->id_list = $requeteListe['id'];
            
            
            $item->save();

            $message = "<div class='alert alert-success col-12'>L'item a bien été modifié</div>";
            $this->viewListeItem($message);
        } else {
            $message = "<div class='alert alert-danger col-12'>L'item n'a pas été modifié</div>";
            $this->viewListeItem($message);
        }
    }


    public function checkMessageItemPrivate(){

        // on regarde si ca existe
        if(isset($this->request->get['idListe']) && isset($this->request->get['idItem'])){

            // si c'est pas vide
            $idListe = $this->request->get['idListe'];
            $idItem = $this->request->get['idItem'];

            // On recupère la date
            $requeteDate = Liste::select('date_final')->where('idPartage', '=', $idListe)->first();

            // On recupère si la liste est pour lui
            $requeteChoix = Liste::select('pourmoi')->where('idPartage', '=', $idListe)->first();

            // si la date existe
            if(isset($requeteDate->date_final)){

                $dateFinal = $requeteDate['date_final'];
                $now = date('Y-m-d');

                // On compare les dates
                if ($dateFinal <= $now) {

                    $resultIdItem = Item::where('id', '=', $idItem)->first();
                    /*->Item()->where('id_list','=',$idListe)->get();*/


                    $vue = new \presentapp\view\PresentView($resultIdItem);
                    $vue->render('renderMessageItemPrivate');

                } else {

                    $message = "<div class='alert alert-danger col-12'>Vous devez attendre la date de l'évenement pour voir les messages</div>";
                    $this->viewListeItem($message);
                }
            } else {

                $this->viewListeItem();

            }
        } else {

            $this->viewListeItem();

        }

    }



    public function checkMessageItemAll(){

        // si la variable existe
        if(isset($_POST['textall']) && !empty($_POST['textall']) && isset($_POST['id_list']) && !empty($_POST['id_list'])){

            $idSend = $_POST['id_list'];

            // tester l'id reçu
            $requeteListe = Liste::select('id')->where('idPartage', '=', $idSend)->first();

            // si l'id est la bonne alors je devrais recevoir un résultat

            if(is_null($requeteListe)){

                // si le login est corrompus on renvoi vers la page de login
                $this->logout();

            } else {

                // si il y a correspondance c'est que la donnée est bonne
                $text = filter_input(INPUT_POST,'textall',FILTER_SANITIZE_SPECIAL_CHARS);
                $id = filter_input(INPUT_POST,'id_list',FILTER_SANITIZE_SPECIAL_CHARS);

                // ajoute le text à la table message
                $message = new Message();

                $message->contenu = $text;
                $message->id_list = $id;

                $message->save();

                // la sauvegarde est effectuée, je renvoie vers listitem

                $this->viewListeItem();

               // pour select après select m.contenu
              
            }

        } else if(empty($_POST['textall'])){

            $this->viewListeItem();
        }

    }
}
