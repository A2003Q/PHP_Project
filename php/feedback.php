<?php
require_once "../CRUD/CRUD.php";

 class Feedback extends CRUD {

    public function getAllFeedback() {
        $sql = "SELECT * FROM feedback";
        return $this->read($sql);
    }
    public function deleteFeedback($id) {
        $sql = "DELETE FROM feedback WHERE feedback_id=$id";
        return $this->delete($sql);
    }
}





?>