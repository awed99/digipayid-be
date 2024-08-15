<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn($buffer) => $buffer);
    } else if (ENVIRONMENT === 'development') {
        Services::routes()->get('__hot-reload', static function () {
            (new HotReloader())->run();
        });
    }

    Events::on('post_controller_constructor', function () {
        // \Sentry\init(['dsn' => 'YOUR_DSN' ]);
        \Sentry\init([
            'dsn' => getenv('DSN_SENTRY'),
            // Specify a fixed sample rate
            'traces_sample_rate' => (float)getenv('DSN_SENTRY_TRACES_SAMPLE_RATE'),
            // Set a sampling rate for profiling - this is relative to traces_sample_rate
            'profiles_sample_rate' => (float)getenv('DSN_SENTRY_PROFILES_SAMPLE_RATE'),
        ]);
    });


    Events::on('DBQuery', function (\CodeIgniter\Database\Query $query) {
        // \Sentry\init(['dsn' => 'YOUR_DSN' ]);

        $message =  new \CodeIgniter\HTTP\Message;
        $request = request();
        $_dataPost = $request->getJSON() ?? $request->getPostGet();
        $ip = $request->getIPAddress();
        $dataPost = json_encode($_dataPost);
        $response = $message->getBody();
        // $response = $res->getJSON();

        // print_r($ip);
        // print_r("------------------");
        // print_r($dataPost);
        // print_r("------------------");
        // print_r($query->getQuery());
        // print_r("------------------");
        // print_r($response);

        // ("select * from app_users where token_login = '" . $request->header('Authorization')->getValue() . "' limit 1");
        // print_r("------------------");
        // print_r($user);


        // $db = db_connect();
        // $db = $query->db->db_connect();
        // $db = \Config\Database::connect();
        // if ($request->header('Authorization')) {
        //     $builder = $db->table('app_users')->where('token_login', $request->header('Authorization')->getValue());
        //     $user = $builder->get()->getRow();
        // } else {
        //     $user = null;
        // }

        // $insert['id_user'] = $user ? $user->id : 0;
        // $insert['token_api'] = $user ? $user->token_api : null;
        // $insert['path'] = $request->getPath();
        // $insert['json_request'] = $dataPost;
        // $insert['json_response'] = $response;
        // $insert['query'] = $query->getQuery();
        // $insert['ip_address'] = $ip;
        // $db->table('log_hit_api')->insert($insert);

        // $db->close();
        $auth = (null !== $request->header('Authorization')) ? $request->header('Authorization')->getValue() : null;

        $servername = getenv('DB_HOST');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');
        $dbname = getenv('DB_NAME');

        // Create connection
        $conn = new \mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "select * from app_users where token_login = '" . $auth . "' limit 1";
        $user = $conn->query($sql)->fetch_assoc()[0] ?? [];
        $id_user = $user ? $user['id_user'] : 0;
        $token_api = $user ? $user['token_api'] : null;

        // print_r("------------------");
        // print_r($user);

        $sql2 = "INSERT INTO log_hit_api (id_user, token_api, path, json_request, json_response, query, ip_address)
                VALUES ('" . $id_user . "', '" . $token_api . "', '" . $request->getPath() . "', '" . $dataPost . "', '" . $response . "', '" . str_replace("'", "`", $query->getQuery()) . "', '" . $ip . "')";
        $conn->query($sql2);

        // if ($result->num_rows > 0) {
        //     // output data of each row
        //     while ($row = $result->fetch_assoc()) {
        //         echo "id: " . $row["id"] . " - Name: " . $row["firstname"] . " " . $row["lastname"] . "<br>";
        //     }
        // } else {
        //     echo "0 results";
        // }
        $conn->close();
        // die();

        \Sentry\init([
            'dsn' => getenv('DSN_SENTRY'),
            // Specify a fixed sample rate
            'traces_sample_rate' => (float)getenv('DSN_SENTRY_TRACES_SAMPLE_RATE'),
            // Set a sampling rate for profiling - this is relative to traces_sample_rate
            'profiles_sample_rate' => (float)getenv('DSN_SENTRY_PROFILES_SAMPLE_RATE'),
        ]);
    });

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
    }
});


// post_system 

// Events::on('post_system', static function () {
//     Events::on('DBQuery', function (\CodeIgniter\Database\Query $query) {
//         // \Sentry\init(['dsn' => 'YOUR_DSN' ]);


//         $message =  new \CodeIgniter\HTTP\Message;
//         $request = request();
//         $_dataPost = $request->getJSON() ?? $request->getPostGet();
//         $ip = $request->getIPAddress();
//         $dataPost = json_encode($_dataPost);
//         $response = $message->getBody();
//         // $response = $res->getJSON();
//         print_r($ip);
//         print_r("------------------");
//         print_r($dataPost);
//         print_r("------------------");
//         print_r($query->getQuery());
//         print_r("------------------");
//         print_r($response);
//         die();


//         // \Sentry\init([
//         //     'dsn' => getenv('DSN_SENTRY'),
//         //     // Specify a fixed sample rate
//         //     'traces_sample_rate' => (float)getenv('DSN_SENTRY_TRACES_SAMPLE_RATE'),
//         //     // Set a sampling rate for profiling - this is relative to traces_sample_rate
//         //     'profiles_sample_rate' => (float)getenv('DSN_SENTRY_PROFILES_SAMPLE_RATE'),
//         // ]);
//     });
// });
