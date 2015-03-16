<?php

include('config.php');

//Connexion a la BD
try {
	$dbconn = new PDO('mysql:host='.$db['server'].';dbname='.$db['name'].';charset=utf8', $db['user'], $db['pass']);
} 
catch (Exception $e) {
	die('Erreur : '.$e->getMessage());
}

if(!array_key_exists('action', $_GET)){
	$_GET['action'] = 'none';
}

switch ($_GET['action']) {
	case 'delete':
		include('php/deleteUsers.php');
		break;
	
	default:
		include('php/listUsers.php');
		break;
}

// Déconnexion de la BD
$dbconn = null;