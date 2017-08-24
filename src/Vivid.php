<?php

namespace Vivid;

use PDO;
use PDOException;

class Vivid
{
    protected $table;

    protected $attributes = [];
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $connection;
    protected $query;
    protected $parameters = [];

    public function __construct($host = null, $username = null, $password = null, $database = null)
    {
        $this->host = $host ?? $_ENV['DB_HOST'];
        $this->username = $username ?? $_ENV['DB_USER'];
        $this->password = $password ?? $_ENV['DB_PASS'];
        $this->database = $database ?? $_ENV['DB_NAME'];

        $this->make();
    }
    public function make()
    {
        $this->connection = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->host, $this->database),
            $this->username,
            $this->password
        );
    }

     public function all($limit = 20)
    {
        if (!$this->table) {
            throw new \Exception('You must set the table.');
        }

        try {
            //connect as appropriate as above
            $statement = $this
                ->connection
                ->prepare("SELECT * FROM {$this->table} LIMIT :limit");

            $statement->bindParam(':limit', $limit, PDO::PARAM_INT);

            $statement->execute();
        } catch(PDOException $ex) {
            vdump($ex->getMessage());
        }

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function limit($limit = 10)
    {
        $this->query .= "LIMIT :limit ";

        $this->addParameter(':limit', $limit, PDO::PARAM_INT);

        return $this;
    }

    public function addParameter($parameter, $value, $attribute = null)
    {
        $this->parameters[$parameter] = [
            'value' => $value,
            'attribute' => $attribute
        ];
    }

    public function where($column, $value)
    {
        $this->query .= "WHERE {$column} = :value ";

        $this->addParameter(':value', $value, PDO::PARAM_STR);

        return $this;
    }

    public function andWhere($column, $values)
    {
        $this->query .= "AND {$column} = :values";

        $this->addParameter(':values', $values, PDO::PARAM_STR);

        return $this;
    }

    public function orWhere($column, $values)
    {
        $this->query .= "OR {$column} = :value ";

        $this->addParameter(':value', $value, PDO::PARAM_STR);

        return $this;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function get()
    {
        try {
            $statement = $this
                ->connection
                ->prepare("SELECT * FROM {$this->table} {$this->query}");

            foreach($this->parameters as $key => $parameter) {
                $statement->bindParam(
                    $key,
                    $parameter['value'],
                    $parameter['attribute']
                );
            }

            $statement->execute();
        } catch(PDOException $ex) {
            vdump($ex->getMessage());
        }

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function insert($data = [])
    {
        try{
            $keys = array_keys($data);
            $key = implode(",", $keys);
            $values = array_values($data);
            $value = implode("','", $values);
            $sql = "INSERT INTO $this->table($key) VALUES ('$value')";
            $query = $this->connection->prepare($sql);
            $query->execute();
            $this->results = $query->fetchAll(PDO::FETCH_OBJ);
            return true;

        }catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function join($table, $primaryKey, $foreignKey)
    {
        $this->query .= "INNER JOIN {$table} ON {$primaryKey} = {$foreignKey}";
        return $this;
    }

    public function update($newInput = [], $asset_id)
    {
        try{
            foreach($newInput as $key=>$value){
                $sql = "UPDATE $this->table SET $key = ? WHERE asset_id = ?";
                $query = $this
                    ->connection
                    ->prepare($sql);
                $query->execute(array($value, $asset_id));
            }

        }catch(PDOException $ex){
            echo $ex->getMessage();
        }

        return $this;
    }

    public function select($attrib)
    {
        try{
            $statement = $this
                ->connection
                ->prepare("SELECT {$attrib} FROM $this->table {$this->query}");

            foreach($this->parameters as $key => $parameter) {
                $statement->bindParam(
                    $key,
                    $parameter['value'],
                    $parameter['attribute']
                );
            }
            $statement->execute();
        }catch(PDOException $ex){
            echo $ex->getMessage();
        }

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function delete()
    {
        try {
            //connect as appropriate as above
            $statement = $this
                ->connection
                ->prepare("DELETE FROM {$this->table} {$this->query}");

            foreach($this->parameters as $key => $parameter) {
                $statement->bindParam(
                    $key,
                    $parameter['value'],
                    $parameter['attribute']
                );
            }
            $statement->execute();
        } catch(PDOException $ex) {
            vdump($ex->getMessage());
        }
    }
}