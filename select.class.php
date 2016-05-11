<?php
class SelectList
{
    protected $conn;
 
        public function __construct()
        {
            $this->DbConnect();
        }
 
        protected function DbConnect()
        {
            include "db_config.php";
            $this->conn = mysql_connect($host,$user,$password) OR die("Unable to connect to the database");
            mysql_select_db($db,$this->conn) OR die("can not select the database $db");
            return TRUE;
        }
 
        public function ShowCategory()
        {
            $sql = "SELECT TypeID, Name FROM typecategory ORDER BY TypeID";
            $res = mysql_query($sql,$this->conn);
            $category = '<option value="0">choose...</option>';
            while($row = mysql_fetch_array($res))
            {
                $category .= '<option value="' . $row['TypeID'] . '">' . $row['Name'] . '</option>';
            }
            return $category;
        }
 
        public function ShowTool()
        {
        		$sql = "SELECT 
			            ID, AbbreviatedDescription, PricePerDay
			        FROM
			            tool
			        WHERE
			            TypeID = $_POST[TypeID]
			        ORDER BY TypeID";
            $res = mysql_query($sql,$this->conn);
            $tool = '<option value="0">choose...</option>';
            while($row = mysql_fetch_array($res))
            {
                $tool .= '<option value="' . $row['ID'] . '">' . $row['ID'] . '. ' . $row['AbbreviatedDescription'] . ' $' . $row['PricePerDay'] . '</option>';
            }
            return $tool;
        }
}
 
$opt = new SelectList();
?>