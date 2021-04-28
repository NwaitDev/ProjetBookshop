<?php
require_once('./bibli_bookshop.php');

// démarrage de la session, pas besoin de démarrer la bufferisation des sorties
session_start();

em_session_exit(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php');
?>
