<?php

require 'vendor/autoload.php';
require '../sharepoint-integration.conf.php';

use Thybag\SharePointAPI;

$conf = $fj16_sharepoint_config; // shorthand

if(empty($conf['secret']) || $conf['secret'] !== $_GET['secret']) {
  header('HTTP/1.0 401 Unauthorized');
  die('Incorrect secret token');
}

header('Content-Type: application/json');

$sp = new SharePointAPI($conf['username'], $conf['password'], $conf['wsdl_url'], $conf['auth_variant']);

$pestit = $sp->query($conf['job_list_guid'])->all_fields()->get();

print json_encode($pestit);
