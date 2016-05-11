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
    if (empty($_POST['login']) or empty($_POST['password']) or empty($_POST['logintype']))
    {
        $errorMsg = "Please provide login, password, and if you are a clerk or a customer.";
    }
    else
    {
        $login = mysql_real_escape_string($_POST['login']);
        $password = mysql_real_escape_string($_POST['password']);

        if ($_POST['logintype'] == 'customer')
        {
            $query = "SELECT * FROM customer WHERE login = '$login' AND password = '$password'";
            $result = mysql_query($query);

            $query2 = "SELECT * FROM customer WHERE login = '$login'";
            $result2 = mysql_query($query2);
        }

        if ($_POST['logintype'] == 'clerk')
        {
            $query = "SELECT * FROM clerk WHERE login = '$login' AND password = '$password'";
            $result = mysql_query($query);

            $query2 = "SELECT * FROM clerk WHERE login = '$login'";
            $result2 = mysql_query($query2);
        }

        if (mysql_num_rows($result) == 0 && $_POST['logintype'] == 'customer')
        {
            /* redirect to the new user page */
            header('Location: NewUser.php');
            exit();
        }

        if (mysql_num_rows($result) == 1 && $_POST['logintype'] == 'clerk')
        {
            /* login successful */
            session_start();
            $_SESSION['login'] = $login;
            /* redirect to the clerk menu page */
            header('Location: ClerkMainMenu.php');
            exit();
        }

        if(mysql_num_rows($result) == 1 && $_POST['logintype'] == 'customer')
        {
            /* login successful */
            session_start();
            $_SESSION['login'] = $login;
            /* redirect to the customer menu page */
            header('Location: CustMainMenu.php');
            exit();
        }

        if (mysql_num_rows($result) == 0) 
        {
            /* login failed */
            $errorMsg = "Login failed.  Please try again.";
        }
    }
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <title>Handyman Tools Login</title>
      <link rel="stylesheet" type="text/css" href="style.css" />
   </head>
   <body style="text-align:center;">
      <div id="main_container">
         <div id="header">
            <div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>
         </div>
         <div class="center_content">
            <div class="text_box">
               <form action="login.php" method="post">
                  <h1>
                     <div class="title">Handyman Tools Login</div>
                  </h1>
                  <table style="text-align: left;" class="centerTable">
                  	<tr>
                  		<td><label class="login_label">Login</label></td>
                  		<td><input type="text" name="login" width="300px" /></td>
                  	</tr>
                  	<tr>
                  		<td><label class="login_label">Password</label></td>
                  		<td><input type="password" name="password" width="300px" /></td>
                  	</tr>
                  </table>
                  <br>
                  <div class="login_form_row">
                     <label class="login_label"></label>
                     <input type="radio" name="logintype" value="clerk"> Clerk
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <input type="radio" name="logintype" value="customer"> Customer
                  </div>
                  
                  <br>
                  <button type="submit" class="login">Enter</button>                     
               </form>
               <?php
                  if (!empty($errorMsg))
                  {
                  	print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
                  }
                  
                  ?>                    
            </div>
            <div class="clear"><br/></div>
         </div>
      </div>
   </body>
</html>