<?php

class Vivid
{

    protected $conn;

    protected $table;

    protected $query;

    protected $results;

    protected $limit;

    protected $conditionColumn;

    public function __construct($host, $root, $password, $dbName)
    {
        try {
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
        return $this;
    }

    public function get()
    {
        try {
            $sql = "SELECT * FROM $this->table";
            $query = $this->conn->prepare($sql);
            $query->execute();
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
            return $this->results;
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function limit($limit)
    {
        if(isset($this->limit)){
            $sql = "SELECT * FROM $this->table LIMIT $this->limit";
        }else {
            $sql = "SELECT * FROM $this->table";
        }

        try {
           $sql = "SELECT * FROM $this->table";
            $query = $this->conn->prepare($sql);
            $query->execute();
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
        }catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        return $this;
    }

    public function where($conditionColumn, $conditionValue)
    {
            if(isset($conditionValue)){
                $whereSql = "SELECT * FROM $this->table WHERE $conditionColumn = ?";
            }else {
                $whereSql = "SELECT * FROM $this->table";
            }

        try {
            $sql = $whereSql;
            $query = $this->conn->prepare($sql);
            $query->execute(array($conditionValue));
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
            return $this->results;
        }catch(PDOException $ex) {
            echo $ex->getMessage();
        }
        return $this;
    }

    public function insert($data = [])
    {
        try{
            $keys = array_keys($data);
            $key = implode(",", $keys);
            $values = array_values($data);
            $value = implode("','", $values);
            $sql = "INSERT INTO $this->table($key) VALUES ('$value')";
            $query = $this->conn->prepare($sql);
            $query->execute();
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
        }catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function edit($newInput = [], $user_id)
    {
        try{
            foreach($newInput as $key=>$value){
            $sql = "UPDATE $this->table SET $key = ? WHERE user_id = ?";
            $query = $this->conn->prepare($sql);
            $query->execute(array($value, $user_id));
            }
        }catch (PDOException $ex) {
            echo $ex->getMessage();
        }
        return $this;
    }
}