<?php

namespace mf\router;
use mf\auth\Authentification;
use presentapp\auth\PresentAuthentification;
use presentapp\control\PresentController;

class Router extends AbstractRouter
{

    public function addRoute($name, $url, $ctrl, $mth, $level)
    {

        self::$routes[$url] = [$ctrl, $mth, $level];
        self::$routes[$name] = [$ctrl, $mth, $level];
    }

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

    public function run()
    {

        // la requête
        $path = $this->http_req->path_info;

        // la route éxiste
        if (isset(self::$routes[$path])) {

            // stock le controller
            $ctrl = self::$routes[$path][0];
            // stock la méthode
            $mth = self::$routes[$path][1];
            // stock le niveau de la fonctionalité
            $lvl = self::$routes[$path][2];


            $check = new Authentification();

            // si mon level est 0 pas de verification
            if($lvl < 0) {

                $c = new $ctrl();
                $c->$mth();

            } else if ($check->checkAccessRight(PresentAuthentification::ACCESS_LEVEL_USER)){

                $c = new $ctrl();
                $c->$mth();

            } else {

                // stock le controller
                $ctrl = self::$routes['default'][0];
                // stock la méthode
                $mth = self::$routes['default'][1];

                $c = new $ctrl();
                $c->$mth();

            }


        } else {

            // stock le controller
            $ctrl = self::$routes['default'][0];
            // stock la méthode
            $mth = self::$routes['default'][1];

            $c = new $ctrl();
            $c->$mth();

        }
    }
}
   
