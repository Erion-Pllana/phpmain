<?php

$file=fopen("example.txt", 'r');

while(!feof($file)){
    echo fgets($file) . "<br>";
}

fclose($file);
?>