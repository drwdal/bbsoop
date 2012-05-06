<?php

/*
function mktripcode($pw)
{
    $pw=mb_convert_encoding($pw,'SJIS','UTF-8');
    $pw=str_replace('&','&amp;',$pw);
    $pw=str_replace('"','&quot;',$pw);
    $pw=str_replace("'",'&#39;',$pw);
    $pw=str_replace('<','&lt;',$pw);
    $pw=str_replace('>','&gt;',$pw);
    
    $salt=substr($pw.'H.',1,2);
    $salt=preg_replace('/[^.\/0-9:;<=>?@A-Z\[\\\]\^_`a-z]/','.',$salt);
    $salt=strtr($salt,':;<=>?@[\]^_`','ABCDEFGabcdef');
    
    $trip=substr(crypt($pw,$salt),-10);
    return $trip;
}
*/

?>