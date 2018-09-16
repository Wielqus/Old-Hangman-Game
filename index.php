<?php

/* 
Hangman the game
Jakub Wielgus project
php index file
 */
?>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="stylesheet" type="text/css"   href="/site.css" />
        <link rel="stylesheet" type="text/css" href="/Semantic-UI-master/dist/semantic.css">
        <link href="https://fonts.googleapis.com/css?family=Bungee" rel="stylesheet">
         
         <script src="/jquery-3.2.1.min.js"></script>
         <script src="/site.js"></script>
         <script src="/Semantic-UI-master/dist/semantic.js"></script>
        
	
	
  </head>
  <?php
// base connection
require_once"connect.php";
$c = @new mysqli($host,$db_user,$db_password,$db_name);
mysqli_query($c,"SET NAMES 'utf8'");
mysqli_query($c,"SET CHARACTER SET utf8 "); 

if(isset($_COOKIE["log"]))
{
	$u_id = $_COOKIE["log"];
}
else 
{
	$u_id = 0;
}

//add users
if(isset($_POST["register_login"])&&$_POST["register_login"]!=""&&isset($_POST["register_password"])&&$_POST["register_password"]!="")
{
	//encode password
	$password=md5($_POST["register_password"]);
	//insert in users
	$wloz = "INSERT INTO hangman_users(login,password) VALUES ('".$_POST["register_login"]."','".$password."')";
	$wykonaj = mysqli_query($c,$wloz);
	$last_id = mysqli_insert_id($c);
	setcookie("log", $last_id, time() + (86400 * 30), "/");//set cookie for one day
	$u_id = $last_id;
}
//login
if(isset($_POST["login_login"])&&$_POST["login_login"]!=""&&isset($_POST["login_password"])&&$_POST["login_password"]!="")
{
	//encode password
	$password=md5($_POST["login_password"]);
	//insert in users
	$q1 = "SELECT id FROM hangman_users WHERE login='".$_POST["login_login"]."' AND password = '".$password."'";
	$r1 = mysqli_query($c,$q1);
	$o1 = mysqli_num_rows($r1);
	$b1 = mysqli_fetch_array($r1);
	if($o1>0)
	{
		setcookie("log", $b1["id"], time() + (86400 * 30), "/");//set cookie for one day
		$u_id = $b1["id"];
	}
	
}
//main query about users
$q1 = "SELECT * FROM hangman_users WHERE id=".$u_id;
$r1 = mysqli_query($c,$q1);
$b1 = mysqli_fetch_array($r1);

//onload disabled
if($u_id==0)
{
	$onload='disabled(1);$(".close_img").hide();';
	$view = '';
}
else
{
	$onload='new_game()';
	$view = 'display:none;';
}
?>

  <body onload='<?php echo $onload; ?>'>

<input type='hidden' id='u_id' name='u_id' value='<?php echo $u_id; ?>' />
<?php//Main container?>
      
<main  class="container">
<?php
//box with end game information
?>
<div style='<?php echo $view; ?>' class='hiddenbox'>
<?php
//close window
?>
<img class='close_img' onclick='$(".hiddenbox").hide();hide()'  src='../img/cancel_icon.svg' />
<?php
//start box
?>
<?php
//error
?>
<br>
<div  id='hiddenbox_error'  class="ui error message ">
  </div>

