<?php

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if (strcmp($method, 'POST')) {
    // connect to the database
    $link = mysqli_connect('h', 'h', 'd', 'd');
    mysqli_set_charset($link, 'utf8');

    // check if the healthcareId exists
    $id = $input["HealthCareId"];
    if (is_numeric($id)) {
        $sql =  "SELECT Id FROM HealthCare WHERE Id = " . $id;
        executeSelectQuery($sql, $link);
    }

    // check if the userId exists
    $id = $input["UserId"];
    if (is_numeric($id)) {
        $sql =  "SELECT Id FROM User WHERE Id = " . $id;
        executeSelectQuery($sql, $link);
    }

    // copy input array
    $order = $input;
    // remove orderlines from copy
    unset($order["OrderLine"]);
    $sql = getInsertQuery($order, "Order", $link);
    executeInsertQuery($sql, $link);

    // add orderId to orderlines
    $insertId = mysqli_insert_id($link);
    $orderLines = $input["OrderLine"];
    foreach ( $orderLines as $line ) {
        $line["OrderId"] = $insertId;
        // TODO: Can be optimized into single query if db access time is a problem
        $sql = getInsertQuery($line, "OrderLine", $link);
        executeInsertQuery($sql, $link);
    }

}

function executeInsertQuery($sql, $link) {
    // excecute SQL statement
    $result = mysqli_query($link, $sql);

    // die if SQL statement failed
    if (!$result) {
        http_response_code(404);
        die(mysqli_error($link));
    }
}

function executeSelectQuery($sql, $link) {
    // excecute SQL statement
    $result = mysqli_query($link, $sql);

    // die if SQL statement failed
    if (mysqli_num_rows($result) == 0) {
        http_response_code(404);
        die(mysqli_error($link));
    }
}


function getInsertQuery($input, $table, $link) {
    $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
    $values = array_map(function ($value) use ($link) {
        if ($value === null) return null;
        return mysqli_real_escape_string($link,(string)$value);
    }, array_values($input));
    return "insert into `$table` (".implode(',', $columns).") values('".implode("','",$values)."')";
}

// close mysql connection
mysqli_close($link);
