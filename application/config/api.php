<?php defined('BASEPATH') OR exit('No direct script access allowed');
$config['tokenVerifier'] = TRUE;
$config['respondFormat'] = 'json';
$config['supportedFormats'] = [
    'json' => 'application/json',
    'array' => 'application/json',
    'csv' => 'application/csv',
    'html' => 'text/html',
    'jsonp' => 'application/javascript',
    'php' => 'text/plain',
    'serialized' => 'application/vnd.php.serialized',
    'xml' => 'application/xml'
];