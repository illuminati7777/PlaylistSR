<?php

require('config.php');
require('db.php');

$songs = mysqli_query($db, 'SELECT `id`, `title`, `artist` FROM `songs` ORDER BY `artist` ASC');

?>

<html>
<head>
	<style type="text/css">

		body {
			margin: 0;
			font: 16px Helvetica, sans-serif;
		}

		.song {
			clear: both;
			padding: 10px 30px;
			text-align: center;
		}

		.song:nth-of-type(odd) {
			background-color: #EEE;
		}

		.id {
			width: 6%;
			float: left;
			color: #007FFF;
		}

		.artist {
			float: right;
			width: 47%;
		}

		.title {
			width: 48%;
		}

	</style>
</head>

<body>

<?php

while ($song = mysqli_fetch_assoc($songs))
{

?>

	<div class="song">
		<div class="id"><?php echo $command . ' ' . $song['id']; ?></div>
		<div class="artist"><?php echo $song['artist']; ?></div>
		<div class="title"><?php echo $song['title']; ?></div>
	</div>

<?php

}

?>

</body>
</html>