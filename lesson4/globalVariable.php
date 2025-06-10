<?php
$x=5;
$y=7;

function globalVariable() {
    global $x, $y;
    $y=$x+$y;
}

sum();
echo $y;
?>