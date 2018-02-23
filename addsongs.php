<?php

require('config.php');

// make sure auth code matches
if ($_GET['auth'] === $auth) {

	require('common.php');
	require('db.php');

	// get list of songs from DB
	$songs_query = mysqli_query($db, 'SELECT `file` FROM `songs`');
	$song_list = [];

	// add to array
	while ($song = mysqli_fetch_assoc($songs_query)) {
		$song_list[] = $song['file'];
	}

	// get list of songs in folder
	$songs = array_diff(scandir($music_folder), array('.', '..'));

	// check if songs already exist in DB
	foreach ($songs as $song) {
		if (!in_array($song, $song_list)) {

			// song doesn't exist in DB
			$file = $song;

			// get tag info
			$tags = explode("\n", shell_exec('mid3v2 ' . $music_folder . escapeshellarg($song)));

			foreach ($tags as $tag) {
				// get title
				if (substr($tag, 0, 4) === 'TIT2') {
					$title = substr($tag, 5);
				}

				// get artist
				if (substr($tag, 0, 4) === 'TPE1') {
					$artist = substr($tag, 5);
				}			
			}

			echo $title . $artist;

			mysqli_query($db, 'INSERT INTO `songs` (`title`, `artist`, `file`) VALUES ("' . mysqli_real_escape_string($db, $title) . '", "' . mysqli_real_escape_string($db, $artist) . '", "' . mysqli_real_escape_string($db, $file) . '")') or die(mysqli_error($db));

		}
	}

// auth code doesn't match
} else {
	exit('access denied');
}