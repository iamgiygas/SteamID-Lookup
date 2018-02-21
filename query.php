<?php
if(isset($_GET['q'])){$steamid=htmlentities($_GET['q']);}else{$steamid="";}?>
<?
if ($steamid=="" or $steamid==" "){
	echo '<div class="notification is-danger"><button class="delete"></button>You must put a SteamID, CommunityID, or custom URL to search.</div>';
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
					
	$privacy['public']="Public";
	$privacy['usersonly']="Steam Users Only";
	$privacy['friendsfriendsonly']="Friends Of Friends";
	$privacy['friendsonly']="Friends Only";
	$privacy['private']="Private";	
					
	$vac['0']="Good";
	$vac['1']="Banned";
	if(libxml_get_errors()!=NULL){
	echo '<div class="notification is-danger"><button class="delete"></button>Oops. Steam is overloaded. Try again Later, or <a href="?'.htmlentities($_SERVER['QUERY_STRING']).'">Re-Search</a> now.</div>';
	}elseif(error_get_last()!=NULL){
	echo '<div class="notification is-danger"><button class="delete"></button>Oops. An error has occurred. Try again Later, or <a href="?'.htmlentities($_SERVER['QUERY_STRING']).'">Re-Search</a> now.</div>';
	}elseif($xml->error=="The specified profile could not be found." || $xml->error=="115"){
	echo '<div class="notification is-warning"><button class="delete"></button>It looks like your search didn\'t find anything.</div>';
	}elseif($xml->privacyMessage){ ?>
	<div class="notification is-danger"><button class="delete"></button>The user you searched for has not set up their Steam profile yet.</div>
						
<div class="box">
	<article class="media">
		<div class="media-left">
			<figure class="image is-64x64">
				<img src="<?php echo htmlentities($xml->avatarMedium); ?>" alt="<?php echo htmlentities($xml->headline); ?>">
					</figure>
		</div>
		<div class="media-content">
		<div class="content">
			<p>
				<strong><?php echo htmlentities($username); ?></strong> <small><?php echo $status["$xml->onlineState"]; ?><?php if($xml->onlineState == "offline" & $xml->privacyState=="public"){ echo '<div>'.htmlentities($xml->stateMessage).'</div>'; } ?></small> <small><?php echo htmlentities($playTime); ?> h</small>
					<br>
		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">SteamID32:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($steam32); ?>" readonly>
			</p>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">SteamID64:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($steam64); ?>" readonly>
			</p>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">VAC Status:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($vac["$xml->vacBanned"]); ?>" readonly>
			</p>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Profile Link:</label>
		</div>
			<a href="http://steamcommunity.com/profiles/<?php echo htmlentities($xml->steamID64); ?>" title="Click here to go to <?php echo htmlentities($xml->steamID); ?>'s Steam Page" target="_blank">Click Here</a>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Friend Link:</label>
		</div>
			<a href="steam://friends/add/<?php echo htmlentities($xml->steamID64); ?>" title="Click to add <?php echo htmlentities($xml->steamID); ?> to your friends list">Click Here</a>
		</div>
		</div>
        </p>
      </div>
    </div>
  </article>
</div>
					<?php }else{
						$username = $xml->steamID;
						if($xml->privacyState!="public"){$steamRating= "Profile Private";}else{$steamRating = $xml->steamRating;}
						if($xml->privacyState!="public"||!isset($xml->hoursPlayed2Wk)){$playTime="Unavailable";}else{$playTime = $xml->hoursPlayed2Wk;} ?>
						
<div class="box">
	<article class="media">
		<div class="media-left">
			<figure class="image is-64x64">
				<img src="<?php echo htmlentities($xml->avatarMedium); ?>" alt="<?php echo htmlentities($xml->headline); ?>">
					</figure>
		</div>
		<div class="media-content">
		<div class="content">
			<p>
				<strong><?php echo htmlentities($username); ?></strong> <small><?php echo $status["$xml->onlineState"]; ?><?php if($xml->onlineState == "offline" & $xml->privacyState=="public"){ echo '<div>'.htmlentities($xml->stateMessage).'</div>'; } ?></small> <small><?php echo htmlentities($playTime); ?> h</small>
					<br>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Steam Rating:</label>
		</div>
<?php echo htmlentities($steamRating); ?>
		</div>
		
		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Privacy:</label>
		</div>
<?php echo htmlentities($privacy["$xml->privacyState"]); ?>
		</div>
		
		<?php echo $status["$xml->onlineState"]; ?>
<?php if($xml->onlineState == "offline" & $xml->privacyState=="public"){ echo '<div>'.htmlentities($xml->stateMessage).'</div>'; } ?>
		</div>
		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Privacy:</label>
		</div>
<?php echo htmlentities($privacy["$xml->privacyState"]); ?>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">SteamID32:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($steam32); ?>" readonly>
			</p>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">SteamID64:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($steam64); ?>" readonly>
			</p>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">VAC Status:</label>
		</div>
		<div class="field-body">
		<div class="field">
			<p class="control">
				<input class="input is-static" type="text" value="<?php echo htmlentities($vac["$xml->vacBanned"]); ?>" readonly>
			</p>
		</div>
		</div>
		</div>
		
		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Trade Status:</label>
		</div>
<?php echo htmlentities($xml->tradeBanState); ?>
		</div>
		</div>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Profile Link:</label>
		</div>
			<a href="http://steamcommunity.com/profiles/<?php echo htmlentities($xml->steamID64); ?>" title="Click here to go to <?php echo htmlentities($xml->steamID); ?>'s Steam Page" target="_blank">Click Here</a>
		</div>

		<div class="field is-horizontal">
		<div class="field-label is-normal">
			<label class="label">Friend Link:</label>
		</div>
			<a href="steam://friends/add/<?php echo htmlentities($xml->steamID64); ?>" title="Click to add <?php echo htmlentities($xml->steamID); ?> to your friends list">Click Here</a>
		</div>
		</div>
        </p>
      </div>
    </div>
  </article>
</div>

    </div>
  </div>
</section>
					<?php }
					} ?>