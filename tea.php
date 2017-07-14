<?php

	// Shuffle shuffle shuffle the arrays
	function pickOne($array) {
		shuffle($array);
		return $array[mt_rand(0, (count($array) - 1))];
	}

	// Include slack library from https://github.com/10w042/slack-api
	include(__DIR__ . '/Slack.php');

	$config = array_merge(
		include(__DIR__ . '/config.skel.php'),
		include(__DIR__ . '/config.php')
	);

	// Remove our keyword from the text to extract name to exclude
	$exclude = str_replace($config['trigger_word'] . ' ', '', $_POST['text']);

	// Connect to Slack
	// Use authentication token found here: https://api.slack.com/
	// Scroll to the bottom and issue a token
	$Slack = new Slack($config['auth_token']);

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

	// Get a random user from the array
	$user = pickOne($teaMakers);

	// SEND OUT THE JSON!! Enjoy your brew
	header('Content-Type: application/json');
	echo json_encode(array(
		'text' => str_replace('{{USER}}', '<@' . $user['id'] . '>', pickOne($config['responses']))
	));

