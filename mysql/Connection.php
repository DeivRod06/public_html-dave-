<?php
class Connection{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "resihive";
    private $connection;
    public function accessConnection(){
        $link = new mysqli($this->host, $this->username, $this->password, $this->database);
        return $link;
    }
}
?>