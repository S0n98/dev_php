<?php
class Ctrl_post {
    public static function view_home_posts()
    {   

        include_once ("./model/M_post.php");
        include_once ("./model/M_db.php");
        include_once ("./model/M_paginator.php");
        $db = new M_db();
        $limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 5;
        $page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
        $links = ( isset( $_GET['links'] ) ) ? $_GET['links'] : 7;
        $query = "SELECT * FROM post";
        $paginate = new Paginator(M_db::$conn, $query);
        $results = $paginate->getData($limit, $page);
        $db->close_con();
        include_once("./views/V_Home.html");
    }

    public static function view_detail_post($id) {
        include_once ("../model/M_db.php");
        include_once ("../model/M_post.php");
        $db = new M_db();
        $post = $db->get_post($_GET["id"]);
        include_once("../views/V_detail.html");
        $db->close_con();
    }

    public static function delete_post($id) {
        include_once ("../model/M_db.php");
        $db = new M_db();
        M_db::delete_post($id);
    }

    public static function view_edit_post($id) {
        include_once ("../model/M_db.php");
        include_once ("../model/M_post.php");
        $db = new M_db();
        $post = $db->get_post($id);
        include_once ("../views/V_Edit.html");
    }

}

    $action = (isset($_GET['action'])) ? $_GET['action'] : '';

    if ($action == 'detail') {
        $id = $_GET['id'];
        Ctrl_post::view_detail_post($id);
    } 
    elseif ($action == '') {
        Ctrl_post::view_home_posts();
    }
    elseif ($action == 'delete') {
        $id = $_GET['id'];
        Ctrl_post::delete_post($id);
        header("Location: http://localhost/");
        die();
    }
    elseif ($action == 'edit') {
        $id = $_GET['id'];
        Ctrl_post::view_edit_post($id);
    }


?>