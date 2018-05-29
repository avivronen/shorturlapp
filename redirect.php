<?php

//Simple Redirect
$link = new Link();
$link->redirectToUrl()->orFail();


class Link {
    private  $host = 'localhost';
    private  $user = '_____';
    private  $pass = '____';
    private  $db = 'urlws';
    private  $path = '';
    private  $longUrl = '';
    private  $conn = null;


    private  function getConnection() {
        if(!$this->conn) {
            $this->conn = new PDO('mysql:host='.$this->host.'; dbname='.$this->db, $this->user, $this->pass);
        }
        return $this->conn;
    }
    private function getPath() {
        if(!@isset($_SERVER['PATH_INFO'])) {
            return false;
        }

        $path = $_SERVER['PATH_INFO'];
        $path = substr($path, 1);
        $this->path = $path;
    }

    private function getLongUrl() {
        //Using Prepare to Secure query
        $statement = $this->getConnection()->prepare("select url from links where shortUrl = :name");
        $statement->execute(array(':name' => $this->path));
        $row = $statement->fetch();
        if(isset($row['url']) AND $row['url'] != '') {
            $this->longUrl = $row['url'];
        }
    }

    public  function redirectToUrl() {

        $this->getPath();
        $this->getLongUrl();

        $longUrl = $this->longUrl;
        if($longUrl != '' AND $longUrl != false) {
            header("Location: {$longUrl}");
            die();
        }
        return $this;

    }

    public function orFail() {
        if(!$this->longUrl OR $this->longUrl == '') {
            die('Nothing Found');
        }
    }
};

?>