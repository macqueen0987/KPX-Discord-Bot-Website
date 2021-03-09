<?php
    require  '../vendor/autoload.php';
    
    include('../include/phpframe.php');
    include('../include/dbframe.php');
    include('../include/check_logged.php');
    include('../include/check_director.php');

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig('../google_api/credential_macqueen0987.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');


        $tokenPath = '../google_api/token_macqueen0987.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }


        if ($client->isAccessTokenExpired()) {
            echo "<script>alert(토큰이 만료되거나 없습니다.);history.go(-1);</script>";
        }
        return $client;
    }


    $client = getClient();
    $service = new Google_Service_Sheets($client);



    $ore_range = 'PriceAndDemand!A5:C20';
    $mineral_range = 'PriceAndDemand!G5:I12';
    $pi_range1 = 'PriceAndDemand!A25:C41';
    $pi_range2 = 'PriceAndDemand!G25:I41';

    $response = $service->spreadsheets_values->get($buyback_sheet, $ore_range);
    $ore_values = $response->getValues();
    $ore_values = json_encode($ore_values);

    $response = $service->spreadsheets_values->get($buyback_sheet, $mineral_range);
    $mineral_values = $response->getValues();
    $mineral_values = json_encode($mineral_values);

    $response = $service->spreadsheets_values->get($buyback_sheet, $pi_range1);
    $pi_values1 = $response->getValues();
    $pi_values1 = json_encode($pi_values1);

    $response = $service->spreadsheets_values->get($buyback_sheet, $pi_range2);
    $pi_values2 = $response->getValues();
    $pi_values2 = json_encode($pi_values2);


    $stmt = $mysqli->prepare('UPDATE buyback_price set updated = now(), ore = ?, mineral = ?, pi1 = ?, pi2 = ?');
    if ($stmt) {
        $stmt->bind_param('ssss', $ore_values, $mineral_values, $pi_values1, $pi_values2);
        $stmt->execute();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    echo "<script>alert('성공적으로 가져왔습니다.');location.href='../priceanddemand.php';</script>";
?>