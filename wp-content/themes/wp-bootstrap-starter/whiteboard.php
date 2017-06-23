<?php
error_reporting( 2 );
//session_start();
$servername = "vdb1b.pair.com";
$username = "working_39";
$password = "EsQBeq3E";
$dbname = "working_examples";
$table = "whiteboard"; 
// Create connection
$conn = mysqli_connect( $servername, $username, $password );
// Check connection
if ( !$conn ) {
    die( "Connection failed: " . mysqli_connect_error() );
    echo( "Connection failed: " . mysqli_connect_error() );
    echo "<br>";
}
$db = mysqli_select_db( $conn, $dbname );

if ( !$db ) {
    die( "Connection failed: " . mysqli_connect_error() );
    echo "<br>";
}
$sql = "SELECT * FROM " . $table;
$sql .= " ORDER BY RAND()";
$result = $conn->query( $sql );

if ( $result->num_rows > 0 ) {
    echo '<div class="example-column">';
    $x = 1;
while($row = $result->fetch_assoc()) {
    $target = $row[ "target" ];
    $video = $row[ "name" ];
    echo '<div class="col-sm-6">';
    echo PHP_EOL;
    echo '<div class="embed-responsive embed-responsive-16by9 box">';
    echo PHP_EOL;
    echo '<iframe class="embed-responsive-item" src="//www.youtube.com/embed/' . $target . '" frameborder="0" allowfullscreen"></iframe>';
    echo PHP_EOL;
    echo '</div>';
    echo PHP_EOL;
    echo '<h3 class="demo">' .$video . '</a></h3>'; 
    echo PHP_EOL;
    echo '</div>';
    echo PHP_EOL;
    $x = $x + 1;
    if ( $x == 7) {
        break;
    }
     }
} else {
     echo "0 results";
}
echo PHP_EOL;
echo '</div>';
echo '<div class="c"></div>';
?>