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
    /**
     * Select the table in which the query will perform
     *
     * @param  string $table
     * @return $this
     */
    public function table($table)
    {

    }
}

$vivid = new Vivid('localhost', 'root', 'password', 'database_name');

$vivid
    ->table('users')
    ->get(); // Select all

$vivid
    ->table('users')
    ->limit(10)
    ->get(); // 10

/**
 * Object
 */
$users = [
    [
        'first_name' => 'Mark'
    ]
];