<?php

/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérification des paramètres reçus dans l'URL
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

ob_start(); //démarre la bufferisation
session_start();

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

/*------------------------- Etape 1 --------------------------------------------
- vérification des paramètres reçus dans l'URL
------------------------------------------------------------------------------*/

// erreurs détectées dans l'URL
$erreurs = array();

// critères de recherche
$recherche = array('type' => 'auteur', 'quoi' => '');
$pageNum = 1;
if ($_GET){ // s'il y a des paramètres dans l'URL
    if (! em_parametres_controle('get', array('type', 'quoi'),array('page'))){
        $erreurs[] = 'L\'URL doit être de la forme "recherche.php?type=auteur&quoi=Moore".';
    }
    else{
        $oks = array('titre', 'auteur');
        if (! in_array($_GET['type'], $oks)){
            $erreurs[] = 'La valeur du "type" doit être égale à "'.implode('" ou à "', $oks).'".';
        }
        $recherche['type'] = $_GET['type'];
        $recherche['quoi'] = trim($_GET['quoi']);
        $l1 = mb_strlen($recherche['quoi'], 'UTF-8');
        if ($l1 < 2){
            $erreurs[] = 'Le critère de recherche est trop court.';
        }
        if ($l1 != mb_strlen(strip_tags($recherche['quoi']), 'UTF-8')){
            $erreurs[] = 'Le critère de recherche ne doit pas contenir de tags HTML.';
        }
        if(array_key_exists('page',$_GET)){
            if(is_numeric($_GET['page'])){
                $pageNum = $_GET['page'];
            }else{
                $erreurs[]= 'le critère page doit être un nombre entier';
            }
        }
    }
}

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

em_aff_debut('BookShop | Recherche', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();
$err = array();
check_update($err);

ng_aff_contenu($recherche, $err, $pageNum, 7);
$nbPages = isset($_SESSION['nbpages']) ? $_SESSION['nbpages'] : 1;
if ($_GET) {
    ng_page_bar($nbPages,isset($_GET['page']) ? $_GET['page'] : 1, 5);
}
em_aff_pied();

em_aff_fin('main');

// fin du script --> envoi de la page 
ob_end_flush();


// ----------  Fonctions locales au script ----------- //

/**
 *  Contenu de la page : formulaire et résultats de la recherche, 
 *  indique dans $_SESSION les derniers critères de recerche utilisés et le tableau des resultats pour vérifier s'il
 *  est nécessaire de se reconnecter à la BDD pour afficher les resultats
 *
 * @param array  $recherche     critères de recherche (type et quoi)
 * @param array  $erreurs       erreurs détectées dans l'URL
 */
function ng_aff_contenu($recherche, $erreurs, $pageNum = 1, $pageSize = 3) {
    
    echo '<h3>Recherche par une partie du nom d\'un auteur ou du titre</h3>'; 
    
    /* choix de la méthode get pour avoir la même forme d'URL lors d'une soumission du formulaire, 
    et lorsqu'on accède à la page suite à un clic sur un nom d'un auteur */
    echo '<form action="recherche.php" method="get">',
            '<p>Rechercher <input type="text" name="quoi" minlength="2" value="', em_html_proteger_sortie($recherche['quoi']), '">', 
            ' dans '; 
                em_aff_liste('type', array('auteur' => 'auteurs', 'titre' => 'titre'), $recherche['type']);
    
    echo       '<input type="submit" value="Rechercher">', // pas d'attribut name pour qu'il n'y ait pas d'élément correspondant au bouton submit dans l'URL
                                                        // lors de la soumission du formulaire
            '</p>', 
          '</form>';
    
    if ($erreurs) {
        $nbErr = count($erreurs);
        $pluriel = $nbErr > 1 ? 's':'';
        echo '<p class="error">',
                '<strong>Erreur',$pluriel, ' détectée', $pluriel, ' :</strong>';
        for ($i = 0; $i < $nbErr; $i++) {
                echo '<br>', $erreurs[$i];
        }
        echo '</p>';
        return; // ===> Fin de la fonction
    }
    $count=0;
    if ($recherche['quoi']){ //si recherche à faire en base de données
        if (!ng_value_comp($_SESSION,$_GET,['type','quoi'])) {
            // ouverture de la connexion, requête
            $_SESSION['type']=$recherche['type'];
            $_SESSION['quoi']=$recherche['quoi'];
            $bd = em_bd_connecter();
            
            $q = em_bd_proteger_entree($bd, $recherche['quoi']); 
            
            if ($recherche['type'] == 'auteur') {
                $critere = " WHERE liID in (SELECT al_IDLivre FROM aut_livre INNER JOIN auteurs ON al_IDAuteur = auID WHERE auNom LIKE '%$q%')";
            } 
            else {
                $critere = " WHERE liTitre LIKE '%$q%'";    
            }

            $sql =  "SELECT liID, liTitre, liPrix, liPages, liISBN13, edNom, edWeb, auNom, auPrenom 
                    FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                                INNER JOIN aut_livre ON al_IDLivre = liID 
                                INNER JOIN auteurs ON al_IDAuteur = auID 
                    $critere
                    ORDER BY liID";

            $res = mysqli_query($bd, $sql) or em_bd_erreur($bd,$sql);
            
            $livres = [];
            $nbPages=0;
            $lastID = -1;
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
                }
                else {
                    $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
                }       
            }
            // libération des ressources
            mysqli_free_result($res);
            mysqli_close($bd);
        
            if ($lastID != -1) {
                $livres[$count++]=$livre; 
                $_SESSION['nblivres']=$count;
            }
            $_SESSION['nblivres']=$count;
            $_SESSION['livres']=$livres;
        }
        $livres=$_SESSION['livres'];
        $count=$_SESSION['nblivres'];
        if($count == 0){
            echo '<p>Aucun livre trouvé</p>';
            return;
        }
        $nbPages =(int)($count/$pageSize);
        if($count!=$nbPages*$pageSize){
            $nbPages++;
        }
        $_SESSION['nbpages']=$nbPages;
        if($pageNum>$nbPages){
            $pageNum = $nbPages;
        }
        if ($pageNum<1) {
            $pageNum = 1;
        }
        $maxbooks = $count<($pageNum-1)*$pageSize+$pageSize ? $count : ($pageNum-1)*$pageSize+$pageSize;
        for ($i=($pageNum-1)*$pageSize; $i<$maxbooks ; $i++) { 
            eml_aff_livre($livres[$i]);
        }
    }
    
}

/**
 *  Affichage d'un livre.
 *
 *  @param  array       $livre      tableau associatif des infos sur un livre (id, auteurs(nom, prenom), titre, prix, pages, ISBN13, edWeb, edNom)
 *
 */
function eml_aff_livre($livre) {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $auteurs = $livre['auteurs'];
    $livre = em_html_proteger_sortie($livre);
    echo 
        '<article class="arRecherche">',
            '<form action="" method="POST">',
            '<input class="addToCart" title="ajouter au panier" type="submit" name="addToCart" value="">',
            '<input name="valeurID" type="hidden" value="',$livre['id'],'">',
            '<input type="submit" class="addToWishlist" title="Ajouter à la liste de cadeaux" name="addToWhishList" value=""></form>',
            '<a href="details.php?article=', $livre['id'], '" title="Voir détails"><img src="../images/livres/', $livre['id'], '_mini.jpg" alt="', 
            $livre['titre'],'"></a>',
            '<h5>', $livre['titre'], '</h5>',
            'Ecrit par : ';
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
        '</article>';
}

?>
