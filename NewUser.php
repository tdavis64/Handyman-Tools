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
    if (empty($_POST['email']) or empty($_POST['password']) or empty($_POST['confirmPassword'])
    		or empty($_POST['firstName']) or empty($_POST['lastName'])
    		or empty($_POST['homeAreaCode']) or empty($_POST['homePhone']) 
    		or empty($_POST['workAreaCode']) or empty($_POST['workPhone'])
    		or empty($_POST['address']))
    {
        $errorMsg = "Please fill in the form completely before hitting submit.";
    }
    elseif ($_POST['password'] <> $_POST['confirmPassword'])
    {
    	$errorMsg = "The password and confirmed password must be the same.";
    }

    else
    {
        $email = mysql_real_escape_string($_POST['email']);
        $query3 = "SELECT * FROM customer WHERE login = '$email'";
        $result3 = mysql_query($query3);
        if(mysql_num_rows($result3) == 1)
        {
            $errorMsg = "This login has already been taken. Please choose another one.";
        }
    
    //TODO: need to check to see if login already exists
        else
        {
            $email = mysql_real_escape_string($_POST['email']);
            $password = mysql_real_escape_string($_POST['password']);
            $FirstName = mysql_real_escape_string($_POST['firstName']);
            $LastName = mysql_real_escape_string($_POST['lastName']);
            $homeAreaCode = mysql_real_escape_string($_POST['homeAreaCode']);
            $homePhone = mysql_real_escape_string($_POST['homePhone']);
            $workAreaCode = mysql_real_escape_string($_POST['workAreaCode']);
            $workPhone = mysql_real_escape_string($_POST['workPhone']);
            $address = mysql_real_escape_string($_POST['address']);

            $query = "INSERT INTO customer (Login, Password, FirstName, LastName, HomePhoneAreaCode, 
            HomePhoneSubscriberNumber, WorkPhoneAreaCode, WorkPhoneSubscriberNumber, Address)
    		VALUES('$email', '$password', '$FirstName', '$LastName', '$homeAreaCode', '$homePhone', 
    		'$workAreaCode', '$workPhone', '$address')";
                $result = mysql_query($query);

                $query2 = "SELECT * FROM customer WHERE login = '$email'";
                $result2 = mysql_query($query2);

            if (mysql_num_rows($result2) == 1)
            {
                /* login created */
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['login'] = $email;
                $_SESSION['logintype'] = 'customer';
                /* redirect to the customer menu page */
                header('Location: CustMainMenu.php');
                exit();
            }

            if (mysql_num_rows($result2) == 0) 
            {
                /* login failed */
                $errorMsg = "Account creation failed.  Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<html xmlns="http://www.w3.org/1999/xhtml">

    

    <head>

        <title>New User</title>

        <link rel="stylesheet" type="text/css" href="style.css" />

    </head>

  

    <body style="text-align:center;">

    <div class="logo"><img src="images/tools.png" border="0" alt="" title="" height="100" width="100" /></div>
        <form action="NewUser.php" method="post">
        <h1>Create a Profile</h1>

        <p>
            <b>
                Handyman Tools Rental requires a valid profile for every user before they can make reservations.
            </b>
        </p>

        <label class="new_user_label">Email Address (Login)</label>
        <input type="text" name="email" class="new_user_input" />

        <br>
        <br>

        <label class="new_user_label">Password</label>
        <input type="password" name="password" class="new_user_input" />

        <br>
        <br>

        <label class="new_user_label">Confirm Password</label>
        <input type="password" name="confirmPassword" class="new_user_input" />

        <br>
        <br>
        
        <label class="new_user_label">First Name</label>
        <input type="text" maxlength="20" name="firstName" class="new_user_input" />
        <br><br>
        <label class="new_user_label">Last Name</label>
        <input type="text" maxlength="20"  name="lastName" class="new_user_input" />

        <br>
        <br>

        <label class="new_user_label">Home Phone</label>
        <input type="text" maxlength="3" size="3" name="homeAreaCode" class="new_user_input" />
        <input type="text" maxlength="7" size="7" name="homePhone" class="new_user_input" />

        <br>
        <br>

        <label class="new_user_label">Work Phone</label>
        <input type="text" maxlength="3" size="3" name="workAreaCode" class="new_user_input" />
        <input type="text" maxlength="7" size="7" name="workPhone" class="new_user_input" />

        <br>
        <br>

        <label class="new_user_label">Address</label>
        <textarea type="text" cols="40" rows="5" name="address" class="new_user_input" ></textarea>

        <br>
        <br>

        <button type="submit">Submit</button>
        </form>















        





       

                  

                    <?php

if (!empty($errorMsg))
{
	print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
}

?>                    

                           

            

            

                <div class="clear"><br/></div> 

               

               

      

           

        

    </body>

</html>