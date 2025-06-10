<?php
    $x=5;
    function localVariable() {
        $y=10;
        echo $y;
    }

    localVariable();
    echo "\n, $x";
?>