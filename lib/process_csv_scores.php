<?php
  class CSVScoreProcessor {

    public static function process($csvAsArray, $gameid, $course) {
      if (!empty($csvAsArray)) {

        $ready_scores = array();

        foreach ($csvAsArray as $row_number => $row_content) {
          $readob = false; // Should ob values be looked for. Resets to false for each row.
          $current_player_firstname = "";
          $score_array = array(); // values to be added after row is read, if readob=true
          $ob_array = array(); // same

          foreach ($row_content as $content_number => $content) {
            /* Frisbeegolf-tulossovelluksen tekemissä csv-tiedostoissa on encoding UTF-16 LE.
            En aluksi tajunnut tätä, mutta löysin seuraavan rivin ratkaisun joka toimii silti.
            Tämä tapa poistaa kuitenkin ei-ASCII-merkit (mm. ä, ö). En saanut UTF-16LE:n muunnosta toimimaan.
            Source: http://stackoverflow.com/a/8781968 */
            $content = preg_replace('/[[:^print:]]/', '', $content);

            // Cycle through every playername in the database and search for them in csv input rows
            // NOTE: All first names have to be unique for this...
            // that is how the app that makes the csvs works.
            $firstnames = Player::all_firstnames();

            foreach ($firstnames as $firstname) {
              if (strpos($content, $firstname) !== false) { // = if substring($firstname) is inside string($content)
                $readob = true; // Row contains player scores, so look for ob values on this row.
                $current_player_firstname = $firstname;

                $scores = explode(";", $content);

                // First score is at index 1, and last is at count($scores) - 3.
                for ($i = 1; $i < count($scores) - 3; $i++) {
                  array_push($score_array, (int)$scores[$i]);
                }

                // First ob value is at the end of this $content, so last value of $scores.
                array_push($ob_array, (int)$scores[count($scores) - 1]);
              }
            }

            if ($readob && !(strpos($content, $current_player_firstname) !== false)) {
              // Add every ob value to $ob_array.
              array_push($ob_array, $content);
            }
          }

          // If true, then player scores were just read and are ready to be added
          if ($readob) {
            // INSERT a new score row into score table
            // $sql_score_insert = "INSERT INTO score (gameid, playerid";
            //
            // for ($i = 1; $i <= count($score_array); $i++) {
            //   $sql_score_insert = $sql_score_insert . ", hole" . $i;
            // }
            // for ($i = 1; $i <= count($ob_array); $i++) {
            //   $sql_score_insert = $sql_score_insert . ", obhole" . $i;
            // }
            //
            // $current_playerid = $connector->get_player_id($current_player_firstname);
            // $sql_score_insert = $sql_score_insert . ") VALUES (" . $gameid . ", " . $current_playerid;
            //
            // foreach ($score_array as $score) {
            //   $sql_score_insert = $sql_score_insert . ", " . $score;
            // }
            // foreach ($ob_array as $ob_value) {
            //   $sql_score_insert = $sql_score_insert . ", " . $ob_value;
            // }
            //
            // $sql_score_insert = $sql_score_insert . ")";

            for ($i = 0; $i < count($score_array); $i++) {
              $current_playerid = Player::find_by_firstname($current_player_firstname)->playerid;

              $new_score = new Score(array(
                'gameid' => $gameid,
                'holeid' => $course->holes[$i]->holeid,
                'playerid' => $current_playerid,
                'stroke' => $score_array[$i],
                'ob' => $ob_array[$i]
              ));
              $ready_scores[] = $new_score;
              Kint::dump($new_score);
            }
          }
        }

        // Validate scores
        $errors = array();

        foreach ($ready_scores as $score) {
          $errors = array_merge($errors, $score->errors());
        }

        if (count($errors) == 0) {
          // Scores were all valid
          // Save scores
          foreach ($ready_scores as $score) {
            $score->save();
          }
        }

        return $errors;
      }
    }
  }
