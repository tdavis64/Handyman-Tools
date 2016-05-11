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

        <title>Tool Availability</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h1>Tool Availabilty</h1> 

	<!-- query to display a table of available tools -->
	<?php
   	
		session_start();
		$tooltype = $_SESSION['tooltype'];
		$startDate = $_SESSION['startDate'];
		$endDate = $_SESSION['endDate'];

		/* connect to database */
		$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

		if (!$connect)
		{
			die("Failed to connect to database");
		}

		mysql_select_db("cs6400project") or die("Unable to select database");
		$errorMsg = "";
		
		$query = "SELECT 
							    ID, AbbreviatedDescription, DepositAmount, PricePerDay
							FROM
							    tool
							WHERE
							    TypeID = $tooltype
							        AND ID NOT IN (SELECT 
							            rsv.ToolID
							        FROM
							            reserves AS rsv INNER JOIN reservation AS res ON res.Number = rsv.ReservationNumber
							        WHERE
							            '$startDate' BETWEEN res.StartDate AND res.EndDate)
							        AND ID NOT IN (SELECT 
							            ToolID
							        FROM
							            serviceorder AS svc
							        WHERE
							            '$startDate' BETWEEN StartDate AND EndDate)
							        AND ID NOT IN (SELECT 
							            rsv.ToolID
							        FROM
							            reserves AS rsv INNER JOIN reservation AS res ON res.Number = rsv.ReservationNumber
							        WHERE
							            '$endDate' BETWEEN res.StartDate AND res.EndDate)
							        AND ID NOT IN (SELECT 
							            ToolID
							        FROM
							            serviceorder AS svc
							        WHERE
							            '$endDate' BETWEEN StartDate AND EndDate)
							   AND IFNULL(Status, 0) <> 1";
							   
		$result = mysql_query($query);

		if (mysql_num_rows($result) > 0) {
			echo "<table align='center' style='text-align:left; width:100%;' border='1'><tr><th>Tool ID</th><th>Abbr. Description</th><th>Deposit ($)</th><th>Price/Day ($)</th></tr>";
			while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
				echo "<tr><td>" . $row["ID"] . "</td>";
				echo "<td>" . $row["AbbreviatedDescription"] . "</td>";
				echo "<td>" . $row["DepositAmount"] . "</td>";
				echo "<td>" . $row["PricePerDay"] . "</td></tr>";
			}
			echo "</table>";
		} else {
			echo "<p>No tools available!</p>";
		}
            
	?>
            

	<p>________________________________________________________________________</p>
	<form action="ToolAvailability.php" method="post">

	<label class="tool_availabilty_label">Part #</label>

    <input type="text" name="part" class="tool_availabilty_input" />

    <button type="submit">View Details</button>
    
    <?php
			/* connect to database */
			$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

			if (!$connect)
			{
			    die("Failed to connect to database");
			}

			mysql_select_db("cs6400project") or die("Unable to select database");
			$errorMsg = "";

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
			    if (empty($_POST['part']))
			    {
			        $errorMsg = "Please provide a part #.";
			    }
			    else
			    {
			        $part = mysql_real_escape_string($_POST['part']);
			        
			        $query = "SELECT 
												    tool.ID,
												    tool.AbbreviatedDescription,
												    tool.PurchasePrice,
												    tool.DepositAmount,
												    tcat.Name,
												    tool.PricePerDay,
												    tool.FullDescription,
												    tool.Status
												FROM
												    tool INNER JOIN typecategory AS tcat ON tool.TypeID = tcat.TypeID
												WHERE
												    tool.ID = $part";
            	$result = mysql_query($query);
            	
            	if (mysql_num_rows($result) > 0) {
								echo "<table align='center' style='text-align:left; width:100%;' border='1'><tr><th>Tool ID</th><th>Abbr. Description</th><th>Purchase Price</th><th>Deposit Amount</th><th>Name</th><th>Price Per Day</th><th>Full Description</th><th>Status</th></tr>";
								$row = mysql_fetch_row($result, MYSQL_BOTH);
								echo "<tr><td>" . $row["ID"] . "</td>";
								echo "<td>" . $row["AbbreviatedDescription"] . "</td>";
								echo "<td>" . $row["PurchasePrice"] . "</td>";
								echo "<td>" . $row["DepositAmount"] . "</td>";
								echo "<td>" . $row["Name"] . "</td>";
								echo "<td>" . $row["PricePerDay"] . "</td>";
								echo "<td>" . $row["FullDescription"] . "</td>";
								echo "<td>" . $row["Status"] . "</td></tr>";
								echo "</table>";
							} else {
								echo "<p>Part # does not exist!</p>";
							}
							
							$query = "SELECT 
												    AccessoryName
												FROM
												    powertool
												WHERE
												    ToolID = $part";
            	$result = mysql_query($query);
            	if (mysql_num_rows($result) > 0) {
            		echo "Accessory List: ";
            		$first = true;
								while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
									if (!$first) {
										echo ", ";
									}
									echo $row["AccessoryName"];
									$first = false;
								}
							}
			    }
			}
        
		?>

    <br>
    <br>

	</form>
	
  <button type="submit" style="width: 200px;" class="clerkMainMenu" onclick="location.href = 'CustMainMenu.php';">Back to Main</button>

	</body>
	
</html>