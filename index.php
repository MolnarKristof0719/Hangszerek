<?php

header("Access-Control-Allow-Origin: *");

// Engedélyezett metódusok (GET, POST, PUT, DELETE, OPTIONS - az OPTIONS a preflight kéréshez kell)
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

// Engedélyezett fejlécek (ha a front-end egyéni fejléceket használ, pl. Content-Type, Authorization)
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

header('Content-Type: application/json');

$mappa = "/phpalapok/07_API";

require './vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    global $mappa;
    $r->addRoute('GET', $mappa . '/api/instruments', 'getAllInstrumentsHandler');
    $r->addRoute('GET', $mappa . '/api/instruments/{id}', 'getSingleInstrumentHandler');
    $r->addRoute('GET', $mappa . '/api/instrumentsabc', 'getInstrumentsAbcHandler');
    $r->addRoute('POST', $mappa . '/api/instruments', 'postInstrumentHandler');
    $r->addRoute('DELETE', $mappa . '/api/instruments/{id}', 'deleteInstrumentHandler');
    $r->addRoute('PATCH', $mappa . '/api/instruments/{id}', 'patchInstrumentHandler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
// var_dump($httpMethod);
// die;
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// echo $uri;
// die;

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
// var_dump($routeInfo);
// die;


function getInstrumentsAbcHandler(){
    $pdo = getConnection();
    $query = "SELECT name, id FROM instruments ORDER BY name";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($rows);
    jsonOut($rows);
}

function getAllInstrumentsHandler($vars)
{
    $pdo = getConnection();
    $query = "SELECT * FROM instruments";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($rows);
    jsonOut($rows);
}

function getSingleInstrumentHandler($vars)
{
    $id = $vars['id'];
    $pdo = getConnection();
    $query = "SELECT * FROM instruments WHERE id = ?";
    $statement = $pdo->prepare($query);
    $statement->execute([$id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $message = "OK";
    if (!$row) {
        //nincs találat
        http_response_code(404);
        $message = "Not found id = $id";
        $row = null;
    }

    jsonOut($row, $message);
}

function postInstrumentHandler($vars)
{
    $body = json_decode( file_get_contents('php://input'), true);
    $pdo = getConnection();
    $query = "INSERT INTO instruments
    (name, description, brand, price, quantity)
    VALUES
    (?, ?, ?, ?, ?)";

    $statement = $pdo->prepare($query);
    $statement->execute([
        $body['name'], 
        $body['description'],
        $body['brand'],
        $body['price'],
        $body['quantity']
    ]);
    
    //hany rekord modosult
    $recordCount = $statement->rowCount();
    if($recordCount > 0){
        //sikerult
        //lekerdezzuk az id-t
        $id = $pdo->lastInsertId();
        $idAssoc = ["id"=>$id];
        $row = $idAssoc +$body;
        jsonOut($row);
    }
    else{
        //nem sikerult
        $row = null;
        $message = "Post fail";
        http_response_code(500);
        jsonOut($row, $message);
    }

    
}

function deleteInstrumentHandler($vars)
{
    $id = $vars['id'];
    $pdo = getConnection();
    $query = "DELETE FROM instruments WHERE id = ?";
    $statement = $pdo->prepare($query);
    $statement->execute([$id]);
    $recordCount = $statement->rowCount();
    $message = "OK";
    $row = ["id"=>$id];
    if($recordCount == 0){
        //nem torolt

        $message = "Delete fault id=$id";
        $row = null;
        http_response_code(404);
    }
    jsonOut($row, $message);
    

}
function patchInstrumentHandler($vars)
{
    //0. body kiolvasasa
    
    $body = json_decode( file_get_contents('php://input'), true);     
    
    //1. megkeressuk id alapjan
    $id = $vars['id'];
    $pdo = getConnection();
    $query = "SELECT * FROM instruments WHERE id = ?";
    $statement = $pdo->prepare($query);
    $statement->execute([$id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $message = "OK";
    //2. ha nincs visszauzenunk hogy nincs es kesz
    if (!$row) {
        //nincs találat
        $message = "Not found id = $id";
        $row = null;
        http_response_code(404);
        jsonOut($row, $message);
        return;
    }

    // jsonOut($row, $message);

    //2. ha van akkor ossze fesuljuk a bodyt azzal amit megkerestunk
    $query = "UPDATE instruments SET
    name = ?,
    description = ?,
    brand = ?,
    price = ?,
    quantity = ?
    WHERE id = ?";

//3. elvegezzuk az update parancsot
    $statement = $pdo->prepare($query);
    $statement->execute([
        $body['name'] ?? $row['name'], 
        $body['description'] ?? $row['description'],
        $body['brand']?? $row['brand'],
        $body['price']?? $row['price'],
        $body['quantity']?? $row['quantity'],
        $id
    ]);
    
    //4. ellenőrizzük hogy sikerul e
    $rowCount = $statement->rowCount();
    if ($rowCount > 0) {
        //Ok: visszakeressuk
        $query = "SELECT * FROM instruments WHERE id = ?";
        $statement = $pdo->prepare($query);
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        //5. visszakuldjuk amit visszaolvastunk 
        jsonOut($row);
    } else {
        //nem ok
        $message = "Patch fail id=$id";
        $row = null;
        http_response_code(500); //internalis szerver hiba
        jsonOut($row, $message);

    }



}


function jsonOut($data, $message = "OK")
{
    $response = [
        "message" => $message,
        "data" => $data
    ];

    $jsonResponse = json_encode(
        $response,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );

    echo $jsonResponse;
}

function getConnection()
{
    $domain = $_SERVER['DB_DOMAIN'];
    $port = $_SERVER['DB_PORT'];
    $database = $_SERVER['DB_DATABASE'];
    $host = "mysql:host=$domain;port=$port;dbname=$database;charset=utf8";
    $user = "root";
    $password = "";
    //var_dump($host);
    return new PDO($host, $user, $password);
}


$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Endpoint Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        $handler($vars);
        break;
}
