<?php

	require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/autoload.php';
	// require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/Facebook.php';

	$fb = new Facebook\Facebook([
	  'app_id' => '{app-id}', // Replace {app-id} with your app id{app-id}'
	  'app_secret' => '{app-secret-id}',// Replace {app-id} with your app id{app-secret-id}'
	  'default_graph_version' => 'v2.2',
	  ]);

	$helper = $fb->getRedirectLoginHelper();

	$permissions = ['email','user_posts','publish_actions','user_photos']; 
	$loginUrl = $helper->getLoginUrl('http://localhost/Datakitty/fb-callback.php', $permissions);

	echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
	

?>