<section style='<?php echo $view; ?>' id='start'>
<header class='hiddenbox_label'>
HANGMAN
<p class='under_title'>the game</p>
</header>
<p class='hiddenbox_info' >Click button to start game</p>
<button onclick='new_game();hide()' class='ui inverted basic button hiddenbox_button'>Start game</button>
</section>
<?php
//win box
?>
<section id='win'>
<header class='hiddenbox_label'>
WELL DONE
</header>
<p class='hiddenbox_info' >YOU SAVE THE HANGMAN AND WIN <span id='score_number'> 18 </span> POINTS</p>
<button onclick='new_game();hide()' class='ui inverted basic button hiddenbox_button'>Play again</button>
</section>
<?php
//lose box
?>
<section  id='lose'>
<header class='hiddenbox_label'>
SO BAD
</header>
<p class='hiddenbox_info' >HANGMAN IS DEAD</p>
<button onclick='new_game();hide()' class='ui inverted basic button hiddenbox_button'>Play again</button>
</section> 
<?php
//ledaerboards
?>
<section id='leaderboards'>
<header class='hiddenbox_label'>
Leaderboard
</header>
<br>
<div class='table'>
<div class='line'><div class='login_line'>Login</div><div class='scores_line'>Scores</div></div>
<div class='results'>

</div>
</div>
</section>
<?php
//register
?>
<section  id='register'>
<header class='hiddenbox_label'>
REGISTER
</header><br>
<?php
//register form
?>
<form style='text-align:center;width:100%;' method='POST' id='register_form' class='ui form'>
<?php
//login
?>
<div class='field'>
<label>Login</label>
<input id='register_login' name='register_login' placeholder="Login" />
<div id='login_error'  class="ui error message ">
  </div>
</div>

<?php
//password
?>
<div class='field'>
<label>Password</label>
<input type='password' id='register_password' name='register_password' placeholder="Password" />
<div  id='password_error'  class="ui error message ">
  </div>
</div>
<?php
//repeat password
?>
<div class='field'>
<label>Repeat password</label>
<input type='password' id='register_Rpassword' name='register_Rpassword' placeholder="Repeat password" />
</div>
</form>

<button onclick='register()' id='register_button'   class='ui inverted basic button hiddenbox_button'>Register</button><br><br><?php //register button submit ?>
</section>
<?php
//login
?>
<section  id='login'>
<header class='hiddenbox_label'>
Login
</header><br>
<form style='text-align:center;width:100%;' method='POST' id='login_form' class='ui form'>
<?php
//errors
?>
<div class='field'>
<div  id='login_error_message'  class="ui error message ">
</div>
</div>
<?php
//login
?>
<div class='field'>
<label>Login</label>
<input id='login_login' name='login_login' placeholder="Login" />
</div>

<?php
//password
?>
<div class='field'>
<label>Password</label>
<input type='password' id='login_password' name='login_password' placeholder="Password" />
</div>
</form>
<button onclick='login()' class='ui inverted basic button hiddenbox_button'>Login</button>
</section> 


