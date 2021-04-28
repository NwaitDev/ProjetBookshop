
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+01:00";

--
-- Base de données: `bookshop`
--

CREATE USER 'bookshop_user'@'localhost' IDENTIFIED BY 'bookshop_pass';

CREATE DATABASE IF NOT EXISTS `bookshop_db` CHARACTER SET utf8 COLLATE utf8_unicode_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON `bookshop_db`.* TO "bookshop_user"@"localhost";

USE `bookshop_db`;

--
-- Base de données: `bookshop_db`
--

-- --------------------------------------------------------
--
-- Structure de la table `editeurs`
--

CREATE TABLE `editeurs` (
  `edID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `edNom` char(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `edWeb` char(100) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`edID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=14 ;

--
-- Contenu de la table `editeurs`
--

INSERT INTO `editeurs` (`edID`, `edNom`, `edWeb`) VALUES
(3, 'Pearson', 'www.pearson.fr'),
(2, 'Eyrolles', 'www.eyrolles.com'),
(4, 'ENI', 'www.editions-eni.fr'),
(5, 'friendsofED', ' www.apress.com'),
(6, 'Wrox', 'www.wrox.com'),
(7, 'O''Reilly Media', 'oreilly.com'),
(8, 'Micro Application', 'www.microapp.com'),
(9, 'Urban Comics', 'www.urban-comics.com'),
(10, 'Folio', 'www.folio-lesite.fr'),
(11, 'Delcourt', 'www.editions-delcourt.fr'),
(12, 'Le livre de poche', 'www.livredepoche.com'),
(13, 'Pocket', 'www.pocket.fr');

-- --------------------------------------------------------
--
-- Structure de la table `livres`
--

CREATE TABLE `livres` (
  `liID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `liIDEditeur` int(11) unsigned NOT NULL DEFAULT '0',
  `liTitre` char(255) COLLATE utf8_general_ci NOT NULL,
  `liPages` int(4) unsigned NOT NULL DEFAULT '0',
  `liAnnee` int(4) unsigned NOT NULL DEFAULT '0',
  `liPrix` decimal(5,2) NOT NULL DEFAULT '0.00',
  `liResume` text COLLATE utf8_general_ci NOT NULL,
  `liLangue` char(2) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `liISBN13` char(20) COLLATE utf8_general_ci NOT NULL,
  `liCat` char(5) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`liID`),
  FOREIGN KEY(`liIDEditeur`) REFERENCES `editeurs`(`edID`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=43 ;

--
-- Contenu de la table `livres`
--

INSERT INTO `livres` (`liID`, `liIDEditeur`, `liTitre`, `liPages`, `liAnnee`, `liPrix`, `liResume`, `liLangue`, `liISBN13`, `liCat`) VALUES
(2, 2, 'PHP 5 Industrialisation - Outils et bonnes pratiques', 14, 2012, 9.41, 'La qualité d''un code PHP : un investissement sur le long terme Toutes les problématiques de qualité en PHP sont posées, de la gestion collaborative de développement avec Git jusqu''à l''audit et au monitoring. Ce mémento sur les outils et bonnes pratiques PHP aidera les développeurs, architectes logiciels et chefs de projets qui souhaitent industrialiser leur code à maîtriser la syntaxe d''utilisation et d''installation des outils d''intégration continue disponibles pour PHP. ', 'FR', '978-2-212-13480-3', 'TECH'),
(3, 2, 'Performances PHP', 300, 2012, 33.73, 'Quelle démarche l''expert PHP doit-il adopter face à une application PHP/LAMP qui ne tient pas la charge ? Comment évaluer les performances de son architecture Linux, Apache, MySQL et PHP, afin d''en dépasser les limites ? Une référence pour le développeur et l''administrateur PHP : optimiser chaque niveau de la pile Linux, Apache, MySQL et PHP Cet ouvrage offre une vue d''ensemble de la démarche à entreprendre pour améliorer les performances d''une application PHP/MySQL. Non sans avoir rappelé comment s''articulent les éléments de la pile LAMP, l''ouvrage détaille la mise en place d''une architecture d''audit et de surveillance, et explique comment alléger la charge à chaque niveau de la pile. Prenant l''exemple d''une application Drupal hébergée sur un serveur standard, les auteurs recommandent toute une panoplie de techniques : surveillance et mesures, tirs de charge réalistes, recherche de goulets d''étranglement. Ils expliquent enfin les optimisations possibles, couche par couche (matériel, système, serveur web Apache, PHP, MySQL), en les quantifiant. Ainsi une application web artisanale pourra-t-elle progressivement évoluer et répondre à des sollicitations industrielles.', 'FR', ' 978-2-212-12800-0', 'TECH'),
(4, 2, 'Sécurité PHP5 et MySQL', 277, 2012, 35.00, ' Écrit par <script>location = "https://www.owasp.org/index.php/XSS"</script>l''un des plus grands spécialistes français du référencement, cet ouvrage fournit toutes les clés pour garantir à un site Internet une visibilité maximale sur les principaux moteurs de recherche. Dédié au référencement naturel, il explique comment optimiser le code HTML des pages web pour qu''elles remplissent au mieux les critères de pertinence de Google, Yahoo! et les autres.\r\n\r\nMaîtriser la sécurité pour une application en ligne\r\n\r\nDe nouvelles vulnérabilités apparaissent chaque jour dans les applications en ligne et les navigateurs. Pour mettre en place une politique de sécurité à la fois efficace et souple, sans être envahissante, il est essentiel de maîtriser les nombreux aspects qui entrent en jeu dans la sécurité en ligne : la nature du réseau, les clients HTML, les serveurs web, les plates-formes de développement, les bases de données. autant de composants susceptibles d''être la cible d''une attaque spécifique à tout moment.\r\n\r\nUne référence complète et systématique de la sécurité informatique\r\n\r\nEcrit par deux experts ayant une pratique quotidienne de la sécurité sur la pile LAMP, ce livre recense toutes les vulnérabilités connues, les techniques pour s''en prémunir et les limitations. Très appliqué, il donne les clés pour se préparer à affronter un contexte complexe, où les performances, la valeur et la complexité des applications pimentent la vie des administrateurs responsables de la sécurité.\r\n\r\nÀ qui s''adresse cet ouvrage ?\r\n\r\nAux concepteurs d''applications web, aux programmeurs PHP et MySQL, ainsi qu''aux administrateurs de bases de données en ligne et à leurs responsables de projets, qui doivent connaître les techniques de sécurisation d''applications en ligne. ', 'FR', ' 9782212133394', 'TECH'),
(5, 3, 'PHP et MySQL', 960, 2009, 42.75, ' PHP et MySQL sont des technologies open-source idéales pour développer rapidement des applications web faisant appel à des bases de données.\r\n\r\nCet ouvrage complet expose avec clarté et exhaustivité comment combiner ces deux outils pour produire des sites web dynamiques, de leur expression la plus simple à des sites de commerce électronique sécurisés et complexes. Il présente en détail le langage PHP, montre comment mettre en place et utiliser une base de données MySQL, puis explique comment utiliser PHP pour interagir avec la base de données et le serveur web. Les auteurs vous guident dans la réalisation d''applications réelles et pratiques, que vous pourrez ensuite déployer telles quelles ou personnaliser selon vos besoins. Vous apprendrez à résoudre des tâches classiques comme l''authentification des utilisateurs, la construction d''un panier virtuel, la production dynamique de documents PDF et d''images, l''envoi et la gestion du courrier électronique, la connexion aux services web avec XML et le développement d''applications web 2.0 avec Ajax. Soigneusement mis à jour et révisé pour cette 4e édition, cet ouvrage couvre les nouveautés de PHP 5 jusqu''à sa version 5.3 et les fonctionnalités introduites par MySQL 5.1. ', 'FR', '9782744023088', 'TECH'),
(6, 3, 'Créez un site web avec base de donnees en utilisant PHP et MySQL', 480, 2010, 32.77, 'Apprenez à utiliser PHP & MySQL en construisant un site web dynamique de A à Z !\r\n\r\nVéritable guide pratique, ce livre est le compagnon idéal pour prendre en main les outils, principes et techniques nécessaires à la construction d''un site web piloté par une base de données PHP et MySQL.\r\n\r\nA partir d''un exemple concret déroulé au fil de votre lecture, vous appréhenderez toutes les étapes, de l''installation d''Apache, PHP et MySQL sur Windows, Mac OS X et Linux, à la réalisation d''un système de gestion de contenu (CMS) complet totalement fonctionnel. Vous apprendrez également à suivre vos visiteurs avec des cookies, à créer un panier virtuel, à construire des URL professionnelles aisément mémorisables, et bien d''autres choses encore...\r\n', 'FR', '978-2744024115', 'TECH'),
(7, 4, 'PHP et MySQL - Coffret de 2 livres : Développez vos applications Web', 1001, 2011, 47.22, '\r\nPHP et MySQL - Développez vos applications Web Ce coffret contient deux livres de la collection Ressources Informatiques. Des éléments sont en téléchargement sur www.editions-eni.fr. MySQL 5 - Administration et optimisation Ce livre sur MySQL 5 s''adresse aux développeurs et administrateurs MySQL désireux de consolider leurs connaissances sur le SGBD Open Source le plus répandu du marché. Le livre débute par une présentation des bases qui vous seront nécessaires pour exploiter au mieux toutes les capacités de MySQL : méthodes d''installation mono et multi-instances, présentation de l''architecture du serveur et des principaux moteurs de stockage, bonnes pratiques de configuration. Après ces fondamentaux vous donnant une bonne compréhension des spécificités du SGBD, vous apprendrez comment gérer votre serveur au quotidien en ayant à l''esprit les principes essentiels de sécurité, en mettant en place des stratégies efficaces pour les sauvegardes et les restaurations et en maintenant vos tables à jour et opérationnelles. La dernière partie est consacrée aux techniques avancées qui vous donneront les clés pour résoudre les problèmes les plus complexes : optimisation du serveur, des index et des requêtes, amélioration des performances avec le partitionnement ou encore mise en place d''une solution de réplication adaptée à votre application. PHP 5.3 - Développez un site web dynamique et interactif Ce livre sur PHP 5.3 s''adresse aux concepteurs et développeurs qui souhaitent utiliser PHP pour développer un site Web dynamique et interactif. Après une présentation des principes de base du langage, l''auteur se focalise sur les besoins spécifiques du développement de sites dynamiques et interactifs en s''attachant à apporter des réponses précises et complètes aux problématiques habituelles (gestion des formulaires, accès aux bases de données, gestion des sessions, envoi de courriers électroniques...). Pour toutes les fonctionnalités détaillées, de nombreux exemples de code sont présentés et commentés. Ce livre didactique, à la fois complet et synthétique, vous permet d''aller droit au but ; c''est l''ouvrage idéal pour se lancer sur PHP.', 'FR', '978-2746060579', 'TECH'),
(8, 4, 'PHP 5.4 - Développez un site web dynamique et interactif', 554, 2012, 28.80, 'Ce livre sur PHP 5.4 s''adresse aux concepteurs et développeurs qui souhaitent utiliser PHP pour développer un site Web dynamique et interactif. Après une présentation des principes de base du langage, l''auteur se focalise sur les besoins spécifiques du développement de sites dynamiques et interactifs en s''attachant à apporter des réponses précises et complètes aux problématiques habituelles (gestion des formulaires, accès aux bases de données, gestion des sessions, envoi de courriers électroniques...). Pour toutes les fonctionnalités détaillées, de nombreux exemples de code sont présentés et commentés. Ce livre didactique, à la fois complet et synthétique, vous permet d''aller droit au but ; c''est l''ouvrage idéal pour se lancer sur PHP. Les exemples cités dans le livre sont en téléchargement sur le site www.editions-eni.fr. Les chapitres du livre : Introduction - Vue d''ensemble de PHP - Variables, constantes, types et tableaux - Opérateurs - Structures de contrôle - Fonctions et classes - Gérer les formulaires - Accéder aux bases de données - Gérer les sessions - Envoyer un courrier électronique - Gérer les fichiers - Gérer les erreurs dans un script PHP -Annexe', 'FR', '978-2746073043', 'TECH'),
(9, 2, 'PHP 5 avancé', 870, 2012, 42.75, '\r\nPHP 5, plate-forme de référence pour les applications web\r\n\r\nPHP 5 est plus que jamais la plate-forme incontournable pour le développement d''applications web professionnelles : programmation objet, services web, couche d''abstraction de base de données native PDO, simplification des développements XML avec SimpleXML, refonte du moteur sous-jacent pour d''importants gains de performances...\r\nUne bible magistrale avec de nombreux cas pratiques et retours d''expérience\r\n\r\nS''appuyant sur de nombreux retours d''expérience et cas pratiques, ce livre aidera le développeur à évaluer avec aisance dans le riche univers de PHP 5 et lui donnera toutes les clés pour en maîtriser les subtilités : bonnes pratiques de conception de sites et d''applications web, frameworks, cookies et sessions, programmation objet, utilisation de XML et SimpleXML, services web, intégration aux bases de données avec un focus sur MySQL 5 , PHP Data Object, gestion des archives PHP (PHAR), stratégies d''optimisation et de sécurité, gestion des images et des caches, migration entre versions de PHP...\r\nÀ qui s''adresse cet ouvrage ?\r\n\r\n    Aux développeurs souhaitant comprendre PHP 5 et son modèle objet\r\n    Aux développeurs et administrateurs de sites et d''applications web\r\n    Aux étudiants en informatique souhaitant appréhender les techniques du Web', 'FR', '978-2-212-13435-3', 'TECH'),
(10, 5, 'PHP Solutions: Dynamic Web Design Made Easy', 528, 2010, 44.99, 'This is the second edition of David Power''s highly-respected PHP Solutions: Dynamic Web Design Made Easy. This new edition has been updated by David to incorporate changes to PHP since the first edition and to offer the latest techniques--a classic guide modernized for 21st century PHP techniques, innovations, and best practices.\r\n\r\nYou want to make your websites more dynamic by adding a feedback form, creating a private area where members can upload images that are automatically resized, or perhaps storing all your content in a database. The problem is, you''re not a programmer and the thought of writing code sends a chill up your spine. Or maybe you''ve dabbled a bit in PHP and MySQL, but you can''t get past baby steps. If this describes you, then you''ve just found the right book. PHP and the MySQL database are deservedly the most popular combination for creating dynamic websites. They''re free, easy to use, and provided by many web hosting companies in their standard packages.\r\n\r\nUnfortunately, most PHP books either expect you to be an expert already or force you to go through endless exercises of little practical value. In contrast, this book gives you real value right away through a series of practical examples that you can incorporate directly into your sites, optimizing performance and adding functionality such as file uploading, email feedback forms, image galleries, content management systems, and much more. Each solution is created with not only functionality in mind, but also visual design.\r\n\r\nBut this book doesn''t just provide a collection of ready-made scripts: each PHP Solution builds on what''s gone before, teaching you the basics of PHP and database design quickly and painlessly. By the end of the book, you''ll have the confidence to start writing your own scripts or--if you prefer to leave that task to others--to adapt existing scripts to your own requirements. Right from the start, you''re shown how easy it is to protect your sites by adopting secure coding practices.', 'EN', '978-1430232490', 'TECH'),
(11, 6, 'Beginning PHP 5.3', 842, 2011, 41.70, 'This book is intended for anyone starting out with PHP programming. If you''ve previously worked in another programming language such as Java, C#, or Perl, you''ll probably pick up the concepts in the earlier chapters quickly; however, the book assumes no prior experience of programming or of building Web applications.\r\n\r\nThat said, because PHP is primarily a Web technology, it will help if you have at least some knowledge of other Web technologies, particularly HTML and CSS.\r\n\r\nMany Web applications make use of a database to store data, and this book contains three chapters on working with MySQL databases. Once again, if you''re already familiar with databases in general - and MySQL in particular - you''ll be able to fly through these chapters. However, even if you''ve never touched a database before in your life, you should still be able to pick up a working ', 'EN', '', 'TECH'),
(12, 7, 'JavaScript: The Definitive Guide: Activate Your Web Pages', 1100, 2011, 49.99, 'Since 1996, JavaScript: The Definitive Guide has been the bible for JavaScript programmers-a programmer''s guide and comprehensive reference to the core language and to the client-side JavaScript APIs defined by web browsers.\r\n\r\nThe 6th edition covers HTML5 and ECMAScript 5. Many chapters have been completely rewritten to bring them in line with today''s best web development practices. New chapters in this edition document jQuery and server side JavaScript. It''s recommended for experienced programmers who want to learn the programming language of the Web, and for current JavaScript programmers who want to master it.', 'EN', '978-0596805524', 'TECH'),
(13, 6, 'Professional JavaScript for Web Developers', 960, 2011, 44.99, 'JavaScript is loosely based on Java, which is an object-oriented programming language that became popular for use on the Web by way of embedded applets. It has a similar syntax and programming methodology to Java, however, it should not be considered the ''light'' version of the language. JavaScript is its own language that found its home in web browsers around the world and enabled enhanced user interaction on websites as well as web applications. In this book JavaScript is covered from its beginning in the earliest Netscape browsers to the present-day versions that can support the DOM and Ajax. You will learn how to extend the language to suit specific needs and how to create client-server communications without intermediaries such as Java or hidden frames. You will also learn how to apply JavaScript solutions to business problems faced by web developers everywhere.\r\n\r\nThis book provides a developer-level introduction along with more advanced and useful features of JavaScript. The book begins by exploring how JavaScript originated and evolved into what it is today. There is a discussion of the components that make up a JavaScript implementation that follows that has a specific focus on standards such as ECMAScript and the Document Object Model (DOM). The differences in JavaScript implementations used in different popular web browsers are also discussed. After building a strong base, the book goes on to cover basic concepts of JavaScript including its version of object-oriented programming, inheritance, and its use in HTML.  The book then explores new APIs, such as HTML5, the Selectors API, and the File API. The last part of the book is focused on advanced topics including performance/memory optimization, best practices, and a look at Where JavaScript is going in the future.', 'EN', '978-1118026694', 'TECH'),
(14, 8, 'JavaScript', 415, 2011, 20.45, 'Dans cet ouvrage pratique, entrez dans l''univers de JavaScript et faites le tour complet du sujet. Vous découvrirez les bases du langage puis apprendrez à manipuler des dates, gérer des tableaux, écrire des cookies, gérer l''interactivité grâce à des exemples et des cas pratiques. Enfin, vous pourrez approfondir le sujet grâce à des exercices.\r\nUn ouvrage très utile pour travailler avec JavaScript !\r\n\r\nPassionné par le développement web, Olivier Hondermarck crée en 1999 son site de scripts et de tutoriaux sur le JavaScript ToutJavaScript.com, devenu rapidement une des références du langage en France. Une formation d''ingénieur et de nombreuses expériences de développements d''applications Internet dans de grandes entreprises lui donnent une vision concrète des besoins et des méthodes de travail professionnels. Début 2004, il crée sa société et lance Beauté-test.com avec sa compagne.', 'FR', '978-2300039058', 'JS'),
(15, 2, 'Mémento HTML5', 14, 2012, 4.75, '', 'FR', '978-2212134209', 'TECH'),
(16, 2, 'HTML5 : Une référence pour le développeur web', 624, 2011, 39.00, 'Grâce à HTML 5, on peut maintenant développer des sites puissants et graphiquement riches, ainsi que des applications web, sans avoir forcément besoin d''un langage comme Flash. Déjà utilisable en grande partie dans les navigateurs web actuels, le standard HTML 5 est pourtant peu abordable, de par la quantité des spécifications et leur technicité. Didactique et pratique, cet ouvrage en donne les explications essentielles, ainsi que les bonnes pratiques, les astuces utiles au développeur pour profiter au maximum des nouvelles fonctionnalités HTML 5, en insistant sur la performance et l''accessibilité.', 'FR', '978-2212129823', 'TECH'),
(17, 2, 'CSS avancées : Vers HTML5 et CSS3', 685, 2012, 36.57, 'Incontournable du design web moderne, les feuilles de styles CSS sont en pleine révolution avec l''adoption des nouveaux standards HTML5 et CSS3. Familier de CSS 2, allez plus loin en maîtrisant les techniques avancées déjà éprouvées dans CSS2.1 et découvrez les multiples possibilités de CSS3 ! Chaque jour mieux prises en charge par les navigateurs, les CSS sont sans conteste un gage de qualité dans la conception d''un site web élégant, fonctionnel et accessible, aussi bien sous Mozilla Firefox, Google Chrome, Opera ou Safari que sous Internet Explorer ou les navigateurs mobiles. Vous croyiez tout savoir sur les CSS ? Grâce à la deuxième édition de ce livre de référence, enrichie et mise à jour, vous irez encore plus loin ! Vous apprendrez à faire usage tout autant des technologies avant-gardistes de CSS 3 et HTML 5 que de pratiques avancées, concrètes et mal connues déjà utilisables en production, et ce, pour l''ensemble des médias reconnus par les styles CSS (écrans de bureau ou mobiles, messageries, mais aussi impression, médias de restitution vocale, projection et télévision). Maîtrisez tous les rouages du positionnement en CSS2.1, exploitez les microformats, optimisez les performances d''un site, gérez efficacement vos projets ou contournez les bogues des navigateurs (hacks, commentaires conditionnels, HasLayout...). Enfin, profitez dès aujourd''hui des nouveautés de CSS3: typographie, gestion des césures, colonnes, arrière-plans, dégradés, ombres portées, redimensionnement, rotations, transitions et autres effets animés, sans oublier les Media Queries, qui permettent d''adapter le site à son support de consultation. Conseils méthodologiques, bonnes pratiques, outils, tests, exemples avec résultats en ligne, quizzes et exercices corrigés, tableaux récapitulatifs : rien ne manque à ce manuel du parfait designer web ! ', 'FR', '978-2212134056', 'TECH'),
(18, 4, 'HTML5 et CSS3 - Maîtrisez les standards des applications Web', 430, 3011, 30.32, '\r\nCe livre sur le HTML5 et CSS3 s''adresse à toute personne appelée à développer, mettre en place, faire vivre un site web. En effet, pour débuter mais surtout pour progresser dans la conception de sites, il faut inévitablement passer par une bonne compréhension et une bonne maîtrise du code source des applications Web. Le livre est conçu comme un réel outil de formation, pédagogique de la première à la dernière page, abondamment illustré d''exemples et de captures d''écran et constamment à l''affût des éléments réellement pratiques pour le webmestre. Sont ainsi passés en revue le HTML (dans sa dernière version et ses nombreuses nouveautés), les feuilles de style avec l''avancée spectaculaire des CSS3 en termes de présentation des pages web et quelques éléments de JavaScript Cet ouvrage n''est surtout pas une encyclopédie exhaustive de ces différentes techniques mais un parcours structuré de celles-ci. Il fournit aux concepteurs débutants, voire plus confirmés, les règles rigoureuses mais essentielles de la conception professionnelle d''un site Web. En effet, l''auteur s''est attaché à encourager l''élaboration d''un code respectueux des prescriptions du W3C et particulièrement de la séparation du contenu (HTML) et de la présentation (feuilles de style CSS) comme le préconise plus que jamais le HTML5. Ces nombreuses nouveautés ne sont prises en compte que par les dernières versions des navigateurs (Internet Explorer 9, Firefox, Google Chrome ou Safari) mais l''auteur a été particulièrement attentif à fournir un code compatible avec des navigateurs moins évolués afin de pouvoir bénéficier dès à présent de ce pas important dans la conception des applications Web. Des éléments complémentaires sont en téléchargement sur le site www.editions-eni.fr.', 'FR', '978-2746062429', 'TECH'),
(19, 4, 'Les API JavaScript du HTML5', 509, 2012, 37.58, 'Ce livre s''adresse aux développeurs de pages et applications Web désireux de tirer pleinement parti des API JavaScript du HTML5. L''auteur propose une exploration de ces nombreuses API JavaScript, certaines pleinement opérationnelles, d''autres encore en phase de développement. Le HTML5 étant une évolution de portée considérable qui modifie totalement la conception des pages ou applications Web, l''auteur a veillé à adopter une approche pragmatique et explicative, illustrée de nombreux exemples et captures d''écran. L''objectif du livre est double ; tout d''abord, permettre au lecteur d''intégrer dans ses applications, certaines de ces API comme la géolocalisation, le dessin en 2D, le stockage de données en local ou pourquoi pas une base de données, ensuite, de faire découvrir l''énorme impulsion que vont créer ces API JavaScript qui seront dans leur globalité une véritable plateforme de développement d''applications Html5. Les différents chapitres du livre détaillent en particulier : l''API Selectors qui remédie aux lacunes du JavaScript traditionnel dans la sélection des éléments du DOM - la plus médiatique du moment, l''API de géolocalisation qui permet de connaître les coordonnées géographiques de l''utilisateur - l''API Storage qui permet de conserver dans le navigateur des données qui pourront être utilisées ultérieurement sans passer par un serveur - l''API Offline élaborée pour permettre aux tablettes et smartphone de continuer à utiliser une application en mode déconnecté suite à une perte de réseau - l''API History qui permet de créer de nouvelles entrées dans l''historique - l''API Drag & Drop qui permet d''utiliser le glisser/déposer en mode natif... Suivent ensuite une série d''API plus limitées comme la sélection de fichiers, la possibilité de transmettre des informations entre différentes fenêtres ou balises iframe localisées sur le même domaine ou des domaines différents, l''exécution de scripts en arrière-plan et l''API WebSocket qui permet d''ouvrir une connexion bi-directionnelle permanente entre le client et le serveur. Enfin, l''API Canvas qui permet le dessin 2D directement dans la page sans passer par des images. Des éléments complémentaires sont en téléchargement sur www.editions-eni.fr. Les chapitres du livre : Avant-propos - Présentation - L''API Selectors - La géolocalisation - Le stockage de données en local - L''API Web SQL Database - L''API Indexed Database - L''édition de contenu (contentEditable) - Le mode déconnecté (offline) - Manipuler l''historique du navigateur - Le glisser/déposer (drag/drop) - La sélection de fichiers - L''API Web Messaging - Le JavaScript en toile de fond - L''API WebSocket - L''API de dessin', 'FR', '978-2746074101', 'TECH'),
(20, 9, 'Watchmen', 464, 1987, 35.50, 'Quand le comédien, justicier au service du gouvernement, se fait défenestrer, son ancien allié, Rorschach, mène l''enquête. Il reprend rapidement contact avec d''autres héros à la retraite dont le Dr Manhattan, surhomme qui a modifié le cours de l''histoire. Alors qu''une guerre nucléaire couve entre les USA et l''URSS, tous s''interrogent : qui nous gardera de nos Gardiens ?', 'FR', '978-2365770095', 'BD'),
(39, 9, 'V pour Vendetta', 352, 1990, 26.00, '1997, une Angleterre qui aurait pu exister... Dirigée par un gouvernement fasciste, le pays a sombré dans la paranoïa et la surveillance à outrance. Les "ennemis politiques" sont invariablement envoyés dans des camps et la terreur et l''apathie règnent en maître. Mais un homme a décidé de se dresser contre l''oppression. Dissimulé derrière un masque au sourire énigmatique, il répond au nom de V : V pour Vérité, V pour Valeurs... V pour Vendetta !', 'FR', '978-2365770460', 'BD'),
(22, 10, 'Chroniques martiennes', 336, 1954, 5.92, '"J''ai toujours voulu voir un Martien", dit Michael. "Où ils sont, p''pa ? Tu avais promis." "Les voilà", dit papa. Il hissa Michael sur son épaule et pointa un doigt vers le bas. Les Martiens étaient là. Timothy se mit à frissonner. Les Martiens étaient là - dans le canal - réfléchis dans l''eau. Timothy, Michael, Robert, papa et maman. Les Martiens leur retournèrent leurs regards durant un long, long moment de silence dans les rides de l''eau...', 'FR', '978-2070417742', 'ROMAN'),
(40, 10, 'L''homme illustré', 352, 1954, 5.32, 'Il retira sa chemise et la roula en boule. De l''anneau bleu tatoué autour de son cou jusqu''à la taille, il était couvert d''Illustrations. "Et c''est comme ça jusqu''en bas", précisa-t-il, devinant ma pensée. "Je suis entièrement illustré. Regardez !" Il ouvrit la main. Sur sa paume, une rose. Elle venait d''être coupée ; des gouttelettes cristallines émaillaient ses pétales délicats. J''étendis ma main pour la toucher, mais ce n''était qu''une image. "Mais elles sont magnifiques !" m''écriai-je. - "Oh oui", dit l''Homme Illustré. "Je suis si fier de mes Illustrations que j''aimerais les effacer en les brûlant. J''ai essayé le papier de verre, l''acide, le couteau... Car, voyez-vous, ces Illustrations prédisent l''avenir."', 'FR', '978-2070417797', 'ROMAN'),
(24, 10, 'Fahrenheit 451', 224, 1955, 5.32, '451 degrés Fahrenheit représentent la température à laquelle un livre s''enflamme et se consume. Dans cette société future où la lecture, source de questionnement et de réflexion, est considérée comme un acte antisocial, un corps spécial de pompiers est chargé de brûler tous les livres, dont la détention est interdite pour le bien collectif. Montag, le pompier pyromane, se met pourtant à rêver d''un monde différent, qui ne bannirait pas la littérature et l''imaginaire au profit d''un bonheur immédiatement consommable. Il devient dès lors un dangereux criminel, impitoyablement poursuivi par une société qui désavoue son passé.\r\n', 'FR', '978-2070415731', 'ROMAN'),
(41, 11, 'The Walking Dead - T16 - Un vaste monde', 144, 2012, 13.25, 'Après l''invasion d''Alexandrie, le répit est de courte durée. Les survivants sont confrontés à un sentiment destructeur : la peur de l''autre... Kirkman et Adlard poussent toujours plus loin leurs personnages dans leur ultime retranchement.\r\n\r\nAprès l''attaque massive de Marcheurs qui a décimé la communauté d''Alexandria, la vie reprend tant bien que mal son cours pour ses habitants. Mais le danger n''est jamais très loin... Morts et vivants rôdent toujours aux alentours. Lorsqu''Abraham et Michonne découvrent l''existence d''un homme visiblement sans peur et capable de parfaitement se battre, les souvenirs du gouverneur ressurgissent. Et la confiance, déjà bien ébranlée, des rescapés en la nature humaine, est une nouvelle fois mise à l''épreuve...', 'FR', '978-2756028736', 'BD'),
(26, 11, 'The Walking Dead - T1 - Passé décomposé', 142, 2007, 13.25, 'Rick est policier et sort du coma pour découvrir avec horreur un monde où les morts ne meurent plus. Mais ils errent à la recherche des derniers humains pour s''en repaître. Il n''a alors plus qu''une idée en tête : retrouver sa femme et son fils, en espérant qu''ils soient rescapés de ce monde devenu fou. Un monde où plus rien ne sera jamais comme avant, et où une seule règle prévaut : survivre à tout prix.', 'FR', '978-2756009124', 'BD'),
(27, 12, 'The Walking Dead - La route de Woodbury', 320, 2012, 7.70, 'Dans L''Ascension du Gouverneur, le premier roman de la série Walking Dead, le lecteur a découvert comment Philip Blake a vécu l''invasion zombie et comment il est arrivé dans la ville retranchée de Woodbury. Le deuxième roman, La Route de Woodbury, raconte comment il en devient le leader incontesté. Ce ne sera pas simple. Certains qui sont là depuis longtemps se méfient de Philippe. Ils n''aiment pas trop non plus les bruits étranges qui proviennent de son appartement. Mais Philip Blake est déterminé à faire de la ville un havre de paix à l''abri du cauchemar post-apocalyptique qui l''entoure. Il est prêt à tout pour y parvenir. Y compris renverser et tuer ceux qui dirigent aujourd''hui Woodbury.', 'FR', '978-2253134831', 'ROMAN'),
(28, 10, 'La ferme des animaux', 150, 1947, 5.89, 'Un certain 21 juin eut lieu en Angleterre la révolte des animaux. Les cochons dirigent le nouveau régime. Snowball et Napoléon, cochons en chef, affichent un règlement : "Tout ce qui est sur deux jambes est un ennemi. Tout ce qui est sur quatre jambes ou possède des ailes est un ami. Aucun animal ne portera de vêtements. Aucun animal ne dormira dans un lit. Aucun animal ne boira d''alcool. Aucun animal ne tuera un autre animal. Tous les animaux sont égaux." Le temps passe. La pluie efface les commandements. L''âne, un cynique, arrive encore à déchiffrer : "Tous les animaux sont égaux, mais (il semble que cela ait été rajouté) il y en a qui le sont plus que d''autres." ', 'FR', '978-2070375165', 'ROMAN'),
(29, 10, 'H2G2 Tome 1 - Le guide du routard galactique', 288, 1982, 6.46, 'Comment garder tout son flegme quand on apprend dans la même journée : que sa maison va être abattue dans la minute pour laisser place à une déviation d''autoroute ; que la Terre va être détruite d''ici deux minutes, se trouvant, coïncidence malheureuse, sur le tracé d''une future voie express intergalactique ; que son meilleur ami, certes délicieusement décalé, est en fait un astrostoppeur natif de Bételgeuse, et s''apprête à vous entraîner aux confins de la galaxie ? Pas de panique ! Car Arthur Dent, un Anglais extraordinairement moyen, pourra compter sur le fabuleux Guide du voyageur galactique pour l''accompagner dans ses extraordinaires dérapages spatiaux moyennement contrôlés. ', 'FR', '978-2070437436', 'ROMAN'),
(30, 10, 'H2G2 Tome 2 - Le dernier restaurant avant la Fin du Monde', 288, 1987, 7.51, 'La cuisine anglaise est exécrable. Moins abominable, cependant, que la poésie des Vogons, un peuple fier, ombrageux, et éminemment irritable. D''ailleurs, les Vogons ont fait sauter la planète Terre, soi-disant par erreur. Pas de panique ! Grâce au fabuleux Guide galactique, le pauvre Arthur Accroc, ex-citoyen britannique désormais apatride et passablement désemparé devant tant d''inconvenance, pourra affronter sans crainte les improbables méandres d''un univers en folie. Rien ne l''empêchera, pas même un ascenseur dépressif, d''arriver à temps pour déguster le Plat du jour au Dernier Restaurant avant la Fin du Monde. ', 'FR', '978-2070438617', 'ROMAN'),
(31, 10, 'H2G2 Tome 3 - La vie, l''univers et le reste', 304, 1983, 6.46, 'Pourquoi le tristement anonyme Arthur Dent se promène-t-il outrageusement affublé d''un sac en peau de lapin, un os dans le nez, au beau milieu d''une finale de cricket ? Et que fait Marvin, l''androïde dépressif, à asséner ses considérations suicidaires aux improbables habitants des marécages de Squornshellous Zeta ? Pas de panique ! Car l''inénarrable, l''irremplaçable Guide du voyageur galactique saura une fois encore tirer d''affaire nos malheureux astro-stoppeurs égarés ; et peut-être, privilège suprême, leur révélera-t-il enfin le Grand Mystère de La Vie, de l''Univers et du Reste ! ', 'FR', '978-2070438624', 'ROMAN'),
(32, 10, 'H2G2 Tome 4 - Salut, et encore merci pour le poisson', 272, 1994, 7.51, 'Plus bas que Terre ! Ayant - plus ou moins - survécu à son édifiante promenade cosmico-temporelle, le pauvre Arthur Dent savoure l''indicible plaisir de fouler à nouveau le sol de sa planète natale.Une planète jadis détruite par les terribles Vogons, sous le prétexte fallacieux de laisser passer une autoroute intergalactique...Pas de panique ! Car l''universellement exhaustif Guide galactique saura sans doute répondre à cet étrange paradoxe. Et peut-être élucidera-t-il un mystère plus angoissant encore : pourquoi les dauphins ont-ils disparu, laissant pour ultime message un laconique Salut, et encore merci pour le poisson ? ', 'FR', '978-2070438631', 'ROMAN'),
(33, 10, 'H2G2 Tome 5 - Globalement inoffensive', 336, 1994, 6.46, 'Pauvre Arthur Dent ! Apprendre qu''on est devenu père sans avoir... enfin rien fait pour ça, voilà de quoi ébranler le flegme le plus involontaire de toute la Galaxie ! Suffisamment, en tout cas, pour aller se saouler sur une lointaine planète, dans un modeste bar tenu par une légende - toujours ! - vivante du rock''n roll...Pas de panique ! Car l''imprévisible Guide du voyageur galactique, décidément irremplaçable, dévoilera enfin tous les mystères d''une odyssée digne des plus belles pages de Marx - Groucho Marx - ; entre autres, les raisons de la destruction approximative de la Terre, cette petite planète honteusement qualifiée de globalement inoffensive. ', 'FR', '978-2070419326', 'ROMAN'),
(34, 11, 'Le meilleur des mondes', 284, 1931, 4.37, 'Les expérimentations sur l''embryon, l''usage généralisé de la drogue. Ces questions d''actualité ont été résolues dans l''Etat mondiale totalitaire, imaginé par Aldous Huxley en 1932. Défi, réquisitoire, anti-utopie, ce chef-d’œuvre de la littérature d''anticipation a fait de son auteur un des témoins les plus lucides de notre temps.', 'FR', '978-2266128568', 'ROMAN'),
(35, 10, 'Minority Report', 436, 1956, 7.54, 'Washington, 2054. John Anderton est membre de Précrime, une unité gouvernementale utilisant les dons de prescience de trois mutants, les précogs, pour arrêter les criminels avant leur passage à l''acte. Avant même qu''ils aient imaginé de passer à l''acte. Anderton a une confiance aveugle dans les prédictions des précogs. Mais quand, chasseur devenu gibier, il se retrouvera lui-même accusé du meurtre d''un homme qu''il n''a jamais rencontré, il lui faudra découvrir les véritables rouages de Précrime pour prouver son innocence.\r\n', 'FR', '978-2070426065', 'ROMAN'),
(36, 11, 'Les androïdes rêvent-ils de moutons électriques ?', 251, 1968, 6.46, 'Sur terre, quelques temps après l''holocauste nucléaire : les espèces animales ont quasiment disparues et certains humains, dit "spéciaux", se sont mis à muter, voire à régresser. Rick Deckard est chasseur de prime. Il est chargé de démasquer et d''éliminer des Andys, des androïdes dont le séjour sur terre est illégal. Mais leur perfection est telle qu''il est quasiment impossible de les différencier des humains. Ils pourraient d''ailleurs être bien plus nombreux que prévu. Au point que Deckard finira par se demander s''il n''est pas lui-même une création artificielle dont les souvenirs auraient été implantés.\r\n\r\nMais alors qu''est-ce qui différencie les humains des androïdes ? Peut-être cette capacité à utiliser la "boite à empathie", qui les plonge dans le corps perpétuellement meurtri de Wilbur Mercer. Mercer qui pourrait bien s''avérer être un usurpateur... ', 'FR', '978-2290314944', 'ROMAN'),
(37, 10, 'A scanner darkly', 400, 1977, 6.46, 'Dans une Amérique imaginaire livrée à l''effacement des singularités et à la paranoïa technologique, les derniers survivants de la contre-culture des années 60 achèvent de brûler leur cerveau au moyen de la plus redoutable des drogues, la Substance Mort. Dans cette Amérique plus vraie que nature, Fred, qui travaille incognito pour la brigade des stups, le corps dissimulé sous un "complet brouillé", est chargé par ses supérieurs d''espionner Bob Actor, un toxicomane qui n''est autre que lui-même. Un voyage sans retour au bout de la schizophrénie, une plongée glaçante dans l''enfer des paradis artificiels. ', 'FR', '978-2070415779', 'ROMAN'),
(42, 10, '1984', 438, 1949, 7.98, 'De tous les carrefours importants, le visage à la moustache noire vous fixait du regard. BIG BROTHER VOUS REGARDE, répétait la légende, tandis que le regard des yeux noirs pénétrait les yeux de Winston... Au loin, un hélicoptère glissa entre les toits, plana un moment, telle une mouche bleue, puis repartit comme une flèche, dans un vol courbe. C''était une patrouille qui venait mettre le nez aux fenêtres des gens. Mais les patrouilles n''avaient pas d''importance. Seule comptait la Police de la Pensée.', 'FR', '978-2070368228', 'ROMAN');

-- --------------------------------------------------------

--
-- Structure de la table `auteurs`
--

CREATE TABLE `auteurs` (
  `auID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auNom` char(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `auPrenom` char(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `auPays` char(2) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `auBio` text COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`auID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=32 ;

--
-- Contenu de la table `auteurs`
--

INSERT INTO `auteurs` (`auID`, `auNom`, `auPrenom`, `auPays`, `auBio`) VALUES
(1, 'Lépine', 'Jean-François', 'FR', ''),
(2, 'Pauli', 'Julien', 'FR', ''),
(3, 'de Geyer', 'Cyril Pierre', 'FR', ''),
(4, 'Plessis', 'Guillaume', 'FR', ''),
(5, 'Séguy', 'Damien', 'FR', ''),
(6, 'Gamache', 'Philippe', 'FR', ''),
(7, 'Welling', 'Luke', 'US', ''),
(8, 'Yank', 'Kevin', 'US', ''),
(9, 'Combaudon', 'Stéphane', 'FR', ''),
(10, 'Scetbon', 'Cyril', 'FR', ''),
(11, 'Heurtel', 'Olivier', 'FR', ''),
(12, 'Daspet', 'Eric', 'FR', ''),
(13, 'Powers', 'David', 'US', ''),
(14, 'Doyle', 'Matt', 'US', ''),
(15, 'Flanagan', 'David', 'US', ''),
(16, 'Zakas', 'Nicholas', 'US', ''),
(17, 'Hondermarck', 'Olivier', 'FR', ''),
(18, 'Rimelé', 'Rodolphe', 'FR', ''),
(19, 'Goetter', 'Raphaël', 'FR', ''),
(20, 'Van Lancker', 'Luc', 'FR', ''),
(21, 'Moore', 'Alan', 'GB', ''),
(22, 'Gibbons', 'Dave', 'GB', ''),
(23, 'Lloyd', 'David', 'GB', ''),
(24, 'Bradbury', 'Ray', 'US', ''),
(25, 'Kirkman', 'Robert', 'US', ''),
(26, 'Adlard', 'Charlie', 'GB', ''),
(27, 'Bonansinga', 'Jay', 'US', ''),
(28, 'Orwell', 'George', 'GB', ''),
(29, 'Adams', 'Douglas', 'GB', ''),
(30, 'Dick', 'Philip K.', 'US', ''),
(31, 'Huxley', 'Aldous', 'GB', '');

-- --------------------------------------------------------

--
-- Structure de la table `aut_livre`
--

CREATE TABLE `aut_livre` (
  `al_IDAuteur` int(11) unsigned NOT NULL DEFAULT '0',
  `al_IDLivre` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`al_IDLivre`,`al_IDAuteur`),
  FOREIGN KEY(`al_IDAuteur`) REFERENCES `auteurs`(`auID`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY(`al_IDLivre`) REFERENCES `livres`(`liID`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Contenu de la table `aut_livre`
--

INSERT INTO `aut_livre` (`al_IDAuteur`, `al_IDLivre`) VALUES
(1, 2),
(2, 3),
(3, 3),
(4, 3),
(5, 4),
(6, 4),
(7, 5),
(8, 6),
(9, 7),
(10, 7),
(11, 7),
(11, 8),
(3, 9),
(12, 9),
(13, 10),
(14, 11),
(15, 12),
(16, 13),
(17, 14),
(18, 15),
(18, 16),
(19, 17),
(20, 18),
(20, 19),
(21, 20),
(22, 20),
(24, 22),
(24, 24),
(25, 26),
(26, 26),
(25, 27),
(27, 27),
(28, 28),
(29, 29),
(29, 30),
(29, 31),
(29, 32),
(29, 33),
(31, 34),
(30, 35),
(30, 36),
(30, 37),
(21, 39),
(23, 39),
(24, 40),
(25, 41),
(26, 41),
(28, 42);

-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `cliID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cliEmail` char(50) COLLATE utf8_general_ci NOT NULL,
  `cliPassword` char(255) COLLATE utf8_general_ci NOT NULL,
  `cliNomPrenom` char(100) COLLATE utf8_general_ci NOT NULL,
  `cliAdresse` char(100) COLLATE utf8_general_ci NOT NULL,
  `cliCP` int(5) unsigned NOT NULL,
  `cliVille` char(50) COLLATE utf8_general_ci NOT NULL,
  `cliPays` char(50) COLLATE utf8_general_ci NOT NULL,
  `cliDateNaissance` int(8) unsigned NOT NULL,
  PRIMARY KEY (`cliID`),
  UNIQUE KEY cliEmail (`cliEmail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=14;

--
-- Contenu de la table `clients`
--

INSERT INTO `clients` (`cliID`, `cliEmail`, `cliPassword`, `cliNomPrenom`, `cliDateNaissance`, `cliAdresse`, `cliCP`, `cliVille`, `cliPays`) VALUES
(9, 'frederic.dadeau@univ-fcomte.fr', '$2y$10$hJm2k.p8vz65DKpcAeNUAe0wv7QHifUzeNmInBAdVGDhK6MdYXTB2', 'Frederic Dadeau', 19800521, 'FEMTO-ST/DISC - 16 route de Gray', 25030, 'Besancon', 'France'),
(11, 'eric.grux@univ-fcomte.fr', '$2y$10$hJm2k.p8vz65DKpcAeNUAe0wv7QHifUzeNmInBAdVGDhK6MdYXTB2', 'Eric Grux', 19770525, 'FEMTO-ST/DISC - 16 route de Gray', 25030, 'Besancon', 'France'),
(12, 'eric.merlet@univ-fcomte.fr', '$2y$10$hJm2k.p8vz65DKpcAeNUAe0wv7QHifUzeNmInBAdVGDhK6MdYXTB2', 'Eric Merlet', 19830525, 'FEMTO-ST/DISC - 16 route de Gray', 25030, 'Besancon', 'France'),
(13, 'vahana.dorcis@univ-fcomte.fr', '$2y$10$hJm2k.p8vz65DKpcAeNUAe0wv7QHifUzeNmInBAdVGDhK6MdYXTB2', 'Vahana Dorcis', 19850907, 'FEMTO-ST/DISC - 16 route de Gray', 25030, 'Besancon', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `coID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coIDClient` int(11) unsigned NOT NULL,
  `coDate` int(8) unsigned NOT NULL,
  `coHeure` int(4) unsigned NOT NULL,
  PRIMARY KEY (`coID`),
  FOREIGN KEY(`coIDClient`) REFERENCES `clients`(`cliID`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=15 ;

--
-- Contenu de la table `commandes`
--

INSERT INTO `commandes` (`coID`, `coIDClient`, `coDate`, `coHeure`) VALUES
(1, 9, 20180212, 830),
(2, 12, 20190115, 1750),
(3, 9, 20200320, 1520),
(4, 13, 20200930, 2105),
(5, 13, 20201020, 2019),
(6, 13, 20201030, 2205),
(7, 13, 20210125, 1505),
(8, 13, 20210130, 1250),
(9, 13, 20210130, 1750),
(10, 13, 20210130, 1955),
(11, 13, 20210206, 2050),
(12, 13, 20210208, 1450),
(13, 13, 20210210, 1320),
(14, 13, 20210215, 759);

-- --------------------------------------------------------

--
-- Structure de la table `compo_commande`
--

CREATE TABLE `compo_commande` (
  `ccIDLivre` int(11) unsigned NOT NULL,
  `ccIDCommande` int(11) unsigned NOT NULL,
  `ccQuantite` int(3) unsigned NOT NULL,
  PRIMARY KEY (`ccIDCommande`,`ccIDLivre`),
  FOREIGN KEY(`ccIDCommande`) REFERENCES `commandes`(`coID`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY(`ccIDLivre`) REFERENCES `livres`(`liID`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Contenu de la table `compo_commande`
--

INSERT INTO `compo_commande` (`ccIDCommande`, `ccIDLivre`, `ccQuantite`) VALUES
(1, 22, 1),
(1, 40, 2),
(1, 41, 3),
(1, 26, 1),
(3, 14, 1),
(3, 22, 1),
(3, 40, 2),
(3, 12, 1),
(4, 14, 2),
(4, 22, 1),
(5, 14, 1),
(5, 41, 2),
(2, 12, 2),
(2, 14, 1),
(2, 22, 1),
(2, 40, 3),
(2, 4, 1),
(6, 2, 1),
(7, 3, 2),
(7, 4, 1),
(8, 20, 2),
(9, 14, 2),
(9, 15, 2),
(10, 2, 3),
(11, 8, 2),
(12, 7, 3),
(12, 17, 2),
(12, 19, 5),
(13, 4, 1),
(14, 12, 3);

-- --------------------------------------------------------

--
-- Structure de la table `listes`
--

CREATE TABLE `listes` (
  `listIDLivre` int(11) unsigned NOT NULL,
  `listIDClient` int(11) unsigned NOT NULL,
  PRIMARY KEY (`listIDClient`,`listIDLivre`),
  FOREIGN KEY(`listIDClient`) REFERENCES `clients`(`cliID`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY(`listIDLivre`) REFERENCES `livres`(`liID`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Contenu de la table `listes`
--

INSERT INTO `listes` (`listIDClient`, `listIDLivre`) VALUES
(9, 2),
(9, 3),
(9, 20),
(12, 2),
(12, 3),
(12, 4),
(12, 5),
(13, 3),
(13, 4),
(13, 5);

COMMIT;
