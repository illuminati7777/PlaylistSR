<?php

require('config.php');
require('common.php');

// make sure auth code matches
if ($_GET['auth'] === $auth) {

?>

<html>
<head>
</head>
<body>

	<div id="song_info"></div>
	<div id="song_player" style="display: none;"><audio id="player" autoplay controls><source src="" type="audio/mpeg"></audio></div>
	
	<script type="text/javascript">

		var finished = false;

		window.onload = function () {
			document.getElementById('player').volume = 0.1;
		}

		document.getElementById('player').addEventListener('ended', function() {
			document.getElementById('player').currentTime = 0;
			finished = true;
		});


		setInterval(function() {
			if (document.getElementById('player').src === '' || finished === true) {
				finished = false;
				var ajax = new XMLHttpRequest();
				ajax.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var song = JSON.parse(this.responseText);
						document.getElementById('song_info').innerHTML = song['title'] + ' - ' + song['artist'];
						document.getElementById('player').src = 'song.php?id=' + song['id'] + '&auth=<?php echo $auth; ?>';
					}
				};
				ajax.open('GET', '<?php echo $script_folder; ?>currentsong.php?auth=<?php echo $auth; ?>', true);
				ajax.send();
			}
		}, 1000);

	</script>

</body>
</html>

<?php

// auth code doesn't match
} else {
	exit('access denied');
}

?>
