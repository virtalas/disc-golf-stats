<?php
  class Score extends BaseModel {

    public $scoreid, $gameid, $holeid, $playerid, $stroke, $ob,
            $player, $hole;

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_stroke', 'validate_ob');
    }

    public function save() {
      $sql = "INSERT INTO score (gameid, holeid, playerid, stroke, ob) 
              VALUES (:gameid, :holeid, :playerid, :stroke, :ob) RETURNING scoreid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid,
                            'holeid' => $this->holeid,
                            'playerid' => $this->playerid,
                            'stroke' => $this->stroke,
                            'ob' => $this->ob));
      $row = $query->fetch();
      $this->scoreid = $row['scoreid'];
    }

    public function update() {
      $sql = "UPDATE score SET stroke = :stroke, ob = :ob WHERE scoreid = :scoreid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('stroke' => $this->stroke,
                            'ob' => $this->ob,
                            'scoreid' => $this->scoreid));
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
        $sql = "SELECT * FROM score WHERE gameid = :gameid AND playerid = :playerid";
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
            'ob' => $row['ob']
          ));
          $score->prepare();
          $scores[] = $score;
        }

        $player_scores[$player->firstname] = $scores;
      }

      return $player_scores;
    }

    // Validators

    public function validate_stroke() {
      return $this->validate_integer($this->stroke, "Tuloksen");
    }

    public function validate_ob() {
      return $this->validate_integer($this->ob, "OB:n");
    }
  }
  