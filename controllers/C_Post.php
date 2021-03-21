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
        $db->close_con();
    }

    public static function view_edit_post($id) {
        include_once ("../model/M_db.php");
        include_once ("../model/M_post.php");
        $db = new M_db();
        $p = $db->get_post($id);
        $db->close_con();
        include_once ("../views/V_Edit.html");
    }

    public static function sub_edit($id) {
        include_once ("../model/M_db.php");
        include_once ("../model/M_post.php");
        $db = new M_db();
        $p = $db->get_post($id);
        $title = $_POST['title'];
        $description = $_POST['desc'];
        $status = $_POST['status'];

        $info = pathinfo($_FILES['image_edit']['name']);
        $ext = $info['extension'];
        $dbtarget = './views/img/'.$id.'.'.$ext;
        $real_target = '.'.$dbtarget;
        move_uploaded_file($_FILES['image_edit']['tmp_name'], $real_target);
        
        M_db::update_post($id, $title, $description, $status, $dbtarget);

        header("Location: http://localhost/controllers/C_Post.php?id=$id&action=detail");
    }

    public static function add_post() {
        include_once ("../model/M_db.php");
        include_once ("../model/M_post.php");
        $db = new M_db();
        $title = $_POST['title'];
        $description = $_POST['desc'];
        $status = $_POST['status'];

        $info = pathinfo($_FILES['image_edit']['name']);
        $ext = $info['extension'];
        if ($info['extension'] != '') {
            $id = M_db::get_last_id();
            $dbtarget = './views/img/'.$id.'.'.$ext;
            $real_target = '.'.$dbtarget;
            move_uploaded_file($_FILES['image_edit']['tmp_name'], $real_target);
        }
        else {
            $dbtarget = './views/img/default.png';
        }

        M_db::add_post($title, $description, $status, $dbtarget);

        header("Location: http://localhost/");
    }

    public static function view_add_post() {
        include_once("../views/V_Add.html");
    }
}
    $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
    switch ($action) {
        case 'detail':
            $id = $_GET['id'];
            Ctrl_post::view_detail_post($id);
            break;
        case '':
            Ctrl_post::view_home_posts();
            break;
        case 'delete':
            $id = $_GET['id'];
            Ctrl_post::delete_post($id);
            header("Location: http://localhost/");
            die();
            break;
        case 'edit':
            $id = $_GET['id'];
            Ctrl_post::view_edit_post($id);
            break;
        case 'sub_edit':
            $id = $_POST['id'];
            Ctrl_post::sub_edit($id);
            break;
        case 'view_add_post':
            Ctrl_post::view_add_post();
            break;
        case 'add_post':
            Ctrl_post::add_post();
            break;
        default:
            Ctrl_post::view_home_posts();
    }
?>