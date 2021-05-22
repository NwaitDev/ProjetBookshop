<?php

ob_start(); //démarre la bufferisation
session_start();

require_once '../php/bibli_generale.php';
require_once ('../php/bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Détail du livre', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

check_update(TRUE);

ng_aff_contenu();

em_aff_pied();

em_aff_fin('main');



// ----------  Fonctions locales au script ----------- //

/** 
 *  Affichage du contenu de la page
 */
function ng_aff_contenu(){

    $bd = em_bd_connecter();

    $sql = 'SELECT liID, liTitre, liPrix, liPages, liResume, liISBN13, edNom, edWeb, auNom, auPrenom
            FROM livres, editeurs, auteurs, aut_livre
            WHERE liIDEditeur = edID
            AND liID = al_IDLivre
            AND auID = al_IDAuteur
            AND liID ='.em_bd_proteger_entree($bd,$_GET['article']).';';
    
    $res = mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);
    
    $lastID = -1;
    while ($t = mysqli_fetch_assoc($res)) {
        if ($t['liID'] != $lastID) {
            if ($lastID != -1) {
                echo '<p>There\'s a problem Houston</p>';
            }
            $lastID = $t['liID'];
            $livre = array( 'id' => $t['liID'], 
                            'titre' => $t['liTitre'],
                            'edNom' => $t['edNom'],
                            'edWeb' => $t['edWeb'],
                            'resume' => $t['liResume'],
                            'pages' => $t['liPages'],
                            'ISBN13' => $t['liISBN13'],
                            'prix' => $t['liPrix'],
                            'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])));
        }
        else {
            $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
        }       
    }
    // libération des ressources
    mysqli_free_result($res);
    mysqli_close($bd);

    if ($lastID != -1) {
        ng_aff_livre_det(em_html_proteger_sortie($livre)); 
    }
}

function ng_aff_livre_det($livre){
    echo 
        '<p style="margin-top: 30px;">', 
            '<img src="../images/livres/', $livre['id'], 
                '.jpg" style="float: left; margin: 0 10px 10px 0; border: solid 1px #000; height: 100px;" alt="',
                $livre['titre'], '">',
            '<strong>', $livre['titre'], '</strong> <br>',
            'Ecrit par : ';
    $i = 0;

    foreach ($livre['auteurs'] as $auteur) {
        echo $i > 0 ? ', ' : '', '<a href="recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']), '">',
        em_html_proteger_sortie($auteur['prenom']), ' ', em_html_proteger_sortie($auteur['nom']) ,'</a>';
        $i++;
    }

    echo    '<br>Editeur : <a class="lienExterne" href="http://', trim($livre['edWeb']), '" target="_blank">', 
                $livre['edNom'], '</a><br>',
            'Prix : ', $livre['prix'], '<br>',
            'Pages : ', $livre['pages'], '<br>',
            'ISBN13 : ', $livre['ISBN13'], '<br>',
            'R&eacute;sum&eacute; : <em>', $livre['resume'], '</em>', 
        '</p>';
    echo '<h2> Ce livre vous intéresse ?</h2>',
            '<form method="POST" action="">','<table><tr><td>',
                '<p>Ajouter au panier <input type="submit" name="addToCart" value=""></p>',
                '</td><td>',
                '<p>Ajouter à la liste de cadeaux <input type="submit" name="addToWhishList" value=""></p>',
                '</td></tr></table>',
                '<input name="valeurID" type="hidden" value="',$livre['id'],'">',
            '</form>';
}


    
?>

