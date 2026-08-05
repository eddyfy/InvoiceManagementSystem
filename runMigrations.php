<?php

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);
// var_dump($files);
foreach($files as $file){
    echo "Running migration: " . basename($file) . "\n";
    require_once $file;
}

?>