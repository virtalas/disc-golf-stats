<?php
  class Score extends BaseModel {

    public $scoreid, $gameid, $holeid, $playerid, $stroke, $ob, $legal, // Ready to use after creation
            $hole_par, $hole_num; // Needs to be prepared in all_game_scores()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_stroke', 'validate_ob');
    }

    /*
    *  Database functions
    */

    public function save() {
      $sql = "INSERT INTO score (gameid, holeid, playerid, stroke, ob, legal)
              VALUES (:gameid, :holeid, :playerid, :stroke, :ob, :legal) RETURNING scoreid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid,
                            'holeid' => $this->holeid,
                            'playerid' => $this->playerid,
                            'stroke' => $this->stroke,
                            'ob' => $this->ob,
                            'legal' => $this->legal));
      $row = $query->fetch();
      $this->scoreid = $row['scoreid'];
    }

    public function update() {
      $sql = "UPDATE score SET stroke = :stroke, ob = :ob, legal = :legal WHERE scoreid = :scoreid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array(
        'stroke' => $this->stroke,
        'ob' => $this->ob,
        'legal' => $this->legal,
        'scoreid' => $this->scoreid
      ));
    }

    public function destroy() {
      $sql = "DELETE FROM score WHERE scoreid = :scoreid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('scoreid' => $this->scoreid));
    }

    /*
    *  Information functions
    */

    public static function players_birdies($playerid) {
      $sql = "SELECT COUNT(*) as birdies
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              WHERE score.playerid = :playerid
              AND hole.par - (score.stroke + score.ob) = 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['birdies'];
    }

    public static function players_aces($playerid) {
      $sql = "SELECT COUNT(*) as aces
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              WHERE score.playerid = :playerid
              AND score.stroke = 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['aces'];
    }

    public static function players_eagles($playerid) {
      // Hole in ones are not counted as eagles.
      $sql = "SELECT COUNT(*) as eagles
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              WHERE score.playerid = :playerid
              AND hole.par - (score.stroke + score.ob) = 2
              AND score.stroke != 0
              AND score.stroke != 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['eagles'];
    }

    public static function all_game_scores($game) {
      $players = Game::games_players($game->gameid);
      $player_scores = array();
      $totals = array();

      foreach ($players as $player) {
        $sql = "SELECT hole.hole_num, hole.par, score.scoreid, score.stroke, score.ob, score.legal FROM score
                JOIN hole ON score.holeid = hole.holeid
                WHERE score.gameid = :gameid AND score.playerid = :playerid
                ORDER BY hole.hole_num ASC";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('gameid' => $game->gameid, 'playerid' => $player->playerid));
        $rows = $query->fetchAll();

        $scores = array();
        $total_score = 0;

        foreach ($rows as $row) {
          $score = new Score(array(
            'hole_num' => $row['hole_num'],
            'hole_par' => $row['par'],
            'scoreid' => $row['scoreid'],
            'stroke' => $row['stroke'],
            'ob' => $row['ob'],
            'legal' => $row['legal']
          ));
          $scores[] = $score;
          $total_score += (int) $row['stroke'];
          $total_score += (int) $row['ob'];
        }

        $player_scores['player'. $player->playerid] = $scores;
        $totals['player'. $player->playerid] = $total_score;
      }

      // Sort array by total score
      uasort($player_scores, function($a, $b) {
        $a_total_score = 0;
        foreach ($a as $score) {
          $a_total_score += $score->stroke;
          $a_total_score += $score->ob;
        }

        $b_total_score = 0;
        foreach ($b as $score) {
          $b_total_score += $score->stroke;
          $b_total_score += $score->ob;
        }

        return $a_total_score - $b_total_score;
      });

      uasort($totals, function($a, $b) {
        return $a - $b;
      });

      $game->scores = $player_scores;
      $game->total_scores = $totals;
    }

    // public static function all_game_scores($gameid) {
    //   $players = Game::games_players($gameid);
    //   $player_scores = array();
    //
    //   foreach ($players as $player) {
    //     $sql = "SELECT hole.hole_num, hole.par, score.scoreid, score.stroke, score.ob, score.legal FROM score
    //             JOIN hole ON score.holeid = hole.holeid
    //             WHERE score.gameid = :gameid AND score.playerid = :playerid
    //             ORDER BY hole.hole_num ASC";
    //     $query = DB::connection()->prepare($sql);
    //     $query->execute(array('gameid' => $gameid, 'playerid' => $player->playerid));
    //     $rows = $query->fetchAll();
    //
    //     $scores = array();
    //     $total_score = 0;
    //
    //     foreach ($rows as $row) {
    //       $score = new Score(array(
    //         'hole_num' => $row['hole_num'],
    //         'hole_par' => $row['par'],
    //         'scoreid' => $row['scoreid'],
    //         'stroke' => $row['stroke'],
    //         'ob' => $row['ob'],
    //         'legal' => $row['legal']
    //       ));
    //       $scores[] = $score;
    //       $total_score += (int) $row['stroke'];
    //       $total_score += (int) $row['ob'];
    //     }
    //
    //     $player_scores['player'. $player->playerid] = $scores;
    //   }
    //
    //   // Sort array by total score
    //   uasort($player_scores, function($a, $b) {
    //     $a_total_score = 0;
    //     foreach ($a as $score) {
    //       $a_total_score += $score->stroke;
    //       $a_total_score += $score->ob;
    //     }
    //
    //     $b_total_score = 0;
    //     foreach ($b as $score) {
    //       $b_total_score += $score->stroke;
    //       $b_total_score += $score->ob;
    //     }
    //
    //     return $a_total_score - $b_total_score;
    //   });
    //
    //   return $player_scores;
    // }

    public static function new_high_score($gameid, $playerid, $courseid) {
      if (self::legal($gameid, $playerid)) {
        $total_score = self::total_score($gameid, $playerid);
        $gamedate = Game::get_gamedate($gameid);

        $sql = "SELECT total_score
                FROM (
                SELECT SUM(score.stroke) + SUM(score.ob) as total_score
                FROM score
                JOIN hole ON score.holeid = hole.holeid
                JOIN course ON hole.courseid = course.courseid
                JOIN game ON score.gameid = game.gameid
                JOIN player ON player.playerid = score.playerid
                WHERE score.legal = TRUE
                AND player.playerid = :playerid
                AND game.gamedate < to_timestamp(:gamedate, 'YYYY-MM-dd HH24:MI:SS')
                AND game.courseid = :courseid
                GROUP BY score.gameid, gamedate, firstname
                ) t1
                WHERE total_score <= :total_score LIMIT 1";
        $query = DB::connection()->prepare($sql);
        $query->execute(array(
          'playerid' => $playerid,
          'total_score' => $total_score,
          'gamedate' => $gamedate,
          'courseid' => $courseid
        ));
        $row = $query->fetch();

        if ($row) {
          return false;
        } else {
          return true;
        }

      } else {
        return false;
      }
    }

    public static function count_all() {
      $sql = "SELECT SUM(stroke) AS score_count FROM score";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $row = $query->fetch();

      $score_count = $row['score_count'];

      if (!$score_count) {
        $score_count = 0;
      }

      return $score_count;
    }

    public static function count_all_player_scores($playerid) {
      $sql = "SELECT SUM(stroke) AS score_count FROM score
              WHERE scoreid IN
              (SELECT scoreid FROM score WHERE playerid = :playerid)";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['score_count'];
    }

    private static function total_score($gameid, $playerid) {
      $sql = "SELECT SUM(score.stroke) + SUM(score.ob) as total_score
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              JOIN course ON hole.courseid = course.courseid
              JOIN game ON score.gameid = game.gameid
              JOIN player ON player.playerid = score.playerid
              WHERE game.gameid = :gameid
              AND player.playerid = :playerid
              GROUP BY score.gameid, gamedate, firstname";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid, 'gameid' => $gameid));
      $row = $query->fetch();

      return $row['total_score'];
    }

    private static function legal($gameid, $playerid) {
      $sql = "SELECT score.gameid
              FROM score
              WHERE playerid = :playerid
              AND gameid = :gameid
              AND legal = TRUE LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid, 'gameid' => $gameid));
      $row = $query->fetch();

      if ($row) {
        return true;
      } else {
        return false;
      }
    }

    /*
    *  Validators
    */

    public function validate_stroke() {
      $errors = $this->validate_integer($this->stroke, "Tuloksen");

      if ($this->stroke == null && $this->stroke != 0) {
        $errors[] = "Skipattu väylä merkataan nollalla.";
      }

      return $errors;
    }

    public function validate_ob() {
      return $this->validate_integer($this->ob, "OB:n");
    }
  }
