<?php

$nb_pages_deleted = 0;
//$nb_users_deleted = 0;

foreach ($_POST as $value) {
	//Suppression des pages ayant un compte a supprimer comme propriétaire.
	$query = "DELETE FROM ".$db['prefixe']."pages WHERE owner = '$value';";
	$nb_pages_deleted += $dbconn->exec($query);
}

include('phtml/deleteUsers.phtml');