<?php

ob_start(); //démarre la bufferisation
session_start();


require_once './php/bibli_generale.php';
require_once './php/bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Bienvenue', './styles/bookshop.css', 'main');

em_aff_enseigne_entete('./');

eml_aff_contenu();
$err = array();
check_update($err);

em_aff_pied();

em_aff_fin('main');

// ----------  Fonctions locales au script ----------- //

/** 
 *  Affichage du contenu de la page
 */
function eml_aff_contenu() {
    
    echo 
        '<h1>Bienvenue sur BookShop !</h1>',
        
        '<p>Passez la souris sur le logo et laissez-vous guider pour découvrir les dernières exclusivités de notre site. </p>',
        
        '<p>Nouveau venu sur BookShop ? Consultez notre <a href="./php/presentation.php">page de présentation</a> !</p>';
    
        
    $derniersAjouts = ng_get_livre(1);

    eml_aff_section_livres(1, $derniersAjouts);
    
    
    $meilleursVentes = ng_get_livre(2);
    
    eml_aff_section_livres(2, $meilleursVentes);    
}



/** 
 *  Affichage d'une section de livres
 *
 *  @param  integer $num        numéro de la section (1 pour les dernières nouveautés, 2 pour les meilleures ventes) 
 *  @param  array   $tLivres    tableau contenant un élément (tableau associatif) pour chaque livre (id, auteurs(nom, prenom), titre)
 *
 */
function eml_aff_section_livres($num, $tLivres) {
    echo '<section>';
    if ($num == 1){
        echo  '<h2>Dernières nouveautés </h2>',
              '<p>Voici les 4 derniers articles ajoutés dans notre boutique en ligne :</p>';   
    }
    elseif ($num == 2){
        echo  '<h2>Top des ventes</h2>',
              '<p>Voici les 4 articles les plus vendus :</p>';
    }

    foreach ($tLivres as $livre) {
        echo 
            '<figure>', 
                '<form action="index.php" method="POST">
                <input class="addToCart" title="ajouter au panier" type="submit" name="addToCart" value="">
                <input name="valeurID" type="hidden" value="',$livre['id'],'">
                <input type="submit" class="addToWishlist" title="Ajouter à la liste de cadeaux" name="addToWhishList" value="">
                </form>',
                '<a href="php/details.php?article=', $livre['id'], '" title="Voir détails"><img src="./images/livres/', 
                $livre['id'], '_mini.jpg" alt="', $livre['titre'],'"></a>',
                '<figcaption>';
        $auteurs = $livre['auteurs']; 
        $i = 0;
        foreach ($livre['auteurs'] as $auteur) {  
            if ($i > 0) {
                echo ', ';
            }
            ++$i;
            echo    '<a title="Rechercher l\'auteur" href="php/recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']), '">', 
                    mb_substr($auteur['prenom'], 0, 1, 'UTF-8'), '. ', $auteur['nom'], '</a>';
        }
        echo        '<br>', 
                    '<strong>', $livre['titre'], '</strong>',
                '</figcaption>',
            '</figure>';
    }
    echo '</section>';
}

//test je suis un commentaire
/**
 * Fonction qui interroge la base de donnée pour renseigner les 4 livre les plus vendus ou les 4 derniers parus
 * @param $type 2 si on veut les livres les plus vendus, 1 si on veut les plus récents
 * @return array un tableau de 4 array('id' => ID, 'auteurs' => array(array('nom'=>nom, 'prenom'=>prenom) ...), 'titre'=>titre)
 */
function ng_get_livre($type=0){
    
    $livre = array('id' => null,'auteurs' => array(), 'titre' => null );
    if($type==1){
        $query = 
        'SELECT liID,auNom,auPrenom,liTitre 
        FROM livres,aut_livre,auteurs 
        WHERE liID = al_IDLivre AND 
            al_IDAuteur = auID
        ORDER BY liID DESC';

        $co = em_bd_connecter();
        $res = mysqli_query($co,$query) or em_bd_erreur($co,$query);
        $newestBooks = array();

        $lastID = -1;
        $nbbooks=0;
        while ($t = mysqli_fetch_assoc($res)) {
            if ($t['liID'] != $lastID) {
                if($lastID!=-1){
                    ++$nbbooks;
                    if ($nbbooks==5) {
                        break;
                    }
                    $newestBooks[]=$livre;
                }
                
                $lastID = $t['liID'];
                $livre = array( 'id' => $t['liID'], 
                                'titre' => $t['liTitre'],
                                'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']))
                            );
            }
            else {
                $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
            }
        }
        // libération des ressources
        mysqli_free_result($res);
        mysqli_close($co);
        return $newestBooks;
    }

    if($type==2){
        $query = 
        'SELECT     liID, auNom, auPrenom, liTitre
        FROM        livres LEFT OUTER JOIN compo_commande ON liID = ccIDLivre, aut_livre, auteurs
        WHERE       al_IDAuteur = auID AND al_IDLivre=liID
        GROUP BY    liID, auID
        ORDER BY    SUM(ccQuantite) DESC';

        $co = em_bd_connecter();
        $res = mysqli_query($co,$query) or em_bd_erreur($co,$query);
        $popularBooks = array();
        $lastID = -1;
        $nbbooks=0;
        while ($t = mysqli_fetch_assoc($res)) {
            if ($t['liID'] != $lastID) {
                if ($lastID!=-1) {
                    ++$nbbooks;
                    if ($nbbooks==5) {
                        break;
                    }
                    $popularBooks[]=$livre;
                }
                
                $lastID = $t['liID'];
                $livre = array( 'id' => $t['liID'], 
                                'titre' => $t['liTitre'],
                                'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']))
                            );
            }
            else {
                $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
            }       
        }
        // libération des ressources
        mysqli_free_result($res);
        mysqli_close($co);
        return $popularBooks;
    }
    return array(
        array(  'id'      => 42, 
                'auteurs' => array( array('prenom' => 'George', 'nom' => 'Orwell')), 
                'titre'   => '1984'),
        array(  'id'      => 41, 
                'auteurs' => array( array('prenom' => 'Robert', 'nom' => 'Kirkman'),
                                    array('prenom' => 'Charlie', 'nom' => 'Adlard')), 
                'titre'   => 'The Walking Dead - T16 Un vaste monde'),
        array(  'id'      => 40, 
                'auteurs' => array( array('prenom' => 'Ray', 'nom' => 'Bradbury')), 
                'titre'   => 'L\'homme illustré'),   
        array(  'id'      => 39, 
                'auteurs' => array( array('prenom' => 'Alan', 'nom' => 'Moore'),
                                    array('prenom' => 'David', 'nom' => 'Lloyd')), 
                'titre'   => 'V pour Vendetta'),  
              ); 
}



?>
