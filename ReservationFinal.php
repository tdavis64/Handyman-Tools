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
	$ToolIDarray = explode(",", $ToolIDs);
	/* Get session variable login for current customer */
	$login = $_SESSION['login'];


	$date1 = new DateTime($startingDate);
	$date2 = new DateTime($endingDate);

	$interval = $date1->diff($date2);
	$daysBetween = $interval->d;
	
	$reservationNumber = "";

	/* Check if any of the tools are already reserved or in service */
 	 $query = "SELECT 
						    ID, AbbreviatedDescription
						FROM
						    tool
						WHERE
							ID IN ($ToolIDs)
						    AND (ID IN (SELECT 
						            ToolID
						        FROM
						            reserves AS rsv INNER JOIN reservation AS res ON rsv.ReservationNumber = res.Number
						        WHERE
						            '$startingDate' BETWEEN StartDate AND EndDate)
						        OR ID IN (SELECT 
						            ToolID
						        FROM
						            serviceorder AS svc
						        WHERE
						            '$startingDate' BETWEEN StartDate AND EndDate)
								OR ID IN (SELECT 
						            ToolID
						        FROM
						            reserves AS rsv INNER JOIN reservation AS res ON rsv.ReservationNumber = res.Number
						        WHERE
						            '$endingDate' BETWEEN StartDate AND EndDate)
						        OR ID IN (SELECT 
						            ToolID
						        FROM
						            serviceorder AS svc
						        WHERE
						            '$endingDate' BETWEEN StartDate AND EndDate))";
						            
	 $result = mysql_query($query);
	 if(mysql_num_rows($result) > 0) {
			$errorMsg .= "Reservation unsuccessful.  The following Tool IDs have an existing service order or reservation during the start and end dates requested: ";
			while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
					$errorMsg .= $row['ID'] . ". " . $row['AbbreviatedDescription'] . " ";
				}
	}

	if(empty($errorMsg)) {
		$query = "INSERT INTO reservation
						(StartDate, EndDate, CustomerLogin)
						VALUES ('$startingDate', '$endingDate', '$login')";

		$result = mysql_query($query);

		if(!$result) {
			$errorMsg .= mysql_error();
		}

		$reservationNumber = mysql_insert_id();

		foreach ($ToolIDarray as &$value) {
		 	 $query = "INSERT INTO reserves
								(ToolID, ReservationNumber)
								VALUES ($value, $reservationNumber)";
			 $result = mysql_query($query);
			 if(!$result) {
					$errorMsg .= mysql_error();
			}
		} 
	}

	/* Using the list of Tool IDs, get the abbreviated name, price per day, and deposit */
	/* Calculate the total rental price and total deposit required for all the tools */
	$TotalRentalPrice = 0;
	$TotalDepositRequired = 0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Reservation Final</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h2>Your Reservation Number is:</h2>
	<?php echo $reservationNumber ?>

	<!-- insert code to dispay reservation number of newly created reservation record -->

	<h3>Tools Rented</h3>

	<!-- insert code to display a list of desired tools -->
	<?php
			if(empty($errorMsg)) {
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
			} else {
				echo "No tools rented";
			}
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
		<input type="button" onclick="location.href = 'CustMainMenu.php';" value="Back to Main Menu" />
  </p>
	<?php
    if (!empty($errorMsg)) {
      print "<div style='color:red'>$errorMsg</div>";
    }
     ?>       

	</body>
	
</html>