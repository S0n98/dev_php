<?php


class M_post {
    public $id;
    public $title;
    public $description;
    public $image;
    public $status;
    public $create_at;
    public $update_at;

    public function __construct($_id, $_title, $_description, 
        $_image, $_status, $_create_at, $_update_at)
    {
        $this->id = $_id;
        $this->title = $_title;
        $this->description = $_description;
        $this->image = $_image;
        $this->status = $_status;
        $this->create_at = $_create_at;
        $this->update_at = $_update_at;
    }

    public static function get_all_posts() {
        $db = new M_db();
        return $db->get_all_post();
    }
}

?>