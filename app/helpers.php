<?php
use Illuminate\Support\Facades\Crypt;

if (!function_exists('encryptMessage')) {
    function encryptMessage($plainText)
    {
        return Crypt::encryptString($plainText);
    }
}

if (!function_exists('decryptMessage')) {
    function decryptMessage($encryptedText)
    {
        return Crypt::decryptString($encryptedText);
    }
}
