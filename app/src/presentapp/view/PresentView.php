<?php
namespace presentapp\view;

use presentapp\control\PresentController;
use \presentapp\model\Item as Item;


use mf\view\AbstractView;

class PresentView extends AbstractView
{
    private $info;
    protected $message     = null;

    /* Constructeur
    *
    * Appelle le constructeur de la classe \mf\view\AbstractView
    */
    public function __construct( $data, $info = null, $message = null ){
        parent::__construct($data);
        $this->info = $info;
        $this->message = $message;
    }

    // HEADER
    private function renderHeader(){

        if(isset($_SESSION['user_login'])){
            $html =
                <<<EOT
                
                
                <header class="header theme-backcolor1">
        		<h1 class="logo"><a href="$this->script_name/liste/"><img src="$this->app_root/app/src/mf/html/web/img/png/003-gift.png" alt="icon" class="icon">Mecado</a></h1>
                <span class="icon-menu" id="btn-menu"></span>
                <nav class="nav" id="nav">
                    <ul class="menu">
                        <li class="menu_item"><a class="menu_link select" href="$this->script_name/logout/">Deconnection</a></li>
                        <li class="menu_item"><a class="menu_link" href="$this->script_name/addliste/">Ajouter une liste</a></li>
                        <li class="menu_item"><a class="menu_link" href="$this->script_name/liste/">Mes listes</a></li>
                    </ul>
                </nav> 
        </header>
EOT;
        } else {

            $html = <<<EOT
            <header class="header">
        <h1 class="logo"><img src="$this->app_root/app/src/mf/html/web/img/png/003-gift.png" alt="icon" class="icon">Mecado</h1>
                <span class="icon-menu" id="btn-menu"></span>
                <nav class="nav" id="nav">
                    <ul class="menu">
                        <li class="menu_item"><a  class="menu_link" href="$this->script_name/signup/">inscription</a></li>
                        <li class="menu_item"><a  class="menu_link" href="$this->script_name/login/">connexion</a></li>
                    </ul>
                </nav> 
        </header>

EOT;

        }


        return $html;
    }

    // FOOTER
    private function renderFooter(){

       return '<div class="container"><h1 class="centrar">Mecado</h1><h2 class="centrar">LP Cisiie 2017/2018</h2><h2 class="centrar">Alexandra MARTIN - Daniel RICKLIN - Faustin RASSU - Yann DUMAS - Gerardo GUTIERREZ - Atelier 1</h2></div>';

    }

    // FORM
    private  function renderViewSignUp(){
        $html = "<section>
        
        <div class='container'>";

        if(isset($this->data)){
            $html .= $this->data;
        }
      
      $html.=  "<div class='formulaire'>
       <h1 class='centrar'>Inscription</h1>
        <form method='post' action='".$this->script_name."/check_signup/'>
                    <input type='text' name='fullname' placeholder='Prénom' required/>
                    <input type='text' name='username' placeholder='Nom' required/>
                    <input type='email' name='mail' placeholder='mail' required/>
                    <input type='password' name='pw' placeholder='password' required/>
                    <input type='password' name='pw_repeat' placeholder='Repeat password' required/>
                   
                    <input type='submit' value='Créer'/>
        </form>
		<p class='centrar'>Indications mot de passe : au moins une minuscule, une majuscule, un chiffre, un caractère spécial et 8 caractères  </p>
       </div> 
     </div>
                
        </section>";
        return $html;


    }

	 // LISTE
    private  function renderViewListe(){

        $html ="<div class='container'>";
        if(isset($this->info)){
            $html .= $this->info;
        }

        if(isset($_SESSION['user_login'])){
        $html .= '<div class="col-12 sp centrar"><a href="'.$this->script_name.'/addliste/">
        <h1><i class="fa fa-plus" aria-hidden="true"></i>Ajouter une liste</h1>
        </a></div>';
        }

		foreach ($this->data as $value){

            $html .="<div class='col-3 sp'>
            <h2>".$value->nom."</h2>";
            $html .= "<h3>Reservation possible jusqu'au: </h3><p>".$value->date_final."</p>";
		
            if(isset($_SESSION['user_login'])){
                $html .= "<h3>Lien de partage : </h3><p>https://".$this->name.$this->script_name."/listeItem/?idListe=".$value->idPartage."</p>";
            }
            $html .= '<a href="'.$this->script_name.'/supprliste/?idListe='.$value->id.'"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></a>';
            $html .= '<a href="' . $this->script_name. '/listeItem/?idListe=' . $value->idPartage . '"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></a>';
            $html .= "</div>";
            
        }
		$html .= "</div>";
		
        return $html;
    }


