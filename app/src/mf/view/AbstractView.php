<?php

namespace mf\view;

abstract class AbstractView {

    // comportement étrange des styles faires attention
    static protected $style_sheets = ['web/css/principal.css','web/icon/css/font-awesome.min.css','web/icomoon/style.css']; /* un tableau de fichiers style */
    static protected $tabJs = ['web/js/menu.js'];
    static protected $app_title    = "Mecado"; /* un titre de document */
    
    protected $app_root    = null; /* répertoire racine de l'application */
    protected $script_name = null; /* le chemin vers le script principal */
    protected $name = null;
    
    protected $data        = null; /* le modèle de données nécessaire */

    
    /* Constructeur 
     * 
     * - Recupérer le répertoire racine de l'application et le nom du
     *   script principal depuis une instance de HttpRequest. Ces
     *   données serviront pour construire les URL des liens et des
     *   actions des formulaires. 
     * 
     * - Stock les données passées en paramètre dans l'attribut $data.
     *   Ces données sont une instance du modèle ou un tableau
     *   d'instances de modèles.
     *
     */

    public function __construct( $data ){
        $http = new \mf\utils\HttpRequest();
        
        $this->script_name  = $http->script_name;
        $this->app_root     = $http->root;
        $this->name = $http->name;

        $this->data         = $data;

    }
    
    /* pour stocker des chemins vers des feuille de sytles */
    static public function setStyleSheet(array $list_of_files){
        self::$style_sheets = $list_of_files;
    }

    /* pour stocker le titre du document HTML */
    static public function setAppTitle($title){
        self::$app_title = $title;
    }

    /* La methode renderBody 
     * 
     * Cette méthode retourne le contenu HTML de la balise body autrement dit 
     * le contenu du document. Elle prend un sélecteur en paramètre dont la 
     * valeur indique quelle vue il faut générer.
     * 
     * cette méthode est a définir dans les classes concrètess des vues, 
     * elle est appelée depuis la methode render ci-dessous.
     * 
     */
    
    abstract protected function renderBody($selector=null);
    


    /* la méthodes render
     * 
     * cette méthode génère le code HTML d'une page complète depuis le doctype 
     * jusqu'au </html>. 
     * 
     * Elle dèfinit les entêtes HTML, le titre de la page et lie les feuilles 
     * de style. le contenu du document est récupéré depuis les méthodes 
     * renderBody des sous classe. 
     *
     * Elle utilise la syntaxe HEREDOC pour définir un patron et
     * l'écrire la chaine de caractère de la page entière. Voir la
     * documentation ici:
     *
     * http://php.net/manual/fr/language.types.string.php#language.types.string.syntax.heredoc
     *
     */
    
    public function render($selector){
        /* le titre du document */
        $title = self::$app_title;

        /* les feuilles de style */
        $styles = '';
        foreach ( self::$style_sheets as $file )
            $styles .= '<link rel="stylesheet" href="'.$this->app_root.'/app/src/mf/html/'.$file.'"> ';

        $js = '';
        foreach (self::$tabJs as $fileJs){
            $js .= '<script src="'.$this->app_root.'/app/src/mf/html/'.$fileJs.'"></script>';
        }
        /* on appele la methode renderBody de la sous classe */
        $body = $this->renderBody($selector);
        

        /* construire la structure de la page 
         * 
         *  Noter l'utilisation des variables ${title} ${style} et ${body}
         * 
         */
                
        $html = <<<EOT
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
	
        <link rel="icon" type="image/png" href="html/img/png/003-gift.redimensionado.png" />


		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">        
        <title>${title}</title>
	    ${styles}
    </head>

    <body>
        
       ${body}
	   
		<script src="../js/menu.js"></script> 
    </body>
    
    ${js}
</html>
EOT;

        /* Affichage de la page 
         *
         * C'est la seule instruction echo dans toute l'application 
         */
        
        echo $html;
    }
    
}