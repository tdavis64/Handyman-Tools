<?php
	/* connect to database */
	$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

	if (!$connect)
	{
		die("Failed to connect to database");
	}

	mysql_select_db("cs6400project") or die("Unable to select database");
	$errorMsg = '';
	$clerkName = '';
	$customerName = '';
	$creditCardNumber = '';
	$startDate = '';
	$endDate = '';

	session_start();
	$reservationNumber = $_SESSION['reservationNumber'];

	if (empty($reservationNumber)) {
		$errorMsg = "Session error: Reservation number unknown.";
	} else {
		$query = "SELECT 
							    res.Number,
							    res.StartDate,
							    res.EndDate,
							    dClk.FirstName AS clkFirstName,
							    dClk.LastName AS clkLastName,
							    res.CreditCardNumber,
							    cus.FirstName,
							    cus.LastName
							FROM
							    reservation AS res
							        INNER JOIN
							    clerk AS dClk ON res.DropOffClerkLogin = dClk.Login
							        INNER JOIN
							    customer AS cus ON res.CustomerLogin = cus.Login
							WHERE
							    res.Number = $reservationNumber";
							    
		$result = mysql_query($query);
		
		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_row($result, MYSQL_BOTH);
			$clerkName = $row['clkFirstName'] . ' ' . $row['clkLastName'];
			$customerName = $row['FirstName'] . ' ' . $row['LastName'];
			$creditCardNumber = $row['CreditCardNumber'];
			$startDate = $row['StartDate'];
			$endDate = $row['EndDate'];
		} else {
			$errorMsg = "Query error: Reservation number not found.";
		}
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Rental Receipt</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h2>
		<i>
			HANDYMAN TOOLS RENTAL CONTRACT
		</i>
	</h2> 

	<b><label class="rental_contract_label">Reservation Number:</label></b>
	<?php echo $reservationNumber ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><label class="rental_contract_label">Clerk on Duty:</label></b>
	<?php echo $clerkName ?>
	<br>
	<br>

	<b><label class="rental_contract_label">Customer Name:</label></b>
	<?php echo $customerName ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b> <label class="rental_contract_label">Credit Card Number:</label></b>
	<?php echo $creditCardNumber ?>
    <br>
	<br>

	<b><label class="rental_contract_label">Start Date:</label></b>
	<?php echo $startDate ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b> <label class="rental_contract_label">End Date:</label></b>
    <?php echo $endDate ?>

    <br>
    <br>

    <b><label class="rental_contract_label">Tools Rented:</label></b>

    <br>
    <br>

    <!-- indert code to populate a list of rented tools -->
    <?php
    
	    $TotalRentalPrice = 0;
			$TotalDepositRequired = 0;
			$GrandTotal = 0;
		
			if (empty($reservationNumber)) {
				$errorMsg = "Session error: Reservation number unknown.";
			} else {    	
					$query = "SELECT
												StartDate,
												EndDate
										FROM
												reservation
										WHERE
												Number = $reservationNumber";
					$result = mysql_query($query);
					
					if (mysql_num_rows($result) > 0) {
						$row = mysql_fetch_row($result, MYSQL_BOTH);
						
						$date1 = new DateTime($row['StartDate']);
						$date2 = new DateTime($row['EndDate']);

						$interval = $date1->diff($date2);
						$daysBetween = $interval->d;

						$query2 = "SELECT 
											    tool.ID,
											    tool.AbbreviatedDescription,
											    tool.PricePerDay,
											    tool.DepositAmount
											FROM
											    reserves AS rsv
											        INNER JOIN
											    tool ON rsv.ToolID = tool.ID
											WHERE
											    rsv.ReservationNumber = $reservationNumber
											ORDER BY ID";
											
		        $result2 = mysql_query($query2);
		        
		        if (mysql_num_rows($result2) > 0) {
							echo "<table style='text-align:left;' border='0' class='centerTable'>";
							while ($row2 = mysql_fetch_row($result2, MYSQL_BOTH)) {
								echo "<tr><td>" . $row2["ID"] . ".  " . $row2["AbbreviatedDescription"] . "</td></tr>";
								$TotalRentalPrice += ($row2["PricePerDay"] * $daysBetween);
								$TotalDepositRequired += $row2["DepositAmount"];
							}
							$GrandTotal = $TotalRentalPrice - $TotalDepositRequired;
							echo "</table>";
						} else {
							$errorMsg = "Reservation number not found.";
						}
						
					} else {
						$errorMsg = "Reservation number not found.";
					}				
    	}
    	mysql_close($connect);
    	
    ?>

    <br>
    <br>

    <b><label class="rental_contract_label">Rental Price:</label></b>
		<?php echo "$" . number_format($TotalRentalPrice, 2) ?>
    <br>
    <br>

    <b><label class="rental_contract_label">Deposit Held:</label></b>
    <?php echo "- $" . number_format($TotalDepositRequired, 2) ?>

    <br>
    <br>

		<p>----------------------------------------------------</p>
    <b><label class="rental_contract_label">Total:</label></b>
    <?php echo "$" . number_format($GrandTotal, 2) ?>
		<br>
		<br>
		<button type="submit" class="clerkMainMenu" onclick="location.href = 'ClerkMainMenu.php';">Main Menu</button>

	<?php
    if (!empty($errorMsg)) {
      print "<div style='color:red'>$errorMsg</div>";
    }
   ?>
   
	</body>
	
</html>