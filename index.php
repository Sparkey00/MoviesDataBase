<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href='css/font-awesome.css' type='text/css'>
        <link rel="stylesheet" href='css/styles.css' type='text/css'>
        
    </head>
    <body>
        <script type='text/javascript'>

            $(document).ready(function () {
                $('input[name=sorting]').change(function () {
                    $('form').submit();
                });
            });

function showSearchResults(str) {
    if (str.length == 0) {
        window.location.reload(true);
        //document.getElementById("results").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("results").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "search.php?search=" + str, true);
        xmlhttp.send();
    }
}
        </script>
        <?php
        require_once 'db_access.php';
        require_once 'classes.php';
        $db = new PDO('mysql:host=' . $DB_HOST . ';dbname=' . $dbName, $dbLogin, $dbPass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));        
         print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">" .
                "<nav class=\"navbar navbar-expand-lg navbar-dark navbar-bg mb-5\"><div >
        <ul class=\"navbar-nav mr-auto\">
        <li class=\"nav-item\">                
                 <a style=\"color: #fff;\" class=\"nav-link\" href=\"index.php\">Database!</a>
            </li>  
            <li class=\"nav-item\">                
                 <a style=\"color: #fff;\" class=\"nav-link\" href=\"db_add.php\">Add new movie to the database!</a>
            </li>           
      <li class=\"nav-item\">
                        <input  name=\"search\" type=\"text\" placeholder=\"Search\" onkeyup=\"showSearchResults(this.value)\">
            <button  class=\"btn btn-info my-2 my-sm-0\" type=\"submit\">Search</button>
             </li>
            </ul>
    </div></nav>
     <div class=\"container\">
  <table id=\"results\" class=\"responsive-table\">
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
        if (isset($_POST['sorting'])) {
            DataBaseResults::sorting($db, $_POST['sorting']);
            unset($_POST['sorting']);
        } elseif (isset($_POST['delete'])) {
            DataBaseResults::deletion($db, $_POST['delete']);
            unset($_POST['delete']);
        } elseif (isset($_POST['search'])) {
            DataBaseResults::searching($db, $_POST['search']);
            unset($_POST['search']);
        } else
            DataBaseResults::showMovies($db);
        ?>
       
    </body>
</html>
