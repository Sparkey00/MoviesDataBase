<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Adding another movie</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href='css/font-awesome.css' type='text/css'>
        <link rel="stylesheet" href='css/styles.css' type='text/css'>
    </head>
    <body>
        <?php
        require_once 'db_access.php';
        require_once 'classes.php';
        $db = new PDO('mysql:host=' . $DB_HOST . ';dbname=' . $dbName, $dbLogin, $dbPass);
print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">" .
                "<nav class=\"navbar navbar-expand-lg navbar-dark navbar-bg mb-5\"><div>
        <ul class=\"navbar-nav mr-auto\">
        <li class=\"nav-item\">                
                 <a style=\"color: #fff;\" class=\"nav-link\" href=\"index.php\">Database!</a>
            </li>  
            <li class=\"nav-item\">                
                 <a style=\"color: #fff;\" class=\"nav-link\" href=\"db_add.php\">Add new movie to the database!</a>
            </li>
            </ul>
    </div></nav>
     <div class=\"container\">";

        if ((!isset($_FILES['upload']['tmp_name']))) {            
        } else {
            DataBaseAdding::fileupload($db);
        }
        if (empty($_POST)) {
            DataBaseAdding::showForm($db);
        } else {
            DataBaseAdding::validateUpload($db);
            DataBaseAdding::showForm($db);
            print "Succesfully uploaded!";
        }
        print "</div>";
        ?>
    </body>
</html>
