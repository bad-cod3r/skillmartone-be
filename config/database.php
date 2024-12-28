<?php

 define('HOST', 'localhost');
 define('USER', 'root');
 define('PASS', '');
 define('DB', 'skillmartone-db');

$db_connect = mysqli_connect( HOST, USER, PASS, DB ) or die ('Unable connect');

header ('content-type: application/json');