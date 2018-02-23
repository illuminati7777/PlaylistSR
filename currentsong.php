<?php

require('config.php');

// make sure auth code matches
if ($_GET['auth'] === $auth) {

	require('db.php');

	// get list of requests in order
	$queue = mysqli_query($db, 'SELECT `id`, `song` FROM `requests` ORDER BY `id` ASC');

	// if there are no requests, continue playing a random song
	if (mysqli_num_rows($queue) === 0) {

		// pick a random song from the DB until we get one which hasn't played in the specified cooldown period
		do
		{
			// get random song info
			$nextsong = mysqli_query($db, 'SELECT `id`, `title`, `artist`, `file` FROM `songs` ORDER BY RAND() LIMIT 1');

			// set song info variables
			$song = mysqli_fetch_assoc($nextsong);
			$song_id = $song['id'];

			// check if it played in the specified cooldown period
			$check_played = mysqli_query($db, 'SELECT `id` FROM `played` WHERE `song` = "' . $song_id . '" AND `time` > DATE_SUB(NOW(), INTERVAL ' . $cooldown . ' SECOND) LIMIT 1');
			$result = mysqli_num_rows($check_played);
		}
		while ($result === 1);

	// the request queue isn't empty
	} else {

		// get the requested song info
		$request = mysqli_fetch_assoc($queue);
		$nextsong = mysqli_query($db, 'SELECT `title`, `artist`, `file` FROM `songs` WHERE `id` = "' . $request['song'] . '" LIMIT 1');

		// remove the request from the queue
		mysqli_query($db, 'DELETE FROM `requests` WHERE `id` = "' . $request['id'] .'"');

		// set song info variables
		$song = mysqli_fetch_assoc($nextsong);
		$song_id = $request['song'];

	}

	// add to played songs
	mysqli_query($db, 'INSERT INTO `played` (`song`) VALUES ("' . $song_id .'")');

	// output the song info in JSON
	echo json_encode($song);

// auth code doesn't match
} else {
	exit('access denied');
}