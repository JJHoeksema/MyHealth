<?php

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim( $_SERVER[ 'PATH_INFO' ], '/'));
$input = json_decode(file_get_contents('php://input'), true);

if (strcmp($method, 'POST')) {

    // connect to the mysql database
    $link = mysqli_connect('localhost', 'user', 'pass', 'dbname');
    mysqli_set_charset($link, 'utf8');

    // retrieve the table and key from the path
    $table = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
    $key = array_shift($request)+0;

    // escape the columns and values from the input object
    $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
    $values = array_map(function ($value) use ($link) {
        if ($value === null) return null;
        return mysqli_real_escape_string($link,(string)$value);
    }, array_values($input));
    $sql = "insert into `$table` set $set";

    // build the SET part of the SQL command
    $set = '';
    for ($i = 0; $i < count($columns); $i++) {
        $set.=($i > 0 ? ',' : '' ).'`'.$columns[$i].'`=';
        $set.=($values[$i] === null ? 'NULL' : '"'.$values[$i].'"');
    }

    // excecute SQL statement
    $result = mysqli_query($link, $sql);

    // die if SQL statement failed
    if (!$result) {
        http_response_code(404);
        die(mysqli_error());
    }
}

// close mysql connection
mysqli_close($link);