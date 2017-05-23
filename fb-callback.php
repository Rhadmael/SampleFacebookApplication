<?php
if(!session_id()) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(~0);
require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '{app-id}', // Replace {app-id} with your app id
  'app_secret' => '{app-secret-id}',// Replace {app-secret-id} with your app id
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$_GET['state'];

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}


// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('1222199474552625'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
  }
}

$_SESSION['fb_access_token'] = (string) $accessToken;

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');
try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=picture.width(300),gender,name,email,id', $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();
$pic = $user->getPicture();
$userID = $user['id'];
copy($pic['url'], 'user-images/'.$userID.'.jpg');
$idImage = imagecreatefromjpeg('id.jpg');
$profilePic  = imagecreatefromjpeg('user-images/'.$userID.'.jpg');
$authority=array("Doctor","Patient","Geek Master","Director","Batman");
$secLevel=array("1","2","3","4","5");
// Allocate A Color For The Text
$black = imagecolorallocate($idImage, 0, 0, 0);
// Set Path to Font File
$font_path = 'Arkham_reg.ttf';
// write data on image
imagettftext($idImage, 22, 0, 367,246, $black, $font_path, $user['name']);
imagettftext($idImage, 22, 0, 348,313, $black, $font_path, $user['gender']);
//imagettftext($idImage, 22, 0, 526,316, $black, $font_path, $user['user_birthday ']);
imagettftext($idImage, 22, 0, 455,440, $black, $font_path, $user['id']);
imagettftext($idImage, 18, 0, 508,315, $black, $font_path, $authority[rand(1,4)]);
imagettftext($idImage, 18, 0, 468,385, $black, $font_path, $secLevel[rand(1,4)]);
// Output and free memory
ob_start (); 
// Copy and merge
imagecopymerge($idImage, $profilePic, 52, 119, 0, 0, 250, 250, 100);
imagejpeg ($idImage);
$image_data = ob_get_contents (); 
ob_end_clean (); 
$image_data_base64 = base64_encode ($image_data);
// Send Image to Browser
echo "<img src='data:image/jpeg;base64,$image_data_base64'>";
file_put_contents('user-images/'.$userID.'.jpg',base64_decode($image_data_base64));
$url__='http://localhost/DataKitty/user-images/'.$userID.'.jpg';

// upload image to Facebook

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->post('/me/photos',array( 'message' => 'DataKitty test.','source' => $fb->fileToUpload($url__)), $accessToken->getValue());
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
$graphNode = $response->getGraphNode();
echo 'Photo ID: ' . $graphNode['id'];

?>