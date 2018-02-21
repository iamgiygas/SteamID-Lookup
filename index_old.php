<?php
error_reporting(1);
require("functions.php");
$startTime = startTimer();
bcscale(0);
if(isset($_GET['searchBar'])){$steamid=htmlentities($_GET['searchBar']);}else{$steamid="";} ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>SteamID Search</title>
		<meta name="keywords" content="Steam, Tool, Search, DJ Wolf" />
		<meta name="description" content="Searches the Steam database for a player then returns the player information. (If Any)" />
		<link rel="stylesheet" href="bulma-0.6.2/css/bulma.css">
		<script defer src="https://use.fontawesome.com/releases/v5.0.0/js/all.js"></script>
		<link rel="icon" type="image/png" href="images/favicon.png" />		
	</head>
	<body>
<noscript>
	<div class="container-fluid d-flex justify-content-center">
	<div class="alert alert-dismissible alert-danger">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	Javascript is disabled on your browser! Expect everything to be broken. Please enable Javacript for us, the site only uses it for good purposes! Not all of us are evil!
	</div>
</div>
</noscript>	
<? require("steamauth/steamauth.php"); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">SteamID Lookup</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarColor03">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#"></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="">Home<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
	  <? echo '<a class="nav-link" href="' . htmlspecialchars($steamprofile['avatarfull']) . '">Welcome, ' . htmlspecialchars($steamprofile['personaname']) . '!</a>'; ?>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Features</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Pricing</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" placeholder="Search" id="searchBar" name="searchBar" type="text" placeholder="Search here">
      <button class="btn btn-secondary my-2 my-sm-0" id="searchButton" type="submit">Search</button>
    </form>
  </div>