    private  function renderViewAddListe(){
        
	
		$html =       
'<section class="container">';
     if($this->data !== null){
			$html .= $this->data;
		}
      $html.='<div class="formulaire">
	  	<h1 class="centrar">Ajouter une nouvelle liste : </h1>
        <form method="post" action="'.$this->script_name.'/checkaddliste/">
            <input type="text" name="nomListe" placeholder="Nom de l\'évènement" required/>
            <textarea placeholder="description" name="description"></textarea>
            <input type="date" id="date" name="dateFinale" placeholder="Date de l\'évènement : AAAA-MM-JJ" required/>
            <h3 class="centrar">La liste est pour vous ?</h3>
            <h4 class="centrar">Oui : </h4><input type="radio" name="reponse" value="oui" checked/>
            <h4 class="centrar">Non : </h4><input type="radio" name="reponse" value="non"/>
            <input type="submit" value="Ajouter"/>
        </form>
        </div>    
 </section>';
        return $html;
    }


    public function renderLogin(){
        $html =

        "<section>
           <div class='container'>";
           if($this->data !== null){
            $html .= $this->data;
            }
      $html .= "<div class='formulaire'>
       <h3 class='centrar'>Connexion</h3>
        <form method='post' action='".$this->script_name."/check_login/'>
         <input type='email' name='email' placeholder='email' required> 
         <input type='password' name='pw' placeholder='password' required>  
         <input type='submit' value='login'>
        </form>
       </div> 
     </div>
    
    </section>";

                return $html;
    }


    private function renderViewAddItem(){
        $idList = $this->data['idListe'];
        $html = "<div class='container'>";
        if(isset($this->data['msg'])){
            $html .= $this->data['msg'];
        }  
     $html .= "<section class='col-8 offset-2'>
      <div class='formulaire'>
       <h1 class='centrar'>Ajouter un nouveau cadeau</h1>
       
       <form method='post' enctype='multipart/form-data' action=".$this->script_name."/addItem/?idListe=".$idList.">
       
        <input type='text' id='nom' name='nom' placeholder='Nom' required/>
         
        <input id='tarif' name='tarif' type='text' placeholder='Tarif' step='0.01'/>
        <input type='text' id='url' placeholder='Url vers un autre site' name='url'/> 
        <Textarea rows='4' cols='15' placeholder='Description' name='description'></Textarea>
        <input type='text' name='urlImage' id='urlimage' placeholder='Ajouter le lien d une image'/>
        <input type='submit' value='ajouter'>
       </form>
       </div> 
     </section>
     </div>";

        return $html;


    }



    public function renderViewModifierItem(){
        $idListe = $this->data->idListe;
        $id = $this->data->id;
        $nom = $this->data->nom;
        $tarif = $this->data->tarif;
        $url = $this->data->url;
        $description = $this->data->description;
        $urlImage = $this->data->urlImage;

        $html = <<<EOT
        
    <div class="container">
     <section class="col-8 offset-2">
      <div class="formulaire">
       <legend>Modifier le cadeau</legend>
       
       <form method="post" enctype="multipart/form-data" action="$this->script_name/modifierItemBDD/?idListe=$idListe&idItem=$id">
       
       <label for="nom">Nom</label><input type="text" id="nom" name="nom" value="$nom" required/>
         
       <label for="tarif">Tarif</label><input id="tarif" name="tarif" type="number" value="$tarif" step="0.01"/>
         <input type="url" id="url" value="$url" name="url"> 
         <textarea rows="4" cols="15" name="description">$description</textarea>
         <input type="text" name="urlImage" id="image" value="$urlImage"/>
         <input type="submit" value="Modifier">
        </form>
       </div> 
     </section>
    </div>

EOT;

        return $html;
    }

