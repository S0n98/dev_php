<?php

    class M_db {
        public static $conn;

        public function __construct()
        {
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = '123456';
            $dbname = 'dev_php';

            if (! M_db::$conn ) {
                M_db::create_db($dbhost, $dbuser, $dbpass, $dbname);
                try {
                    M_db::$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
                    M_db::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo('<script>console.log("Connect successfully")</script>');
                }
                catch (PDOException $e) {
                    echo('<script>console.log("Error connect: can not establish the connection")</script>');
                }
            }

            $check = 1;
            try {
                $check_tbl = M_db::$conn->prepare("select 1 from `post` LIMIT 1");
                $check_tbl->execute();
                $check = 0;
            }
            catch (Exception $e) {
                $check = 1;
            }

            if ($check) {
                $create_sql = "CREATE TABLE post (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(100),
                    description TEXT,
                    image VARCHAR(128),
                    status INT(2),
                    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    update_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                try {
                    M_db::$conn->query($create_sql);
                    echo('<script>console.log("Table post created successfully")</script>');
                }
                catch (PDOException $e) {
                    echo('<script>console.log("Table exist can not create!")</script>');
                }
            }
            else {
                echo('<script>console.log("Table has been existed can not create!")</script>');
            }

        }

        public function get_post($id) {
            $query = M_db::$conn->prepare("SELECT * FROM post WHERE id=$id");
            $query->execute();
            $post = $query->fetch();
            $p = new M_post($post["id"], $post["title"], 
                $post["description"], $post["image"], 
                ($post["status"] == 1) ? "Enabled" : "Disabled",
                $post["create_at"], $post["update_at"]);
            return $p;
        }
        
        public function get_all_post() {
            $posts = M_db::$conn->prepare("SELECT * FROM `post`");
            $posts->execute();
            
            $list_post = array();
            foreach($posts->fetchAll() as $post) {
                $p = new M_post($post["id"], $post["title"], 
                    $post["description"], $post["image"], 
                    ($post["status"] == 1) ? "Enabled" : "Disabled",
                    $post["create_at"], $post["update_at"]);
                if ($p->image == "test") {
                    $p->image = "./views/img/default.png";
                }
                $list_post[] = $p;
            }

            return $list_post;
        }

        public static function create_db($dbhost, $dbuser, $dbpass, $dbname) {
            try {
                M_db::$conn = new PDO("mysql:host=$dbhost", $dbuser, $dbpass);
                // set the PDO error mode to exception
                M_db::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
                // use exec() because no results are returned
                M_db::$conn->exec($sql);
                echo('<script>console.log("Database created successfully")</script>');
            } catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }

        public static function close_con() {
            M_db::$conn = null;
        }

        public static function delete_post($id) {
            $delete = M_db::$conn->prepare("DELETE FROM post WHERE id=$id");
            $delete->execute();
            M_db::close_con();
        }

        public static function update_post($id, $title, $description, $status, $target) {
            $update = M_db::$conn->prepare(
                'UPDATE post SET title = "'.$title.'", description = "'.$description.'", status = '.$status.', image = "'.$target.'" WHERE id='.$id);
            $update->execute();
            M_db::close_con();
        }

        public static function add_post($title, $description, $status, $target) {
            $add = M_db::$conn->prepare(
                'INSERT INTO post (title, description, status, image) VALUES("'.$title.'", "'.$description.'", '.$status.', "'.$target.'")');
            $add->execute();
            M_db::close_con();
        }

        public static function get_last_id() {
            $last_id = M_db::$conn->prepare('SHOW INDEXES FROM post');
            $last_id->execute();
            $row = $last_id->fetch(PDO::FETCH_ASSOC);
            return $row["Cardinality"];
        }

       }
