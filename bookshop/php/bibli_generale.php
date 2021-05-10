<?php

/*********************************************************
 *        Bibliothèque de fonctions génériques          
 * 
 * Les régles de nommage sont les suivantes.
 * Les fonctions commencent par le préfixe fd (cf f-rédéric d-adeau) 
 * ou em (cf e-ric m-erlet) pour les différencier
 * des fonctions php.
 *
 * Généralement on trouve ensuite un terme définisant le "domaine" de la fonction :
 *  _aff_   la fonction affiche du code html / texte destiné au navigateur
 *  _html_  la fonction renvoie du code html / texte
 *  _bd_    la fonction gère la base de données
 *
 *********************************************************/


/**
 *  Fonction affichant le début du code HTML d'une page.
 *
 *  @param  String  $titre          Titre de la page
 *  @param  String  $css            Chemin relatif vers la feuille de style CSS.
 *  @param  String  $enfant_body    Nom du tag ouvrant à ouvrir après le tag <body> si non vide
 */
function em_aff_debut($titre, $css = '', $enfant_body = '') {
    echo 
        '<!doctype html>',
        '<html lang="fr">',
            '<head>',
                '<title>', $titre, '</title>', 
                '<meta charset="UTF-8">',
                ($css ? "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">" : ''),    
            '</head>',
            '<body>',
                ($enfant_body ? "<{$enfant_body}>" : '');
}


/**
 *  Fonction affichant la fin du code HTML d'une page.
 *
 * @param  String  $enfant_body     Nom du tag fermant à fermer avant le tag </body> si non vide
 */
function em_aff_fin($enfant_body = '') {
    echo ($enfant_body ? "</{$enfant_body}>" : '') ,'</body></html>';
}

//____________________________________________________________________________
/** 
 *  Ouverture de la connexion à la base de données
 *  En cas d'erreur de connexion le script est arrêté.
 *
 *  @return objet   connecteur à la base de données
 */
function em_bd_connecter() {
    $conn = mysqli_connect(BD_SERVER, BD_USER, BD_PASS, BD_NAME);
    if ($conn !== FALSE) {
        //mysqli_set_charset() définit le jeu de caractères par défaut à utiliser lors de l'envoi
        //de données depuis et vers le serveur de base de données.
        mysqli_set_charset($conn, 'utf8') 
        or em_bd_erreur_exit('<h4>Erreur lors du chargement du jeu de caractères utf8</h4>');
        return $conn;     // ===> Sortie connexion OK
    }
    // Erreur de connexion
    // Collecte des informations facilitant le debugage
    $msg = '<h4>Erreur de connexion base MySQL</h4>'
            .'<div style="margin: 20px auto; width: 350px;">'
            .'BD_SERVER : '. BD_SERVER
            .'<br>BD_USER : '. BD_USER
            .'<br>BD_PASS : '. BD_PASS
            .'<br>BD_NAME : '. BD_NAME
            .'<p>Erreur MySQL numéro : '.mysqli_connect_errno()
            .'<br>'.mysqli_connect_error()
            .'</div>';
    em_bd_erreur_exit($msg);
}

//____________________________________________________________________________
/**
 * Arrêt du script si erreur base de données 
 *
 * Affichage d'un message d'erreur, puis arrêt du script
 * Fonction appelée quand une erreur 'base de données' se produit :
 *      - lors de la phase de connexion au serveur MySQL
 *      - ou indirectement lorsque l'envoi d'une requête échoue
 *
 * @param string    $msg    Message d'erreur à afficher
 */
function em_bd_erreur_exit($msg) {
    ob_end_clean(); // Suppression de tout ce qui a pu être déja généré

    echo    '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">',
            '<title>Erreur base de données</title>',
            '<style>',
                'table{border-collapse: collapse;}td{border: 1px solid black;padding: 4px 10px;}',
            '</style>',
            '</head><body>',
            $msg,
            '</body></html>';
    exit(1);        // ==> ARRET DU SCRIPT
}

//____________________________________________________________________________
/**
 * Gestion d'une erreur de requête à la base de données.
 *
 * A appeler impérativement quand un appel de mysqli_query() échoue 
 * Appelle la fonction em_bd_erreurExit() qui affiche un message d'erreur puis termine le script
 *
 * @param objet     $bd     Connecteur sur la bd ouverte
 * @param string    $sql    requête SQL provoquant l'erreur
 */
function em_bd_erreur($bd, $sql) {
    $errNum = mysqli_errno($bd);
    $errTxt = mysqli_error($bd);

    // Collecte des informations facilitant le debugage
    $msg =  '<h4>Erreur de requête</h4>'
            ."<pre><b>Erreur mysql :</b> $errNum"
            ."<br> $errTxt"
            ."<br><br><b>Requête :</b><br> $sql"
            .'<br><br><b>Pile des appels de fonction</b></pre>';

    // Récupération de la pile des appels de fonction
    $msg .= '<table>'
            .'<tr><td>Fonction</td><td>Appelée ligne</td>'
            .'<td>Fichier</td></tr>';

    $appels = debug_backtrace();
    for ($i = 0, $iMax = count($appels); $i < $iMax; $i++) {
        $msg .= '<tr style="text-align: center;"><td>'
                .$appels[$i]['function'].'</td><td>'
                .$appels[$i]['line'].'</td><td>'
                .$appels[$i]['file'].'</td></tr>';
    }

    $msg .= '</table>';

    em_bd_erreur_exit($msg);    // ==> ARRET DU SCRIPT
}



/** 
 *  Protection des sorties (code HTML généré à destination du client).
 *
 *  Fonction à appeler pour toutes les chaines provenant de :
 *      - de saisies de l'utilisateur (formulaires)
 *      - de la bdD
 *  Permet de se protéger contre les attaques XSS (Cross site scripting)
 *  Convertit tous les caractères éligibles en entités HTML, notamment :
 *      - les caractères ayant une signification spéciales en HTML (<, >, ", ', ...)
 *      - les caractères accentués
 * 
 *  Si on lui transmet un tableau, la fonction renvoie un tableau où toutes les chaines
 *  qu'il contient sont protégées, les autres données du tableau ne sont pas modifiées. 
 *
 *  @param  mixed  $content   la chaine à protéger ou un tableau contenant des chaines à protéger 
 *  @return mixed             la chaîne protégée ou le tableau
 */
function em_html_proteger_sortie($content) {
    if (is_array($content)) {
        foreach ($content as &$value) {
            $value = em_html_proteger_sortie($value);   
        }
        unset ($value); // à ne pas oublier (de façon générale)
        return $content;
    }
    if (is_string($content)){
        return htmlentities($content, ENT_QUOTES, 'UTF-8');
    }
    return $content;
}

/**
*  Protection des entrées (chaînes envoyées au serveur MySQL)
* 
* Avant insertion dans une requête SQL, certains caractères spéciaux doivent être échappés (", ', ...).
* Toutes les chaines de caractères provenant de saisies de l'utilisateur doivent être protégées 
* en utilisant la fonction mysqli_real_escape_string() (si elle est disponible)
* Cette dernière fonction :
* - protège les caractères spéciaux d'une chaîne (en particulier les guillemets)
* - permet de se protéger contre les attaques de type injections SQL. 
*
*  Si on lui transmet un tableau, la fonction renvoie un tableau où toutes les chaines
*  qu'il contient sont protégées, les autres données du tableau ne sont pas modifiées.  
*   
*   @param    objet   $bd         l'objet représantant la connexion au serveur MySQL
*   @param    mixed   $content    la chaine à protéger ou un tableau contenant des chaines à protéger 
*  @return    mixed               la chaîne protégée ou le tableau
*/  
function em_bd_proteger_entree($bd, $content) {
    if (is_array($content)) {
        foreach ($content as &$value) {
            $value = em_bd_proteger_entree($bd,$value);   
        }
        unset ($value); // à ne pas oublier (de façon générale)
        return $content;
    }
    if (is_string($content)){
        if (function_exists('mysqli_real_escape_string')) {
            return mysqli_real_escape_string($bd, $content);
        }
        if (function_exists('mysqli_escape_string')) {
            return mysqli_escape_string($bd, $content);
        }
        return addslashes($content);
        
    }
    return $content;
}

//___________________________________________________________________
/**
* Contrôle des clés présentes dans les tableaux $_GET ou $_POST - piratage ?
*
*
* Soit $x l'ensemble des clés contenues dans $_GET ou $_POST 
* L'ensemble des clés obligatoires doit être inclus dans $x.
* De même $x doit être inclus dans l'ensemble des clés autorisées, formé par l'union de l'ensemble 
* des clés facultatives et de l'ensemble des clés obligatoires.
* Si ces 2 conditions sont vraies, la fonction renvoie true, sinon, elle renvoie false.
* Dit autrement, la fonction renvoie false si une clé obligatoire est absente ou 
* si une clé non autorisée est présente; elle renvoie true si "tout va bien"
* 
* @param string    $tab_global 'post' ou 'get'
* @param array     $cles_obligatoires tableau contenant les clés qui doivent obligatoirement être présentes
* @param array     $cles_facultatives tableau contenant les clés facultatives
* @return boolean  true si les paramètres sont corrects, false sinon
*/
function em_parametres_controle($tab_global, $cles_obligatoires, $cles_facultatives = array()){
    $x = strtolower($tab_global) == 'post' ? $_POST : $_GET;

    $x = array_keys($x);
    // $cles_obligatoires doit être inclus dans $x
    if (count(array_diff($cles_obligatoires, $x)) > 0){
        return false;
    }
    // $x doit être inclus dans $cles_obligatoires Union $cles_facultatives
    if (count(array_diff($x, array_merge($cles_obligatoires,$cles_facultatives))) > 0){
        return false;
    }
    
    return true;
}

//___________________________________________________________________
/**
 * Teste si une valeur est une valeur entière
 *
 * @param mixed     $x  valeur à tester
 * @return boolean  TRUE si entier, FALSE sinon
 */
function em_est_entier($x) {
    return is_numeric($x) && ($x == (int) $x);
}

//___________________________________________________________________
/**
 * Teste si un nombre est compris entre 2 autres
 *
 * @param integer   $x  nombre ‡ tester
 * @return boolean  TRUE si ok, FALSE sinon
 */
function em_est_entre($x, $min, $max) {
    return ($x >= $min) && ($x <= $max);
}


//___________________________________________________________________
/**
 * Renvoie un tableau contenant le nom des mois (utile pour certains affichages)
 *
 * @return array    Tableau à indices numériques contenant les noms des mois
 */
function em_get_tableau_mois(){
    return array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
}

//___________________________________________________________________
/**
 * Affiche une liste déroulante à partir des options passées en paramètres.
 *
 * @param string    $nom       Le nom de la liste déroulante (valeur de l'attribut name)
 * @param array     $options   Un tableau associatif donnant la liste des options sous la forme valeur => libelle 
 * @param string    $default   La valeur qui doit être sélectionnée par défaut. 
 */
function em_aff_liste($nom, $options, $defaut) {
    echo '<select name="', $nom, '">';
    foreach ($options as $valeur => $libelle) {
        echo '<option value="', $valeur, '"', (($defaut == $valeur) ? ' selected' : '') ,'>', $libelle, '</option>';
    }
    echo '</select>';
}

//___________________________________________________________________
/**
 * Affiche une liste déroulante représentant les 12 mois de l'année
 *
 * @param string    $nom       Le nom de la liste déroulante (valeur de l'attribut name)
 * @param int       $default   Le mois qui doit être sélectionné par défaut (1 pour janvier)
 */
function em_aff_liste_mois($nom, $defaut) {
    $mois = em_get_tableau_mois();
    $m = array();
    foreach ($mois as $k => $v) {
        $m[$k+1] = mb_strtolower($v, 'UTF-8');   
        // comme on est en UTF-8 on utilise la fonction mb_strtolower
        // voir : https://www.php.net/manual/fr/function.mb-strtolower.php
    }
    em_aff_liste($nom, $m, $defaut);
}

//___________________________________________________________________
/**
 * Affiche une liste déroulante d'une suite de nombre à partir des options passées en paramètres.
 *
 * @param string    $nom       Le nom de la liste déroulante (valeur de l'attribut name)
 * @param int       $min       La valeur minimale de la liste
 * @param int       $max       La valeur maximale de la liste 
 * @param int       $pas       Le pas d'itération (si positif, énumération croissante, sinon décroissante) 
 * @param int       $default   La valeur qui doit être sélectionnée par défaut. 
 */
function em_aff_liste_nombre($nom, $min, $max, $pas, $defaut) {
    echo '<select name="', $nom, '">';
    if ($pas > 0) {
        for ($i=$min; $i <= $max; $i += $pas) {
            echo '<option value="', $i, '"', (($defaut == $i) ? ' selected' : '') ,'>', $i, '</option>';
        }
    }
    else {
        for ($i=$max; $i >= $min; $i += $pas) {
            echo '<option value="', $i, '"', (($defaut == $i) ? ' selected' : '') ,'>', $i, '</option>';
        }
    }
    echo '</select>';
}

//___________________________________________________________________
/**
 * Affiche 3 listes déroulantes (jour, mois, année) représentant une date
 *
 * La liste des jours est une liste de nombres (1-31) nommée {$name}_j
 * La liste des mois, nommée {$name}_m, est une liste d'options associant le nom du mois (libellé) à une valeur entière
 * (exemple : février est associé à la valeur 2)
 * La liste des années, nommée {$name}_a, est une liste de nombres ({$annee_min}-{$annee_max})
 * Si le jour sélectionné vaut 0, la fonction sélectionne le jour courant. Idem pour le mois et l'année.
 *
 * @param string    $name           Le nom utilisé comme préfixe pour nommer les listes déroulantes
 * @param int       $annee_min      La plus petite année affichée
 * @param int       $annee_max      La plus grande année affichée
 * @param int       $j_s            Le jour sélectionné
 * @param int       $m_s            Le mois sélectionné (1 pour janvier)
 * @param int       $a_s            L'année sélectionnée
 * @param int       $pas_annee      Le pas d'itération de l'année (si positif, énumération croissante, sinon décroissante) 
 */
function em_aff_listes_date($name, $annee_min, $annee_max, $j_s = 0, $m_s = 0, $a_s = 0, $pas_annee = -1){ 
    list($jj, $mm, $aa) = explode('-', date('j-n-Y'));
    em_aff_liste_nombre("{$name}_j", 1, 31, 1, $j_s ? $j_s : $jj);
    em_aff_liste_mois("{$name}_m", $m_s ? $m_s : $mm);
    em_aff_liste_nombre("{$name}_a", $annee_min, $annee_max, $pas_annee, $a_s ? $a_s : $aa);
}

//___________________________________________________________________
/**
 * Affiche une ligne d'un tableau permettant la saisie d'une date
 *
 * La ligne est constituée de 2 cellules :
 * - la 1ère cellule contient un libellé
 * - la 2ème cellule contient les 3 listes déroulantes (jour, mois, année) représentant la date
 *
 * @param string    $libelle        Le libellé affiché à gauche des listes déroulantes
 * @param string    $name           Le nom utilisé comme préfixe pour nommer les listes déroulantes
 * @param int       $annee_min      La plus petite année affichée
 * @param int       $annee_max      La plus grande année affichée
 * @param int       $j_s            Le jour sélectionné
 * @param int       $m_s            Le mois sélectionné (1 pour janvier)
 * @param int       $a_s            L'année sélectionnée
 * @param int       $pas_annee      Le pas d'itération de l'année (si positif, énumération croissante, sinon décroissante) 
 */
function em_aff_ligne_date($libelle, $name, $annee_debut, $annee_fin, $j_s = 0, $m_s = 0, $a_s = 0, $pas_annee = -1){
    echo '<tr>', '<td>', $libelle, '</td>', '<td>';
    em_aff_listes_date($name, $annee_debut, $annee_fin, $j_s, $m_s, $a_s, $pas_annee);
    echo '</td>', '</tr>';
}


//___________________________________________________________________
/**
 * Affiche une ligne d'un tableau permettant la saisie d'un champ input de type 'text', 'password' ou 'email'
 *
 * La ligne est constituée de 2 cellules :
 * - la 1ère cellule contient un label permettant un "contrôle étiqueté" de l'input 
 * - la 2ème cellule contient l'input
 *
 * @param string    $libelle        Le label associé à l'input
 * @param array     $attributs      Un tableau associatif donnant les attributs de l'input sous la forme nom => valeur
 * @param string    $prefix_id      Le préfixe utilisé pour l'id de l'input, ce qui donne un id égal à {$prefix_id}{$attributs['name']}
 */
function em_aff_ligne_input($libelle, $attributs = array(), $prefix_id = 'text'){
    echo    '<tr>', 
                '<td><label for="', $prefix_id, $attributs['name'], '">', $libelle, '</label></td>',
                '<td><input id="', $prefix_id, $attributs['name'], '"'; 
                
    foreach ($attributs as $cle => $value){
        echo ' ', $cle, ($value !== false ? "='{$value}'" : '');
    }
    echo '></td></tr>';
}

/**
 * Affiche une barre de séléction de page
 * 
 * @param int $nbPages      Le nombre de pages total
 * @param int $current      Le numéro de la page actuellement affichée
 * @param int $range        Le nombre de pages à proposer de rejoindre avant et après la page courante
 */
function ng_page_bar($nbPages, $current,$range){
    echo '<p class="pagebar"> Page : ';
    $addr =  $_SERVER['PHP_SELF'].'?';
        foreach ($_GET as $key => $value) {
            if($key=='page'){
                continue;
            }
            $addr.=$key.'='.$value.'&';
        }
    if($current>1){
        echo '<a href="'.$addr.'page='.($current-1).'"> << </a>';
    }
    for ($i=$current-$range; $i<$current+$range  ; ++$i) { 
        if($i<1){
            continue;
        }
        if($i>$nbPages){
            break;
        }
        if($i==$current){
            echo $i.' ';
        }else{
            echo '<a href="',$addr.'page='.$i,'">',$i,'</a> ';
        }
    }
    if($current<$nbPages){
        echo '<a href="'.$addr.'page='.($current+1).'"> >> </a>';
    }
    echo '</p>';
}

/**
 *  Vérifie que les valeurs des valeurs d'indexs $keys sont identiques pour chaque tableau
 *  
 *  @param array $tab1      premier tableau de données à comparer
 *  @param array $tab2      second tableau de données à comparer
 *  @param array $keys      liste des indexs à comparer
 *  @return bool            true si les tableaux sont identiques aux indexs keys, false sinon
 */
function ng_value_comp($tab1, $tab2, $keys){
    foreach ($keys as $key) {
        if (isset($tab1[$key]) && isset($tab2[$key])) {
            if($tab1[$key]!=$tab2[$key]){
                return false;
            }else{
                continue;
            }
        }else{
            return false;
        }
    }
    return true;
}

?>
