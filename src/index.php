<?php

require_once('../vendor/autoload.php');

$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$new = new Vivid;
$results = $new->all();
vdump($results);
