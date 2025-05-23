<?php

require_once('config.php');

$db = new mysqli(hostname: $db_host, username: $db_user, password: $db_pass, database: $db_name, port: $db_port);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}