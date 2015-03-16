<?php

//Supprime tous les commentaires.
$query = "DELETE FROM ".$db['prefixe']."pages WHERE tag LIKE 'Comment%'";
$nb_comment_deleted = $dbconn->exec($query);

//Vide les refferrers
$query = "DELETE FROM ".$db['prefixe']."referrers";
$nb_referrers_deleted = $dbconn->exec($query);

//Mise a zéro des ACLs
$nb_acls_deleted = false;
if($reset_acls) {
	$query = "DELETE FROM ".$db['prefixe']."acls";
	$nb_acls_deleted = $dbconn->exec($query);
}

//Suppression de tous les links faisant référence a une page inéxistante
$tags = array();
$query = "SELECT DISTINCT tag FROM ".$db['prefixe']."pages";
foreach ($dbconn->query($query) as $page) {
	$tags[$page['tag']] = $page['tag'];
}

$nb_links_deleted = 0;
$query = "SELECT * FROM ".$db['prefixe']."links";
foreach ($dbconn->query($query) as $link) {
	if(   !array_key_exists($link['from_tag'], $tags)
		 || !array_key_exists($link['to_tag'],   $tags)) {

		//Supprimer le links
		 	$query = "DELETE FROM ".$db['prefixe']."links WHERE " 
		 					."from_tag = '".$link['from_tag']."' AND "
		 					."to_tag = '".$link['to_tag']."';";
		 	$nb_links_deleted += $dbconn->exec($query);
	}
}

//Liste des utilisateurs 
$users = array();

$query = "SELECT name FROM ".$db['prefixe']."users";
foreach ($dbconn->query($query) as $row) {
	$user = $row['name'];
	$users[$user] = array(
		'owner' => array(), 	// Propriétaire de la page.
		'user' => array(),
	);

	$query = "SELECT tag from ".$db['prefixe']."pages WHERE user='$user'";
	foreach ($dbconn->query($query) as $page) {
		$users[$user]['user'][] = $page['tag'];
	}

	$query = "SELECT tag from ".$db['prefixe']."pages WHERE owner='$user'";
	foreach ($dbconn->query($query) as $page) {
		$users[$user]['owner'][] = $page['tag'];
	}
}

//Supprime tout les utilisateurs n'ayant ni créer ni modifié de page.
foreach ($users as $user => $pages) {
	if(empty($pages['owner']) && empty($pages['user'])) {
		$query = "DELETE FROM ".$db['prefixe']."users WHERE name = '".$user."'";
		$dbconn->exec($query);
		unset($users[$user]);
	}
}

include('phtml/listUsers.phtml');
