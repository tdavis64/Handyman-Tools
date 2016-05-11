<?php
	/* connect to database */
	$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

	if (!$connect)
	{
		die("Failed to connect to database");
	}

	mysql_select_db("cs6400project") or die("Unable to select database");
	$errorMsg = "";

	if(isset($_POST) == true && empty($_POST) == false)
	{
	    if (empty($_POST['toolID']) or empty($_POST['startDate']) or empty($_POST['endDate']) or empty($_POST['estCost'])) {
	        $errorMsg = "Please fill in the form completely before hitting submit.";
	    } else {
	    	session_start();
	    	$login = $_SESSION['login'];
	    	$ToolID = $_POST['toolID'];
	    	$StartDate = $_POST['startDate'];
	    	$EndDate = $_POST['endDate'];
	    	$estCost = $_POST['estCost'];
	    	
	    	$query1 = "SELECT 
									    ID, AbbreviatedDescription
									FROM
									    tool
									WHERE
										ID = $ToolID
									    AND (ID IN (SELECT 
									            ToolID
									        FROM
									            reserves AS rsv INNER JOIN reservation AS res ON rsv.ReservationNumber = res.Number
									        WHERE
									            '$StartDate' BETWEEN StartDate AND EndDate)
											OR ID IN (SELECT 
									            ToolID
									        FROM
									            reserves AS rsv INNER JOIN reservation AS res ON rsv.ReservationNumber = res.Number
									        WHERE
									            '$EndDate' BETWEEN StartDate AND EndDate))";

				$result1 = mysql_query($query1);
				
				if (mysql_num_rows($result1) > 0) {
					$errorMsg = "Cannot place service order over an existing reservation.";
				} else {
	    	
		      $query2 = "INSERT INTO serviceorder (ToolID, StartDate, EndDate, RepairCost, ClerkLogin)
		        				VALUES ($ToolID, '$StartDate', '$EndDate', $estCost, '$login')";
		        				
		      $result2 = mysql_query($query2);

	        if ($result2) {
	            $statusMsg = "Service order created.";
	        } else {
	            $errorMsg = "Error creating service order.";
	        }
	      }
	    }
	    
	    mysql_close($connect);
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Service Order Request</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h1>Service Order Request</h1> 
	<form action="ServiceOrder.php" method="post">
	<label class="service_order_label">Tool ID</label>
    <input type="text" name="toolID" class="service_order_input" />

    <br>
    <br>

    <label class="service_order_label">Starting Date</label>
    <input type="date" name="startDate" class="service_order_input" />

    <br>
    <br>

    <label class="service_order_label">Ending Date</label>
    <input type="date" name="endDate" class="service_order_input" />

    <br>
    <br>

    <label class="service_order_label">Estimated Cost of Repair $</label>
    <input type="decimal" name="estCost" class="service_order_input" />

    <br>
    <br>

		<p>
    	<button style="width: 150px;" type="submit">Submit</button>
    </p>
  </form>
		<p>
  		<input type="button" style="width: 150px;" class="clerkMainMenu" onclick="location.href = 'ClerkMainMenu.php';" value="Main Menu" />
		</p>
  <?php
    if (!empty($errorMsg)) {
    	print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
    }
    if (!empty($statusMsg)) {
    	print "<div style='font-weight: bold;'>$statusMsg</div>";
    }
    
  ?>   
	</body>
	
</html>