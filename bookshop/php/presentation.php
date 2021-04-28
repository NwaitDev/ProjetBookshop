<?php

ob_start(); //démarre la bufferisation
session_start();

require_once '../php/bibli_generale.php';
require_once ('../php/bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Présentation', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

eml_aff_contenu();

em_aff_pied();

em_aff_fin('main');



// ----------  Fonctions locales au script ----------- //

/** 
 *  Affichage du contenu de la page
 */
function eml_aff_contenu() {
    
    echo 
        '<p>BookShop est une librairie virtuelle en ligne qui vous donne accès à un large choix de livres en tous genres (romans, manuels techniques, bandes dessinées, magazines), que vous pourrez commander pour vous offrir, vous faire offrir, ou offrir à vos amis.</p>',
        '<nav>',
            '<ul>',
                '<li><a href="#quetrouveton">Que trouve-t-on sur BookShop ?</a></li>',
                '<li><a href="#listekdo">Liste de cadeaux</a>',
                    '<ul>',
                        '<li><a href="#votreListe">Votre liste de cadeaux</a></li>',
                        '<li><a href="#autresListes">La liste de cadeaux de vos amis</a></li>',
                    '</ul>',
                '</li>',
                '<li><a href="#livraisonEtPaiement">Paiement et livraison</a>',
                    '<ul>',
                        '<li><a href="#paiement">Paiement sécurisé</a></li>',
                        '<li><a href="#livraison">Livraison en 24h chrono partout dans le monde</a></li>',
                    '</ul>',
                '</li>',
                '<li><a href="#confidentialite">Politique de confidentialité</a></li>',
            '</ul>',
        '</nav>',


        '<section>',
            '<h2 id="quetrouveton">Que trouve-t-on sur BookShop ?</h2>',

            '<p><img src="../images/fleche_droite.png" alt="fleche" width="19" height="19"> BookShop vous propose en exclusivité :</p>',

            '<ul>',
                '<li>un <strong>large catalogue</strong> de livres : romans, bandes dessinées, livres techniques, etc.</li>',
                '<li>l\'accès offert aux versions <strong>numériques</strong> des oeuvres achetées au format papier. </li>',
                '<li>des <strong>suggestions personnalisées</strong> d\'ouvrages correspondant à vos goûts.</li>',
                '<li>la possibilité de déposer votre <strong>liste de cadeaux</strong>, et de consulter celle de vos amis.',
                '</li>',
                '<li>la livraison <strong>rapide et sûre</strong> par drone utilisant le dernier cri de la technologie issue de l\'<a href="http://www.esa.int" target="_blank" class="lienExterne">Agence Spaciale Européenne</a> (disponibilité variable suivant la région). </li>',
            '</ul>',

            '<div id="bcCompo">',
                '<img src="../images/H2G2.jpg" alt="H2G2">',
                '<img src="../images/watchmen.jpg" alt="watchmen">',
                '<img src="../images/TWD.jpg" alt="TWD">',
                '<img src="../images/1984.jpg" alt="1984">',
                '<img src="../images/PHP4dummies.jpg" alt="PHP4dummies">',
                '<img src="../images/lecteur.png" alt="lecteur">',
            '</div>',

        '</section>',

        '<section>',
            '<h2 id="listekdo">Liste de cadeaux</h2>',

            '<p>Le site de BookShop intègre, pour vous, la possibilité de concevoir votre liste d\'envies. Cette liste pourra être partagée avec vos amis, pour leur indiquer vos goûts, ou leur suggérer des idées de cadeaux.</p>',

            '<h3 id="votreListe">Votre liste de cadeaux</h3>',

            '<img src="../images/cadeau_souris.jpg" alt="cadeau souris">',

            '<p><img src="../images/fleche_droite.png" alt="fleche" width="19" height="19"> Fini le temps où l\'on ne savait pas quoi offrir pour Noël ou pour un anniversaire ! Déposez et publiez la liste des vos envies, et partagez-la avec vos amis. Ceux-ci pourront vous offrir les articles que vous avez identifiés et ainsi profiter d\'une réduction sur leur prochaine commande.</p>',

            '<h3 id="autresListes">Les listes de cadeaux de vos amis</h3>',

            '<p><img src="../images/fleche_droite.png" alt="fleche" width="19" height="19"> Un anniversaire en vue ? Vous souhaitez offrir un présent qui dure à quelqu\'un que vous aimez, mais vous êtes en mal d\'inspiration ? Fini les achats irréfléchis et au succès aléatoire : consultez les listes d\'envies de vos amis, et offrez-leur un cadeau qui leur plaira vraiment !</p>',

        '</section>',

        '<section>',
            '<h2 id="livraisonEtPaiement">Livraison et paiement</h2>',

            '<article>',
                '<h3 id="livraison">Un service de livraison efficace</h3>',

                '<img src="../images/livraison.jpg" alt="livraison">',

                '<p><img src="../images/fleche_droite.png" alt="fleche" width="19" height="19"> BookShop dispose d\'une armée de livreurs, disponibles 24h/24 dans le monde entier, pour assurer la livraison de vos commandes dans les meilleurs délais. Dans les grandes villes, la livraison par drone est prévue en 2015. Dans le désert, et les campagnes les plus reculées, BookShop a lancé en précurseur la livraison à dos de chameau depuis 1998. <br> Ce service de livraison à la pointe de la technologie est gratuit à partir de 10 euros d\'achat.',
                '</p>',

            '</article>',

            '<article>',

                '<h3 id="paiement" class="clear">Un service de paiement ultra-sécurisé</h3>',

                '<img src="../images/paiement_securise.jpg" alt="paiement securise">',

                '<p><img src="../images/fleche_droite.png" alt="fleche" width="19" height="19"> Vous hésitez à acheter en ligne car vous craignez le piratage ? N\'hésitez plus. BookShop propose un système de paiement en ligne ultra-sécurisé. Votre numéro de carte est hashé menu et chiffré par des algorithmes à complexité exponentielle, pour une plus grande sécurité. Ce système est certifié par les experts de la NSA.',
                '</p>',

            '</article>',

        '</section>',

        '<section>',

            '<h2 id="confidentialite"> Notre politique de confidentialité </h2>',

            '<p>Les listes de cadeaux enregistrées chez BookShop ne sont diffusées qu\'aux personnes avec lesquelles vous décidez de les partager, et ne sont ainsi pas accessibles sans votre autorisation. BookShop ne collecte pas d\'informations sur ses clients, et ne vend pas les données personnelles de ses clients à des tiers <span class="font6">(en fait, on les donne gratuitement)</span>.</p>',

        '</section>';
}



    
?>

