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
  if (empty($_POST['tooltype']) or empty($_POST['startDate']) or empty($_POST['endDate'])) {
  	$errorMsg = "Please provide tool category, start date, and end date.";
  } 
  else {
  	session_start();
	/*persist variables*/
	$_SESSION['tooltype'] = mysql_real_escape_string($_POST['tooltype']);
  	$_SESSION['startDate'] = mysql_real_escape_string($_POST['startDate']);
  	$_SESSION['endDate'] = mysql_real_escape_string($_POST['endDate']);
    /* redirect to the tool availability page */
    header('Location: ToolAvailability.php');
    exit();
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>

        <title>Check Availabilty</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

</head>

	<body style="text-align:center;">
		<form action="CheckAvailability.php" method="post">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

	<h2>Select Tool Category</h2> 

	<p>________________________________________________________________________</p>

	<table style="text-align:center;">
	<td style="text-align:left;">
	</tr><input style="text-align:left;" type="radio" name="tooltype" value="2"> Hand Tools</tr>

	<br>

    </tr><input style="text-align:left;" type="radio" name="tooltype" value="3"> Construction Equipment</tr>

    <br>

    <tr><input style="text-align:left;" type="radio" name="tooltype" value="1"> Power Tools</tr>
	</td>
	</table>

    <p>________________________________________________________________________</p>

    <label class="check_availabilty_label">Start Date</label>
    <input type="date" name="startDate" class="check_availabilty_input" />

    <br>
    <br>

    <label class="check_availabilty_label">End Date</label>
    <input type="date" name="endDate" class="check_availabilty_input" />

    <br>
    <br>

    <button style="width:150px;" type="submit">Submit</button>
	 	<p>
			<input type="button" style="width:150px;" onclick="location.href = 'CustMainMenu.php';" value="Main Menu" />
		</p>
  </form>
  <?php
    if (!empty($errorMsg))
    {
    	print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
    }
    
  ?>   

	</body>
	
</html>