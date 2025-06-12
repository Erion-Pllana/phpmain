<?php
    
    // $sport1= "Football"
    // $sport2= "Basketball";
    // $sport3= "Handball";
    // $sport4= "Volleyball";

    // $sports=['Football', 'Basketball', 'Handball', 'Volleyball'];

    $sports=['Football', 'Basketball', 'Handball', 'Volleyball'];
    // echo $sports[0];
    // exho end($sports);
    echo count($sports);

    // for ($i =0; $i<4; $i++) {
    //     echo $sports[$i], "\n";
    // }

    $len = count($sports);

    for ($i = 0; $i < $len; $i++) {
        echo $sports[$i], "\n";
    }

?>