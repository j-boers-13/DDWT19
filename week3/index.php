<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt19_week3', 'ddwt19', 'ddwt19');

/* Set credentials */
$cred = set_cred("ddwt19", "ddwt19");

/* Create Router instance */
$router = new \Bramus\Router\Router();

$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    if (!check_cred($cred)){
        $errorObj = new stdClass();
        $errorObj->message = "Invalid credentials!";
        echo json_encode($errorObj);
        exit();
    }
});

// Add routes here
$router->mount('/api', function () use ($db, $router) {
    // will result in '/api/'
    http_content_type('application/json');
    $router->get('/', function () {
        echo 'API';
    });
    $router->get('/series', function() use($db) {
        echo json_encode(get_series($db));
    });
    $router->post('/series', function() use($db) {
        echo json_encode(add_serie($db, $_POST));
    });
    $router->get('/series/(\d+)', function($id) use ($db) {
       echo json_encode(get_serieinfo($db, $id));
    });
    $router->put('/series/(\d+)', function($id) use ($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        echo json_encode(update_serie($db, $serie_info));
    });
    $router->delete('/series/(\d+)', function($id) use ($db) {
        echo json_encode(remove_serie($db, $id));
    });
    // will result in '/movies/id'
    $router->get('(\d+)', function ($id) {
        echo 'API id ' . htmlentities($id);
    });
});

$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    echo "Page not found";
});

/* Run the router */
$router->run();
