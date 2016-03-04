<!doctype html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name=author content="Elizabeth Hau and Emily Cetlin">
    <title>WMDB Search</title>
    <style> /* CSS for styling */

body {
  font-family: Verdana, Geneva, sans-serif;
  padding:2em;
  background-color: #D4EDFF; /* light blue*/
}

a:hover { 
    color: #3399FF;

}

 
input[type=text]:focus, textarea:focus {
  box-shadow: 0 0 5px rgba(81, 203, 238, 1);
  padding: 3px 3px 3px 3px;
  margin: 5px 3px 3px 3px;
  border: 1px solid rgba(81, 203, 238, 1);
}

.centerDiv { 
  width: 90%; 
  text-align: center;
  margin: 0 auto; 
  height:150px;
} 

#searchDiv  {
    margin: 10px 10px;
    border: 2px solid blue;
    width: 90%;
    border-radius: 10px;
    text-align: center;
    position:relative;
    padding: 1em;        
    background-color: #FFFF71; /* yellow */
    top: 0; 
    left: 0; 
    bottom: 0; 
    right: 0;
}

#resultDiv {
  margin: 10px 10px;
    border: 2px solid blue;
    width: 90%;
    border-radius: 10px;
  text-align: center;
  position:relative;
    padding: 1em;        
    background-color: #F3F6FF; /* very light blue */
    top: 0; 
    left: 0; 
    bottom: 0; 
    right: 0;
}


</style>
</head>
<body>
<div id = "whole_page">
<div class = "centerDiv">
<h2>Welcome to the Wellesley Movie Database (WMDB)</h2>
<p>By Emily Cetlin and Elizabeth Hau<br>
<p>This is the Wellesley Movie Database
<p>Enter a search item below to see what data we have
</div>
 <div id="searchDiv">
<h2>Search the WMDB</h2>
<form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<select name="tables">
    <div class = 'shadow'> <option value="both">Both
          <option value="titles">Titles
    <option value="names">Names</div>
     </select>
     <br>
<div>
     <input type="text" name="sought"> <br>
     <input type="submit" value="GO!">
</div>
</form>
</div> <!-- close searchDiv-->

<div id = "resultDiv">
<?php
// The following loads the Pear MDB2 class and our functions
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");

// The following defines the data source name (username, password,
// host and database).
require_once('wmdb-dsn.inc');

// The following connects to the database, returning a database handle (dbh)
$dbh = db_connect($wmdb_dsn);

// Checks if there is an entry in the textbox or a specific nm or tt is given
if(isset($_GET['sought'])){
    $choice = $_GET['tables']; // option selected by user (names, titles, or both)
    $result = $_GET['sought']; // user entry
    
    if($choice == "names"){

      fetchnames($result);

    } elseif($choice=="titles") {

      fetchtitles($result);

    } elseif($choice=="both") {
      
      fetchtitles($result);
      fetchnames($result);
 
	 }
} elseif (isset($_GET['nm'])){

  	 $name = $_GET['nm'];

     fetchnm($name);

} elseif (isset($_GET['tt'])) {
    $title = $_GET['tt'];
    fetchtt($title);
} 
else {
  echo"<p>The results from the search will be displayed here";
}


/* This function searches for names in the WMDB database that are like that of the user's input.
 * The function takes in a parameter, $result, that is the user's input into the textbox.
 */
function fetchnames($result){
   global $dbh;
   global $choice;
   $sql = "SELECT nm, name,birthdate FROM person WHERE name like ?";
   $values=array("%".$result."%");

   // This executes the query. We supply the DBH and the query string. It
  // gives us back a resultset object.
   $resultset = prepared_query($dbh,$sql,$values);
   $num_names = $resultset -> numRows();


     echo "<h3>".$num_names." name(s) found</h3>"; // displays the number of matches found

     if($num_names == 0 && $choice == "names") {
      echo "<p>Sorry, there are no $choice that match '$result' in the WMDB";
     }

  // The resultset object has a fetchRow method that can give us back the
  // next row of the resultset, stored in an associative array with
  // keys named for the columns we asked for. 
   while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
     $name = $row['name'];
     $birthdate = $row['birthdate'];
     $nm=$row['nm'];;

     // if there is only one match, display full information
     if($num_names == 1 && $choice=="names") {
        fetchnm($nm); // displays the information
     } else {
          // if there is more than one match, display in a compact way with hyperlinks that lead to full display
          echo "<p><a href=http://cs.wellesley.edu".$_SERVER['PHP_SELF']."?nm=$nm>$name ($birthdate)</a>";
     }

}
}

/* This function searches for titles in the WMDB database that are like that of the user's input.
 * The function takes in a parameter, $result, that is the user's input into the textbox.\
 * This function works similarly to the fetch names function above.
 */
