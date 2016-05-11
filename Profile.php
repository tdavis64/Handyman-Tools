<?php
/* connect to database */
$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

if (!$connect)
{
	die("Failed to connect to database");
}

mysql_select_db("cs6400project") or die("Unable to select database");
$errorMsg = "";

session_start();
$login = $_SESSION['login'];

$query = "SELECT Login, FirstName, LastName, HomePhoneAreaCode, HomePhoneSubscriberNumber, WorkPhoneAreaCode, WorkPhoneSubscriberNumber, Address FROM customer WHERE Login = '$login'";
$result = mysql_query($query);

$row = mysql_fetch_row($result, MYSQL_BOTH);
$Email = $row["Login"];
$Name = $row["FirstName"] . " " . $row["LastName"];
$HomePhone = "(" . $row["HomePhoneAreaCode"] . ") " . $row["HomePhoneSubscriberNumber"];
$WorkPhone = "(" . $row["WorkPhoneAreaCode"] . ") " .	$row["WorkPhoneSubscriberNumber"];
$Address = $row["Address"];

mysql_close($connect);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Profile</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h1>Profile</h1> 

	<p>________________________________________________________________________</p>

	<label class="profile_label">Email Address: <?php echo $Email; ?></label>

	<br>
	<br>
	
	<label class="profile_label">Name: <?php echo $Name; ?></label>

	<br>
	<br>

	<label class="profile_label">Home Phone: <?php echo $HomePhone; ?></label>

	<br>
	<br>

	<label class="profile_label">Work Phone: <?php echo $WorkPhone; ?></label>

	<br>
	<br>

	<label class="profile_label">Address: <?php echo $Address; ?></label>

	<p>________________________________________________________________________</p>

	<h3>Reservation History</h3>

	<!-- insert code to query this customer's reservations and put them in a table -->
<?php
/* connect to database */
$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

if (!$connect)
{
	die("Failed to connect to database");
}

mysql_select_db("cs6400project") or die("Unable to select database");
$errorMsg = "";

$login = $_SESSION['login'];

$query = "SELECT res.Number, res.StartDate, res.EndDate, pClk.FirstName AS pFirstName, dClk.FirstName AS dFirstName, tool.AbbreviatedDescription, tool.PricePerDay, tool.DepositAmount FROM reservation AS res LEFT JOIN clerk AS pClk ON res.PickupClerkLogin = pClk.Login LEFT JOIN clerk AS dClk ON res.DropOffClerkLogin = dClk.Login INNER JOIN reserves AS rsv ON res.Number = rsv.ReservationNumber INNER JOIN tool ON rsv.ToolID = tool.ID WHERE res.CustomerLogin = '$login' ORDER BY res.StartDate DESC";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0) {
	echo "<table style='text-align:left; width:100%;' border='1' class='centerTable'><tr><th>Res #</th><th>Tools</th><th>Start</th><th>End</th><th>Rental Price</th><th>Deposit</th><th>Pick-Up Clerk</th><th>Drop-Off Clerk</th></tr>";
	while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
		echo "<tr><td>" . $row["Number"] . "</td>";
		echo "<td>" . $row["AbbreviatedDescription"] . "</td>";
		echo "<td>" . $row["StartDate"] . "</td>";
		echo "<td>" . $row["EndDate"] . "</td>";
		echo "<td>" . $row["PricePerDay"] . "</td>";
		echo "<td>" . $row["DepositAmount"] . "</td>";
		echo "<td>" . $row["pFirstName"] . "</td>";
		echo "<td>" . $row["dFirstName"] . "</td></tr>";
	}
	echo "</table>";
} else {
	echo "<p>No reservations found!</p>";
}


mysql_close($connect);

?>	
		<p>
  		<input type="button" style="width:150px;" class="clerkMainMenu" onclick="location.href = 'CustMainMenu.php';" value="Main Menu" />
		</p>
	</body>
	
</html>