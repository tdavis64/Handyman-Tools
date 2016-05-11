<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <title>Clerk-of-the-Month Report 3</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body style="text-align:center;">
	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>
	<h1>Clerk-of-the-Month Report 3</h1> 

	<?php
	/* connect to database */
	$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

	if (!$connect)
	{
		die("Failed to connect to database");
	}

	mysql_select_db("cs6400project") or die("Unable to select database");
	$errorMsg = "";
	
	$MonthStart = date('Y-m-01');
	$MonthEnd  = date('Y-m-t');
	
	/*
	* echo "Month Start: " . $MonthStart;
	* echo "Month End: " . $MonthEnd;
	*/

	$query = "SELECT 
						    clk.Login,
						    clk.FirstName,
						    clk.LastName,
						    IFNULL(pUp.PickupCount, 0) AS PickupCnt,
						    IFNULL(dOff.DropOffCount, 0) AS DropOffCnt,
						    (IFNULL(pUp.PickupCount, 0) + IFNULL(dOff.DropOffCount, 0)) AS TotPickupsDropOffs
						FROM
						    clerk AS clk
						        LEFT JOIN
						    (SELECT 
						        rsv.PickupClerkLogin,
						            COUNT(rsv.PickupClerkLogin) AS PickupCount
						    FROM
						        reservation AS rsv
						    WHERE
						        rsv.StartDate BETWEEN CAST('$MonthStart' AS DATE) AND CAST('$MonthEnd' AS DATE)
						    GROUP BY rsv.PickupClerkLogin) AS pUp ON clk.Login = pUp.PickupClerkLogin
						        LEFT JOIN
						    (SELECT 
						        rsv.DropOffClerkLogin,
						            COUNT(rsv.DropOffClerkLogin) AS DropOffCount
						    FROM
						        reservation AS rsv
						    WHERE
						        rsv.EndDate BETWEEN CAST('$MonthStart' AS DATE) AND CAST('$MonthEnd' AS DATE)
						    GROUP BY rsv.DropOffClerkLogin) AS dOff ON clk.Login = dOff.DropOffClerkLogin
						WHERE
							(IFNULL(pUp.PickupCount, 0) + IFNULL(dOff.DropOffCount, 0)) <> 0
						ORDER BY (IFNULL(pUp.PickupCount, 0) + IFNULL(dOff.DropOffCount, 0)) DESC , clk.Login";
						    
						$result = mysql_query($query);

	if (mysql_num_rows($result) > 0) {
		echo "<table style='text-align:left;width:100%' border='1' class='centerTable'><tr><th>Login</th><th>First Name</th><th>Last Name</th><th>Pick-Ups</th><th>Drop-Offs</th><th>Total</th></tr>";
		while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
			echo "<tr><td>" . $row["Login"] . "</td>";
			echo "<td>" . $row["FirstName"] . "</td>";
			echo "<td>" . $row["LastName"] . "</td>";
			echo "<td>" . number_format($row["PickupCnt"]) . "</td>";
			echo "<td>" . number_format($row["DropOffCnt"]) . "</td>";
			echo "<td>" . number_format($row["TotPickupsDropOffs"]) . "</td></tr>";
		}
		echo "</table>";
	} else {
		echo "<p>Error!</p>";
	}


	mysql_close($connect);

	?>
	
	<p>
		<input type="button" class="clerkMainMenu" onclick="location.href = 'GenerateReports.php';" value="Report Menu" />
	</p>
		
</body>

</html>