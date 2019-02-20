<?php
    //Открытый ключ - (e, r); Закрытый ключ - (d, r)
    $H = 100;
    $type = $_POST['action'];
    $filename =  $_POST['file'];
    
    $file = file_get_contents($filename);
    
    if($type == 'check'){
        
        $load_data = json_decode($file, true);
        $array = unpack('C*', $load_data['data']);
        $m = $load_data['key'];
        $e = $_POST['e'];
        $r = $_POST['r'];
        
        echo CheckES($array, $m, $r, $e, $H);
        
    } elseif ($type == 'generate') {
        
        $array = unpack('C*', $file);
        $p = $_POST['p'];
        $q = $_POST['q'];
        $d = $_POST['d'];
        
        $m = GetES($array, $p, $q, $d, $H);
        if(is_int($m)){
            $data = array('data' => $file, 'key' => $m);
            file_put_contents('json-'.$filename, json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            echo $m;
        }    
        
    }

function GetES($array, $p, $q, $d, $H)
{
    if(gmp_prob_prime($p) != 2)
        return "Переменная [p] не простая";
        
    if(gmp_prob_prime($q) != 2)
        return "Переменная [q] не простая"; 
    
    $r = $p*$q;
    if($r < 256)
        return  "Переменная [r] < 256";
        
    $yr = ($p - 1)*($q - 1);
    if(($d < 2) or ($d > $yr))
        return "Переменная [d] не соответствует 1 < d < y(r)";
        
    $nod = gmp_gcd($d, $yr);
    if($nod != 1)
        return "d и y(r) не взаимнопростые";
        
    $e = gmp_strval(gmp_gcdext($yr, $d)['t']);  
    
    foreach($array as $M)
        $H = (($H + $M)*($H + $M)) % $r;
        
    $S = intval(gmp_powm($H, $d, $r));
    
    echo "Хэш: $H<br>ЭЦП: $S<br>r: $r<br>e: $e";
    
    return $S;
    
}

function CheckES($array, $m, $r, $e, $H)
{
    
    if($r < 256)
        return  "Переменная [r] < 256";
    
    $S = intval(gmp_powm($m, $e, $r));
    
    foreach($array as $M)
        $H = (($H + $M)*($H + $M)) % $r;
    
    if($S == $H)
        return "Текст не изменился :)<br>Вычислено: $H<br>Передано: $S";
    else
        return "Кто-то подделал данные :(<br>Вычислено: $H<br>Передано: $S";
}