<?php

if (!function_exists('unique_code')) {
    function unique_code() {
        return md5(uniqid(mt_rand(), true));
    }
}