<?php
  class Score extends BaseModel {

    public $scoreid, $gameid, $holeid, $playerid, $stroke, $ob, $legal, // Ready to use after creation
            $hole_par, $hole_num; // Needs to be prepared in all_game_scores()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_stroke', 'validate_ob', 'validate_score_legal');
    }

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
      
    }

    public static function all_game_scores($gameid) {
      $players = Game::games_players($gameid);
      $player_scores = array();

      foreach ($players as $player) {
        $sql = "SELECT hole.hole_num, hole.par, score.scoreid, score.stroke, score.ob, score.legal FROM score
                JOIN hole ON score.holeid = hole.holeid
                WHERE score.gameid = :gameid AND score.playerid = :playerid
                ORDER BY hole.hole_num ASC";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('gameid' => $gameid, 'playerid' => $player->playerid));
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

      return $player_scores;
    }

    public static function count_all() {
      $sql = "SELECT SUM(stroke) AS score_count FROM score";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $row = $query->fetch();

      return $row['score_count'];
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

    // Validators

    public function validate_stroke() {
      $errors = $this->validate_integer($this->stroke, "Tuloksen");

      if ($this->stroke == null) {
        $errors[] = "Skipattu väylä merkataan nollalla.";
      }

      return $errors;
    }

    public function validate_ob() {
      return $this->validate_integer($this->ob, "OB:n");
    }

    public function validate_score_legal() {
      // Check if game has conditions variant or doubles
      $errors = array();
      return $errors;
    }
  }
