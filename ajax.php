<?php
/* 
Hangman the game
Jakub Wielgus project
ajax file
 */
 
// base connection
require_once"connect.php";
$c = @new mysqli($host,$db_user,$db_password,$db_name);
mysqli_query($c,"SET NAMES 'utf8'");
mysqli_query($c,"SET CHARACTER SET utf8 "); 
 

//start session
session_start(); 
 if(isset($_GET["pobierz"])&&$_GET["pobierz"]!="")
 {
	 $q1 = "SELECT text,status FROM hangman_text WHERE 1 ORDER BY text ASC status ASC";
$r1 = mysqli_query($c,$q1);
?>
<table>
<?php
while($b1 = mysqli_fetch_array($r1))
{
	?>
	<tr><td><?php echo $b1["text"];?></td><td><?php echo $b1["status"];?></td></tr>
	<?php
}	
?>
</table>
<?php
 }
//create game
if(isset($_POST["create"])&&$_POST["create"]==1)
{
		$q1 = "SELECT * FROM hangman_text WHERE LENGTH(text)<30 ORDER BY RAND() LIMIT 1";//GET RANDOM TEXT
		$r1 = mysqli_query($c,$q1);
		$b1 = mysqli_fetch_array($r1);
		
		$q2 = "SELECT * FROM hangman_category WHERE id=".$b1["category"];//GET CATEGORY
		$r2 = mysqli_query($c,$q2);
		$b2 = mysqli_fetch_array($r2);
		
		$text= strtoupper($b1["text"]);
		$number = strlen($text);// GET NUMBERS OF LETTERS
		$j=0;
		$_SESSION["text"]=$text;//GET TEXT
		
		
		$_SESSION["category"]=$b2["category"];//GET CATEGORY
		$_SESSION["max_points"]=$b1["max_value"];//GET POINTS
		$_SESSION["min_points"]=$b1["min_value"];//GET POINTS
		$_SESSION["score"] = $b1["max_value"];//current score
		$_SESSION["move"] = 9;//current score
		
		$data = array();
		
		
		
		for($i=0;$i<$number;$i++)
		{
			if($i==0)
			{
				?>
				
				<?php
			}
			if($text[$i]=='-')
			{
				?>
				<span class='space' >-</span>
				<?php
			}
			else if($text[$i]=="'")
			{
				?>
				<span class='space' >'</span>
				<?php
			}
			else if($text[$i]!=' ')
			{
			?>
			<span class='space' id='letter<?php echo $j; ?>' >_</span>
			<?Php
			$_SESSION["letter".$j]=$text[$i];
			$j++;
			}
			else
			{
				?>
				&nbsp &nbsp
				<?php
			}
			if($i==$number-1)
			{
				?>
				
				<?php
			}
		}
		$_SESSION["number"]=$j;
		
}
//sub points and move
if(isset($_POST["sub"])&&$_POST["sub"]==1)
{
	$_SESSION["move"]--;
	$_SESSION["score"]-=2;
	$ansver["move"] = $_SESSION["move"];
	$ansver["score"] = $_SESSION["score"];
	
	echo json_encode($ansver);
}
//load data category and points
if(isset($_POST["load"])&&$_POST["load"]==1)
{
	$data = array();
	
	$data["category"]="Category:<br>".$_SESSION["category"];
	$data["max_points"]=$_SESSION["max_points"];
	$data["min_points"]=$_SESSION["min_points"];
	echo json_encode($data);//return value in json
}
//check one letter
if(isset($_POST["check"])&&$_POST["check"]==1)
{
	$numbers =array(); //create array
	$j=0;
	for($i=0;$i<$_SESSION["number"];$i++)
		{		
			if($_SESSION["letter".$i]==$_POST["letter"]) 
			{	
				$numbers[$j]=$i;//number of field to array
				$j++;
			}
		}
	$numbers["how"]=$j;	 //numbers of field
	$numbers["letter"]=$_POST["letter"];//letter
	echo json_encode($numbers);//return in json

	
	
}
//try to ask
if(isset($_POST["check"])&&$_POST["check"]==2)
{
	 $text = strtoupper($_POST["text"]);
	 if($text==$_SESSION["text"]) echo 1;
	 else echo 0;
	 
}
//add scores
if(isset($_POST["add"])&&$_POST["add"]==1)
{
	$wloz = "UPDATE hangman_users SET scores =scores+".$_SESSION["score"]." WHERE id=".$_POST["u_id"];
	$wykonaj = mysqli_query($c,$wloz);
	
	$q1 = "SELECT scores FROM hangman_users WHERE id=".$_POST["u_id"];
	$r1 = mysqli_query($c,$q1);
	$b1 = mysqli_fetch_array($r1);
	
	echo $b1["scores"];
}
//validate register
if(isset($_POST["validate"])&&$_POST["validate"]==1)
{
	//default valiables
	$e_login="";
	$e_password="";
	//login length
	if(strlen($_POST["login"])<3||strlen($_POST["login"])>20)
	{
		$e_login = "Login must be 3 to 20 characters long";
	}
	
	//login exist
	$q1 = "SELECT id FROM hangman_users WHERE login LIKE '".$_POST["login"]."'";
	$r1 = mysqli_query($c,$q1);
	$o1 = mysqli_num_rows($r1);
	if($o1>0)
	{
		$e_login = "The login already exists";
	}
	
	
	//password length
	if(strlen($_POST["password"])<8||strlen($_POST["password"])>20)
	{
		$e_password ="Password must be 8 to 20 characters long";
	}
	//password repeat
	if($_POST["password"]!=$_POST["Rpassword"])
	{
		$e_password ="Passwords do not match";
	}
	$re["error_l"] = $e_login;
	$re["error_p"] = $e_password;
	
	echo json_encode($re);
}
//validate login
if(isset($_POST["validate"])&&$_POST["validate"]==2)
{
	$error = "";
	//login exist
	$q1 = "SELECT id,password FROM hangman_users WHERE login LIKE '".$_POST["login"]."'";
	$r1 = mysqli_query($c,$q1);
	$o1 = mysqli_num_rows($r1);
	if($o1==0)
	{
		$error = "The login doesn't exist";
	}
	else
	{
		$b1 = mysqli_fetch_array($r1);
		$password = md5($_POST["password"]);
		if($password!=$b1["password"])
		{
			$error = "Bad password";
		}
	}

	
	echo $error;
}
//load html to leaderboard
if(isset($_POST["leaderboard"])&&$_POST["leaderboard"]==1)
{
	$q1 = "SELECT * FROM hangman_users WHERE 1 ORDER BY scores DESC";
	$r1 = mysqli_query($c,$q1);
	$i=1;
	while($b1 = mysqli_fetch_array($r1))
	{
		if($_POST["u_id"]==$b1["id"])
		{
			$color="color:#3366FF;";
			$id="id='this'";
		}
		else
		{
			$color="";
			$id="";
		}
		echo $link."<div ".$id." class='line'   style='".$color."'><div class='login_td' >".$i.".".$b1["login"]."</div><div class='score_td' >".$b1["scores"]."</div></div>";	
		$i++;
	}
}
?>
