<?php
ob_start();
session_start();

require_once '../php/bibli_generale.php';
require_once ('../php/bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Mon panier', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

check_update(TRUE);

ng_aff_panier();

em_aff_pied();

em_aff_fin('main');

ob_flush();


function ng_aff_panier(){
    echo '<h2>Mon panier</h2>';
    if(!isset($_SESSION['cart'][0])){
        echo '<p>Votre panier est vide</p>';
    }
    echo '<tr>';
    //foreach($i) TODO NATHAN
    
    
    echo '</tr>';
}
?>