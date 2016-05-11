<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
        <title>Profit Report 1</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body style="text-align:center;">
	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>
	<h1>Profit Report 1</h1> 

	<?php
	/* connect to database */
	$connect = mysql_connect("cs6400project.clfigk34pbwn.us-east-1.rds.amazonaws.com:3306", "cs6400project", "cs6400project");

	if (!$connect)
	{
		die("Failed to connect to database");
	}

	mysql_select_db("cs6400project") or die("Unable to select database");
	$errorMsg = "";

	$query = "SELECT 
						    tool.ID,
						    tool.AbbreviatedDescription,
						    tool.PurchasePrice,
						    IFNULL(RentProfit.RentalProfit, 0) AS RentalProfit,
						    IFNULL(ToolCost.CostofTool, 0) AS CostOfTool,
						    (IFNULL(RentProfit.RentalProfit, 0) - IFNULL(ToolCost.CostofTool, 0)) AS TotalProfit
						FROM
						    tool
						        LEFT JOIN
						    (SELECT 
						        ID,
						            (PurchasePrice + IFNULL(svcCost.TotRepairCost, 0)) AS CostOfTool
						    FROM
						        tool
						    LEFT JOIN (SELECT 
						        ToolID, SUM(RepairCost) AS TotRepairCost
						    FROM
						        serviceorder
						    GROUP BY ToolID) AS svcCost ON tool.ID = svcCost.ToolID) AS ToolCost ON tool.ID = ToolCost.ID
						        LEFT JOIN
						    (SELECT 
						        tool.ID,
						            (tool.PricePerDay * IFNULL(toolRentDays.TotalDaysRented, 0)) AS RentalProfit
						    FROM
						        tool
						    LEFT JOIN (SELECT 
						        rsv.ToolID,
						            SUM(res.EndDate - res.StartDate + 1) AS TotalDaysRented
						    FROM
						        reservation AS res
						    INNER JOIN reserves AS rsv ON res.Number = rsv.ReservationNUmber
						    GROUP BY rsv.ToolID) AS toolRentDays ON tool.ID = toolRentDays.ToolID) AS RentProfit ON tool.ID = RentProfit.ID
						WHERE
						    IFNULL(tool.status,0) <> 1
						ORDER BY (IFNULL(RentProfit.RentalProfit, 0) - IFNULL(ToolCost.CostofTool, 0)) DESC";
						$result = mysql_query($query);

	if (mysql_num_rows($result) > 0) {
		echo "<table style='text-align:left;width:100%;' border='1' class='centerTable'><tr><th>Tool ID</th><th>Abbr Desc</th><th>Purchase Price</th><th>Rental Profit</th><th>Tool Cost</th><th>Total Profit</th></tr>";
		while ($row = mysql_fetch_row($result, MYSQL_BOTH)) {
			echo "<tr><td>" . $row["ID"] . "</td>";
			echo "<td>" . $row["AbbreviatedDescription"] . "</td>";
			echo "<td>" . number_format($row["PurchasePrice"],2) . "</td>";
			echo "<td>" . number_format($row["RentalProfit"],2) . "</td>";
			echo "<td>" . number_format($row["CostOfTool"],2) . "</td>";
			echo "<td>" . number_format($row["TotalProfit"],2) . "</td></tr>";
		}
		echo "</table>";
	} else {
		echo "<p>Error!</p>";
	}


	mysql_close($connect);

	?>
	<p>
		<input type="button" style='width:150pt;' class="clerkMainMenu" onclick="location.href = 'GenerateReports.php';" value="Report Menu" />
	</p>

		
</body>

</html>