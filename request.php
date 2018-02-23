<?php 

require('config.php');

// make sure auth code matches
if ($_GET['auth'] === $auth) {

	// make sure song request ID is only numbers so it's safe to use in SQL queries
	if (ctype_digit($_GET['id'])) {

		$id = $_GET['id'];

		require('db.php');

		// check if song has played in specified cooldown
		$check_played = mysqli_query($db, 'SELECT `id` FROM `played` WHERE `song` = "' . $id . '" AND `time` > DATE_SUB(NOW(), INTERVAL ' . $cooldown . ' SECOND) LIMIT 1');
		if (mysqli_num_rows($check_played) === 0) {

			// check if song is already requested
			$check_requested = mysqli_query($db, 'SELECT `id` FROM `requests` WHERE `song` = "' . $id . '" LIMIT 1');
			if (mysqli_num_rows($check_requested) === 0) {

				// song hasn't played or been requested
				// get song info
				$song = mysqli_query($db, 'SELECT `title`, `artist` FROM `songs` WHERE `id` = "' . $id . '"');

				// no song matches with that ID
				if (mysqli_num_rows($song) === 1) {

					// add to request queue
					mysqli_query($db, 'INSERT INTO `requests` (`song`, `requestor`) VALUES ("' . $id . '", "' . $_GET['name'] . '")');

					// get song info
					$request = mysqli_fetch_assoc($song);

					// inform user song has been added
					exit('@' . $_GET['name'] . ' , Your song "' . $request['title'] . ' - ' . $request['artist'] . '" has been added to the queue');

				// no song exists with specified ID	
				} else {
					exit('@' . $_GET['name'] . ' , No song exists with that ID');
				}

			// song is already in the request queue
			} else {
				exit('@' . $_GET['name'] . ' , That song has already been requested');
			}

		// song has already played in the specified cooldown
		} else {
			exit('@' . $_GET['name'] . ' , That song has already played recently');
		}

	// specified song ID is invalid (not a number)
	} else {
		exit('@' . $_GET['name'] . ' , Invalid song ID');
	}

// auth code doesn't match
} else {
	exit('access denied');
}