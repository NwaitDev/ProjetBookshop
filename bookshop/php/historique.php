<?php
/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérification de la connexion au site
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

ob_start(); //démarre la bufferisation
session_start();

require_once 'bibli_generale.php';
require_once 'bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

/*------------------------- Etape 1 --------------------------------------------
- vérification de la connexion au site
------------------------------------------------------------------------------*/

// si utilisateur pas authentifié, redirection vers login.php
if (!em_est_authentifie()){
    header('Location: login.php');
    exit();
}

//connexion à la base de donnée
$bd = em_bd_connecter();

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

em_aff_debut('BookShop | Historique des commandes', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

eml_aff_contenu($bd);

em_aff_pied();

em_aff_fin('main');


mysqli_close($bd);

ob_end_flush();


// ----------  Fonctions locales du script ----------- //

/**
 * Affichage du contenu de la page (formulaire d'inscription)
 *
 * @global  array   $_POST
 */
function eml_aff_contenu($bd) {

    $id = 13;
    $sql = "SELECT cliNomPrenom,coID,liID,coDate,coHeure,ccQuantite,liTitre,liPrix 
            FROM clients LEFT JOIN commandes ON cliID=coIDClient LEFT JOIN compo_commande ON coID=ccIDLivre LEFT JOIN livres ON ccIDLivre=liID 
            WHERE cliID = '$id'";
    $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);    
    $commandes = [];
    $count=0;
    $lastIDcommande = -1;
    $user='';
    while ($t = mysqli_fetch_assoc($res)) {
        if ($t['coID'] != $lastIDcommande) {
            if ($lastIDcommande != -1) {
                $commandes[$count++]=$commande;
                //var_dump($commande); 
            }
            if ($lastIDcommande == -1){
                $user=$t['cliNomPrenom'];
            }
            $lastIDcommande = $t['coID'];
            $commande = array( 'coID' => $t['coID'], 
                            'date' => td_int_to_date($t['coDate']),
                            'heure' => td_int_to_heure($t['coHeure']),
                            'montant' => $t['liPrix'] * $t['ccQuantite'],
                            'livres' => array(array('titre' => $t['liTitre'], 'quantite' => $t['ccQuantite'],'prix' => $t['liPrix'],'id' => $t['liID']))
                        );
            //var_dump($commande); 
        }
        else {
            $commande['livres'][] = array('titre' => $t['liTitre'], 'quantite' => $t['ccQuantite'],'prix' => $t['liPrix'], 'id' => $t['liID']);
            $commande['montant'] += $t['liPrix']*$t['ccQuantite'];
        }       
    }
    //var_dump($commandes);
    // libération des ressources
    mysqli_free_result($res);
    echo '<h1>Historique de commande de ',$user,'</h1>';
    for($i=0;$i<$count;$i++){
        tdl_aff_commande($commandes[$i]);
    }
}   


/**
 *  Affichage d'une commande
 *
 *  @param  array       $commande      tableau associatif des infos sur une commande (coID, date, heure, livre(titre,quantite,prix),montant)
 *
 */
function tdl_aff_commande($commande) {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $livres = $commande['livres'];
    $commande = em_html_proteger_sortie($commande);
    echo 
        '<article class="arRecherche">',
            '<h5>ID de la commande : ', $commande['coID'], '</h5>',
            'Commande passée le ',$commande['date']['jour'],'/',$commande['date']['mois'],'/',$commande['date']['annee'],
            ' à ',$commande['heure']['heure'],'h',$commande['heure']['minute'],'.<br>',
            'Contenu :<br>';
    foreach ($livres as $livre) {
        echo    'Titre : <a href="details.php?article=', $livre['id'], '" title="Voir détails">',$livre['titre'],'</a><br>',
                'Prix : ',$livre['prix'],' €<br>',
                'Quantité : ',$livre['quantite'],'<br>';
    }
            
    echo    '<br>Montant total : ',$commande['montant'],' €',
        '</article>';
}

?>
