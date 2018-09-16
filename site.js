/* 
Hangman the game
Jakub Wielgus project
js file
 */


var move=9; //variable  number of moves
var img=1;//variable for image
var min_score; //minimum 
var max_score; //max score 
var score=0;//current score
//create new game
function new_game()
{
	//show cancel 
	$(".close_img").show()
	//load default values
	$('.hangman').css("background-image", "url(img/s0.jpg)");  
	move=9;
	img=1;
	how2=0;
	
	document.getElementById('ask').value="";//clear input
	document.getElementById('phone_ask').value="";//clear input
	
	$(".hiddenbox").hide(0);//hide hiddenbox
	$("#win").hide(0);//hide hiddenbox
	$("#lose").hide(0);//hide hiddenbox
	$("#start").hide(0);//hide hiddenbox
	disabled(0);//enable buttons
	$(".letter").attr('class', 'ui inverted basic button letter');//default class
	$(".phone_letter").attr('class', 'ui inverted basic button phone_letter');//default class
	document.getElementById('ask').value="";//clear input
	
	//load text
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{create:1},
		success:function(data)
		{
			$(".query").html(data);//load the text
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
		
		
	});
	//load categorie and points
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{load:1},
		success:function(data)
		{
			data = JSON.parse(data);//get the array
			var max_points = data.max_points;
			var min_points = data.min_points;
			var category = data.category;
			
			max_score = max_points;
			min_score = min_points;
			
			$("#points").html(max_points);//load the category
			$("#category").html(category);//load the points
			$("#big_category").html(category);//load the points
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
		
		
	});
	
}
//check one letter
function check_letter(x)
{
		$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{check:1,letter:x},
		success:function(data)
		{
			numbers = JSON.parse(data);
			how = numbers.how; //get array size
			var letter = numbers.letter //get letter
			//check numbers of elements
			button = document.getElementById('letter'+letter);
			phone_button = document.getElementById('phone_letter'+letter);
			if(how!=0)
			{
				for(var i=0;i<how;i++)
				{
					$("#letter"+numbers[i]).html(letter);//show letter
					$("#phone_letter"+numbers[i]).html(letter);//show letter
				}
				
				button.className='ui inverted green  button letter';//change the view
				phone_button.className='ui inverted green  button phone_letter';//change the view
				check_all_letter();
				
				
			}
			else
			{
				button.className='ui inverted red  button letter';//change the view
				phone_button.className='ui inverted red button phone_letter';//change the view
				check();
			}
			phone_button.disabled=true; //disabled button
			button.disabled=true; //disabled button
			load_score();
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
		
		
	});
	
}

