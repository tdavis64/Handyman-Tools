<?php
	/* connect to database */
	$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

	if (!$connect)
	{
		die("Failed to connect to database");
	}

	mysql_select_db("cs6400project") or die("Unable to select database");
	$errorMsg = "";
 	$toolDetails = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['btnViewToolDetails'])) {
			if (empty($_POST['toolID'])) {
				$errorMsg = "Please fill in a Tool ID before trying to view details.";
			} else {
				/* Display the same data as found on the ToolAvailability page */
				
				$part = mysql_real_escape_string($_POST['toolID']);
        
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
					$toolDetails = "<table align='center' style='text-align:left;width:100%' border='1'><tr><th>Tool ID</th><th>Abbr. Description</th><th>Purchase Price</th><th>Deposit Amount</th><th>Name</th><th>Price Per Day</th><th>Full Description</th><th>Status</th></tr>";
					$row = mysql_fetch_row($result, MYSQL_BOTH);
					$toolDetails .= "<tr><td>" . $row["ID"] . "</td>";
					$toolDetails .= "<td>" . $row["AbbreviatedDescription"] . "</td>";
					$toolDetails .= "<td>" . $row["PurchasePrice"] . "</td>";
					$toolDetails .= "<td>" . $row["DepositAmount"] . "</td>";
					$toolDetails .= "<td>" . $row["Name"] . "</td>";
					$toolDetails .= "<td>" . $row["PricePerDay"] . "</td>";
					$toolDetails .= "<td>" . $row["FullDescription"] . "</td>";
					$toolDetails .= "<td>" . $row["Status"] . "</td></tr>";
					$toolDetails .= "</table>";
				} else {
					$errorMsg = "Tool ID does not exist!";
				}
				
				$query = "SELECT 
									    AccessoryName
									FROM
									    powertool
									WHERE
									    ToolID = $part";
      	$result = mysql_query($query);
      	if (mysql_num_rows($result) > 0) {
      		$toolDetails .= "<p>Accessory List: ";
      		$first = true;
					while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
						if (!$first) {
							$toolDetails .= ", ";
						}
						$toolDetails .= $row["AccessoryName"];
						$first = false;
					}
					$toolDetails .= "</p>";
				}

			}
		} elseif (isset($_POST['btnCompleteDropOff'])) {
			if (empty($_POST['reservationNum'])) {
				$errorMsg = "Please fill in the reservation number.";
			} else {
				/* Update the reservation with the drop-off clerk login */
				session_start();
    		$login = $_SESSION['login'];
    	
    		$resNum = $_POST['reservationNum'];

				$query = "SELECT
											Number
									FROM 
											reservation
									WHERE
											Number = $resNum
											AND DropOffClerkLogin IS NULL";
				
				$result = mysql_query($query);

    		if(mysql_num_rows($result) == 0) {
    			$errorMsg = "Reservation number does not exist or reservation has already been dropped off.";
    			mysql_close($connect);
    		} else {
					$query2 = "UPDATE reservation
    							SET DropOffClerkLogin = '$login'
    							WHERE Number = $resNum
    									AND DropOffClerkLogin IS NULL";
							
					$result2 = mysql_query($query2);
					
					if(!$result2) {
						$errorMsg .= "Cannot update reservation.";
						mysql_close($connect);    
					} else {
						/* Put the reservation number in session and redirect to the RentalReceipt page */
						mysql_close($connect);
						$_SESSION['reservationNumber'] = $resNum;
	    			header('Location: RentalReceipt.php');
	          exit();
	    		}
    		}
			}
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Drop Off</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body style="text-align:center;">
	<div class="logo">
		<img src="images/tools.png" border="0" alt="" title="" height="100" width="100" />
	</div>

	<br>
	<form action="DropOff.php" method="post">
	<b><label class="pickup_label">Reservation Number:</label></b>
	<input type="text" name="reservationNum" class="pickup_input" /> 
	<button type="submit" style="width:150px;" name="btnReservatonLookup" class="clerkMainMenu">Lookup</button>
	<br>
	<br>
	<b><label class="pickup_label">Tools Required:</label></b>
	<br>
	<br>
	<?php
		$TotalRentalPrice = 0;
		$TotalDepositRequired = 0;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['btnReservatonLookup'])) {
				if (empty($_POST['reservationNum'])) {
					$errorMsg = "Please fill in a reservation number before trying to lookup reservation.";
				} else {
					$reservationNumber = $_POST['reservationNum'];
					
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
							echo "</table>";
						} else {
							$errorMsg = "Reservation number not found.";
						}
						
					} else {
						$errorMsg = "Reservation number not found.";
					}

					mysql_close($connect);
				}
			}
		}
	?>
	<!-- insert code to populate a list of required tools -->
	<br>
	<br>
	<b><label class="pickup_label">Deposit Required:</label></b>
	<?php echo "$" . number_format($TotalDepositRequired, 2) ?>
	<br>
	<br>
	<b><label class="pickup_label">Estimated Cost:</label></b>
	<?php echo "$" . number_format($TotalRentalPrice, 2) ?>
	<p>________________________________________________________________________</p>
	<label class="pickup_label">Tool ID</label>
    <input type="text" name="toolID" class="pickup_input" />
    <button type="submit" style="width:150px;" name="btnViewToolDetails" class="clerkMainMenu">View Details</button>
	<?php echo $toolDetails ?>
	<p>________________________________________________________________________</p>

	<br>
	<br>

	<p>
    <button type="submit" style="width:150px;" name="btnCompleteDropOff" class="clerkMainMenu">Complete Drop Off</button>
  </p>
	</form>
	<p>
		<input type="button" style="width:150px;" class="clerkMainMenu" onclick="location.href = 'ClerkMainMenu.php';" value="Main Menu" />
	</p>

	<?php
    if (!empty($errorMsg)) {
      print "<div style='color:red'>$errorMsg</div>";
    }
   ?>       
     
	</body>
	
</html>