    public function renderViewListeItem(){

        $html = '<div class="container">';
        if($this->data['msg'] !== null){
            $html .= $this->data['msg'];
        }
        $html .="<h1 class='col-12'>Liste pour l'évenement: " . $this->data->nom . "</h1><br>";
        $html .= "<h1 class='col-12'>Date de l'évènement : ".$this->data->date_final . "</h1>";

        if(isset($_SESSION['user_login'])){
            $html .= "<div class='col-12 sp centrar'><a href=".$this->script_name."/ViewAddItem/?idListe=".$this->data->idPartage.">
                    <h1><i class=\"fa fa-plus\" aria-hidden=\"true\"></i> Ajouter Cadeau</h1></a></div>";
        }

        $tab = $this->data->items()->where('id_list','=',$this->data->id)->get();

        foreach ($tab as $key => $value){
            if($value['status']==0){
                $status= 'disponible </br>';
				$status .= "<p><a href=".$this->script_name."/reserverMessageItem/?idListe=".$this->data->idPartage."&idItem=".$value['id'].">je souhaite reserver ce cadeau</a></p>";
            }else{
                $status="déjà pris";
            }

            $html .= '<div class="col-3 sp">';
            $html .= '<h1 class="sp1">'.$value['nom'].'</h1>';
            $html .= '<img src="'.$value['urlImage'].'" alt="'.$value['urlimage'].'" class="cadeau"/>';
            $html .= '<h4>Description : </h4><p>'.$value['description'].'</p>';
            $html .= '<h4>'.$value['tarif'].'€</h4>';

            if(isset($value['url']) && !empty($value['url'])){

                $html .= '<h4><a href="'.$value['url'].'">Lien vers la boutique (cliquer ici)</a></h4>';
            }

			if(!isset($_SESSION['user_login'])){
                $html .= "<p>Status : $status</p>";
                $html .= "<a class='button' href=".$value['url'].">Plus d'information</a>";
            }
            if(isset($_SESSION['user_login'])) {

                $html .= '<a href="' . $this->script_name . '/supprItem/?idListe=' . $this->data->idPartage . '&idItem=' . $value->id . '"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></a>';
                $html .= '<a href="' . $this->script_name . '/modifierItem/?idListe=' . $this->data->idPartage . '&idItem=' . $value->id . '"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></a>';

                $html .= "<div class='col-12'><p><a href=" . $this->script_name . "/messageItemPrivate/?idListe=" . $this->data->idPartage . "&idItem=" . $value->id . ">Voir les messages déposer par vos invités</a></p></div>";


            }


            $html .= '</div>';
        }

        $html .= "<div class='container'>";
        $html .= "<section class ='col-12 sp centrar'><h1 class='centrar'>Ajouter un message pour tous les participants : </h1>";
        $html .= '<form method="post" action="'.$this->script_name.'/messageItemAll/?idListe='.$this->data->idPartage.'">';
        $html .= '<textarea placeholder="votre message" name="textall"></textarea>';
        $html .= '<input type="hidden" name="id_list" value="'.$this->data->idPartage.'">';
        $html .= '<input type="submit" value="poster">';
        $html .= '</form>';
        $html .= '</section>';


        // afficher les messages

        // je récupere les données du message
        $mess = $this->message;

        $html .= "<section class ='col-12 sp centrar'>";
        $html .= "<h1>Message adressé au groupe</h1>";
        foreach ($mess as $key => $value){

            $html .= '<p><i class="fa fa-commenting" aria-hidden="true"></i> '.$value["contenu"].'</p>';

        }
        $html .= '</section>';
        $html .= '</div>';


        return $html;
    }

    public function renderViewReserverItem(){
        
        $idItem = $this->data['idItem'];
        $idListe = $this->data['idListe'];
        $nom = $this->data['nom']->nom;
        
        $html = "<div class='container'><h1>Reserver : ". $nom."</h1>";
        $html .= "
        <div class='col-8 offset-2'>
        <div class='formulaire'><form method='post' action=".$this->script_name."/reserverItem/?idItem=".$idItem."&idListe=".$idListe.">
            <label for='nom'>Nom</label>
            <input type='text' placeholder='Votre nom' name='nom'/>
            <label for='message'>Message</label>
            <textarea placeholder='Message' name='message'></textarea>
            <input type='submit' value='Reserver'/>
        </form></div></div></div>";
        return $html;        
    }




    public function renderMessageItemPrivate(){

        if(isset($this->data->message)){

            $html = '<div class="container">';

            $html .= '<div class="col-3 sp">';
            $html .= '<h3 class="sp1">'.$this->data->nom.'</h3>';

            $html .= '<h4>Description : </h4><p>'.$this->data->description.'</p>';

            $html .= '<h4>Offert par : </h4><p>'.$this->data->reservePart.'</p>';

            $html .= '<h4>Message déposé par l\'invité :</h4><p>'.$this->data->message.'</p>';
            $html .= '</div></div>';

            return $html;

        } else {

            $return = new PresentController();
            $return->viewListeItem();
        }
    }





    public function renderBody($selector=null){

        $header = $this->renderHeader();
        $footer = $this->renderFooter();

        $body = $this->$selector();

        $html =

            <<<EOT

        ${header}
        <section id="container" class="theme-backcolor2">
            ${body}
        </section>
        <footer class="theme-backcolor1">${footer}</footer>

EOT;

        return  $html;
    }
}