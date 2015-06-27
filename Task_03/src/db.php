<?php

Class Db extends PDO{

    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self('sqlite:game.db');
            chmod("game.db", 0755);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE TABLE IF NOT EXISTS Players
                (
                    Id INTEGER PRIMARY KEY,
                    Name TEXT,
                    Motion INTEGER
      )";
            self::$instance->exec($sql);
        }
        return self::$instance;
    }

    public function savePlayer($name, $count){
        $stmt = self::$instance->prepare('INSERT INTO Players( Name, Motion) VALUES(:name, :count);');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':count', $count);
        $stmt->execute();
    }

    public function getWinners(){
        $stmt = self::$instance->prepare('SELECT * FROM Players ORDER BY Motion DESC LIMIT 8');
        $stmt->execute();
        return $stmt->fetchAll();
    }

}