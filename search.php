<?php

require_once 'db_access.php';
require_once 'classes.php';
$db = new PDO('mysql:host=' . $DB_HOST . ';dbname=' . $dbName, $dbLogin, $dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
$q = $_REQUEST['search'];

$hint = "<table id=\"results\" class=\"responsive-table\">
   <thead>
      <tr>
        <th scope=\"col\"><input hidden type=\"radio\" selected id=\"title\"
        name=\"sorting\" value=\"film_name\">
        <label for=\"title\">Movie Title</label></th>
        <th scope=\"col\"><input hidden type=\"radio\" id=\"year\"
     name=\"sorting\" value=\"film_year\">
    <label for=\"year\">Release Year</label></th>
        <th scope=\"col\"><input hidden type=\"radio\" id=\"format\"
     name=\"sorting\" value=\"film_format\">
    <label for=\"format\">
   Format</label></th>
        <th scope=\"col\">Actors</th>
        <th scope=\"col\">Delete</th>     
    </thead>";
$result = DataBaseResults::searching($db, $q,true);
$showing = DataBaseResults::showMovies($db, $result, true);

if($showing === "")
{
DataBaseResults::showMovies($db);

}
else echo $hint.$showing;
