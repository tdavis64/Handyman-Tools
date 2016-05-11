<?php

$errorMsg = "";

if(isset($_POST) == true && empty($_POST) == false)
{
    if (empty($_POST['startingDate']) or empty($_POST['endingDate']) or empty($_POST['selectedTool'])) {
        $errorMsg = "Please fill out all fields" . $_POST['startingDate'];
    } 
	else {
    		session_start();
        $_SESSION['startingDate'] = $_POST['startingDate'];
        $_SESSION['endingDate'] = $_POST['endingDate'];
        $selectedTool = $_POST['selectedTool']; // array
        
        $first = true;
				$ToolIDs = '';
        
        foreach ($selectedTool as $a => $b) {
        	if ($first) {
        		$ToolIDs .= $selectedTool[$a];
        		$first = false;
        	} else {
        		$ToolIDs .= ',' . $selectedTool[$a];
        	}
        }
        
        $_SESSION['selectedTool'] = $ToolIDs;
        
				/* redirect to the clerk menu page */
				header('Location: ResSummary.php');
				exit();
            
    }

}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript">
        	function getComponentType(ele) {
					    var ths = $(ele);
					    $.ajax({
					        type: "POST",
					        url: "select_tool.php",
					        data:'TypeID='+ths.val(),
					        success: function(data){
					            ths.parent().parent().find("#selectedTool").html(data);
					        }
					    });
					}

					function addRow(tableID) {
						var table = document.getElementById(tableID);
						var rowCount = table.rows.length;
						if(rowCount < 52){                            // limit the user from creating fields more than your limits
							var row = table.insertRow(rowCount);
							var colCount = table.rows[0].cells.length;
							for(var i=0; i <colCount; i++) {
								var newcell = row.insertCell(i);
								newcell.innerHTML = table.rows[1].cells[i].innerHTML;
							}
						}else{
							 alert("Maximum number of tools is 50");
						}
					}

					function deleteRow(tableID) {
						var table = document.getElementById(tableID);
						var rowCount = table.rows.length;
						var lastRow = rowCount - 1;
						if(rowCount <= 2) {
							alert("Cannot remove all tools");
						//	break;
						} else {
							table.deleteRow(lastRow);
						}
					}
        </script>
    </head>
		<body style="text-align:center;">
    	<?php include "get_tools.php"; ?>
    	<div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>

			<h1>Make Reservation</h1> 

      <form id="select_form" action="MakeReservation.php" method="post">
      	
			<label class="make_reservation_label">Starting Date</label>
	    	<input type="date" name="startingDate" class="make_reservation_input" />
			<label class="make_reservation_label">eg. YYYY-MM-DD</label>
	    	<br>
	    	<br>

	    	<label class="make_reservation_label">Ending Date</label>
	    	<input type="date" name="endingDate" class="make_reservation_input" />
			<label class="make_reservation_label">eg. YYYY-MM-DD</label>

		    <br>
	  	    <br>
  	  
	    
        <table style="text-align:center;" border=0 class="centerTable" id="component_table">
			<thead>
				<tr>
					<th style="width: 250px;">Type of Tool</th>
					<th style="width: 250px;">Tool</th>
				</tr>
			</thead>
				<tbody id="tool_tb">
				<tr>
			<td><?php echo $opt->ShowCategory(); ?></td>
            <td>
            	<select id="selectedTool" name="selectedTool[]">
            		<?php echo $opt->ShowBlankTool(); ?>
           		</select>
         	</td> 
				</tr>
				</tbody>
		</table>
			<br>
			<br>
			<div id="calcTotal"></div>
			<input type="button" style="width:150px;" value="Add More Tools" onClick="addRow('component_table')" />
			<input type="button" style="width:150px;" value="Remove Last Tool" onClick="deleteRow('component_table')" />
			<button type="submit" style="width:150px;" id="calcTotal">Calculate Total</button>
			<input type="button" style="width:150px;"  value="Main Menu" onclick="location.href = 'CustMainMenu.php';"/>
		</form>
		<?php
      if (!empty($errorMsg)) {
        print "<div style='color:red'>$errorMsg</div>";
      }
     ?>       
    </body>
</html>


