<?php

ob_start();
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_bookshop.php';

error_reporting(E_ALL);

// si utilisateur n'est pas authentifié, on le redirige vers la page précédente ou vers index.php
if(!em_est_authentifie()){
    $_SESSION['prev'] = $_SERVER['HTTP_REFERER'];
}
$addr = isset($_SESSION['prev']) ? $_SESSION['prev'] : '../index.php';
if (!em_est_authentifie()){
    header('Location: '.$addr);
    exit();
}

em_aff_debut('Bookshop | Ma liste de Cadeaux','../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

ng_aff_liste();

em_aff_pied();
em_aff_fin('main');

ob_flush();

function ng_aff_liste(){
    //TODO
}
