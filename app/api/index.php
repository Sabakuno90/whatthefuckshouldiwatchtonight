<?php

error_reporting(E_ALL);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require 'config.php';

/**
 * Attempt to create a new PDO connection using the values entered in the
 * config file.
 *
 * @return  PDO
 */

function connect() {
    try {
        $db = new PDO('mysql:host=' . HOST . ';port=' . PORT . ';dbname=' . DB, USER, PWD);
    } catch (PDOException $e) {
        return [];
    }

    $db->exec('SET NAMES UTF8');
    return $db;
}

/**
 * Query the database using a prepared statement with optional parameter
 * bindings.
 *
 * @param   $statement  string
 * @param   $params     [string => string]
 *
 * @return  [string => string]
 */

function query($statement, $params = []) {
    $db = connect();
    $query = $db->prepare($statement);

    // Bind the given parameters to their values.

    foreach ($params as $key => $val) {
        $query->bindParam(':' . $key, $val);
    }

    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    return count($result) > 0 ? $result : [];
}

/**
 * Convenience method for retrieving a single row from the database.
 *
 * @see query($statement, $params)
 */

function querySingle($statement, $params = []) {
    $result = query($statement, $params);
    return count($result) > 0 ? $result[0] : null;
}

/**
 * @TODO document
 */

function insert($statement, $values = []) {
    $db = connect();
    $db->beginTransaction();

    $query = $db->prepare($statement);

    try {
        foreach ($values as $value) {
            $query->execute($value);
        }
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }

    $db->commit();
    return true;
}

$app = new \Slim\App();

/**
 * /emotions/ lists all emotions from the database. There is no further
 * configuration.
 */

$app->get('/emotions/', function (Request $request, Response $response) {
    $emotions = query('SELECT * FROM emotions');

    return $response->withJson([ 'emotions' => $emotions ]);
});

/**
 * /movies/{emotion}/ lists all movies that have been reviewed as matching a
 * given emotion. It includes the share of reviews for the given emotion.
 */

$app->get('/movies/{emotion:[a-z]+}/', function (Request $request, Response $response) {
    $movies = query('SELECT m.*, (SELECT COUNT(*) FROM reviews r WHERE r.emotion_id = (SELECT e.id FROM emotions e WHERE e.emotion = :emotion) AND r.movie_id = m.id) / (SELECT COUNT(*) FROM reviews r WHERE r.movie_id = m.id) AS percentage FROM movies m HAVING percentage > 0 LIMIT 102', [
        'emotion' => $request->getAttribute('emotion')
    ]);

    return $response->withJSON([ 'movies' => $movies ]);
});

/**
 * /movies/{id}/ retrieves information about a single movie. Included is the
 * number of reviews posted for each emotion and the list of directors and cast.
 */

$app->get('/movies/{id:[0-9]+}/', function (Request $request, Response $response) {
    $movie = querySingle('SELECT m.* FROM movies m WHERE m.id = :id', [
        'id' => $request->getAttribute('id')
    ]);

    $emotions = query('SELECT e.emotion, (SELECT COUNT(r.id) FROM reviews r WHERE r.movie_id = :id AND r.emotion_id = e.id) AS count FROM emotions e', [
        'id' => $request->getAttribute('id')
    ]);

    $crew = query('SELECT p.*, mp.credit_order FROM persons p, movie_persons mp WHERE mp.person_id = p.id AND mp.movie_id = :id ORDER BY mp.credit_order', [
        'id' => $request->getAttribute('id')
    ]);

    // Counts are retrieved as an associative array containing name and count.
    // We want to combine all the emotions into a single associative array
    // [emotion => count].

    $movie['emotions'] = array_reduce($emotions, function ($c, $i) {
	    return array_merge($c, [ $i['emotion'] => $i['count'] ]);
    }, []);

    // Directors don't have a credit order. We only need their full name.

    $movie['directors'] = array_map(function ($e) {
        return $e['full_name'];
    }, array_values(array_filter($crew, function ($e) {
        return $e['credit_order'] == 0;
    })));

    // Cast members have a credit order. We only need their full name.

    $movie['cast'] = array_map(function ($e) {
        return $e['full_name'];
    }, array_values(array_filter($crew, function ($e) {
        return $e['credit_order'] > 0;
    })));

    return $response->withJSON($movie);
});

$app->post('/movies/', function (Request $request, Response $response) {
    $body = $request->getParsedBody();

    $exists = query('SELECT EXISTS(SELECT * FROM movies WHERE id = :id) AS exists', [
        'id' => $body['id']
    ]);

    if ($exists['exists'] == 0) {
        $i1 = insert('INSERT INTO movies (id, title, poster_path, runtime, release_year) VALUES (?, ?, ?, ?, ?)', [
            [ $body['id'], $body['title'], $body['poster_path'], $body['runtime'], substr($body['release_date'], 0, 4) ]
        ]);

        $cast = array_map(function ($c) {
            return [ $c['id'], $c['name'], $c['order'] + 1 ];
        }, array_slice($body['credits']['cast'], 0, 5));

        $crew = array_map(function ($c) {
            return [ $c['id'], $c['name'], 0 ];
        }, array_values(array_filter($body['credits']['crew'], function ($c) {
            return $c['job'] == 'Director';
        })));

        $i2 = insert('INSERT INTO persons (id, full_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE id = id', array_map(function ($c) {
            return [ $c[0], $c[1] ];
        }, array_merge($cast, $crew)));

        $i3 = insert('INSERT INTO movie_persons (movie_id, person_id, credit_order) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE movie_id = movie_id', array_map(function ($c) use ($body) {
            return [ $body['id'], $c[0], $c[2] ];
        }, array_merge($cast, $crew)));
    }

    $i4 = insert('INSERT INTO reviews (movie_id, emotion_id) VALUES (?, ?)', [
        [ $body['id'], $body['emotionId'] ]
    ]);

    return $response->withStatus($i1 && $i2 && $i3 && $i4 ? 201 : 500);
});

$app->run();
