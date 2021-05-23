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
            '<a href="lorem_ipsum.php">A propos</a> - ',
            '<a href="lorem_ipsum.php">Emplois @ BookShop</a> - ',
            '<a href="lorem_ipsum.php">Conditions d\'utilisation</a>',
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

function check_update($err,$DEBUG = FALSE){
    if (array_key_exists('addToWhishList',$_POST)) {
        ng_wishlist_update(1);
    }
    if(array_key_exists('rmFromWishList',$_POST)){
        ng_wishlist_update(2);
    }
    if(array_key_exists('addToCart',$_POST)){
        ng_cart_update(1,$DEBUG);
    }
    if(array_key_exists('rmFromCart',$_POST)){
        ng_cart_update(2,$DEBUG);
    }
    if(array_key_exists('cartupdate',$_POST)){
        ng_cart_update(3,$DEBUG);
    }
    if(array_key_exists('cartreset',$_POST)){
        ng_cart_update(4,$DEBUG);
    }
    if(array_key_exists('btnCommander',$_POST)){
        ng_cart_update(3,$DEBUG);
        ng_passer_commande($err);
    }
    
}

/**
 * Met à jour les informations relatives à la wishlist
 * @param int cliID, l'id du client
 * @param int liID, l'id du livre
 * @param int $type :
 *  1 pour ajouter l'id de $_POST['valeurID'] à la wishlist du client
 *  2 pour supprimer l'id de $_POST['valeurID'] de la wishlist du client
 */
function ng_wishlist_update($type){
    if(em_est_authentifie()){
        if($type==1){
            add_to_wishlist($_SESSION['id'],$_POST['valeurID']);
        }else{
            rm_from_wishlist($_SESSION['id'],$_POST['valeurID']);
        }
    }else{
        echo '<script>alert(\'Veuillez vous connecter pour ajouter un livre à votre liste de cadeaux\');</script>';
    }
}
/**
 * Met à jour les données du panier 
 * @param boolean $DEBUG affiche le contenu du panier et permet de le vider avec un petit formulaire
 * @param int $type :
 *  1 pour ajouter l'id de $_POST['valeurID'] à la wishlist du client
 *  2 pour supprimer l'id de $_POST['valeurID'] de la wishlist du client
 *  3 pour mettre à jour la quantité de chaque id à la valeur de $_POST['qte(id)']
 *  autre nombre pour n'effectuer aucune de ces actions
 */
function ng_cart_update($type, $DEBUG){
    if (!isset( $_SESSION['cart'])) {
        $_SESSION['cart']=array();
    }
    if($type==1 || $type == 2){
        $iddulivre= $_POST['valeurID'];
        if($type == 1){
            if(!isset($_SESSION['cart'][$iddulivre])){
                $_SESSION['cart'][$iddulivre]=0;
            }
            $_SESSION['cart'][$iddulivre]+=1;
        }
        if($type==2){
            $_SESSION['cart'][$iddulivre]-=1;
            if($_SESSION['cart'][$iddulivre]==0){
                unset($_SESSION['cart'][$iddulivre]);
            }
        }
    }
    if($type==3){
        foreach($_POST as $id => $qte){
            if(is_numeric($id)){
                $_SESSION['cart'][$id]=$qte;
                if($_SESSION['cart'][$id]==0){
                    unset($_SESSION['cart'][$id]);
                }
            }
        }
    }
    if(array_key_exists('cartreset',$_POST)){
        for($i=count($_SESSION['cart']);$i>=0;--$i){
            array_pop($_SESSION['cart']);
        }
    }
    if($DEBUG){
    ////////////////////
    echo '<p>panier : </p>';
    var_dump($_SESSION['cart']);
    echo '<p>post:</p>';
    var_dump($_POST);
    echo '<form action="" method="POST">',
        '<input title="Reset_Cart" type="submit" name="cartreset" value="Réinitialiser le panier">',
        '</form>';
    ///////////////////
    }
}

/**
 * fonction qui permet de récupérer un tableau de tous les livres ajoutés à la liste de souhait de l'utilisateur.
 * @param integer $IDClient l'identifiant du client dont on veut la wishlist
 * @return array $livres la liste des livre de la wishlist du client
 */
function ng_get_wishlist($IDclient){
    $bd = em_bd_connecter();
    $sql = "SELECT liID, liTitre, liPrix, liPages, liISBN13, edNom, edWeb, auNom, auPrenom 
            FROM livres INNER JOIN editeurs ON liIDEditeur = edID
            INNER JOIN aut_livre ON al_IDLivre = liID 
            INNER JOIN auteurs ON al_IDAuteur = auID 
            INNER JOIN listes ON liID = listIDLivre
            WHERE listIDClient = $IDclient
            ORDER BY liID";
    $res = mysqli_query($bd, $sql) or em_bd_erreur($bd,$sql);
            
    $livres = [];
    $lastID = -1;
    $count = 0;
    while ($t = mysqli_fetch_assoc($res)) {
        if ($t['liID'] != $lastID) {
            if ($lastID != -1) {
                $livres[$count++]=$livre; 
            }
            $lastID = $t['liID'];
            $livre = array( 'id' => $t['liID'], 
                            'titre' => $t['liTitre'],
                            'edNom' => $t['edNom'],
                            'edWeb' => $t['edWeb'],
                            'pages' => $t['liPages'],
                            'ISBN13' => $t['liISBN13'],
                            'prix' => $t['liPrix'],
                            'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']))
                    );
        } else {
            $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
        }
    }
    // libération des ressources
    mysqli_free_result($res);
    mysqli_close($bd);

    if ($lastID != -1) {
        $livres[$count++]=$livre;
    }
    return $livres;
}

