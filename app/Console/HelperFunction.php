<?php
    function getRandomHex($num_bytes=4) {
        return bin2hex(random_bytes($num_bytes));
    }