//try to ask
function ask(x)
{
	$(".ask_button").attr("disabled","disabled");//disabled button
	setTimeout(function (){$(".ask_button").removeAttr('disabled')},1000)//enabled button
	if(x==1)
	{
		var text = document.getElementById("ask").value;//get value
	}
	else
	{
		var text = document.getElementById("phone_ask").value;//get value
	}
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{check:2,text:text},//send value 
		success:function(data)
		{
			if(data==1)
			{
				end_game(1);
			}
			else
			{	
				document.getElementById('ask').value="";//clear input
				document.getElementById('phone_ask').value="";//clear input
				check();
			}
			load_score();
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
			
	});
}
//end game
function end_game(win)
{
	$(".hiddenbox").show(0);
	if(win==1)
	{	
		$("#win").show(50);
		var u_id = $("#u_id").val();
		$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{u_id:u_id,add:1},//send value 
		success:function(data)
		{
			$("#mainscores").html(data);
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
		});
		
	}
	else $("#lose").show(50);
	disabled(1);//disalbed buttons
	
}
//disabled keyboard
function disabled(x)
{
	if(x==1)
	{
		$(".letter").attr("disabled","disabled");
		$(".phone_letter").attr("disabled","disabled");
		$(".ask_button").attr("disabled","disabled");
	}
	else
	{
		$(".letter").removeAttr('disabled')
		$(".phone_letter").removeAttr('disabled')
		$(".ask_button").removeAttr('disabled')
	}
	
}
//check status of game
function check()
{
		//sub values
		$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{sub:1},//send value 
		success: function(data)
		{
			ansver = JSON.parse(data);
			move = ansver.move;
			score = ansver.score;
		},
		error:function()
		{
			$(".query").html("Error.Check your internet connection");//load the text
		}
		});
		
		draw();
		if(move==0)
		{
			end_game(0);
		}
	
}
//draw a hangman
function draw()
{
	$('.hangman').css("background-image", "url(img/s="+img+".jpg)");  
	img++;
}
//load the score
function load_score()
{
	$("#points").html(score);//load the category
	$("#score_number").html(score);//load the category
}
//show\hide the right column
function show(x)
{
	//hide box
	if(x==1)
	{
		$(".small").hide(500);//show small column
		$(".arrow_div").attr('class', 'arrow_div hide');
		$(".arrow_div").attr('onclick', 'show(2)');//change on click
		$("#icon").attr('class', 'chevron left icon ikona');
	}
	//show box
	else
	{
		$(".small").show(500);//show small column
		$(".arrow_div").attr('class', 'arrow_div show');
		$(".arrow_div").attr('onclick', 'show(1)');//change on click
		$("#icon").attr('class', 'chevron right icon ikona');
	}
	
	
}
//check all letter after each move
function check_all_letter()
{
	var ok = true;
	letter = document.getElementsByClassName("space");
	for(var i = 0;i<letter.length;i++)
	{
		space = letter[i].innerHTML
		if(space=="_") ok=false;
	}
	if(ok==true)
	{
		end_game(1);
	}


}
//register
function register(){
	
	//get value
	var login = $("#register_login").val();//login
	var password = $("#register_password").val();//password
	var Rpassword = $("#register_Rpassword").val();//repeat password
	//reset errors
	$("#login_error").hide();
	$("#password_error").hide();
	
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{login:login,password:password,Rpassword:Rpassword,validate:1},//send value 
		success:function(data)
		{
			var errors = JSON.parse(data);//get the array
			var login_error = errors.error_l;
			var password_error = errors.error_p;
			var ok=true;
			if(login_error!="")
			{
				$("#login_error").html("<p>"+login_error+"</p>");
				$("#login_error").show();
				ok=false;
			}
			if(password_error!="")
			{
				$("#password_error").html("<p>"+password_error+"</p>");
				$("#password_error").show();
				ok=false;
			}
			//submit form
			if(ok==true)
			{
				$("#register_form").submit();
			}
			$("#hiddenbox_error").hide();
		},
		error:function()
		{
			$("#hiddenbox_error").html("Error.Check your internet connection");//load the text
			$("#hiddenbox_error").show();
		}
			
	});

}
function login()
{
	//get value
	var login = $("#login_login").val();//login
	var password = $("#login_password").val();//password
	//reset errors
	$("#login_error_message").hide();
	
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{login:login,password:password,validate:2},//send value 
		success:function(data)
		{
			var ok=true;
			var error = data;
			if(error!="")
			{
				ok=false;
				$("#login_error_message").html(error);
				$("#login_error_message").show();
			}
			//submit form
			if(ok==true)
			{
				$("#login_form").submit();
			}
			$("#hiddenbox_error").hide();
		},
		error:function()
		{
			$("#hiddenbox_error").html("Error.Check your internet connection");//load the text
			$("#hiddenbox_error").show();
		}
			
	});
}
//logout
function logout()
{
	const cookieName = encodeURIComponent('log');
    document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';//delete cookie
	location.href='http://pkhangman.cba.pl/';//reload
}
//load values to leaderboards
function leaderboards(u_id)
{
	$("#leaderboards").show();
	$(".hiddenbox").show();
	
	$.ajax({
		url:"ajax.php",
		type:"POST",
		data:{leaderboard:1,u_id:u_id},//send value 
		success:function(data)
		{
			$(".results").html(data)
			if(u_id!=0)
			{
				var elmnt = document.getElementById("this");
				elmnt.scrollIntoView();
			}
			$("#hiddenbox_error").hide();
		},
		error:function()
		{
			$("#hiddenbox_error").html("Error.Check your internet connection");//load the text
			$("#hiddenbox_error").show();
		}
			
	});
}
//hide boxes
function hide()
{
	$("#start").hide();
	$("#win").hide();
	$("#lose").hide();
	$("#login").hide();
	$("#register").hide();
	$("#leaderboards").hide();
	$(".close_img").show();
	
	var w_width=$(window).width()
	
	if(w_width<800)
	{
		$(".small").hide();
		$(".arrow_div").attr('class','arrow_div hide');
		$(".arrow_div").attr('onclick','show(2)');
		$("#icon").attr('class','chevron left icon ikona')
	}
}
