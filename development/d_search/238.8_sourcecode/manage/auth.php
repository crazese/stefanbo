<?php
class auth
{
    var $HOST = "localhost";        
    var $USERNAME = "sothink";              
    var $PASSWORD = "K2v3P494";      
    var $DBNAME = "cart";       
	
	function authenticate($username, $password)
	{
		$query = "SELECT * FROM admin WHERE UserName='$username' ";
		$connection = mysql_connect($this->HOST, $this->USERNAME, $this->PASSWORD);
		$result = mysql_db_query($this->DBNAME, $query);
		$err=mysql_error();
		mysql_close();
		if($err)
		{
		  return -1;
		}
		else
		{
			$row = mysql_fetch_array($result);
			if($row==true)
			{
			  if($row["Password"]==$password)
			  {
			    $level=($row["Level"]==1)?1:0;
				return $level;
			  }
			  else
			     return -1;
			}
			else
			   return -1;
    	}
	} // End: function authenticate
} // End: class auth

// $detail = 0;
// seed with microseconds
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}

function generatePassword ($length = 8)
{
  // start with a blank password
  $password = "";
  // define possible characters
  $possible = "23456789abcdfghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ"; 
  // set up a counter
  $i = 0; 
  // add random characters to $password until $length is reached
  mt_srand(make_seed());
  while ($i < $length) 
  { 
    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want this character if it's already in the password
    if (!strstr($password, $char))
	 { 
      $password .= $char;
      $i++;
    }
  }
  // done!
  return $password;
}//End: function generatePassword
?>
