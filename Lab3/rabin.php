<?php

function Cipher($p, $q, $b, $filename)
{
    if(gmp_prob_prime($p) != 2)
        return "Переменная [p] не простая";
    if(gmp_prob_prime($q) != 2)
        return "Переменная [q] не простая"; 
    if(($p % 4) != 3)
        return  "$p mod 4 != 3 [p]";
    if(($q % 4) != 3)
        return "$q mod 4 != 3 [q]";
    
    $n = $p * $q;
    $b = 571;
    if(($b >= $n) or (gmp_prob_prime($b) != 2))
        return  "Переменная [b] неверная";
    
    $enc_file = fopen($filename, 'rb');
    $chunk = fread($enc_file, filesize($filename));
    fclose($enc_file);
    $m_nums = unpack('C*', $chunk);
    
    echo "Исходный текст:<br>";
    for($i = 0; $i < 40; $i++) echo $m_nums[$i]." ";

    $cph = array();
    foreach($m_nums as $m)
        $cph[]  = ($m * ($m + $b) ) % $n;
    
    echo "<br>Зашифрованный текст:<br>";
    for($i = 0; $i < 40; $i++) echo $cph[$i]." ";
    
    $str = implode(" ", $cph); 
    
    $file_out = fopen($filename.'.cph', 'wb');
    fwrite($file_out, $str);
    fclose($file_out); 
    return;
}

function DeCipher($p, $q, $b, $filename)
{
    if(gmp_prob_prime($p) != 2)
        return "Переменная [p] не простая";
    if(gmp_prob_prime($q) != 2)
        return "Переменная [q] не простая"; 
    if(($p % 4) != 3)
        return  "$p mod 4 != 3 [p]";
    if(($q % 4) != 3)
        return "$q mod 4 != 3 [q]";
    
    $n = $p * $q;
    $b = 571;
    if(($b >= $n) or (gmp_prob_prime($b) != 2))
        return  "Переменная [b] неверная";
    
    $enc_file = fopen($filename, 'rb');
    $chunk = fread($enc_file, filesize($filename));
    fclose($enc_file);
    $cph = explode(' ', $chunk);
    
    echo "<br>Зашифрованный текст:<br>";
    for($i = 0; $i < 40; $i++) echo $cph[$i]." ";

    $decipher = array();
    foreach($cph as $key => $c){
        $D = ($b*$b + 4*$c) % $n;
        $s = gmp_powm($D, ($p+1) / 4, $p);
        $r = gmp_powm($D, ($q+1) / 4, $q);
        $y = gmp_gcdext($p, $q);

        $d_nums = array(
            ($y['s']*$p*$r + $y['t']*$q*$s) % $n, 
            $n - (($y['s']*$p*$r + $y['t']*$q*$s) % $n),
            ($y['s']*$p*$r - $y['t']*$q*$s) % $n,
            $n - (($y['s']*$p*$r - $y['t']*$q*$s) % $n)
        );

        foreach($d_nums as $d){
            if(($d - $b) % 2 == 0)
                $m = (((-1*$b) + $d) / 2) % $n;
            else 
                $m = (((-1*$b) + $n + $d) / 2) % $n;
            if($m < 256)
               $decipher[] = $m;
        }   
    }
    $path_parts = pathinfo($filename);
    $str = '';
    
    echo "<br>Расшифрованный текст:<br>";
    for($i = 0; $i < 40; $i++) echo $decipher[$i]." ";
    
    if(count($cph) == count($decipher)){
        $filename = '1'.$path_parts['filename'];
        foreach($decipher as $m)
           $str .= pack('C*', $m); 
    } else {
        $path = pathinfo($path_parts['filename']);
        $filename = '1'.$path['filename'].'.txt';
        $str = implode(" ", $decipher); 
    }
    $file_out = fopen($filename, 'wb');
    fwrite($file_out, $str);
    fclose($file_out);
    
    return;
}
$filename = $_POST['file'];
$type = $_POST['type'];
$p = $_POST['p'];
$b = $_POST['b'];
$q = $_POST['q'];
if(intval($type) == 1)
    echo Cipher(intval($p), intval($q), intval($b), $filename);
else 
    echo DeCipher(intval($p), intval($q), intval($b), $filename);