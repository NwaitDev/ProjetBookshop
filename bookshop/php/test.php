<?php

//démarre la bufferisation
session_start();

require_once ('./bibli_generale.php');
require_once ('./bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

if ($_GET){ // s'il y a des paramètres dans l'URL
    if (! em_parametres_controle('get', array('livre'))){
        $erreurs[] = 'L\'URL doit être de la forme "add_cart.php?livre=blabla".';
    }
    else{
        if ( is_int($_GET['livre'])) {
            $_SESSION['cart'][] = $_GET['livre'];
        }
    }
}

header('Location: ../index.php' );

?>