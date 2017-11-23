<?php

namespace mf\router;

abstract class AbstractRouter {

    protected $http_req = null;

    
    /*
     * Attribut statique qui stocke les routes possibles de l'application 
     * 
     * - Une route est représentée par un tableau :
     *       [ le controlleur, la methode ]
     * 
     */
    
    static public $routes = array ();

    public function __construct(){
        $this->http_req = new \mf\utils\HttpRequest();
    }

    
    /* 
     * Méthode addRoute : ajoute une route a la liste des routes 
     *
     * Paramètres :
     * 
     * - $name (String) : un nom pour la route
     * - $url (String)  : l'url de la route
     * - $ctrl (String) : le nom de la classe du Contrôleur 
     * - $mth (String)  : le nom de la méthode qui réalise la fonctionalité 
     *                     de la route
     * 
     * Algorithme :
     *
     * - Ajouter le tablau [ $ctrl, $mth ] au tableau $this->route 
     *   sous la clé $url et sous la clé $name 
     *
     */


    abstract public function addRoute($name, $url, $ctrl, $mth, $level);

    /*
     * Méthode run : execute une route en fonction de la requête 
     *
     *
     * Algorithme :
     * 
     * - l'URL de la route est stockée dans l'attribut $path_info de 
     *         $http_request
     *   Et si une route existe dans le tableau $route sous le nom $path_info
     *     - créer une instance du controleur de la route
     *     - exécuter la méthode de la route 
     * - Sinon 
     *     - exécuter la route par défaut : 
     *        - créer une instance du controleur de la route par défault
     *        - exécuter la méthode de la route par défault
     * 
     */
    
    abstract public function run();

}
    
   
