<?php
/* connect to database */
$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

if (!$connect)
{
	die("Failed to connect to database");
}

mysql_select_db("cs6400project") or die("Unable to select database");
$errorMsg = "";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

        <title>Customer Main Menu</title>
        <link rel="stylesheet" type="text/css" href="style.css" />

</head>
	<body style="text-align:center;">
	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

    <form action="CustMainMenu.php" method="post">
	<h1>Main Menu</h1> 
    	<p>
    		<button type="submit" style="width:170px" class="custMainMenu" formaction="Profile.php">View Profile</button>
    	</p>
    	
    	<p>	
    		<button type="submit" style="width:170px" class="custMainMenu" formaction="CheckAvailability.php">Check Tool Availability</button>
    	</p>

    	<p>
    		<button type="submit" style="width:170px" class="custMainMenu" formaction="MakeReservation.php">Make Reservation</button>
    	</p>	

    	<p>
    		<button type="submit" style="width:170px" class="custMainMenu" formaction="login.php">Exit</button>
    	</p>

	</body>
    </form>

</html>	