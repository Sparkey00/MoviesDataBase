# MoviesDataBase
# in order for this to work you need to:
1. Perform these SQL querries:
CREATE DATABASE movies; 
CREATE TABLE `movies`.`actor_info` ( `actor_id` INT NOT NULL AUTO_INCREMENT ,  `actor_name` VARCHAR(100) NOT NULL ,    PRIMARY KEY  (`actor_id`)) ENGINE = InnoDB;
CREATE TABLE `movies`.`film_info` ( `film_id` INT NOT NULL AUTO_INCREMENT , `film_name` VARCHAR(64) NOT NULL , `film_year` INT NOT NULL , `film_format` VARCHAR(10) NOT NULL , PRIMARY KEY (`film_id`)) ENGINE = InnoDB;
CREATE TABLE `movies`.`film_actor` ( `film_id` INT NOT NULL , `actor_id` INT NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `film_actor` ADD PRIMARY KEY( `film_id`, `actor_id`);
ALTER TABLE `actor_info`DEFAULT  CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `film_info` DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
2. Open db_access.php and set your own login/pass/db_name/host for accessing your db.
3. Start you php-server and go to ../index.php

App on it's own has 2 pages: 
index.php for displaying the table contents and db_add.php for adding aditional movies to the database.
# Main functions
1. Adding movies - go to db_add.php and use either form for manual or automatic adding via .txt file.
2. Deleting films - go to index.php, where you can see button "Delete" In "Delete" column of the table. each button delets it's own movie.
3. Film info is shown on the index.php page in a form of a table.
4. Sorting of films in alphabetical order - click on the collumn name (Title, Year, Format)to sort movies by it.
5. Find the movie by it's or by actor's name - type the name you're seeking in te textbox on top of a page and click "Search" button.
6. Import films to the database through file (see №1)
