<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function shorten($n) 
{
    $codeset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $base = strlen($codeset);
    $converted = "";
    while ($n > 0) 
    {
      $converted = substr($codeset, ($n % $base), 1) . $converted;
      $n = floor($n/$base);
    }
    return $converted;
}

function unshorten($converted) 
{
    $codeset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $base = strlen($codeset);
    $c = 0;
    for ($i = strlen($converted); $i; $i--) 
    {
      $c += strpos($codeset, substr($converted, (-1 * ( $i - strlen($converted) )),1))  * pow($base,$i-1);
    }
    return $c;
}
