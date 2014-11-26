<?php

	// Include slack library from https://github.com/10w042/slack-api
	include 'Slack.php';

	// Remove our keyword from the text to extract name to exclude
	$exclude = str_replace('!tea ', '', $_POST['text']);

	// Connect to Slack
	// Use authentication token found here: https://api.slack.com/
	// Scroll to the bottom and issue a token
	$Slack = new Slack('AUTH TOKEN');

	// Get the info for the channel requested from
	$data = $Slack->call('channels.info', array('channel' => $_POST['channel_id']));

	$teaMakers = array();

	// Loop through channel members
	foreach ($data['channel']['members'] as $m) {
		// Get user data
		$userData = $Slack->call('users.info', array('user' => $m));
		$user = $userData['user'];

		// If there is an exclude, check to see if it matches a user real name (lowercase)
		// If it does not, add it to the $teaMakers array
		if($exclude) {
			if(!(strpos(strtolower($user['real_name']), strtolower($exclude)) !== false))
				$teaMakers[] = $user;
		} else {
			$teaMakers[] = $user;
		}
	}

	// Shuffle the array
	shuffle($teaMakers);

	// Get a random user from the array
	$user = $teaMakers[rand(0, (count($teaMakers) - 1))];

	// SEND OUT THE JSON!! Enjoy your brew
	header('Content-Type: application/json');
	echo json_encode(array(
		'text' => 'The tea maker is..... <@' . $user['id'] . '|' . $user['name'] . '>!! Get brewing.'
	));
