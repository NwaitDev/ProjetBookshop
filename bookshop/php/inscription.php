<?php
/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérifications diverses et traitement des soumissions
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

/*------------------------- Etape 1 --------------------------------------------
- vérifications diverses et traitement des soumissions
------------------------------------------------------------------------------*/

// si utilisateur déjà authentifié, on le redirige vers la page index.php
if (em_est_authentifie()){
    header('Location: ../index.php');
    exit();
}

// traitement si soumission du formulaire d'inscription
$err = isset($_POST['btnSInscrire']) ? eml_traitement_inscription() : array(); 

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

em_aff_debut('BookShop | Inscription', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

eml_aff_contenu($err);

em_aff_pied();

em_aff_fin('main');

ob_end_flush();


// ----------  Fonctions locales du script ----------- //

/**
 * Affichage du contenu de la page (formulaire d'inscription)
 *
 * @param   array   $err    tableau d'erreurs à afficher
 * @global  array   $_POST
 */
function eml_aff_contenu($err) {

    $anneeCourante = (int) date('Y');

    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    $email = isset($_POST['email']) ? em_html_proteger_sortie(trim($_POST['email'])) : '';
    $nomprenom = isset($_POST['nomprenom']) ? em_html_proteger_sortie(trim($_POST['nomprenom'])) : '';
    $jour = isset($_POST['naissance_j']) ? (int)$_POST['naissance_j'] : 1;
    $mois = isset($_POST['naissance_m']) ? (int)$_POST['naissance_m'] : 1;
    $annee = isset($_POST['naissance_a']) ? (int)$_POST['naissance_a'] : $anneeCourante;

    echo 
        '<h1>Inscription à BookShop</h1>';
        
    if (count($err) > 0) {
        echo '<p class="error">Votre inscription n\'a pas pu être réalisée à cause des erreurs suivantes : ';
        foreach ($err as $v) {
            echo '<br> - ', $v;
        }
        echo '</p>';    
    }
    
    echo    
        '<p>Pour vous inscrire, merci de fournir les informations suivantes. </p>',
        '<form method="post" action="inscription.php">',
            '<table>';

    em_aff_ligne_input('Votre adresse email :', array('type' => 'email', 'name' => 'email', 'value' => $email, 'required' => false));
    em_aff_ligne_input('Choisissez un mot de passe :', array('type' => 'password', 'name' => 'passe1', 'value' => '', 'required' => false));
    em_aff_ligne_input('Répétez le mot de passe :', array('type' => 'password', 'name' => 'passe2', 'value' => '', 'required' => false));
    em_aff_ligne_input('Nom et prénom :', array('type' => 'text', 'name' => 'nomprenom', 'value' => $nomprenom, 'required' => false));
    em_aff_ligne_date('Votre date de naissance :', 'naissance', $anneeCourante - NB_ANNEE_DATE_NAISSANCE + 1, $anneeCourante, $jour, $mois, $annee);
            
    echo 
                '<tr>',
                    '<td colspan="2">',
                        '<input type="submit" name="btnSInscrire" value="S\'inscrire">',
                        '<input type="reset" value="Réinitialiser">', 
                    '</td>',
                '</tr>',
            '</table>',
        '</form>';
}   


/**
 *  Traitement de l'inscription 
 *
 *      Etape 1. vérification de la validité des données
 *                  -> return des erreurs si on en trouve
 *      Etape 2. enregistrement du nouvel inscrit dans la base
 *      Etape 3. ouverture de la session et redirection vers la page protegee.php 
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
function eml_traitement_inscription() {

    if( !em_parametres_controle('post', array('email', 'nomprenom', 'naissance_j', 'naissance_m', 'naissance_a', 
                                              'passe1', 'passe2', 'btnSInscrire'))) {
        em_session_exit();   
    }
    
    $erreurs = array();
    
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
    $passe1 = trim($_POST['passe1']);
    $passe2 = trim($_POST['passe2']);
    if ($passe1 !== $passe2) {
        $erreurs[] = 'Les mots de passe doivent être identiques.';
    }
    $nb = mb_strlen($passe1, 'UTF-8');
    if ($nb < LMIN_PASSWORD || $nb > LMAX_PASSWORD){
        $erreurs[] = 'Le mot de passe doit être constitué de '. LMIN_PASSWORD . ' à ' . LMAX_PASSWORD . ' caractères.';
    }

    // vérification de la date de naissance
    if (! (em_est_entier($_POST['naissance_j']) && em_est_entre($_POST['naissance_j'], 1, 31))){
        em_session_exit(); 
    }
    
    if (! (em_est_entier($_POST['naissance_m']) && em_est_entre($_POST['naissance_m'], 1, 12))){
        em_session_exit(); 
    }
    $anneeCourante = (int) date('Y');
    if (! (em_est_entier($_POST['naissance_a']) && em_est_entre($_POST['naissance_a'], $anneeCourante  - NB_ANNEE_DATE_NAISSANCE + 1, $anneeCourante))){
        em_session_exit(); 
    }
    
    $jour = (int)$_POST['naissance_j'];
    $mois = (int)$_POST['naissance_m'];
    $annee = (int)$_POST['naissance_a'];
    if (!checkdate($mois, $jour, $annee)) {
        $erreurs[] = 'La date de naissance n\'est pas valide.';
    }
    else if (mktime(0,0,0,$mois,$jour,$annee+18) > time()) {
        $erreurs[] = 'Vous devez avoir au moins 18 ans pour vous inscrire.'; 
    }
    
    // vérification des noms et prenoms
    $nomprenom = trim($_POST['nomprenom']);
    
    if (empty($nomprenom)) {
        $erreurs[] = 'Le nom et le prénom doivent être renseignés.'; 
    }
    else {
        if (mb_strlen($nomprenom, 'UTF-8') > LMAX_NOMPRENOM){
            $erreurs[] = 'Le nom et le prénom ne peuvent pas dépasser ' . LMAX_NOMPRENOM . ' caractères.';
        }
        $noTags = strip_tags($nomprenom);
        if ($noTags != $nomprenom){
            $erreurs[] = 'Le nom et le prénom ne peuvent pas contenir de code HTML.';
        }
        else {
            mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
            if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $nomprenom)){
                $erreurs[] = 'Le nom et le prénom contiennent des caractères non autorisés';
            }
        }
    }
    

    if (count($erreurs) == 0) {
        // vérification de l'unicité de l'adresse email 
        // (uniquement si pas d'autres erreurs, parce que ça coûte un bras)
        $bd = em_bd_connecter();

        // pas utile, car l'adresse a déjà été vérifiée, mais tellement plus sécurisant...
        $email = em_bd_proteger_entree($bd, $email);
        $sql = "SELECT cliID FROM clients WHERE cliEmail = '$email'"; 
    
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        
        if (mysqli_num_rows($res) != 0) {
            $erreurs[] = 'L\'adresse email spécifiée existe déjà.';
            // libération des ressources 
            mysqli_free_result($res);
            mysqli_close($bd);
        }
        else{
            // libération des ressources 
            mysqli_free_result($res);
        }
        
    }
    
    // s'il y a des erreurs ==> on retourne le tableau d'erreurs    
    if (count($erreurs) > 0) {  
        return $erreurs;    
    }
    
    // pas d'erreurs ==> enregistrement de l'utilisateur
    $nomprenom = em_bd_proteger_entree($bd, $nomprenom);
    
    $passe1 = password_hash($passe1, PASSWORD_DEFAULT);
    $passe1 = em_bd_proteger_entree($bd, $passe1);
    
    $aaaammjj = $annee*10000  + $mois*100 + $jour;

    
    $sql = "INSERT INTO clients(cliNomPrenom, cliEmail, cliDateNaissance, cliPassword, cliAdresse, cliCP, cliVille, cliPays) 
            VALUES ('$nomprenom', '$email', $aaaammjj, '$passe1', '', 0, '', '')";
            
    mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

    // mémorisation de l'ID dans une variable de session 
    // cette variable de session permet de savoir si le client est authentifié
    $_SESSION['id'] = mysqli_insert_id($bd);
    
    // libération des ressources
    mysqli_close($bd);
    
    // redirection vers la page protegee.php
    header('Location: protegee.php'); //TODO : à modifier dans le projet
    exit();
}
    


?>
