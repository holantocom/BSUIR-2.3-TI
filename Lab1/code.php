<?php
    switch ($_POST['id']) {
        case 1:
            $string = file_get_contents('input.txt');
            $string = preg_replace("/[^A-Z]/","",strtoupper($string));
            $key = preg_replace("/[^A-Z]/","",strtoupper($_POST['text']));
            if(strlen($key) == 0){
                echo "Неверный ключ.";
                break;
            }
            $encrypt = ColumnEncrypt($string, $key);
            echo $string."<br>Шифр: ".$encrypt;
            file_put_contents('crypto.txt', $encrypt);
            break;
        case 2:
            $string = file_get_contents('crypto.txt');
            $string = preg_replace("/[^A-Z]/","",strtoupper($string));
            $key = preg_replace("/[^A-Z]/","",strtoupper($_POST['text']));
            if(strlen($key) == 0){
                echo "Неверный ключ.";
                break;
            }
            $decrypt = ColumnDecrypt($string, $key);
            echo $decrypt;
            file_put_contents('output.txt', $decrypt);
            break;
        case 3:
            $string = file_get_contents('input.txt');
            $string = preg_replace("/[^A-Z]/","",strtoupper($string));
            $key_str = $_POST['text'];
            $width = (strlen($key_str) / 4);
            $key = array();
            for($i = 0; $i < $width; $i++){
                for($j = 0; $j < 4; $j++){
                    $key[$i][$j] = intval($key_str[($i * 4) + $j]);
                }
            }

            $encrypt = GrilleEncrypt($key, $string);
            echo $string."<br>Шифр: ".$encrypt;
            file_put_contents('crypto2.txt', $encrypt);
            break;
        case 4:
            $string = file_get_contents('crypto2.txt');
            $string = preg_replace("/[^A-Z]/","",strtoupper($string));
            $key_str = $_POST['text'];
            $width = (strlen($key_str) / 4);
            $key = array();
            for($i = 0; $i < $width; $i++){
                for($j = 0; $j < 4; $j++){
                    $key[$i][$j] = intval($key_str[($i * 4) + $j]);
                }
            }

            $decrypt = GrilleDecrypt($key, $string);
            file_put_contents('output2.txt', $decrypt);
            echo $decrypt;
            break;
        case 5:
            $alphabet = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
            $key_input = preg_replace('/[^\p{Cyrillic}]/ui',"", mb_strtoupper($_POST['text']));
            if(mb_strlen($key_input) == 0){
                echo "Неверный ключ.";
                break;
            }
            $string = file_get_contents('input3.txt');
            $string = preg_replace('/[^\p{Cyrillic}]/ui',"", mb_strtoupper($string));
            $key = $key_input.$string;

            $encrypt = VigenereEncrypt($key, $string, $alphabet);
            echo $string."<br>Шифр: ".$encrypt;
            file_put_contents('crypto3.txt', $encrypt);
            break;
        case 6:
            $alphabet = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
            $string = file_get_contents('crypto3.txt');
            $key = preg_replace('/[^\p{Cyrillic}]/ui',"", mb_strtoupper($_POST['text']));
            if(mb_strlen($key) == 0){
                echo "Неверный ключ.";
                break;
            }
            $decrypt = VigenereDecrypt($key, $string, $alphabet);
            echo $decrypt;
            file_put_contents('output3.txt', $decrypt);
            break;
        default:
            echo "Возникла какая-то ошибка";
    }

function ColumnEncrypt($string, $key)
{
    $matrix[0] = str_split($key);
    $matrix[1] = NumberLetters($key);

    for($i = 0; $i <= strlen($string); $i++) $matrix[intdiv($i, strlen($key)) + 2][$i % strlen($key)] = $string[$i];

    $encrypt = '';
    for($i = 1; $i <= strlen($key); $i++){
        for($k = 1; $k <= strlen($key); $k++) {
            if ($matrix[1][$k - 1] == $i) {
                for ($j = 2; $j < count($matrix); $j++) $encrypt .= $matrix[$j][$k - 1];
            }
        }
    }

    return $encrypt;
}

function ColumnDecrypt($string, $key)
{

    $support[0] = NumberLetters($key);
    $support[1] = array_fill(0 , strlen($key), 0); //Для кол-ва повторений
    $support[2] = array_fill(0 , strlen($key), ''); //Для букв

    for($i = 0; $i < strlen($string); $i++) $support[1][$i % strlen($key)]++;

    $offset = 0;
    for($i = 1; $i <= strlen($key); $i++){
        $arr_key = array_search($i, $support[0]);
        $arr_count = $support[1][$arr_key];
        $support[2][$i - 1] = substr($string, $offset, $arr_count);
        $offset += $arr_count;
    }

    $k = 1;
    $decrypt = '';
    while($k <= strlen($string)){
        for($i = 0; $i < strlen($key); $i++){
            $arr_num = $support[0][$i] - 1;
            if($support[1][$i] != 0 ){
                $decrypt .= $support[2][$arr_num][-1 * $support[1][$i]];
                $support[1][$i]--;
            }
            $k++;
        }
    }

    return $decrypt;
}

