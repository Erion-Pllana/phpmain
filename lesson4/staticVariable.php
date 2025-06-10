<?php
    function callCounter() {
        static $counter = 0;
        $counter++;
        echo "This value of count variable is: $counter <br>";
    }

    callCounter();
    callCounter();
    callCounter();
    callCounter();
    callCounter();
?>