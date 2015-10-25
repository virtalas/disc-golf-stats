<?php
  class Score extends BaseModel {

    public $scoreid, $gameid, $holeid, $playerid, $stroke, $ob, $legal, // Ready to use after creation
            $player, $hole; // Need to be prepared via prepare()

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

    public function prepare() {
      $this->load_player();
      $this->load_hole();
    }

    private function load_player() {
      $this->player = Player::find($this->playerid);
    }

    private function load_hole() {
      $this->hole = Hole::find($this->holeid);
    }

    public static function all_game_scores($gameid) {
      $players = Game::games_players($gameid);
      $player_scores = array();

      foreach ($players as $player) {
        $sql = "SELECT * FROM score
                JOIN hole ON score.holeid = hole.holeid
                WHERE score.gameid = :gameid AND score.playerid = :playerid
                ORDER BY hole.hole_num ASC";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('gameid' => $gameid, 'playerid' => $player->playerid));
        $rows = $query->fetchAll();
        $scores = array();

        foreach ($rows as $row) {
          $score = new Score(array(
            'scoreid' => $row['scoreid'],
            'gameid' => $row['gameid'],
            'holeid' => $row['holeid'],
            'playerid' => $row['playerid'],
            'stroke' => $row['stroke'],
            'ob' => $row['ob'],
            'legal' => $row['legal']
          ));
          $score->prepare();
          $scores[] = $score;
        }

        $player_scores[$player->playerid] = $scores;
      }

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
