<?php
include_once("session.php");
include_once("utils/SparkFile.php");
include_once("storage/SparkFileHTTPResponse.php");

$user_logo = new SparkFile("logo");
$user_logo->setPath(CACHE_PATH);

$response = new SparkFileHTTPResponse();
if ($user_logo->exists()) {
    $response->setFile($user_logo);
    $response->send();
    exit;
}
else {
    $origin_logo = new SparkFile("logo_header.svg");
    $origin_logo->setPath(INSTALL_PATH."/sparkfront/images/");
    $response->setFile($origin_logo);
    $response->send();
    exit;
}
?>