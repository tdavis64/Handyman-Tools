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
    if (empty($_POST['abbDes']) or empty($_POST['purchasePrice']) or empty($_POST['rentalPrice'])  or empty($_POST['depositAmount']) or empty($_POST['fullDes']))
    {
        $errorMsg = "Please fill out all fields";
    }
	else if (($_POST['purchasePrice'] <0) or($_POST['rentalPrice'] < 0) or ($_POST['depositAmount'] < 0)){
		$errorMsg = "The purchase price, rental price and deposit amounts must be equal to or greater than 0.";
	}
    else 
    {
        $abbDes = mysql_real_escape_string($_POST['abbDes']);
        $purchasePrice = mysql_real_escape_string($_POST['purchasePrice']);
        $rentalPrice = mysql_real_escape_string($_POST['rentalPrice']);
        $depositAmount = mysql_real_escape_string($_POST['depositAmount']);
        $fullDes = mysql_real_escape_string($_POST['fullDes']);
        $toolType =  mysql_real_escape_string($_POST['tooltype']);
        $accessory = $_POST['accessory']; // array


        $query = "INSERT INTO tool (AbbreviatedDescription, PurchasePrice, PricePerDay, DepositAmount, FullDescription, TypeId)
                  VALUES ('$abbDes', '$purchasePrice', '$rentalPrice', '$depositAmount', '$fullDes', '$toolType')";

        $result = mysql_query($query);

        if($result) {
            $statusMsg = $abbDes . " successfully added.";
            $ToolID = mysql_insert_id();
            
            if((!empty($accessory)) and ($toolType == 1)) {
            	/* Add accessories if they exist and toolType is a power tool */
        			foreach ($accessory as $a => $b) {
        				$one_accessory = $accessory[$a];
        				$query2 = "INSERT INTO powertool (ToolID, AccessoryName)
        									VALUES ($ToolID, '$one_accessory')";
								$result2 = mysql_query($query2);
        			}
        			if($result2) {
        				$statusMsg .= " Accessories added.";
        			} else {
        				$errorMsg .= "Error adding accessories. ";
        			}
        		}
        
        } else {
            $errorMsg .= " Error adding tool.";
        }
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">


<head>
	<title>Add New Tool</title>
	<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script type="text/javascript">
		function addRow(tableID) {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if(rowCount < 10){                            // limit the user from creating fields more than your limits
				var row = table.insertRow(rowCount);
				var colCount = table.rows[0].cells.length;
				for(var i=0; i <colCount; i++) {
					var newcell = row.insertCell(i);
					newcell.innerHTML = table.rows[0].cells[i].innerHTML;
				}
			}else{
				 alert("Maximum number of accessories is 10");
			}
		}

		function deleteRow(tableID) {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			var lastRow = rowCount - 1;
			if(rowCount < 2) {
				alert("Cannot remove all accessories");
			//	break;
			} else {
				table.deleteRow(lastRow);
			}
		}

		jQuery(document).ready(function(){
        jQuery('#hideshow').on('click', function(event) {        
             jQuery('#content').toggle('show');
        });
    });
    
    function checkPT(sel) {
   		var hiddenDiv = document.getElementById('powertool_section');
    	if(sel.value == 1) {
   			hiddenDiv.style.display = "block";
    	} else {
    		hiddenDiv.style.display = "none";
    	}
    }
  </script>

</head>

	<body style="text-align:center;">

	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

    <form action="AddNewTool.php" method="post">

	<h1>Add New Tool</h1> 

	<label class="new_tool_label">Abbreviated Description:</label>
    <input type="text" name="abbDes" class="new_tool_input" />

    <br>
    <br>

    <label class="new_tool_label">Purchase Price: $</label>
    <input type="decimal" name="purchasePrice" class="new_tool_input" />

    <br>
    <br>

    <label class="new_tool_label">Rental Price (per day): $</label>
    <input type="decimal" name="rentalPrice" class="new_tool_input" />

    <br>
    <br>

    <label class="new_tool_label">Deposit Amount: $</label>
    <input type="decimal" name="depositAmount" class="new_tool_input" />

    <br>
    <br>

    <label class="new_tool_label">Full Description</label>
    <br>
    <textarea type="text" cols="40" rows="5" name="fullDes" class="new_tool_input" ></textarea>

    <br>
    <br>

    <label class="new_tool_label">Tool Type:</label>
    <select name="tooltype" class="new_tool_input" onChange="checkPT(this);">
			<option value="2">Hand Tool</option>
			<option value="3">Construction</option>
			<option value="1">Power Tool</option>
		</select>

	<br>
	<br>
	<div id="powertool_section" name="powertool_section" style="display: none;">
		If new item is a Power Tool, then include accessories.
		<br>
		<input type='button' id='hideshow' value='Add Accessories'>
		<div id='content' style='display:none;'>
			<table style="text-align:left;" border=0 class="centerTable" id="accessories_table">
				<tr>
						<td>Accessory Name:</td>
						<td><input id="accessory" name="accessory[]" type="text"></td>
				</tr>
			</table>
			<input type="button" value="Add Accessory" onClick="addRow('accessories_table')" />
			<input type="button" value="Remove Accessory" onClick="deleteRow('accessories_table')" />
		</div>
	</div>
	<br>
	<br>
	<p>
		<button type="submit">Submit New Tool</button>
	</p>
	<p>
		<input type="button" class="clerkMainMenu" onclick="location.href = 'ClerkMainMenu.php';" value="Main Menu" />
	</p>
    </form>
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