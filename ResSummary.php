<?php
/* connect to database */
$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

if (!$connect)
{
	die("Failed to connect to database");
}

mysql_select_db("cs6400project") or die("Unable to select database");
$errorMsg = "";

/* Get all variables that are in session from MakeReservation */
session_start();
$startingDate = isset($_SESSION['startingDate'])?$var=mysql_escape_string($_SESSION['startingDate']):$var='01/01/1900';
$endingDate = isset($_SESSION['endingDate'])?$var=mysql_escape_string($_SESSION['endingDate']):$var='01/01/1900';
$ToolIDs = isset($_SESSION['selectedTool'])?$var=mysql_escape_string($_SESSION['selectedTool']):$var='0';  /* Comma separated list of ToolIDs that will need to be parsed */
/* Get session variable login for current customer */
$login = $_SESSION['login'];


$date1 = new DateTime($startingDate);
$date2 = new DateTime($endingDate);

$interval = $date1->diff($date2);
$daysBetween = $interval->d;

//$first = true;
//$ToolIDs = '';

//foreach ($selectedTool as $a => $b) {
//	if ($first) {
//		$ToolIDs .= $selectedTool[$a];
//		$first = false;
//	} else {
//		$ToolIDs .= ',' . $selectedTool[$a];
//	}
//}



/* Using the list of Tool IDs, get the abbreviated name, price per day, and deposit */
/* Calculate the total rental price and total deposit required for all the tools */
$TotalRentalPrice = 0;
$TotalDepositRequired = 0;

/* When submit button is pressed, create the reservation and then pass that reservation number to the ReservationFinal.php page */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>
	<title>Reservation Summary</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body style="text-align: center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h2>Reservation Summary</h2> 

	<h3>Tools Desired</h3>

	<!-- insert code to display a list of desired tools -->
	<?php
	
	      $query = "SELECT ID, AbbreviatedDescription, PricePerDay, DepositAmount FROM tool WHERE ID IN (" . $ToolIDs . ") ORDER BY ID";
        $result = mysql_query($query);
        
        if (mysql_num_rows($result) > 0) {
					echo "<table style='text-align:left;' border='0' class='centerTable'>";
					while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
						echo "<tr><td>" . $row["ID"] . ".  " . $row["AbbreviatedDescription"] . "</td></tr>";
						$TotalRentalPrice += ($row["PricePerDay"] * $daysBetween);
						$TotalDepositRequired += $row["DepositAmount"];
					}
					echo "</table>";
				} else {
					echo "<p>Error!</p>";
				}
				
				mysql_close($connect);
	?>	

	<table class="centerTable" style="text-align: left;">
		<tr>
			<td><h3>Start Date</h3></td>
			<td><?php echo date_format($date1, 'm/d/Y') ?></td>
		</tr>
		<tr>
			<td><h3>End Date</h3></td>
			<td><?php echo date_format($date2, 'm/d/Y') ?></td>
		</tr>
		<tr>
			<td><h3>Total Rental Price</h3></td>
			<td><?php echo "$" . number_format($TotalRentalPrice, 2) ?></td>
		</tr>
		<tr>
			<td><h3>Total Deposit Required</h3></td>
			<td><?php echo "$" . number_format($TotalDepositRequired, 2) ?></td>
		</tr>
	</table>

	<p>
		<form id="select_form" action="ReservationFinal.php" method="post">
			<button type="submit" style="width:150px;" class="resSummary">Submit</button>
			<input type="button" style="width:150px;" onclick="location.href = 'MakeReservation.php';" value="Reset" />
		</form>
	</p>

	</body>
	
</html>