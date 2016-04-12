<?php

	/*
		Couple of functions to get a random tea meme
	*/

	function get_url_contents($url) {
	    $crl = curl_init();

	    curl_setopt($crl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
	    curl_setopt($crl, CURLOPT_URL, $url);
	    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);

	    $ret = curl_exec($crl);
	    curl_close($crl);
	    return $ret;
	}

	function get_tea_meme() {
		// Google API key
		$api_key 			= 'YOUR_API_KEY';
		// Google Custom Search Engine ID
		$cse_id 			= 'YOUR_CSE_ID';
		// Search Term
		$search_term	= 'tea+meme';

		// Do a Google API call to find a tea meme
		$results_page = rand(1,5); 
		$json 				= get_url_contents('https://www.googleapis.com/customsearch/v1?key='.$api_key.'&cx='.$cse_id.'&q='.$search_term.'&searchType=image&alt=json&imgSize=medium&start='.$results_page); 
		$data 				= json_decode($json);
		$result_num 	= rand(0,9); // get a random result from the returned data
		$data 				= $data->items[$result_num];
		// return a random meme!
		return $data->link;
	}

	/*
		The real bot logic
	*/


	$auth_token = 'AUTH TOKEN';

	$trigger_word = '!tea';
	$responses = array(
		"It's about time {{USER}} put the kettle on - off you trot!",
		"Pop the kettle on {{USER}} - it's your turn to make a cuppa",
		"Who wants a drink? {{USER}} is heading to the kitchen to make one",
		"Coffee? Tea? Sugar? Peppermint Tea? Green Tea? Get your orders in as {{USER}} is making a round",
		"That's very nice of {{USER}} to make a round of tea!",
		"Mine is milk 2 sugars please {{USER}} - what about everyone else?",
		"The tea maker is... {{USER}}! Get brewing."
	);

	// Include slack library from https://github.com/10w042/slack-api
	include 'Slack.php';

	// Remove our keyword from the text to extract name to exclude
	$exclude = str_replace($trigger_word . ' ', '', $_POST['text']);

	// Connect to Slack
	// Use authentication token found here: https://api.slack.com/
	// Scroll to the bottom and issue a token
	$Slack = new Slack($auth_token);

	// Get the info for the channel requested from
	$data = $Slack->call('channels.info', array('channel' => $_POST['channel_id']));

	$teaMakers = array();

	// Loop through channel members
	foreach ($data['channel']['members'] as $m) {
		// Get user data
		$userData = $Slack->call('users.info', array('user' => $m));
		// Check to see if the user is online before adding them to list of brewers
		$presence = $Slack->call('users.getPresence', array('user' => $m));
		
		$user = $userData['user'];

		// If there is an exclude, check to see if it matches a user real name (lowercase)
		// If it does not, add it to the $teaMakers array
		if($presence['presence'] == 'active')
			if($exclude) {
				if(!(strpos(strtolower($user['real_name']), strtolower($exclude)) !== false))
					$teaMakers[] = $user;
			} else {
				$teaMakers[] = $user;
			}
	}

	// Shuffle shuffle shuffle the arrays
	function pickOne($array) {
		shuffle($array);
		return $array[mt_rand(0, (count($array) - 1))];
	}

	// Get a random user from the array
	$user = pickOne($teaMakers);

	// SEND OUT THE JSON!! Enjoy your brew
	header('Content-Type: application/json');
	$append_meme = ($show_meme) ? "\n\r" . get_tea_meme() : '' ;
	echo json_encode(
		array(
		'text' => str_replace('{{USER}}', '<@' . $user['id'] . '>', pickOne($responses)) . $append_meme
		)
	);

