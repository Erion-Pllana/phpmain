<?php

$a=100;
$b=50;

function add($a, $b) {
    return $a + $b;
}
echo add($a, $b) . "<br>"; 
function subtract($a, $b) {
    return $a - $b;
}
echo subtract($a, $b) ."<br>";
function multiply($a, $b) {
    return $a * $b;
}
echo multiply($a, $b) ."<br>";
function divide($a, $b) {
    if ($b == 0) {
        return null;
    }
    return $a / $b;
}
echo divide($a, $b) ."<br>";
?>