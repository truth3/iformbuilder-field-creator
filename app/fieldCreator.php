<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    die('GET requests are not allowed');
}
header('Access-Control-Allow-Methods: POST, PUT');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once "../auth/iFormTokenResolver.php";
require_once "../auth/keys.php";
use iForm\Auth\iFormTokenResolver;

global $client,$secret;
$server = '####';
$tokenUrl = 'https://' . $server . '.iformbuilder.com/exzact/api/oauth/token';

//::::::::::::::  FETCH ACCESS TOKEN   ::::::::::::::

// Couldn't wrap method call in PHP 5.3 so this has to become two separate variables
$tokenFetcher = new iFormTokenResolver($tokenUrl, $client, $secret);
$token = $tokenFetcher->getToken();


//:::::::::::::: Get total number of profiles in the DB beyond default reponse by parsing out the response header Total-Count ::::::::::::::
$profileListUrl = "https://" . $server . ".iformbuilder.com/exzact/api/v60/profiles?limit=1&access_token=" . $token;
$profileRequestHeaders = (get_headers($profileListUrl)[4]);
$finalProfileCount = = preg_split("/[\s,]+/", $profileRequestHeaders)[1];
echo("Number of Profiles:" . $finalProfileCount . "\r\n");

//:::::::::::::: Use the total count of profiles to begin looping through each one ::::::::::::::

for ($i=0; $i<$finalProfileCount; $i++){

  //:::::::::::::: Get a new access token for every profile we go through to keep it from expiring ::::::::::::::
  $tokenFetcher = new iFormTokenResolver($tokenUrl, $client, $secret);
  $token = $tokenFetcher->getToken();


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://" . $server . ".iformbuilder.com/exzact/api/v60/profiles?limit=1&offset=$i");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);

  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer $token"
    ));

    $response = curl_exec($ch);
    if(curl_errno($ch))
        echo 'Curl error: '.curl_error($ch);
    curl_close($ch);
    $returnProfile = json_decode($response,true);

    //:::::::::::::: For each profile we need to loop through all the forms ::::::::::::::

    foreach($returnProfile as $activeProfile) {
    $activeProfile = $returnProfile[0]['id'];
    print_r("Active Profile: " . $activeProfile . "\r\n");
    print_r("Access Token: " . $token . "\r\n");
  }

    //:::::::::::::: Get total number of pages in the profile beyond default reponse by parsing out the response header Total-Count ::::::::::::::
    $pageListUrl = "https://" . $server . ".iformbuilder.com/exzact/api/v60/profiles/$activeProfile/pages?limit=1&access_token=" . $token;
    $pageRequestHeaders = (get_headers($pageListUrl)[4]);
    $finalPageCount = = preg_split("/[\s,]+/", $pageRequestHeaders)[1];
    echo("Number of Total Forms:" . $finalPageCount . "\r\n");


    //:::::::::::::: Use the total count of pages to start looping through each one to get the page ID ::::::::::::::

    for ($j=0; $j<$finalPageCount; $j++){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://" . $server . ".iformbuilder.com/exzact/api/v60/profiles/$activeProfile/pages?limit=1&offset=$j");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);

      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token"
        ));

        $response = curl_exec($ch);
        if(curl_errno($ch))
            echo 'Curl error: '.curl_error($ch);
        curl_close($ch);

        $activePageJson = json_decode($response,true);
        $activePage = ($activePageJson[0]["id"]);
        echo($activePage . "\r\n");

        //:::::::::::: For each page we get back, create the new element which is combining profile ID and page ID in dynamic value that is hidden on the device:::::::::::::::

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $server . ".iformbuilder.com/exzact/api/v60/profiles/$activeProfile/pages/$activePage/elements");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
              "name": "dfa_report_key",
              "label": "Dataflow Report Key",
              "data_type": 1,
              "condition_value": "false",
              "dynamic_value": "\"' . $activeProfile . '|' . $activePage . '\""
              }');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $token",
            "Content-Type: application/json"
          ));

          $response = curl_exec($ch);
          if(curl_errno($ch))
              echo 'Curl error: '.curl_error($ch);
          curl_close($ch);
          echo($response . "\r\n");

      }
}

?>
