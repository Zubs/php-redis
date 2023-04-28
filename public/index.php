<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$router = new AltoRouter();
$client = new Client();
$redis = new Redis();
$redis->connect('127.0.0.1');

const REDIS_STANDARD_EXPIRY = 3600;

$router->map('GET', '/', function () {
    echo json_encode(['data' => 'Hello World']);
});

$router->map('GET', '/photos', function () use ($client, $redis) {
    if (!$redis->exists('photos')) {
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/photos');

        $redis->setex(
            'photos',
            REDIS_STANDARD_EXPIRY,
            $response->getBody()->getContents()
        );
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'data' => json_decode($redis->get('photos'))
    ]);
});

$router->map('GET', '/photos/[i:id]', function (int $id) use ($client, $redis) {
    if (!$redis->exists('photos:' . $id)) {
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/photos/' . $id);

        $redis->setex(
            'photos:' . $id,
            REDIS_STANDARD_EXPIRY,
            $response->getBody()->getContents()
        );
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'data' => json_decode($redis->get('photos:' . $id))
    ]);
});

$match = $router->match();

if( is_array($match) && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
