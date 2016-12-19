<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/18/16
 * Time: 10:17 PM
 */
require_once '../vendor/autoload.php';
$configJSON = file_get_contents(__DIR__ . "/../conf/conf.json");
$config = json_decode($configJSON, true);
$_SERVER['DOCUMENT_ROOT'] = $config['rootDir'];
# needs to be changed in tests that require posts
$_SERVER['REQUEST_METHOD'] = 'GET';