function fetchtitles($result){
 global $dbh;
 global $choice;
  $sql = "SELECT tt,title,`release`FROM movie WHERE title like ?";
  $values=array("%".$result."%");


 $resultset = prepared_query($dbh,$sql,$values);
 $num_titles = $resultset -> numRows();

 echo "<h3>".$num_titles." movie(s) found</h3>";
 if($num_titles == 0 && $choice == "titles") {
      echo "<p>Sorry, there are no $choice that match '$result' in the WMDB";
  }

 while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
  $title=$row['title'];
	$release=$row['release'];
	$tt=$row['tt'];
  
  if($num_titles == 1 && $choice=="titles") {
    fetchtt($tt); // displays the movie information
  } else {
    echo "<p><a href=http://cs.wellesley.edu".$_SERVER['PHP_SELF']."?tt=$tt>$title ($release)</a>";
  }
	

 }

}

/* This function searches for one actor in the WMDB database that is exactly the same as the input.
 * The function takes in a parameter, $nm, that is a name the user has clicked on.
 */
function fetchnm($nm){
  global $dbh;
  $name_nm = $nm;
  $sql = "SELECT nm,name,birthdate FROM person WHERE nm = ?";

  $values = array($name_nm);
  $resultset = prepared_query($dbh,$sql,$values);
  $numRows = $resultset -> numRows();
  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    $name=$row['name'];
    $birthdate=$row['birthdate'];

    echo "<h2>$name</h2>
          <p>Born on $birthdate
          <p>Filmography:";
          fetchFilmography($name_nm); // used to display the movies each actor has been in
    echo "<p>Here's the real <a href=http://www.imdb.com/name/nm".$name_nm.">IMDB entry for $name</a>";
  }
}

/* This fuction searches for all of the movies an actor has been in. The fuction takes in the parameter
 * $nm, which tells us which actor to search for. 
 */
function fetchFilmography($nm){
  global $dbh;
  
  $name=$nm;
  
  $credit = "SELECT tt,title, `release` from movie where tt in (select tt from credit where nm=?)";
  $values = array($name);
  $resultset = prepared_query($dbh,$credit,$values);

  while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
    
    $title=$row['title'];
    $release=$row['release'];
    $tt=$row['tt'];
    echo "<p><a href=http://cs.wellesley.edu".$_SERVER['PHP_SELF']."?tt=$tt>$title ($release)</a>";
  }
}

/* This function searches for one movie in the WMDB database that is exactly the same as the input.
 * This function takes in one parameter, $tt, that is the title of the movie the user has clicked on.
 */
function fetchtt($tt){
  global $dbh;
  $title_tt=$tt;
  // fetches the movie along with the name of the director (instead of the directors id number)
  $sql = "SELECT tt, title, `release`, director,
          (select name from person,movie where person.nm=movie.director and tt = ?) as director_name 
          from movie WHERE tt = ?";
  $values = array($title_tt,$title_tt);
  $resultset = prepared_query($dbh,$sql,$values);

  $numRows = $resultset -> numRows();

  while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {

    $title=$row['title'];
    $release = $row['release'];
    $director_nm=$row['director'];
    $director_name = $row['director_name']; // name of the director
    //displays unknown if there is no director listed.
    if($director_nm == NULL) {
      echo "<h2>$title ($release)</h2>
          <p>Director Unknown
          <p>Cast:";
          fetchCredits($title_tt);
      echo "<p>Here's the real <a href=http://www.imdb.com/title/tt".$title_tt.">IMDB entry for $title</a>";
    } else {
      echo "<h2>$title ($release)</h2>
          <p>Directed by <a href=http://cs.wellesley.edu".$_SERVER['PHP_SELF']."?nm=$director_nm>$director_name</a>
          <p>Cast:";
          fetchCredits($title_tt);
      echo "<p>Here's the real <a href=http://www.imdb.com/title/tt".$title_tt.">IMDB entry for $title</a>";
    }
    
  }
}

/* This function searches the WMDB database for the actors that appear in a movie. 
 * The function takes in $tt, indicating which movie we are looking at.
 */
function fetchCredits($tt) {
  global $dbh;
  $title=$tt;

  $credit = "SELECT nm, name from person where nm in (select nm from credit where tt=?)";
  $values = array($title);
  $resultset = prepared_query($dbh,$credit,$values);
  $numRows = $resultset -> numRows();

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
    
    $name=$row['name'];
    $nm=$row['nm'];

    echo "<p><a href=http://cs.wellesley.edu".$_SERVER['PHP_SELF']."?nm=$nm>$name</a>";
  }
}

?> </div> 
</body>
</html>