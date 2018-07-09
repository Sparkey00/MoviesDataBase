<?php
require_once 'db_access.php';
class DataBaseAdding
{
   public static function showForm($db, $values = [], $errors = []) {
        $film_name = $values['film_name'] ?? '';
        $film_year = $values['film_year'] ?? '';
        $film_format = $values['film_format'] ?? '';
        $film_actors = $values['film_actors'] ?? '';

        
        $form = <<<_FORM
                <table class=\"responsive-table\">
   <thead>
      <tr>
        <th scope="col">You can either submit all the info manually...</th>
        <th scope="col">..or give us the file with info, and we'll do everything ourselves :)</th>
       </tr>
    </thead>
       <tr>
           <td>
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            Title of the film: </br><input type="text" name="film_name" value="$film_name" required="Please, enter the name of the film!"></br>
            Release Year:</br> <input type="number" maxlength="4" name="film_year" value="$film_year" required="Please, enter the release year!"></br>
            Format:</br> <select size="1" single name="film_format">    
                <option value="VHS">VHS</option>
                <option selected value="DVD">DVD</option>
                <option value="Blu-Ray">Blu-Ray</option>    
            </select></br>
            List of Actors: </br><textarea name="film_actors" value="$film_actors" cols="24" rows="3" required> </textarea></br>
            <button  class="btn btn-info my-2 my-sm-0" type="submit" value="Add Movie">Add Movie!</button>
        </form>
</td><td>
<form action="{$_SERVER['PHP_SELF']}" method="post" enctype="multipart/form-data">
<input class="btn btn-info my-2 my-sm-0" type="file" required name="upload" value="">
<button  class="btn btn-info my-2 my-sm-0" type="submit" value="Add File">Upload File!</button></form>
</td></tr></table>
_FORM;
        print $form ;

        if ($errors) {
            print "Correct errors below and try again: </br>";
            foreach ($errors as $key => $value) {
                print $value . "</br>";
            }
        }
    }

    public static function fileupload($db) {
        $newFilename = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
        $uploadInfo = $_FILES['upload'];
        switch ($uploadInfo['type']) {
            case 'text/plain':
                $newFilename .= '.txt';
                break;

            default:
                echo 'This file type is not supported :(';
                exit;
        }


        if (!move_uploaded_file($uploadInfo['tmp_name'], $newFilename)) {
            echo 'File saving wasn\'t successful :(';
        }
        self::fileParse($newFilename, $db);
    }

    private static function fileParse(string $filename, $db) {
        $stringOfFilms = file_get_contents($filename);
        preg_match_all("#Title: (.+\n)Release Year: (.+\n)Format: (.+\n)Stars: (.+\n)#", $stringOfFilms, $matches);
        array_shift($matches);
        $result;
        for ($i = 0; $i < count($matches[0]); $i++) {
            $result[$i]['film_name'] = htmlentities($matches[0][$i]);
            $result[$i]['film_year'] = htmlentities($matches[1][$i]);
            $result[$i]['film_format'] = htmlentities($matches[2][$i]);
            $result[$i]['film_actors'] = htmlentities($matches[3][$i]);
        }
        foreach ($result as $values) {
            self::validateUpload($db, $values);
        }
        
        unset($_FILES['upload']['tmp_name']);
    }

