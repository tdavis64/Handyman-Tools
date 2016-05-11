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

        <title>Clerk Main Menu</title>
        <link rel="stylesheet" type="text/css" href="style.css" />

</head>
	<body style="text-align:center;">
	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>
	<h1>Main Menu</h1> 

		<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'PickUp.php';">Pick-Up Reservation</button>
    	</p>
    	
    	<p>	
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'DropOff.php';">Drop-Off Reservation</button>
    	</p>

    	<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'ServiceOrder.php';">Service Order</button>
    	</p>	

    	<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'AddNewTool.php';">Add New Tool</button>
    	</p>

    	<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'SellTool.php';">Sell Tool</button>
    	</p>

    	<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'GenerateReports.php';">Generate Reports</button>
    	</p>

    	<p>
    		<button type="submit" class="clerkMainMenu" style="width: 150px;" onclick="location.href = 'login.php';">Exit</button>
    	</p>
	</body>

</html>