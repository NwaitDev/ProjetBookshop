<?php

ob_start(); //démarre la bufferisation
session_start();

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

// si utilisateur déjà authentifié, on le redirige vers la page précédente (TODO) ou vers index.php
if (em_est_authentifie()){
    header('Location: ../index.php');
    exit();
}

// traitement si soumission du formulaire d'inscription
$err = isset($_POST['btnConn']) ? ng_traitement_connexion(array()) : array(); 


em_aff_debut('BookShop | Connexion', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

ng_aff_form_conn($err);

em_aff_pied();

em_aff_fin('main');

// fin du script --> envoi de la page 
ob_end_flush();

/*---------------Fonctions locales au script------------*/

/**
 * 
 */
function ng_aff_form_conn($err){
    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    $email = isset($_POST['email']) ? em_html_proteger_sortie(trim($_POST['email'])) : '';
    
    echo '<h1>Connectez-vous à BookShop</h1>';
        
    if (count($err) > 0) {
        echo '<p class="error">La connexion n\'a pas pu être réalisée à cause des erreurs suivantes : ';
        foreach ($err as $v) {
            echo '<br> - ', $v;
        }
        echo '</p>';    
    }

    echo '<p>Pour continuer, veuillez vous connecter :</p>',
            '<form method="POST" action="">',
                '<table>';
                em_aff_ligne_input('email :', array('type' => 'email', 'name' => 'email', 'value' => $email, 'required' => false));
                em_aff_ligne_input('mot de passe :', array('type' => 'password', 'name' => 'passe', 'value' => '', 'required' => false));
    echo        '<tr>',
                    '<td colspan="2">',
                        '<input type="submit" name="btnConn" value="Connexion">',
                        '<input type="reset" value="Réinitialiser">', 
                    '</td>',
                '</tr>',
                '</table>',
            '</form>';
    echo '<p> Pas encore membre de la Bookshop society ? <a href="./inscription.php">Inscrivez-vous</a> dès aujourd\'hui !</p>';
}

/**
 *  Traitement de la connexion 
 *
 *      Etape 1. vérification de la validité des données
 *                  -> return des erreurs si on en trouve
 *      Etape 2. vérification de lacohérence des données avec la base
 *      Etape 3. ouverture de la session et redirection vers la page d'origine 
 *
 * Toutes les erreurs détectées qui nécessitent une modification du code HTML sont considérées comme des tentatives de piratage 
 * et donc entraînent l'appel de la fonction em_session_exit() sauf les éventuelles suppressions des attributs required 
 * car l'attribut required est une nouveauté apparue dans la version HTML5 et nous souhaitons que l'application fonctionne également 
 * correctement sur les vieux navigateurs qui ne supportent pas encore HTML5
 *
 * @global array    $_POST
 *
 * @return array    tableau assosiatif contenant les erreurs
 */
function ng_traitement_connexion($erreurs){
    if( !em_parametres_controle('post', array('email', 'passe', 'btnConn'))) {
        em_session_exit();   
    }

    // vérification du format de l'adresse email
    $email = trim($_POST['email']);
    if (empty($email)){
        $erreurs[] = 'L\'adresse mail ne doit pas être vide.'; 
    }
    else {
        if (mb_strlen($email, 'UTF-8') > LMAX_EMAIL){
            $erreurs[] = 'L\'adresse mail ne peut pas dépasser '.LMAX_EMAIL.' caractères.';
        }
        // la validation faite par le navigateur en utilisant le type email pour l'élément HTML input
        // est moins forte que celle faite ci-dessous avec la fonction filter_var()
        // Exemple : 'l@i' passe la validation faite par le navigateur et ne passe pas
        // celle faite ci-dessous
        if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'L\'adresse mail n\'est pas valide.';
        }
    }
    
    
    // vérification des mots de passe
    $passe = trim($_POST['passe']);
    $nb = mb_strlen($passe, 'UTF-8');
    if ($nb < LMIN_PASSWORD || $nb > LMAX_PASSWORD){
        $erreurs[] = 'Le mot de passe doit être constitué de '. LMIN_PASSWORD . ' à ' . LMAX_PASSWORD . ' caractères.';
    }

    if (count($erreurs) == 0) {
        // vérification de la presence de l'adresse mail et du mdp dans la base
        // (uniquement si pas d'autres erreurs, parce que ça coûte une jambe)
        $bd = em_bd_connecter();

        // pas utile, car déjà été vérifié, mais teeeeellement plus sécurisant...
        $email = em_bd_proteger_entree($bd, $email);
        $sql = "SELECT cliID, cliPassword FROM clients WHERE cliEmail = '$email'"; 
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        if ($row = mysqli_fetch_assoc($res)) {
            if(password_verify($passe,$row['cliPassword'])){
                $_SESSION['id']=$row['cliID'];
            }else{
                $erreurs[]='L\'adresse mail ou le mot de passe est incorrect';
            }
        }else{
            $erreurs[]='L\'adresse mail ou le mot de passe est incorrect';
        }
        mysqli_free_result($res);
        mysqli_close($bd);
        
    }

    if (count($erreurs) > 0) {  
        return $erreurs;    
    }
    
    // redirection vers la page précédente
    $addr = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
    header("Location: $addr");
    exit();

}

?>