function showLogin(){
	$("#login").css("background-color", "#1ab188");
	$("#register").css("background-color", "#435159");
	$("#bodyRegister").hide();
	$("#bodyLogin").show();
}
function showRegister(){
	$("#login").css("background-color", "#435159");
	$("#register").css("background-color", "#1ab188");
	$("#bodyLogin").hide();
	$("#bodyRegister").show();
}


document.addEventListener('backbutton', function(){
	if (typeof payment === "back"){
		back();
	}
});


$(document).ready(
function(){
	$("#login").click(function(){
		showLogin();
	});
	$("#register").click(function(){
		showRegister();
	});
	showLogin();
	
	$('div.coverup').addClass('hidden');
});

function adjustDiv(){
	//Set spacer to make the login/register page not 'bounce' around
	var height=document.getElementById('bodyLogin').clientHeight;
	var height2=document.getElementById('bodyRegister').clientHeight;
	document.getElementById("registerSpacer").style.height=(height-height2).toString()+"px";
	//alert(height-height2);
}



