<?php
ob_start();
session_start();

require_once '../php/bibli_generale.php';
require_once ('../php/bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Mon panier', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

check_update();

ng_aff_panier();

em_aff_pied();

em_aff_fin('main');

ob_flush();


function ng_aff_panier(){
    echo '<h2>Mon panier</h2>';
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
    echo '<form method="POST"><table class="cart"><tr><td><strong>ARTICLES</strong></td><td>Quantité</td><td><strong>Prix</strong></td></tr>';
    $row = array();
    $totalprice = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr><td><a href="details.php?article=', $row['liID'], '" title="Voir détails">',em_html_proteger_sortie($row['liTitre']),'</a></td><td>';
        em_aff_liste_nombre($row['liID'],1,100,1,$_SESSION['cart'][$row['liID']]);
        echo '<input class="rmFrom" title="retirer du panier" type="submit" name="rmFromCart" value="">';
        $totalprice+=$row['liPrix']*$_SESSION['cart'][$row['liID']];
        echo '</td><td>',$row['liPrix']*$_SESSION['cart'][$row['liID']],'€</td></tr>';
    }
    mysqli_free_result($res);
    mysqli_close($bd);
    echo '<tr><td colspan="2"><strong>TOTAL</strong></td><td>',$totalprice,'€</td></tr>';
    echo '<tr><td colspan="3">',
            '<input type="submit" name="cartupdate" value="Actualiser Panier">',
            '<input type="submit" name="cartreset" value="Réinitialiser Panier">',
            '<input type="submit" name="btnCommander" value="Confirmer Commande"', 
            '</td>','</tr>';
    echo '</table></form>';
}
?>