<?php
class Post {
    private string $title;
    private string $imageUrl;
    private string $timeStamp;
    function __construct(string $title, string $imageUrl, string $timeStamp)
    {
        $this->title = $title;
        $this->imageUrl = $imageUrl;
        $this->timeStamp = $timeStamp;
    }
    public function getFilename() : string {
        return $this->imageUrl;
    }
    public function getTitle() : string {
        return $this->title;
    }
    public function getTimeStamp() : string {
        return $this->timeStamp;
    }
    static function get(int $id) : Post {
        global $db;
        $q = $db->prepare("SELECT * FROM zdjecia WHERE id = ?");
        $q->bind_param('i', $id);
        $q->execute();
        $result = $q->get_result();
        $resultArray = $result->fetch_assoc();
        return new Post($resultArray['title'], $resultArray['filename'], $resultArray['timestamp']);
    }
    static function getLast() : Post {
        //odwołuję się do bazy danych
        global $db;
        //Przygotuj kwerendę do bazy danych
        $query = $db->prepare("SELECT * FROM zdjecia ORDER BY timestamp DESC LIMIT 1");
        //wykonaj kwerendę
        $query->execute();
        //pobierz wynik
        $result = $query->get_result();
        //przetwarzanie na tablicę asocjacyjną - bez pętli bo będzie tylko jeden
        $row = $result->fetch_assoc();
        //tworzenie obiektu
        $p = new Post($row['id'], $row['filename'], $row['timestamp']);
        //zwracanie obiektu
        return $p; 
    }

    static function getPage(int $pageNumber = 1, int $postsPerPage = 10) : array {
        global $db;
        $q = $db->prepare("SELECT * FROM zdjecia ORDER BY timestamp DESC LIMIT ? OFFSET ?");
        $offset = ($pageNumber-1) * $postsPerPage;
        $q->bind_param('ii', $postsPerPage, $offset);
        $q->execute();
        $result = $q->get_result();
        $postsArray = array();
        while($row = $result->fetch_assoc()) {
            $post = new Post($row['title'], 
                            $row['file'],
                            $row['timestamp']);
            array_push($postsArray, $post);
        }
        return $postsArray;
    }
    static function upload(string $tempFileName, $postTitle) {
        //funkcja działa bez tworzenia instancji obiektu
        // uwaga wywołanie metodą Post::upload()
        $uploadDir = "file/";
        //sprawdź czy mamy do czynienia z obrazem
        $imgInfo = getimagesize($tempFileName);
        //jeśli plik nie jest poprawnym obrazem
        if(!is_array($imgInfo)) {
            die("BŁĄD: Przekazany plik nie jest obrazem!");
        }
        //wygeneruj _możliwie_ losowy ciąg liczbowy
        $randomSeed = rand(10000,99999) . hrtime(true);
        //wygeneruj hash, który będzie nową nazwą pliku
        $hash = hash("sha256", $randomSeed);
        //wygeneruj kompletną nazwę pliku
        $targetFileName = $uploadDir . $hash . ".webp";
        //sprawdź czy plik przypadkiem już nie istnieje
        if(file_exists($targetFileName)) {
            die("BŁĄD: Podany plik już istnieje!");
        }
        //zaczytujemy cały obraz z folderu tymczasowego do stringa
        $imageString = file_get_contents($tempFileName);
        //generujemy obraz jako obiekt klasy GDImage
        //@ przed nazwa funkcji powoduje zignorowanie ostrzeżeń
        $gdImage = @imagecreatefromstring($imageString);
        //zapisz plik do docelowej lokalizacji
        imagewebp($gdImage, $targetFileName);

        global $db;

        $db = new mysqli("localhost", "root", "", "zdjeciastrona");
        $q = $db -> prepare(
            "INSERT INTO zdjecia 
            (id, file, timestamp, title)
            VALUES (NULL, ?, ?, ?)"
        );
        $dbTimestamp = date("Y-m-d H:i:s");
        $q -> bind_param("sss", $targetFileName, $dbTimestamp, $postTitle);
        $success = $q->execute();
    }
}

?>