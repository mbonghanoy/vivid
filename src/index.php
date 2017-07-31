<?php

/**
 * Github:
 * @author rdpascua/vivid
 *
 * Must use composer
 * Must use PDO
 */

/**
 * Install mysql-workbench
 */

class Vivid
{

    protected $conn;

    protected $table;

    protected $query;

    protected $results;

    protected $limit;

    public function __construct($host, $root, $password, $dbName)
    {
        try{
        $this->conn = new PDO("mysql:host=$host;dbname=$dbName", $root, $password);
        } catch(PDOException $e){
            die($e->GetMessage());
        }
    }
        /**
         * Select the table in which the query will perform
         *
         * @param  string $table
         * @return $this
         */
    public function table($table)
    {
        $this->table = $table;
        return $this->results;
    }

    public function get(){
        try{
        $sql = "SELECT * FROM $this->table";
        $query = $this->conn->prepare($sql);
        $query->execute();
        $this->results = $query->fetchAll(PDO::FETCH_OBJ);
        return $this->results;
        }catch(PDOException $ex){
        echo $ex->getMessage();
        }
        }

    public function limit($limit)
    {
        if(isset($this->limit)){
            $sql = "SELECT * FROM $this->$table LIMIT $this->limit";
        }else {
            $sql = "SELECT * FROM $this->limit";
        }
        try{
            $sql = "SELECT * FROM $this->table";
            $query = $this->conn->prepare($sql);
            $query->execute();
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
        }catch(PDOException $ex){
            echo $ex->getMessage();
        }
        return $this->results;
    }

}

$vivid = new Vivid('localhost', 'root', 'password', 'phonebook');

$vivid
    ->table('person')
    ->get(); // Select all

$vivid
    ->table('users')
    ->limit(10)
    ->get(); // 10

/**
 * Object
 */
/*$users = [
    [
        'first_name' => 'Mark'
    ]
];*/