    public static function validateUpload($db, $values = [], $errors = []) {
        if ($_POST) {
            $values = $_POST;
        } else
            $values = $values;

        foreach ($values as $key => $value) {
            $values[$key] = trim(htmlentities($value));
            if (strlen(trim($values[$key])) == 0)
                $errors[] = "Field $key is empty.";
        }
        if($values['film_year']>(date('Y')+1)||$values['film_year']<1895)//1895 - release year of "L'Arrivée d'un train en gare de la Ciotat"
        {$errors[]="Check the release year of {$values['film_name']} ";}
        $film_name = $values['film_name'];
        $stmt = $db->prepare("SELECT * FROM film_info WHERE film_name =\"$film_name\"");
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result))
        {$errors[] = "Film with that name already exists";}
        
        if (empty($errors))
            self::executeUpload($db, $values);
        else {
            self::showForm($db, $values, $errors);
        }
    }

    private static function executeUpload($db, $values) {
        $film_name = $values['film_name'];
        $film_year = $values['film_year'];
        $film_format = $values['film_format'];
        $statement = $db->prepare("INSERT INTO film_info (film_name, film_year, film_format)VALUES(\"$film_name\",\"$film_year\",\"$film_format\")");
        $statement->execute();
        $actors = explode(", ", $values['film_actors']);
        $statement = $db->prepare("SELECT film_id FROM film_info WHERE film_name = \"$film_name\"");
        $statement->execute();
        $film_id = $statement->fetchAll();
        $film_id = $film_id[0]['film_id'] ?? null;
        foreach ($actors as $actor) {
            $statement = $db->prepare("SELECT actor_id FROM actor_info WHERE actor_name = \"$actor\"");
            $statement->execute();
            $actor_id = $statement->fetchAll();
            $actor_id = $actor_id[0]['actor_id'] ?? null;
            if (!$actor_id) {
                $statement = $db->prepare("INSERT INTO actor_info (actor_name) VALUES (\"$actor\")");
                $statement->execute();
                $statement = $db->prepare("SELECT actor_id FROM actor_info WHERE actor_name = \"$actor\"");
                $statement->execute();
                $actor_id = $statement->fetchAll();
                $actor_id = $actor_id[0]['actor_id'];
                $statement = $db->prepare("INSERT INTO film_actor (film_id, actor_id) VALUES(\"$film_id\",\"$actor_id\")");
                $statement->execute();
            } else {
                $statement = $db->prepare("INSERT INTO film_actor (film_id, actor_id) VALUES(\"$film_id\",\"$actor_id\")");
                $statement->execute();
            }
        }        
        print "Succesfully uploaded!";
        header ("location: {$_SERVER['PHP_SELF']}");
    }

}

class DataBaseResults {

    public static function sorting($db, $by = "") {
        $stmt = $db->prepare("SELECT fi.film_name, fi.film_format,fi.film_year,fi.film_id, group_concat(actor_name) FROM film_info fi INNER JOIN film_actor using(film_id) INNER JOIN actor_info using(actor_id) group by film_id ORDER BY fi.$by ASC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        self::showMovies($db, $result);
    }

    public static function deletion($db, $delete) {
        $stmt = $db->prepare("DELETE FROM film_info WHERE film_info.film_id = $delete");
        $stmt->execute();
        //$result = $stmt->fetchAll();
        self::showMovies($db);
    }

    public static function searching($db, $search,$aj=false) {
        $search=ucwords(mb_strtolower($search));        
        $search = "'%" . $search . "%'";
        $stmt = $db->prepare("SELECT fi.film_name, fi.film_format, fi.film_year, fi.film_id, group_concat(actor_name) FROM film_info fi INNER JOIN film_actor USING(film_id) INNER JOIN actor_info USING(actor_id) WHERE fi.film_name LIKE $search GROUP BY film_id ");
        $stmt->execute();
        $result = $stmt->fetchAll();
         
            if (isset($result[0]['film_name'])) {
               if(!$aj) self::showMovies($db, $result);
               else return $result;
            } else {
                $stmt = $db->prepare("SELECT fi.film_name, fi.film_format, fi.film_year, fi.film_id, group_concat(actor_name) FROM film_info fi INNER JOIN film_actor USING(film_id) INNER JOIN actor_info USING(actor_id) WHERE actor_info.actor_name LIKE $search GROUP BY film_id ");
                $stmt->execute();
                $result = $stmt->fetchAll();
                if(!$aj) self::showMovies($db, $result);
                else return $result;
                
            }
        
        
    }

    public static function showMovies($db, $result = '',$aj=false) {
        if ($result === '') {
            $stmt = $db->prepare("SELECT fi.film_name, fi.film_format,fi.film_year,fi.film_id, group_concat(actor_name) FROM film_info fi INNER JOIN film_actor using(film_id) INNER JOIN actor_info using(actor_id) group by film_id");
            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        $ajaxreply = "";

        foreach ($result as $key => $array) {

            $ajaxreply .= "<tr>";
            $ajaxreply .= "<th scope=\"row\"> {$array['film_name']}</th>";
            $ajaxreply .= "<td> {$array['film_year']}</td>";
            $ajaxreply .= "<td> {$array['film_format']}</td>";
            $ajaxreply .= "<td>" . str_replace(",", ", ", $array['group_concat(actor_name)']) . "</td>";
            $ajaxreply .= "<td><button class=\"btn btn-info my-2 my-sm-0\" type=\"submit\" name=\"delete\" value =\"{$array['film_id']}\">Delete</button></td>";

            $ajaxreply .= "</tr>";
        }
        $ajaxreply .= "</table></form>";
        if ($aj)
        {return $ajaxreply;}
        else {
            echo $ajaxreply;
        }
    }

}
