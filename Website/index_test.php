<?php 

$curlVersion = curl_version();
echo $curlVersion['version']; // e.g. 7.24.0

$url = "http://kpx.kro.kr/test.php"; //주소셋팅
$postfields = 'id=sssss&password=123456'; //post값 셋팅 (id값과 password 값이 셋팅됨)

$ch = curl_init(); //curl 로딩
curl_setopt($ch, CURLOPT_URL,$url); //curl에 url 셋팅
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 이 셋팅은 1로 고정하는 것이 정신건강에 좋음
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 주소가 https가 아니라면 지울것
curl_setopt($ch, CURLOPT_SSLVERSION,3); // 주소가 https가 아니라면 지울것

curl_setopt($ch, CURLOPT_POST, 1); // 포스트 전송 활성화 
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields); // curl에 포스트값 셋팅

$result = curl_exec ($ch); // curl 실행 및 결과값 저장
var_dump( $result); //출력
curl_close ($ch); // curl 종료
?>