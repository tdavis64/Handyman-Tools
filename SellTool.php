<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Sell Tool</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h1>Sell Tool</h1> 

	<form action="SellTool.php" method="post">
		<label class="new_tool_label">Tool Number:</label>
    <input type="text" name="toolid" class="sell_tool_input" />

    <br>
    <br>
		<p>
			<button type="submit">Sell Tool</button>
		</p>
		<p>
  		<input type="button" class="clerkMainMenu" onclick="location.href = 'ClerkMainMenu.php';" value="Main Menu" />
		</p>
  </form>
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
		  if (empty($_POST['toolid'])) {
		  	$errorMsg = "Please provide a tool number.";
		  } else {
				$toolid = mysql_real_escape_string($_POST['toolid']);
        
        $query = "SELECT 
									    (PurchasePrice / 2) AS SalesPrice,
									    Status
									FROM
									    tool
									WHERE
									    ID = $toolid";
      	$result = mysql_query($query);
	
				if (mysql_num_rows($result) > 0) {
					$row = mysql_fetch_row($result, MYSQL_BOTH);
					$salesPrice  = round($row["SalesPrice"],2);
					$status = $row["Status"];
					
					if ($status == 1) {
						$errorMsg = "Tool number " . $toolid . " has already been sold!";									
					} else {
						$query2 = "UPDATE tool 
										   SET 
												   Status = 1
											 WHERE
											     ID = $toolid";
											     
						$result2 = mysql_query($query2);
						
						if ($result2 == TRUE) {
							$soldMsg = "Tool number " . $toolid . " has been sold for $" . $salesPrice . "!";									
						}
					}	
				} else {
					$errorMsg = "Tool number " . $toolid . " does not exist!";
				}
				mysql_close($connect);
		  }
		}
		
    if (!empty($errorMsg)) {
    	print "<br /><div class='login_form_row' style='color:red;font-weight: bold;'>$errorMsg</div>";
    } elseif (!empty($soldMsg)) {
    	print "<br /><div class='login_form_row' style='color:green;font-weight: bold;'>$soldMsg</div>";
    }
    
  ?>   


	</body>
	
</html>