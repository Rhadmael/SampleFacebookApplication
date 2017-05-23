<?php

	require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/autoload.php';
	// require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/Facebook.php';

	$fb = new Facebook\Facebook([
	  'app_id' => '1222199474552625', // Replace {app-id} with your app id
	  'app_secret' => 'e0f69f318b6e71af6e944e50303a1e18',
	  'default_graph_version' => 'v2.2',
	  ]);

	$helper = $fb->getRedirectLoginHelper();

	$permissions = ['email','user_posts','publish_actions','user_photos']; 
	$loginUrl = $helper->getLoginUrl('http://localhost/Datakitty/fb-callback.php', $permissions);

	echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
	

?>