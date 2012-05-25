<?php
 $cache_expire = 60*60*24*365;
 header("Pragma: public");
 header("Cache-Control: max-age=".$cache_expire);
 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
 ?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/tipsy.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="js/jquery.tipsy.js"></script>
	
	<script>
	var c1 = 0;
	
	$(function(){
		$(window).mousemove(function(){
			clearTipsy();
		});	
	});
	
	function getJSONFeed(qS){
		
		c1++;
		var queryString = qS;
		
		//Load JSON-encoded data from youtube-feed using a GET HTTP request.
		$.getJSON('http://gdata.youtube.com/feeds/api/videos?q=' + queryString + '&max-results=10&v=2&alt=json', function(data){
			
			//Loop trough each entry (a video).
			$(data.feed.entry).each(function(i){
				
				//Append data to table.
				$('#table1 > tbody').append('<tr><td>Video ' + i + '</td><td>' + this.title.$t.toString() + '</td><td>' + this.author[0].name.$t.toString() + '</td><td>' + this.yt$statistics.viewCount + '</td><td class="thumbUrl" style="display: none;">' + this.media$group.media$thumbnail[1].url.toString() + '</td>/tr>');
				
				//Append tipsy with thumbnail.
				$('tr:last').tipsy({gravity: 'w', fallback: '<img src="' + $('tr:last').find('.thumbUrl').html() + '" />', html:true, offset: 5});
			});
			
		});
		
	}
	
	//BUGFIX, remove old tipsies.
	function clearTipsy(){
		$('.tipsy:not(:first-child)').remove();
	}
	
	function onClick(){
		FB.api('/me/likes', function(response) {
				getJSONFeed(response.data[c1].name);		
		});
	}
	</script>
</head>
<body>
<div id="wrapper">
	<div id="content">
		<h1 style="text-align: center; margin-bottom: 10px;">Facebook/Youtube API Demo</h1>
		<p style="font-style: italic; text-align: center;"></p>
		<p id="2" style="font-style: italic; text-align: center;">Login with <strong>Facebook</strong> to get <strong>Youtube</strong> videos of things you like! ^_^</p>
		<button class="btn" onClick="onClick()" style=" width: 120px;margin: 20px auto 0px auto; display: block;">Click!</button>
		<fb:login-button id="loginBtnFB" size="large" scope="user_likes" style=" width: 76px;margin: 20px auto 0px auto; display: block;">Login</fb:login-button>
		
		<table id="table1" class="table table-condensed table-bordered table-striped" style="width: 900px; margin: 25px auto 20px auto;">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Owner</th>
					<th>Views</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
		
	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '476472759035994', // App ID
	      status     : true, // check login status
	      cookie     : true, // enable cookies to allow the server to access the session
	      xfbml      : true  // parse XFBML
	    });
	
		FB.Event.subscribe('auth.login', function(response) {
			window.location.reload();
		});

		FB.getLoginStatus(function(r){			
			if(r.status == 'connected'){
				var firstname;
				$('button').show(); $('#loginBtnFB').hide(); $('p:nth-child(1)').hide();
				
				//Invoke the graph.
				FB.api('/me', function(r){
					
					firstname = r.first_name;
					
					FB.api('/me/likes', function(r){
						console.log('al: ' + r.data.length);
						$('p:first').html('Hi <strong>' + firstname + '</strong>, we noticed you liked <strong>' + r.data.length + '</strong> things! ^_^');
						$('#2').html('Click to get some <strong> videos</strong> you like!');
					});
				});
				
			}
			
			else $('button').hide();
		});
	  };

	  //Load the SDK Asynchronously.
	  (function(d){
	     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement('script'); js.id = id; js.async = true;
	     js.src = "//connect.facebook.net/en_US/all.js";
	     ref.parentNode.insertBefore(js, ref);
	   }(document));
	</script>
</body>
</html>