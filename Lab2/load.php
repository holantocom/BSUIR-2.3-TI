<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ТИ-2</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <style>
        body{
            font-family: monospace;
        }
    </style>
</head>
<body>
<?php if (!isset($_FILES['upload']['tmp_name'])) : ?>
    <form method="POST" enctype="multipart/form-data">
        <input name="upload" type="file">
        <br><br>
        <input name="key" id="key" type="text" size="40" pattern="[0-1]{26}" placeholder="Ключ" required>  <span id="p">0</span>
        <br>
        <p><input name="type" type="radio" value="1"> Зашифровать</p>
        <p><input name="type" type="radio" value="2"> Расшифровать</p>
        <br><br>
        <input type="submit" value="Выполнить">
    </form>

<?php else: ?>
    <?php
    $newFilename = $_FILES['upload']['name'];
    $uploadInfo = $_FILES['upload'];
    $key = bindec($_POST['key']);

    if (!move_uploaded_file($uploadInfo['tmp_name'], $newFilename)) {
        echo 'Не удалось осуществить сохранение файла';
    }

    include 'index.php';
    if(intval($_POST['type']) == 1)
        $non = Cipher(basename($newFilename), $key);
    else
        $non = DeCipher(basename($newFilename), $key);
    ?>

<?php endif; ?>
<script>
    $('#key').keyup(function(){
        var count = $(this).val().length;
        $('#p').text(count);
    });
</script>

</body>
</html>