<?php

/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *               à l'application BookShop                *
 *********************************************************/

/** Constantes : les paramètres de connexion au serveur MySQL */
define ('BD_SERVER', 'localhost');

define ('BD_NAME', 'degieux_bookshop');
define ('BD_USER', 'degieux_u');
define ('BD_PASS', 'degieux_p');

/*define ('BD_NAME', 'merlet_bookshop');
define ('BD_USER', 'merlet_u');
define ('BD_PASS', 'merlet_p');*/

define('LMAX_EMAIL', 50); //longueur du champ dans la base de données
define('LMAX_NOMPRENOM', 100); //longueur du champ dans la base de données

// paramètres de l'application
define('LMIN_PASSWORD', 4);
define('LMAX_PASSWORD', 20);

define('NB_ANNEE_DATE_NAISSANCE', 120);

define('LMAX_VILLE',50);
define('LMAX_CP',5);
define('LMAX_ADRESSE',100);
define('LMAX_PAYS',50);
 

/**
 *  Fonction affichant l'enseigne et le bloc entête avec le menu de navigation.
 *
 *  @param  string      $prefix     Prefixe des chemins vers les fichiers du menu (usuellement "./" ou "../").
 */
function em_aff_enseigne_entete($prefix = '../') {
    echo 
        '<aside>',
            '<a href="http://www.facebook.com" target="_blank"></a>',
            '<a href="http://www.twitter.com" target="_blank"></a>',
            '<a href="http://plus.google.com" target="_blank"></a>',
            '<a href="http://www.pinterest.com" target="_blank"></a>',
        '</aside>',
        
        '<header>';
    
    em_aff_menu($prefix);
    echo    '<img src="', $prefix,'images/soustitre.png" alt="sous titre">',
        '</header>';
}


/**
 *  Fonction affichant le menu de navigation de l'application BookShop 
 *
 *  @param  string      $prefix     Prefixe des chemins vers les fichiers du menu (usuellement "./" ou "../").
 */
function em_aff_menu($prefix) {      
    echo '<nav>',    
            '<a href="', $prefix, 'index.php" title="Retour à la page d\'accueil"></a>';
    
    $liens = array( 'recherche'   => array( 'position' => 1, 'title' => 'Effectuer une recherche'),
                    'panier'      => array( 'position' => 2, 'title' => 'Voir votre panier'),
                    'liste'       => array( 'position' => 3, 'title' => 'Voir une liste de cadeaux'),
                    'compte'      => array( 'position' => 4, 'title' => 'Consulter votre compte'),
                    'deconnexion' => array( 'position' => 5, 'title' => 'Se déconnecter'));
                    
    if (! em_est_authentifie()){
        unset($liens['compte']);
        unset($liens['deconnexion']);
        
        ++$liens['recherche']['position'];
        ++$liens['panier']['position'];
        ++$liens['liste']['position'];
        /*TODO :    - peut-on implémenter les 3 incrémentations ci-dessus avec un foreach ? */
        // OUI : //mais ça marche pas ça...
        /*
        foreach ($liens as $value) {
            ++$value['position'];
        }
        */
        $liens['login'] = array( 'position' => 5, 'title' => 'Se connecter');
        /* Debug :
        echo '<pre>', print_r($liens, true), '</pre>';
        exit;*/
    }
    
    foreach ($liens as $cle => $elt) {
        echo '<a class="pos', $elt['position'], '" href="', $prefix, 'php/', $cle, '.php" title="', $elt['title'], '"></a>';
    }
    echo '</nav>';
}


/**
 *  Fonction affichant le pied de page de l'application BookShop.
 */
function em_aff_pied() {
    echo 
        '<footer>', 
            'BookShop &amp; Partners &copy; ', date('Y'), ' - ',
            '<a href="apropos.html">A propos</a> - ',
            '<a href="confident.html">Emplois @ BookShop</a> - ',
            '<a href="conditions.html">Conditions d\'utilisation</a>',
        '</footer>';
}

//_______________________________________________________________
/**
* Détermine si l'utilisateur est authentifié
*
* @global array    $_SESSION 
* @return boolean  true si l'utilisateur est authentifié, false sinon
*/
function em_est_authentifie() {
    return  isset($_SESSION['id']);
}

//_______________________________________________________________
/**
 * Termine une session et effectue une redirection vers la page transmise en paramètre
 *
 * Elle utilise :
 *   -   la fonction session_destroy() qui détruit la session existante
 *   -   la fonction session_unset() qui efface toutes les variables de session
 * Elle supprime également le cookie de session
 *
 * Cette fonction est appelée quand l'utilisateur se déconnecte "normalement" et quand une 
 * tentative de piratage est détectée. On pourrait améliorer l'application en différenciant ces
 * 2 situations. Et en cas de tentative de piratage, on pourrait faire des traitements pour 
 * stocker par exemple l'adresse IP, etc.
 * 
 * @param string    URL de la page vers laquelle l'utilisateur est redirigé
 */
function em_session_exit($page = '../index.php') {
    session_destroy();
    session_unset();
    $cookieParams = session_get_cookie_params();
    setcookie(session_name(), 
            '', 
            time() - 86400,
            $cookieParams['path'], 
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httponly']
        );
    header("Location: $page");
    exit();
}

/**
 * Met à jour les variables de session relatives au panier et à la wishlist
 * @param boolean $DEBUG = TRUE permet :
 * 1) d'afficher le contenu du panier et de la wishlist
 * 2) d'ajouter des boutons pour vider la wishlist et le panier
 * 3) d'afficher le dernier article enregistré ($_POST)
 */
function ng_localtabs_update($DEBUG = FALSE){
    if(array_key_exists('addToCart',$_POST)){
        if (!isset( $_SESSION['cart'])) {
            $_SESSION['cart']=array();
        }
        $test= $_POST['valeurID'];
        $_SESSION['cart'][]=$test;
    }
    if(array_key_exists('addToWhishList',$_POST)){
        if (!isset($_SESSION['wish'])) {
            $_SESSION['wish']=array();
        }
        $test= $_POST['valeurID'];
        $_SESSION['wish'][]=$test;
    }
    if ($DEBUG) {
        if(array_key_exists('cartreset',$_POST)){
            $_SESSION['cart']=array();
        }
        if(array_key_exists('wishreset',$_POST)){
            $_SESSION['wish']=array();
        }
        ////////////////////
        echo '<p>panier : </p>';
        var_dump($_SESSION['cart']);
        echo '<p>wishlist : </p>';
        var_dump($_SESSION['wish']);
        echo '<form action="" method="POST">',
            '<input title="Reset_Cart" type="submit" name="cartreset" value="Réinitialiser le panier">',
            '<input title="Reset_Wishlist" type="submit" name="wishreset" value="Réinitialiser la wishlist">',
            '</form>';
        var_dump($_POST);
        //////////////////*/
    }

}

?>
