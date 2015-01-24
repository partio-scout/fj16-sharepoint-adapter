<?php

require 'vendor/autoload.php';
require '../sharepoint-integration.conf.php';

use Thybag\SharePointAPI;

$conf = $fj16_sharepoint_config; // shorthand

if(empty($conf['secret']) || empty($_GET['secret']) || $conf['secret'] !== $_GET['secret']) {
  header('HTTP/1.0 401 Unauthorized');
  die('Incorrect secret token');
}

header('Content-Type: application/json');

$sp = new SharePointAPI($conf['username'], $conf['password'], $conf['wsdl_url'], $conf['auth_variant']);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $application = json_decode(file_get_contents('php://input'), TRUE);
  
  if(empty($application['Title'])) {
    header('HTTP/1.0 400 Bad request');
    die("Can't parse JSON body");
  }

  $res = $sp->write($conf['application_list_guid'], $application);

  // Check that returned result contains error code 0
  $success = strpos($res['raw_xml'], '0x00000000') !== FALSE;

  if($success) {
    print json_encode(array('result' => 'OK'));
    die();
  } else {
    header('HTTP/1.0 503 Bad gateway');
    print json_encode(array('error' => $res['raw_xml']));
    die();
  }

} else {
  $jobs = $sp->query($conf['job_list_guid'])->all_fields()->get();
  print json_encode($jobs);  
}
