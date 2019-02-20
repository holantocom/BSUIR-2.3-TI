<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ТИ-4</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        body{
            font-family: monospace;
        }
    </style>
</head>
<body>
    
    
    Сгенерировать ЭЦП:
    <form>
        <input name="p" type="text" placeholder="P" pattern="[1-9][0-9]{0,23}" autocomplete="off" required><br>
        <input name="q" type="text" placeholder="Q" pattern="[1-9][0-9]{0,23}" autocomplete="off" required><br>
        <input name="d" type="text" placeholder="D" pattern="[1-9][0-9]{0,23}" autocomplete="off" required><br>
        <input type="hidden" name="action" value="generate">
        <br>
        <?php
            $dir = glob(dirname(__FILE__) . "/*.*");
            $json_dir = glob(dirname(__FILE__) . "/json-*.*");
            $dir = array_diff($dir, $json_dir);
            foreach($dir as $file){
                $path_parts = pathinfo($file);
                if($path_parts['extension'] != 'php')
                    echo '<p><input name="file" type="radio" value="'.$path_parts['basename'].'" checked>'. $path_parts['basename'].'</p>';
            }
        ?>
        <br>
        <input class="megabutton" type="button" value="Сгенерировать"/>
    </form>
    <p id="generate"></p>
    
    
    <br>
    Проверить текст:
    <form>
        <input name="r" type="text" placeholder="R" pattern="[1-9][0-9]{0,23}" autocomplete="off" required><br>
        <input name="e" type="text" placeholder="E" pattern="[1-9][0-9]{0,23}" autocomplete="off" required><br>
        <input type="hidden" name="action" value="check">
        <br>
        <?php
            $dir = glob(dirname(__FILE__) . "/json-*.*");
            foreach($dir as $file){
                $path_parts = pathinfo($file);
                if($path_parts['extension'] != 'php')
                    echo '<p><input name="file" type="radio" value="'.$path_parts['basename'].'" checked>'. $path_parts['basename'].'</p>';
            }
        ?>
        <br>
        <input class="megabutton" type="button" value="Проверить"/>

    </form>
    <p id="check"></p>
    
    
<script>
$(".megabutton").click(function( event ) {
    
    var type = $(this).parent().find('input[name="action"]').val();
    
    if($(this).parent()[0].checkValidity()){
        
        $.post( "ES.php", $(this).parent().serialize())
        .done(function( data ) {
            $('#' + type).html(data);
        });
        
    } else {
        
        alert("Заполните все поля верными данными");
        
    }
    
});
</script>
</body>
</html>