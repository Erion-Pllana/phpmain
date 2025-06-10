<?php
    function fullyDivisible($n) {
        if (($n%2)==0){
            return "$n is fully divisible by 2.";
        } else {
            return "$n is not fully divisible by 2.";
        }
    }

    print_r(fullyDivisible(4));
    print_r(fullyDivisible(36));
    print_r(fullyDivisible(16));
    print_r(fullyDivisible(5));
?>