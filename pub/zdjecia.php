<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $db = new mysqli("localhost", "root", "", "zdjeciastrona");

    $q = "SELECT file FROM zdjecia;";
    $result = $db->query($q);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          echo "<img src=\"".$row["file"]."\">". "<br>";
        }
      } else {
        echo "0 results";
      }
    ?>
</body>
</html>