/**
 * Ajoute à la wishlist du client d'id $IDClient le livre d'identifiant $IDLivre
 * /!\ cette fonction ne vérifie pas que le client existe
 * @param integer $IDClient l'id du client à qui ajouter un livre à sa wishlist
 * @param integer $IDLivre l'id du livre à ajouter à la wishlist
 */
function add_to_wishlist($IDClient, $IDLivre){
    $bd = em_bd_connecter();
    $sql = "INSERT INTO listes (listIDLivre,listIDClient) VALUES ($IDLivre, $IDClient) ON DUPLICATE KEY UPDATE listIDLivre=listIDLivre;";
    mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
    mysqli_close($bd);
}

/**
 * supprime de la wishlist du client d'id $IDClient le livre d'identifiant $IDLivre
 * /!\ cette fonction ne vérifie pas que le client existe et nécessite que la wishlist soit déjà dans $_SESSION
 * @param integer $IDClient l'id du client pour qui supprimer un livre à sa wishlist
 * @param integer $IDLivre l'id du livre à supprimer de la wishlist
 */
function rm_from_wishlist($IDClient, $IDLivre){
    $is_there = False;
    $tab = ng_get_wishlist($IDClient);
    foreach($tab as $livre){
        if($livre['id']==$IDLivre){
            $is_there = TRUE;
            break;
        }
    }
    if ($is_there) {
        $bd = em_bd_connecter();
        $sql = "DELETE FROM listes WHERE listIDLivre = $IDLivre AND listIDClient = $IDClient;";
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        mysqli_close($bd);
    }
}

/**
 * affiche un livre comme un élément d'une liste (l'objet livre a pour format : 
 *  array( 'id','titre','edNom','edWeb','pages','ISBN13','prix','auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])));)
 * @param obj $livre le livre à afficher
 * @param int $option l'option d'affichage :
 *  0 : avec tous les boutons
 *  1 : sans le bouton d'ajout à la liste de souhait
 *  2 : sans le bouton d'ajout au panier  
 */
function ng_aff_livre($livre, $option = 0) {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $auteurs = $livre['auteurs'];
    $livre = em_html_proteger_sortie($livre);
    echo '<article class="arRecherche">',
            '<form action="" method="POST">';
    if($option != 2){
        echo '<input class="addToCart" title="ajouter au panier" type="submit" name="addToCart" value="">';
    }
    if($option !=1){
        echo '<input type="submit" class="addToWishlist" title="Ajouter à la liste de cadeaux" name="addToWhishList" value="">';
    }
    echo '<input name="valeurID" type="hidden" value="',$livre['id'],'">',
            '<a href="details.php?article=', $livre['id'], '" title="Voir détails"><img src="../images/livres/', $livre['id'], '_mini.jpg" alt="', 
            $livre['titre'],'"></a>',
            '<h5>', $livre['titre'].' ';

    if($option!=0){
        if($option!=1){
            echo '<input class="rmFrom" title="retirer du panier" type="submit" name="rmFromCart" value="">';
        }
        if ($option!=2) {
            echo '<input class="rmFrom" title="retirer de la liste de cadeaux" type="submit" name="rmFromWishList" value="">';
        }
    }
    echo '</h5>Ecrit par : ';
    $i = 0;
    foreach ($auteurs as $auteur) {
        echo $i > 0 ? ', ' : '', '<a href="recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']), '">',
        em_html_proteger_sortie($auteur['prenom']), ' ', em_html_proteger_sortie($auteur['nom']) ,'</a>';
        $i++;
    }

    echo    '<br>Editeur : <a class="lienExterne" href="http://', trim($livre['edWeb']), '" target="_blank">', $livre['edNom'], '</a><br>',
            'Prix : ', $livre['prix'], ' &euro;<br>',
            'Pages : ', $livre['pages'], '<br>',
            'ISBN13 : ', $livre['ISBN13'],
            '</form>',
        '</article>';
}

function ng_passer_commande($err){
    if(em_est_authentifie()){
        $bd = em_bd_connecter();
        $sql = 'SELECT cliID, cliAdresse, cliVille, cliPays, cliCP FROM clients WHERE cliID = '.$_SESSION['id'].';';
        $res = mysqli_query($bd,$sql);
        $row = array();
        if($row = mysqli_fetch_assoc($res)){
            foreach ($row as $value) {
                if(!isset($value)){
                    mysqli_free_result($res);
                    mysqli_close($bd);
                    $err[]='Avant de commander, veuillez renseigner votre adresse postale <a href="compte.php">ici</a>';
                    break;
                }
            }
            if(count($err)==0){
                mysqli_free_result($res);
                $sql = 'INSERT INTO commandes(coIDClient,coDate,coHeure) VALUES ('.$row['cliID'].','.date("Ymd").','.date("hi").');';
                mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
                $idcommande = mysqli_insert_id($bd);
                foreach ($_SESSION['cart'] as $id => $qte) {
                    $sql = 'INSERT INTO compo_commande(ccIDLivre,ccIDCommande,ccQuantite) VALUES ('.$id.', '.$idcommande.', '.$qte.');';
                    mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
                }
                mysqli_close($bd);
                echo '<p>Votre commande a bien été enregistrée</p>';
                for($i=count($_SESSION['cart']);$i>=0;--$i){
                    array_pop($_SESSION['cart']);
                }
            }
        }
    }else{
        $err[]='Avant de commander, veuillez vous connecter <a href="login.php">ici</a>';
        exit;
    }
}

?>
