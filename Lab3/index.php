<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ТИ-3</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <style>
        body{
            font-family: monospace;
        }
    </style>
</head>
<body>
    <form action="rabin.php" method="POST">
        <input name="p" type="text" placeholder="P" pattern="^[ 0-9]+$" required><br>
        <input name="q" type="text" placeholder="Q" pattern="^[ 0-9]+$" required><br>
        <input name="b" type="text" placeholder="B" pattern="^[ 0-9]+$" required><br>
        <br><br>
        <?php
            $dir = glob(dirname(__FILE__) . "/*.*");
            foreach($dir as $file){
                $path_parts = pathinfo($file);
                if($path_parts['extension'] != 'php')
                    echo '<p><input name="file" type="radio" value="'.$path_parts['basename'].'" checked>'. $path_parts['basename'].'</p>';
            }
        ?>
        <br>
        <p><input name="type" type="radio" value="1" checked> Зашифровать</p>
        <p><input name="type" type="radio" value="2"> Расшифровать</p>
        <br><br>
        <input type="submit" value="Выполнить">

    </form>
</body>
</html>