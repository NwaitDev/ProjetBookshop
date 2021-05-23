<?php
ob_start();
session_start();

require_once '../php/bibli_generale.php';
require_once ('../php/bibli_bookshop.php');
date_default_timezone_set('Europe/Paris');
error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Mon panier', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();
$err = array();
check_update($err);
ng_aff_panier($err);

em_aff_pied();

em_aff_fin('main');

ob_flush();


function ng_aff_panier($erreurs){
    echo '<h2>Mon panier</h2>';
    if ($erreurs) {
        $nbErr = count($erreurs);
        $pluriel = $nbErr > 1 ? 's':'';
        echo '<p class="error">',
                '<strong>Erreur',$pluriel, ' détectée', $pluriel, ' :</strong>';
        for ($i = 0; $i < $nbErr; $i++) {
                echo '<br>', $erreurs[$i];
        }
        echo '</p>';
    }
    if(!isset($_SESSION['cart']) || count($_SESSION['cart'])==0){
        echo '<p>Votre panier est vide</p>';
        return;
    }
    $bd = em_bd_connecter();
    $sql = "SELECT liID, liTitre, liPrix FROM livres WHERE ";
    foreach($_SESSION['cart'] as $id => $qte){
        $sql.="liID = $id OR ";
    }
    $sql.="FALSE;";
    $res = mysqli_query($bd,$sql);
    echo '<form method="POST"><table class="cart"><tr><td>ARTICLE(S)</td><td>Quantité(s)</td><td><strong>Prix</strong></td></tr>';
    $row = array();
    $totalprice = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr><td><a href="details.php?article=', $row['liID'], '" title="Voir détails">',em_html_proteger_sortie($row['liTitre']),'</a></td><td>';
        em_aff_liste_nombre($row['liID'],0,100,1,$_SESSION['cart'][$row['liID']]);
        $totalprice+=$row['liPrix']*$_SESSION['cart'][$row['liID']];
        echo '</td><td>',$row['liPrix']*$_SESSION['cart'][$row['liID']],'€</td></tr>';
    }
    mysqli_free_result($res);
    mysqli_close($bd);
    echo '<tr style="border-top:solid black; border-bottom:solid black;"><td colspan="2"><strong>TOTAL</strong></td><td>',$totalprice,'€</td></tr>';
    echo '<tr><td colspan="3">',
            '<input type="submit" name="cartupdate" value="Actualiser">',
            '<input type="submit" name="cartreset" value="Vider Panier">',
            '<input type="submit" name="btnCommander" value="Confirmer">', 
            '</td>','</tr>';
    echo '</table></form>';
}
?>