</div>
    <section class="big column" >
		<header class='big_category' >
		<h1 id='big_category' ></h1>
		</header>
        <div class='hangman'>
           
        </div>
        <article class='text'>
			<p class='query'></p>
        </article>
		<div class='phone_keyboard'>
		<div class='phone_ansver'>
            <div class='ui inverted form error'>
			
                <label>Try to ask</label>
				<div class='ui input'>
					<input id='phone_ask' type="text" placeholder="TRY TO ASK"   />
				</div>
                <button  id='ask_button2'  onclick='ask(2)' class="ask_button ui inverted basic button">Ask</button>
            </div>
		</div>
       
		 <?Php
            //Wypisuje całą klawiaturę
            $letter[0]="Q";
            $letter[1]="W";
            $letter[2]="E";
            $letter[3]="R";
            $letter[4]="T";
            $letter[5]="Y";
            $letter[6]="U";
            $letter[7]="I";
            $letter[8]="O";
            $letter[9]="P";
            $letter[10]="A";
            $letter[11]="S";
            $letter[12]="D";
            $letter[13]="F";
            $letter[14]="G";
            $letter[15]="H";
            $letter[16]="J";
            $letter[17]="K";
            $letter[18]="L";
            $letter[19]="Z";
            $letter[20]="X";
            $letter[21]="C";
            $letter[22]="V";
            $letter[23]="B";
            $letter[24]="N";
            $letter[25]="M";
            //W pętli wyświetlam klawiature
            $create=true;
            $j=0;
            for($i=0;$i<=25;$i++)
            {
                if($create==true)
                {
                    ?>
                    <div class='phone_line'>
                    <?php
                    $create=false;
                }
                ?>
                <button id='phone_letter<?php echo $letter[$i]; ?>' onclick='check_letter("<?php echo $letter[$i]; ?>")' class='ui inverted basic button phone_letter'><?php echo $letter[$i];?></button>
                <?php
                if($letter[$i]=='P'||$letter[$i]=="L"||$letter[$i]=="M")
                {
                     ?>
                    </div>
                    <?php
                    
                    $create=true;
                }
            }
            ?>
		</div>
    </section>
	<div onclick='show(2)'  class='arrow_div hide'>
	<i id='icon' class="chevron left icon ikona"></i>
	</div>
    <section  class="small column">
	
	<div class='up'>
        <?Php//header?>
        <header class='info'>
            <h1>Hangman The game </h1>
        </header>
        <?Php //buttons?>
		<?php
		//if user isn't logged
		if($u_id==0)
		{
			?>
        <div class='buttons'>
				<button onclick='new_game();hide()'  id='new_game' class="new_game mains_button ui inverted basic button hiddenbox_button">New game</button>
				<button onclick='hide();leaderboards(<?php echo $u_id;?>);' class="mains_button ui inverted basic button hiddenbox_button">Leaderboard</button>
				<button onclick='$(".hiddenbox").show(0);hide();$("#login").show(50)'  class="mains_button ui inverted basic button hiddenbox_button">Log in</button>
               <button onclick='hide();$(".hiddenbox").show(0);$("#register").show(50);' class="mains_button ui inverted basic button hiddenbox_button">Register</button>          
			    
			       
		</div>
		<?php
		}
		//if user is logged
		else
		{
			?>
			 <div class='scores'>
            Logged as: <?php echo $b1["login"]; ?><br><br>
            Scores: <span id='mainscores'><?php echo $b1["scores"];?></span><br>
			</div>
			<div class='buttons'>
				<button onclick='new_game();hide()'  class="new_game mains_button ui inverted basic button hiddenbox_button">New game</button>	
			   <button onclick='hide();leaderboards(<?php echo $u_id;?>);' class="mains_button ui inverted basic button hiddenbox_button">Leaderboard</button>
				<button onclick='logout()'  class="mains_button ui inverted basic button hiddenbox_button">Log Out</button>			   
			   		   
			</div>
			<?php
		}
		?>
        <div class='scores'>
            Score for this phrase:<br> <span id='points'></span><br><br>
            <span id='category'></span><br><br>
        </div>
	</div>
	<div class='down'>
        <div class='keyboard'>
           <?php
            //W pętli wyświetlam klawiature
            $create=true;
            $j=0;
            for($i=0;$i<=25;$i++)
            {
                if($create==true)
                {
                    ?>
                    <div class='line'>
                    <?php
                    $create=false;
                }
                ?>
                <button id='letter<?php echo $letter[$i]; ?>' onclick='check_letter("<?php echo $letter[$i]; ?>")' class='ui inverted basic button letter'><?php echo $letter[$i];?></button>
                <?php
                if($letter[$i]=='P'||$letter[$i]=="L"||$letter[$i]=="M")
                {
                     ?>
                    </div>
                    <?php
                    
                    $create=true;
                }
            }
            ?>
        </div>
	</div>
        <footer class='ansver'>
            <div class='ui inverted form error'>
			
                <label>Try to ask</label>
				<div class='ui input'>
					<input id='ask' type="text" placeholder="TRY TO ASK"  style='width:200px;'/>
				</div>
                <button  id='ask_button'  onclick='ask(1)' class="ask_button ui inverted basic button">Ask</button>
            </div>
        </footer>
        
                
            
            
        
    </section>
    
</main>
  </body>
</html>

