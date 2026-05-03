<?php

const APP_BASE_URL = 'http://192.168.1.153/parrainage-project';

function getConnection(): PDO
{
    static $conn = null;

    if ($conn === null) {
        $conn = new PDO('mysql:host=localhost;dbname=parrainage_db', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $conn;
}
