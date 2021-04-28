<?php
ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

// si l'utilisateur n'est pas authentifié, on le redirige sur la page login.php
if (! em_est_authentifie()){
    header('Location: login.php');
    exit;
}

em_aff_debut('BookShop | Protégée');


$bd = em_bd_connecter();

$sql = "SELECT *
        FROM clients
        WHERE cliID = {$_SESSION['id']}";

$res = mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

$T = mysqli_fetch_assoc($res);
$T = em_html_proteger_sortie($T);

mysqli_free_result($res);
mysqli_close($bd);

echo '<h1>Accès restreint aux utilisateurs authentifiés</h1>';

echo '<ul>';
echo '<li><strong>ID : ', $_SESSION['id'], '</strong></li>';
echo '<li>SID : ', session_id(), '</li>';
foreach($T as $cle => $val){
    echo '<li>', $cle, ' : ', $val, '</li>';
} 
echo '</ul>'; 

em_aff_fin();

ob_end_flush();


?>
