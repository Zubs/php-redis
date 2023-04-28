<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

$router->map('GET', '/', function () {
    echo json_encode(['data' => 'Hello World']);
});

$router->map('GET', '/photos', function () {
    echo json_encode(['data' => 'Get All Photos']);
});

$router->map('GET', '/photos/[i:id]', function (int $id) {
    echo json_encode(['data' => 'Get Photo ' . $id]);
});

// Todo: fix this
//$router->map('GET', '/', 'App\Src\Controllers\PhotosController#index');
$match = $router->match();

if( is_array($match) && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
//
//echo $redis->ping();
