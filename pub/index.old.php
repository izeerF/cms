<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <label for="uploadedFileInput">Wybierz plik to wgrania na serwer:</label>
    <br>
    <input type="file" name="uploadedFile" id="uploadedFileInput">
    <br>
    <input type="submit" value="Wyślij plik" name="submit">
</form>
<?php
require('./../src/config.php');
if(isset($_POST['submit'])) {
    Post::upload($_FILES['uploadedFile']['tmp_name'], $_FILES['uploadedFile']['name']);
}

?>

</body>
</html>
