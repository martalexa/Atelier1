<?php
namespace presentapp\auth;

use presentapp\model\Createur;

class PresentAuthentification extends \mf\auth\Authentification {

	/*
	 *
	 * LEVEL ACCESS
	 *
	 */

	const ACCESS_LEVEL_USER  = 100;   
	const ACCESS_LEVEL_ADMIN = 200;

	/* constructeur */
	public function __construct(){
		parent::__construct();
	}

	//
	// CREATE USER
    //
	public function createUser($username, $pass, $fullname,$email,
				$level=self::ACCESS_LEVEL_USER) {

		$utilisateur=Createur::select('email')->where('email','=',$email)->first();

		if(isset($utilisateur->email)){ //S'il y a déjà un utilisateur

			throw new \Exception(" Un utilisateur avec cet email utilisateur éxiste déja.");

		}else{

		    //Sinon tout va bien !
			$user=new Createur();
			$user->nom=$username;
			$user->password=$this->hashPassword($pass);
			$user->prenom=$fullname;
			$user->level=$level;
			$user->email=$email;
			$user->save();

			// et on en profite pour connecter directement l'utilsateur
            $this->login($email,$pass);

		}
	}

	//
    // LOGIN WITH UPDATE SESSION AND VERIFY PASS
    //
	public function login($email, $password){
		$userBDD=Createur::select('password','level')->where('email','=',$email)->first();
		if(!isset($userBDD)){
			throw new \Exception("Login ou mot de pass incorrecte.");
		}else{
			if($this->verifyPassword($password, $userBDD->password)){
				$this->updateSession($email,$userBDD->level);				
			}else{
				throw new \Exception("Login ou mot de pass incorrecte.");
			}
		}
	}

}

