<?php
ob_start();

$connection = mysqli_connect("localhost","root","","dooble");
if(mysqli_connect_error()){
    die("Error: Unable to connect".mysqli_connect_error());    
}
