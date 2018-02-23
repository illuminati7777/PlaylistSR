<?php

require('config.php');

// make sure auth code matches
if ($_GET['auth'] === $auth) {

	// make sure ID is a digit
	if (ctype_digit($_GET['id'])) {

		$id = $_GET['id'];

		require('common.php');
		require('db.php');

		$song = mysqli_query($db, 'SELECT `file` FROM `songs` WHERE `id` = "' . $id . '"');

		$song_file = mysqli_fetch_assoc($song);

		$file = $music_folder . $song_file['file'];

// make sure file exists
if (file_exists($file)) {

	// set headers
	header('Content-Type: audio/mpeg');
	header('Content-Disposition: inline');
	header('Content-length: ' . filesize($file));
	header('Cache-Control: no-cache');
	header('Content-Transfer-Encoding: binary');

	// output file
	readfile($file);


} else {
    exit('file not found');
}



	// id is invalid
	} else {
		exit('invalid ID');
	}

// incorrect auth code
} else {
	exit('access denied');
}