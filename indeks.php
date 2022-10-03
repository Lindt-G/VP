<?php
	require_once "../config.php";
	$author_name = "Gerhard Lindt";
	$full_time_now = date("d.m.Y H:i:s");
	$weekday_now = date("N");
	//echo $weekday_now;
	$weekdaynames_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	$hours_now = date("H");
	$part_of_day = "suvaline päeva osa";
	// <> >= <= == !=
	if($weekday_now <= 1 and $weekday_now <=5){
		if($hours_now < 7) {
		$part_of_day = "uneaeg";
		}
		// and or
		if($hours_now >= 8 and $hours_now < 18 ) {
		$part_of_day = "kooli aeg";
		}
		if($hours_now >= 7 and $hours_now < 8 ) {
		$part_of_day = "hommik";
		}
		if($hours_now >= 18 and $hours_now <=24 ) {
		$part_of_day = "vabaeg";
		}
	}
	else if($weekday_now == 6 or $weekday_now == 7){
		$part_of_day = "nadalavahetus";
	}
	//uurime semestrei kesmist
	$semester_begin = new DateTime("2022-9-5");
	$semester_end = new DateTime ("2022-12-18");
	$semester_duration = $semester_begin->diff($semester_end);
	$semester_duration_days = $semester_duration->format("%r%a");
	$from_semester_begin = $semester_begin->diff(new DateTime("now"));
	$from_semester_begin_day = $from_semester_begin->format("%r%a");
	
	$wisdom_words = ["kingitud hobuse suhu ei vaata", "hommik on ohtust targem", "Enne töö, siis lõbu"];

	$todays_adjective = "sisesta omadusona";
	if(isset($_POST["todays_adjective_input"]) and !empty($_POST["todays_adjective_input"])){
		$todays_adjective = $_POST["todays_adjective_input"];
	}
	//pildi valimine
	$photo_dir = "pildid";
	$all_files = array_slice(scandir($photo_dir), 2);
	$allowed_photo_types = ["image/jpeg", "image/png"];
	$photo_files = [];
	foreach ($all_files as $filename) {
		$file_info = getimagesize($photo_dir ."/" .$filename);
		if (isset($file_info["mime"])) {
			if (in_array($file_info["mime"], $allowed_photo_types)) {
				array_push($photo_files, $filename);
			}
		}
	}
	$photo_index = mt_rand(0, count($photo_files)-1);
	if (isset($_POST["photo_select"]) and $_POST["photo_select"] >= 0) {
		$photo_index = $_POST["photo_select"];
	}
	$photo_src = $photo_dir ."/" .$photo_files[$photo_index];
	$photo_html = '<img src="' .$photo_src .'" alt="Tallinna pilt">';
	$todays_adjective = "tavaline";
	if (isset($_POST["todays_adjective_input"]) and !empty($_POST["todays_adjective_input"]))
	$todays_adjective = $_POST["todays_adjective_input"];
	// kas klikiti paeva kommentaari nuppu
	$comment_error = null;
	$grade = 7;

	if (isset($_POST["comment_submit"])) {
		if (isset($_POST["comment_input"]) and !empty($_POST["comment_input"])) {
			$comment = $_POST["comment_input"];
		} else {
			$comment_error = "Kommentaar jäi kirjutamata!";
		}
		
		$grade = $_POST["grade_input"];

		if (empty($comment_error)) {
			// loon andmebaasiga uhenduse
			// server, kasutaja, parool, andmebaas
			$db_connection = new mysqli($server_host, $server_user_name, $server_password, $database);

			// maaran suhtlemisel kasutatava kooditabeli
			$db_connection->set_charset("utf8");

			// valmistame ette andmete saatmise SQL käsu
			$stmt = $db_connection->prepare("INSERT INTO vp_daycomment (comment, grade) VALUES (?, ?)");
			echo $db_connection->error;

			// seome SQL käsu oigete andmetega
			// andmetüübid: i - integer, d - decimal, s - string
			$stmt->bind_param("si", $comment, $grade);
			if ($stmt->execute()) {
				$grade = 7;
				$comment = null;
			}

			// sulgeme käsu
			$stmt->close();

			// sulgeme andmebaasi uhenduse
			$db_connection->close();
		}
	}
?>


<!DOCTYPE html>
<html lang="et">
	<head>
		<meta charset="utf-8">
		<title><?php echo $author_name;?>	programeerib veebi</title>
	</head>
	<body>
	<img src="photos/vp_banner_gs.png" alt="banner">
	<h1>Gerhard Lindt Programeerib Veebi</h1>
		<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat sisu!</p>
		<p><a href="read_comments.php" target="_blank">Lappa kommentaare</a> või <a href="read_film.php" target="_blank">lappa filme</a> või isegi <a href="write_film.php" target="_blank">lisa uusi filme</a></p>
		<p>Õppetöö toimus <a href="https://www.tlu.ee" target="_blank">Tallinna ülikoolis</a></p>
		<a href="https://www.tlu.ee" target="_blank"><img src="pildid/tlu_37.jpg" alt="Tallinna Ülikooli Uus Astra Õppehoone"></a>
		<p> Lehe avamise hetk oli: <?php echo $full_time_now;?> ja nädalapäev oli <?php echo $weekdaynames_et [$weekday_now-1];?></p> 
		<p> Praegu on <?php echo $part_of_day;?></p>
		<p> Semester kestab veel <?php echo $semester_duration_days;?> päeva ja on kestnud <?php echo $from_semester_begin_day;?> päeva</p>
		<p> Vanasona: <?php echo $wisdom_words[mt_rand(0, count($wisdom_words)-1)]; ?></p>
		<form method="POST"> 
			<input type="text" id="todays_adjective_input" name="todays_adjective_input" placeholder="sisesta omadusona">
			<input type="submit" id="todays_adjective_submit"  name="todays_adjective_submit" value="saada omadussona!">
		</form>
		<p> Tana on <?php echo $todays_adjective; ?> paev </p>
	<hr>

	<form method="POST">
		<label for="comment_input">Kommentaar tänase päeva kohta (140 tähte)</label>
		<br>
		<textarea id="comment_input" name="comment_input" cols="35" rows="4" placeholder="Kommentaar"></textarea>
		<br>
		<label for="grade_input">Hinne tänasele päevale (0 - 10)</label>
		<input type="number" id="grade_input" name="grade_input" min="0" max="10" step="1" value="<?php echo $grade; ?>">
		<br>
		<input type="submit" id="comment_submit" name="comment_submit" value="Salvesta">
		<span><?php echo $comment_error; ?></span>
	</form>

	<hr>
		<form method="POST">
		<select id="photo_select" name="photo_select">
			<?php 
				// <option value="0">tlu_5.jpg</option>
				// loome rippmenuu valikud
				$select_html = '<option value="" disabled>Vali pilt</option>';
				for ($i = 0; $i < count($photo_files); $i++) {
					if ($i == $photo_index) {
						$select_html .= '<option value="' .$i .'" selected>' .$photo_files[$i] ."</option>";
					} else {
						$select_html .= '<option value="' .$i .'">' .$photo_files[$i] ."</option>";
					}
				}
				echo $select_html;
			?>
		</select>
		<input type="submit" id="photo_submit" name="photo_submit" value="Määra pilt">
	</form>

	<hr>
		<?php echo $photo_html; ?>
	<hr>
	</body>

</html>