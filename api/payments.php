<?php
if($_GET['type']=='nequi'){
    $url = "https://oauth.sandbox.nequi.com/oauth2/token?grant_type=client_credentials";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$keyBase = base64_encode("7fmusm4tvrs5thdpbj2cm0ds3u").":".base64_encode("lk155mn8jdjd61npdjpd0n130gs8poa17rurlbjnqf0loth47ti");
$headers = array(
   "Authorization: Basic $keyBase",
   "Content-Type: application/x-www-form-urlencoded",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);
}
?>