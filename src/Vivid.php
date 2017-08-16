<?php

namespace Vivid;

use PDO;
use PDOException;

class Vivid
{
    protected $table='user';

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
}
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$user = new Vivid;
vdump($user->all());