</nav>
			<?php if($steamid!="" or $steamid!==" ") echo '<div class="jumbotron">Result for '.htmlentities($steamid).''; ?>					
				<?php if ($steamid=="" or $steamid==" "){
					echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">You must put a SteamID, CommunityID, or custom URL to search.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				}else
				{
					$xmlf = buildSteamURL($steamid);
					libxml_use_internal_errors(TRUE);
					$xml = simplexml_load_file($xmlf);
					if(!isset($steam64)){$steam64 = $xml->steamID64;}
					if(!isset($steam32)){$steam32 = get_steamid_community($steam64);}
					
					$status['offline']="Offline";
					$status['online']="Online";
					$status['in-game']=str_ireplace('-</span> <a class="friend_join_game_link"',"- <a",str_ireplace("<br />",": ",$xml->stateMessage));
					
					$privacy['public']=str_ireplace('Public',"",str_ireplace("<span class=\"badge badge-pill badge-success\">Success</span>",": ",$xml->stateMessage));;
					$privacy['public']="Public";
					$privacy['usersonly']="Steam Users Only";
					$privacy['friendsfriendsonly']="Friends Of Friends";
					$privacy['friendsonly']="Friends Only";
					$privacy['private']="Private";	
					
					$vac['0']="Good";
					$vac['1']="Banned";
					if(libxml_get_errors()!=NULL){
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Oops. Steam is overloaded. Try again Later, or <a href="?'.htmlentities($_SERVER['QUERY_STRING']).'">Re-Search</a> now.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}elseif(error_get_last()!=NULL){
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">An Error has occurred. Try again later, or <a href="?'.htmlentities($_SERVER['QUERY_STRING']).'">Re-Search</a> now.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}elseif($xml->error=="The specified profile could not be found." || $xml->error=="115"){
						echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">You searched for a Player that does not exist. Did you type it in correctly?<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}elseif($xml->privacyMessage){ ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">This user doesn't have a profile set up!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
						
						<table class="table table-hover table-sm">
							<thead>
								<tr>
									<th scope="col">Label</th>
									<th scope="col">Information</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>SteamID32</td>
									<td><?php echo htmlentities($steam32); ?></th>
								</tr>
								<tr>
									<td>SteamID64</td>
									<td><?php echo htmlentities($steam64); ?></td>
								</tr>
								<tr>
									<td><abbr title="Valve Anticheat" class="initialism">VAC</abbr> Status</td>
									<td><?php echo htmlentities($vac["$xml->vacBanned"]); ?></td>
								</tr>
								<tr>
									<td>Trade Ban</td>
									<td><?php echo htmlentities($xml->tradeBanState); ?></td>
								</tr>
								<tr>
									<td>Link</td>
									<td><a href="http://steamcommunity.com/profiles/<?php echo htmlentities($xml->steamID64); ?>" title="Click here to go to <?php echo htmlentities($xml->steamID); ?>'s Steam Page" target="_blank">Click Here</a></td>
								</tr>
								<tr>
									<td>Add to Friends</td>
									<td><a href="steam://friends/add/<?php echo htmlentities($xml->steamID64); ?>" title="Click to add <?php echo htmlentities($xml->steamID); ?> to your friends list">Click Here</a></td>
								</tr>
								<tr>
									<td>XML Version</td>
									<td><a href="<?php echo htmlentities($xmlf); ?>" title="XML File" target="_blank">Click Here</a></td>
								</tr>
							</tbody>
						</table>
					<?php }else{
						$username = $xml->steamID;
						if($xml->privacyState!="public"){$steamRating= "Profile Private";}else{$steamRating = $xml->steamRating;}
						if($xml->privacyState!="public"||!isset($xml->hoursPlayed2Wk)){$playTime="Unavailable";}else{$playTime = $xml->hoursPlayed2Wk;} ?>
						
						<table class="table table-hover table-sm">
								<thead>
									<tr>
										<th scope="col">Label</th>
										<th scope="col">Information</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Steam Picture</td>
										<td><img  max-width: 10% height: 10% class="img-thumbnail" src="<?php echo htmlentities($xml->avatarFull); ?>" alt="<?php echo htmlentities($xml->headline); ?>"></td>
									</tr>
									<tr>
										<td>Username</td>
										<td><?php echo htmlentities($username); ?></td>
									</tr>
									<tr>
										<td>Status</td>
										<td><?php echo $status["$xml->onlineState"]; ?></td>
										<?php if($xml->onlineState == "offline" & $xml->privacyState=="public"){ echo '<div>'.htmlentities($xml->stateMessage).'</div>'; } ?>
									</tr>
									<tr>
										<td>Profile State</td>
										<td><?php echo htmlentities($privacy["$xml->privacyState"]); ?></td>
									</tr>
									<tr>
										<td>Steam Rating</td>
										<td><?php echo htmlentities($steamRating); ?></td>
									</tr>
									<tr>
										<td>Playtime (Hours, 2 Weeks)</td>
										<td><?php echo htmlentities($playTime); ?></td>
									</tr>
									<tr>
										<td>SteamID32</td>
										<td><?php echo htmlentities($steam32); ?></td>
									</tr>
									<tr>
										<td>SteamID64</td>
										<td><?php echo htmlentities($steam64); ?></td>
									</tr>
									<tr>
										<td><abbr title="Valve Anticheat" class="initialism">VAC</abbr> Status</td>
										<td><?php echo htmlentities($vac["$xml->vacBanned"]); ?></td>
									</tr>
									<tr>
										<td>Trade Ban</td>
										<td><?php echo htmlentities($xml->tradeBanState); ?></td>
									</tr>
									<tr>
									<td>Link</td>
										<td><button type="button" onclick="window.location.href='https://steamcommunity.com/profiles/<?=$xml->steamID64?>'" class="btn btn-primary btn-block">Steam Page</button></td>
									</tr>
									<tr>
										<td>Add to Friends</td>
										<td><button type="button" onclick="window.location.href='steam://friends/add/<?=$xml->steamID64?>'" class="btn btn-primary btn-block">Add on Steam</button></td>
									</tr>
									<tr>
										<td>XML Version</td>
										<td><button type="button" onclick="window.location.href='<?=$xmlf?>'" class="btn btn-primary btn-block">Raw XML</button></td>
									</tr>
							</tbody>
						</table>
					<?php }
					} ?>
			</div>
</div>
	<div id="footer" class="fixed-bottom">
All trademarks are the property of their respective owners. <a href="http://steampowered.com" target="_blank">Powered by Steam</a>
	</div>
</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>