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

// si utilisateur pas authentifié, redirection vers login.php
if (!em_est_authentifie()){
    header('Location: login.php');
    exit();
}

// traitement si soumission du formulaire d'inscription
$err = isset($_POST['btnModifier']) ? eml_traitement_inscription() : array(); 

//connexion à la base de donnée
$bd = em_bd_connecter();

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

em_aff_debut('BookShop | Profil Utilisateur', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

eml_aff_contenu($err,$bd);

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
function eml_aff_contenu($err, $bd) {

    $userData = tdl_requete_utilisateur($bd);

    //affichage des données de l'utilisateur
    $email = $userData['cliEmail'];
    $nomprenom = $userData['cliNomPrenom'];
    $jour = $userData['cliDateNaissance']%100;
    $mois = ($userData['cliDateNaissance']/100)%100;
    $annee = (int)($userData['cliDateNaissance']/10000);
    $adresse = $userData['cliAdresse'];
    $codepostal = $userData['cliCP'];
    $ville = $userData['cliVille'];
    $pays = $userData['cliPays'];

    echo '<h1>Profil utilisateur</h1>';

    echo '<p>Pour consulter votre historique de commande, consultez ce <a href="historique.php" title="Voir détails">lien</a>.</p>';
        
    if (count($err) > 0) {
        echo '<p class="error">Votre inscription n\'a pas pu être réalisée à cause des erreurs suivantes : ';
        foreach ($err as $v) {
            echo '<br> - ', $v;
        }
        echo '</p>';    
    }

    echo    
        '<p>Pour modifier votre compte veuillez remplir ce formulaire : </p>',
        '<form method="post" action="inscription.php">',
            '<table>';

    em_aff_ligne_input('Votre adresse email :', array('type' => 'email', 'name' => 'email', 'value' => $email, 'required' => false));
    em_aff_ligne_input('Nom et prénom :', array('type' => 'text', 'name' => 'nomprenom', 'value' => $nomprenom, 'required' => false));
    em_aff_ligne_date('Votre date de naissance :', 'naissance', 2020 - NB_ANNEE_DATE_NAISSANCE + 1, 2020, $jour, $mois, $annee);

    em_aff_ligne_input('Votre adresse :',array('type'=> 'text', 'name' => 'adresse', 'value' => $adresse, 'required' => false));
    em_aff_ligne_input('Votre code postal :',array('type'=> 'text', 'name' => 'cp', 'value' => $codepostal, 'required' => false));
    em_aff_ligne_input('Votre ville :',array('type'=> 'text', 'name' => 'ville', 'value' => $ville, 'required' => false));
    em_aff_ligne_input('Votre pays :',array('type'=> 'text', 'name' => 'pays', 'value' => $pays, 'required' => false));

    em_aff_ligne_input('Choisissez un mot de passe :', array('type' => 'password', 'name' => 'passe1', 'value' => '', 'required' => false));
    em_aff_ligne_input('Répétez le mot de passe :', array('type' => 'password', 'name' => 'passe2', 'value' => '', 'required' => false));
            
    echo 
                '<tr>',
                    '<td colspan="2">',
                        '<input type="submit" name="btnModifier" value="Modifier">',
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
 * @param  mysqli   $bd      connexion à la base de donnée
 *
 * @return array    tableau assosiatif contenant les erreurs
 */
function eml_traitement_inscription($bd) {

    if( !em_parametres_controle('post', array('email', 'nomprenom', 'naissance_j', 'naissance_m', 'naissance_a', 
                                              'adresse','cp','ville','pays','passe1', 'passe2', 'btnModifier'))) {
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
    
    //vérification de l'adresse
    $adresse = trim($_POST['adresse']);

    if(mb_strlen($adresse,'UTF-8')> LMAX_ADRESSE){
        $erreurs[] = 'L\'adresse ne peux pas dépasser '.LMAX_ADRESSE.'caractères.';
    }
    $noTags = strip_tags($adresse);
    if ($noTags != $adresse){
        $erreurs[] = 'L\'adresse ne peux pas contenir de code HTML.';
    } else {
        mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
        if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $adresse)){
            $erreurs[] = 'L\'adresse contiens des caractères non autorisés';
        }
    }

    //vérification de la ville
    $ville = trim($_POST['ville']);

    if(mb_strlen($ville,'UTF-8')> LMAX_VILLE){
        $erreurs[] = 'La ville ne peux pas dépasser '.LMAX_VILLE.'caractères.';
    }
    $noTags = strip_tags($ville);
    if ($noTags != $ville){
        $erreurs[] = 'La ville ne peux pas contenir de code HTML.';
    } else {
        mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
        if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $ville)){
            $erreurs[] = 'La ville contiens des caractères non autorisés';
        }
    }

    //vérification du code postal
    $codepostal = trim($_POST['cp']);

    if(mb_strlen($codepostal,'UTF-8')!= LMAX_CP){
        $erreurs[] = 'Le code postal doit faire '.LMAX_CP.'caractères.';
    }
    $noTags = strip_tags($codepostal);
    if ($noTags != $codepostal){
        $erreurs[] = 'Le code postal ne peux pas contenir de code HTML.';
    } else {
        mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
        if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $codepostal)){
            $erreurs[] = 'Lecode postal contiens des caractères non autorisés';
        }
    }

    //vérification du pays
    $pays = trim($_POST['pays']);

    if(mb_strlen($adresse,'UTF-8')> LMAX_PAYS){
        $erreurs[] = 'Le pays ne peux pas dépasser '.LMAX_PAYS.'caractères.';
    }
    $noTags = strip_tags($pays);
    if ($noTags != $pays){
        $erreurs[] = 'Le pays ne peux pas contenir de code HTML.';
    } else {
        mb_regex_encoding ('UTF-8'); //définition de l'encodage des caractères pour les expressions rationnelles multi-octets
        if( !mb_ereg_match('^[[:alpha:]]([\' -]?[[:alpha:]]+)*$', $pays)){
            $erreurs[] = 'Lepays contiens des caractères non autorisés';
        }
    }


    if (count($erreurs) == 0) {
        // vérification de l'unicité de l'adresse email 
        // (uniquement si pas d'autres erreurs, parce que ça coûte un bras)
        

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
    $id = $_SESSION['id'];
    $nomprenom = em_bd_proteger_entree($bd, $nomprenom);
    
    $passe1 = password_hash($passe1, PASSWORD_DEFAULT);
    $passe1 = em_bd_proteger_entree($bd, $passe1);
    
    $aaaammjj = $annee*10000  + $mois*100 + $jour;

    $adresse = em_bd_proteger_entree($bd,$adresse);
    $ville = em_bd_proteger_entree($bd,$ville);
    $codepostal = em_bd_proteger_entree($bd,$codepostal);
    $pays = em_bd_proteger_entree($bd,$pays);
    
    $sql = "UPDATE clients
            SET cliNomPrenom = '$nomprenom',
                cliEmail = '$email',
                cliDateNaissance = '$aaaammjj',
                cliAdresse = '$adresse',
                cliVille = '$ville',
                cliCP = '$codepostal',
                cliPays = '$pays'
            WHERE cliID = '$id'";
            
    $res = mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);
    if($res){
        mysqli_close($bd);
        header("Location: compte.php");
        exit();
    }
    // libération des ressources
    mysqli_close($bd);
    // redirection vers la page précédente
    $addr = 'compte.php';
    header("Location: $addr");
    exit();
}

/**
 * Requête des données de l'utilisateur connecté
 * 
 * @param       mysqli      $bd    la connexion à la base de donnée
 * @return      array       un tableau associatif contenant les données de l'utilisateur connecté
 */
function tdl_requete_utilisateur($bd) {
    $id = $_SESSION['id'];
    $sql = "SELECT cliEmail,cliNomPrenom, cliAdresse, cliCP, cliVille,cliPays, cliDateNaissance FROM clients WHERE cliID ='$id'";
    $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
    $row = mysqli_fetch_assoc($res);

    return $row;
}


?>
