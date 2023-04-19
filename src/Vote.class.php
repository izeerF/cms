<?php

class Vote {
    private int $postId;
    private int $userId;
    private bool $type;

    function __construct(int $postId, int $userId, bool $type) {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->type = $type;
    }

    public function vote() {
        $id = $this->postId;
        $userId = $this->userId;
        $type = $this->type;
        global $db;
        $q = $db->prepare("DELETE FROM votes WHERE user_id = ? AND post_id = ?");
        $q->bind_param('ii', $userId, $id);
        $q->execute();
        $query2 = $db->prepare("INSERT INTO votes VALUES(NULL, ?, ?, ?)");

        
        if($type) {
            //like
            $v = 1;
            $query2->bind_param('iii', $id, $userId, $v);
        }
        else {
            //dislike
            $v = -1;
            $query2->bind_param('iii', $id, $userId, $v);
        }
        $query2->execute();
    }
    
    public static function getLikes(int $post_id) {
        global $db;

        $q = $db->prepare("SELECT value FROM votes WHERE post_id = ?");

        $q->bind_param('i', $post_id);
        $q->execute();

        $result = $q->get_result();

        $likes = 0;
        while($row = $result->fetch_array()) {
            $likes += $row['value'];
        }
        return $likes;

    }
}
    ?>