function NumberLetters($key){
    $numbers = array_fill(0, strlen($key), 0);
    for($i = 0; $i <= strlen($key); $i++){
        $min = 99999;
        $indexOfMin = 0;
        for($j = 0; $j < strlen($key); $j++) {
            if ($numbers[$j] == 0) {
                $c = $key[$j];
                if ($c < $min){
                    $min = $c;
                    $indexOfMin = $j;
                }
            }
        }
        $numbers[$indexOfMin] = $i;
    }
    return $numbers;
}

function GrilleEncrypt($key, $string)
{
    $arrays_count = ceil(strlen($string) / 16);
    $offset = 0;
    $array = array();
    $size = 4;

    for ($i = 0; $i < $arrays_count; $i++) {
        for ($j = 0; $j < count($key); $j++)
            $array[$i] = array_fill(0, count($key), array_fill(0, count($key[0]), '0'));
    }
    while($offset < strlen($string)){

        AddLetters($size, $offset, $array, $string, $key);

        foreach($key as $i => $value)
            $key[$i] = array_reverse($key[$i]);

        AddLetters($size, $offset, $array, $string, $key);

        $key = array_reverse($key);

        AddLetters($size, $offset, $array, $string, $key);

        foreach($key as $i => $value)
            $key[$i] = array_reverse($key[$i]);

        AddLetters($size, $offset, $array, $string, $key);

        $key = array_reverse($key);

    }

    $str = multi_implode('', $array);
    return $str;
}

function GrilleDecrypt($key, $string)
{
    $size = 4;
    $offset = 0;
    $arrays_count = intdiv(strlen($string), $size*$size);

    for ($i = 0; $i < $arrays_count; $i++) {
        for ($j = 0; $j < count($key); $j++)
            $array[$i] = array_fill(0, count($key), array_fill(0, count($key[0]), '0'));
    }
    for ($i = 0; $i < $arrays_count; $i++) {
        for ($j = 0; $j < count($key); $j++){
            for ($k = 0; $k < count($key); $k++){
                $array[$i][$j][$k] = $string[$offset];
                $offset++;
            }
        }

    }

    //foreach ($array as $input){
    //    for ($i = 0; $i < count($input); $i++) {
    //        for ($j = 0; $j < count($input[$i]); $j++) {
    //            echo $input[$i][$j] . " ";
    //        }
    //        echo "<br>";
    //    }
    //    echo "<br>";
    //}

    $decrypt = '';
    for($h = 0; $h < count($array); $h++){

        GetLetters($size, $array, $decrypt, $key, $h);

        foreach($key as $i => $value)
            $key[$i] = array_reverse($key[$i]);

        GetLetters($size, $array, $decrypt, $key, $h);

        $key = array_reverse($key);

        GetLetters($size, $array, $decrypt, $key, $h);

        foreach($key as $i => $value)
            $key[$i] = array_reverse($key[$i]);

        GetLetters($size, $array, $decrypt, $key, $h);

        $key = array_reverse($key);

    }

    $decrypt = preg_replace("/[^A-Z]/","", $decrypt);
    return $decrypt;
}

function AddLetters($size, &$offset, &$array, $string, $key)
{
    for ($i = 0; $i < $size; $i++) {
        for ($j = 0; $j < $size; $j++) {
            if($key[$i][$j] === 1){
                if(strlen($string) > $offset){
                    $array[intdiv($offset, ($size*$size))][$i][$j] = $string[$offset];
                    $offset++;
                } else{
                    $array[intdiv($offset, ($size*$size))][$i][$j] = chr($offset % strlen($string) + 65);
                    $offset++;
                }
            }
        }
    }
}
function GetLetters($size, $array, &$string, $key, $h)
{
    for ($i = 0; $i < $size; $i++) {
        for ($j = 0; $j < $size; $j++) {
            if($key[$i][$j] === 1){
                $string .= $array[$h][$i][$j];
            }
        }
    }
}

function multi_implode($glue, $array) {
    $_array=array();
    foreach($array as $val)
        $_array[] = is_array($val)? multi_implode($glue, $val) : $val;
    return implode($glue, $_array);
}

function VigenereEncrypt($key, $string, $alphabet)
{
    $encrypt = '';
    for($i = 0; $i < strlen($string); $i += 2){
        $code = (strpos($alphabet, $string[$i].$string[$i + 1]) + strpos($alphabet, $key[$i].$key[$i + 1])) % 66;
        $encrypt .= $alphabet[$code].$alphabet[$code + 1];
    }

    return $encrypt;
}

function VigenereDecrypt($key, $string, $alphabet)
{
    $decrypt = '';
    for($i = 0; $i < strlen($string); $i += 2){
        $code = (strpos($alphabet, $string[$i].$string[$i + 1]) - strpos($alphabet, $key[$i].$key[$i + 1]) + 66) % 66;
        $decrypt .= $alphabet[$code].$alphabet[$code + 1];
        $key .= $alphabet[$code].$alphabet[$code + 1];
    }
    return $decrypt;
}
?>