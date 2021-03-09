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


    $update_range = 'Price_Public!A1';
    $range = array('Price_Public!C3:D32','Price_Public!F3:G32', 'Price_Public!I3:J32','Price_Public!L3:M32','Price_Public!O3:P33');
    $params = ['valueInputOption' => 'USER_ENTERED'];

    $values = [['KPX']];
    $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
    $update_sheet = $service->spreadsheets_values->update($id_sheet, $update_range, $body, $params);

    $arr1 = array();
    for ($i=0; $i < count($range); $i++) { 
        $response = $service->spreadsheets_values->get($id_sheet, $range[$i]);
        $value = $response->getValues();
        array_push($arr1, $value);
    }



    $values = [['GENESIS']];
    $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
    $update_sheet = $service->spreadsheets_values->update($id_sheet, $update_range, $body, $params);

    $arr3 = array();
    for ($i=0; $i < count($range); $i++) { 
        $response = $service->spreadsheets_values->get($id_sheet, $range[$i]);
        $value = $response->getValues();
        array_push($arr3, $value);
    }

    $arr1_json = json_encode($arr1);
    $arr3_json = json_encode($arr3);

    $stmt = $mysqli->prepare('UPDATE WOP_price SET updated = now(), price_json = ?, export_json = ?');
    if ($stmt) {
        $stmt->bind_param('ss', $arr1_json, $arr3_json);
        $stmt->execute();
        $stmt->close();
    }else{
        die($mysqli->error);
    }

    echo "<script>alert('성공적으로 반영되었습니다.');history.go(-1);</script>";
?>