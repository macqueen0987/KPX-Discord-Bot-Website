<?php
    require  '../vendor/autoload.php';
    
    include('../include/phpframe.php');
    include('../include/check_logged.php');

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig('../google_api/credential_macqueen0987.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $tokenPath = '../google_api/token_macqueen0987.json';
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        return $client;
    }

    $ore_cnt = $mineral_cnt = $pi1_cnt = $pi2_cnt = 0;
    $posted = $_POST;
    $posted_key = array_keys($posted);
    $ore_arr = $mineral_arr = $pi1_arr = $pi2_arr = array();
    for ($i=0; $i < count($posted)/2; $i++) { 
        if (strpos($posted_key[$i*2], 'ore') !== false) {
            $ore_arr[$ore_cnt] =  array($posted[$posted_key[2*$i]]);
            $ore_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'mineral') !== false) {
            $mineral_arr[$mineral_cnt] =  array($posted[$posted_key[2*$i]]);
            $mineral_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'pi1') !== false) {
            if (intval($posted[$posted_key[2*$i+1]]) == 0) {
                $pi1_arr[$pi1_cnt] = array($posted[$posted_key[2*$i]]);
            }else{
                if (strcmp($posted[$posted_key[2*$i]], 'Urgent') == 0) {
                    $pi1_arr[$pi1_cnt] =  array($posted[$posted_key[2*$i]]);
                }else{
                    $pi1_arr[$pi1_cnt] =  array($posted[$posted_key[2*$i]]);
                }
            }
            $pi1_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'pi2') !== false) {
            if (strcmp($posted[$posted_key[2*$i]], 'Urgent') == 0) {
                $pi2_arr[$pi2_cnt] =  array($posted[$posted_key[2*$i]]);
            }else{
                $pi2_arr[$pi2_cnt] =  array($posted[$posted_key[2*$i]]);
            }
            $pi2_cnt ++;
        }
    }

    $client = getClient();
    $service = new Google_Service_Sheets($client);

    $arrs = array();
    $arrs[0] = $ore_arr;
    $arrs[1] = $mineral_arr;
    $arrs[2] = $pi1_arr;
    $arrs[3] = $pi2_arr;




    $ore_range = 'PriceAndDemand!C5:C20';
    $mineral_range = 'PriceAndDemand!I5:I12';
    $pi_range1 = 'PriceAndDemand!C25:C41';
    $pi_range2 = 'PriceAndDemand!I25:I41';

    $range_arr = array($ore_range, $mineral_range, $pi_range1, $pi_range2);


    for ($i=0; $i < 4; $i++) { 
        $values = $arrs[$i];
        $body = new Google_Service_Sheets_ValueRange(['values' => $values]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $update_sheet = $service->spreadsheets_values->update($buyback_sheet, $range_arr[$i], $body, $params);
    }

    $num_arr = json_decode("[[0, 16], [1, 28], [2, 8], [3, 2], [4, 25], [5, 17], [6, 7], [7, 15], [8, 21], [9, 9], [10, 5], [11, 6], [12, 26], [13, 19], [14, 0], [15, 10], [16, 20], [17, 27], [18, 33], [19, 12], [20, 31], [21, 24], [22, 4], [23, 1], [24, 3], [25, 18], [26, 29], [27, 30], [28, 11], [29, 32], [30, 14], [31, 13], [32, 22], [33, 23]]", true);


    $ore_cnt = $mineral_cnt = $pi1_cnt = $pi2_cnt = 0;
    $ore_arr = $mineral_arr = $pi1_arr = $pi2_arr = array();
    $temp_arr = array();
    for ($i=0; $i < count($posted)/2; $i++) { 
        if (strpos($posted_key[$i*2], 'ore') !== false) {
            $ore_arr[$ore_cnt] =  array(intval($posted[$posted_key[2*$i+1]]));
            $ore_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'mineral') !== false) {
            $mineral_arr[$mineral_cnt] =  array(intval($posted[$posted_key[2*$i+1]]));
            $mineral_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'pi1') !== false) {
            $pi1_arr[$pi1_cnt] =  array(intval($posted[$posted_key[2*$i+1]]));
            $pi1_cnt ++;
        }
        if (strpos($posted_key[$i*2], 'pi2') !== false) {
            $pi2_arr[$pi2_cnt] =  array(intval($posted[$posted_key[2*$i+1]]));
            $pi2_cnt ++;
        }
    }


    $pi_arr = array();
    $pi_arr = array_merge($pi1_arr, $pi2_arr);
    $temp_arr = array_fill(0, count($pi_arr), 0);

    foreach ($num_arr as $num) {
        $temp_arr[$num[0]] = $pi_arr[$num[1]];
    }


    $params = ['valueInputOption' => 'USER_ENTERED'];

    $body = new Google_Service_Sheets_ValueRange(['values' => $temp_arr]);
    $update_sheet = $service->spreadsheets_values->update($id_sheet, 'Hanger_Input!I3:I36', $body, $params);

    $body = new Google_Service_Sheets_ValueRange(['values' => $ore_arr]);
    $update_sheet = $service->spreadsheets_values->update($id_sheet, 'Hanger_Input!I69:I84', $body, $params);

    $body = new Google_Service_Sheets_ValueRange(['values' => $mineral_arr]);
    $update_sheet = $service->spreadsheets_values->update($id_sheet, 'Hanger_Input!I53:I60', $body, $params);
    echo "<script>alert('성공적으로 반영되었습니다.');location.href='get_buyback.php';</script>";
?>