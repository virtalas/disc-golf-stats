myApp.services = {

  nextHole: function() {
    if (myApp.holeIndex < course.holes.length - 1) {
      myApp.holeIndex++;
      myApp.services.updatePage();
    } else if (myApp.holeIndex == course.holes.length - 1) {
      // Pressing next on the last hole takes the user to game conditons page
      fn.load("../../app/views/game/mobilegui/conditions.html");
    }
  },

  previousHole: function() {
    if (myApp.holeIndex > 0) {
      myApp.holeIndex--;
      myApp.services.updatePage();
    }
  },

  updateStroke: function(playerid, stroke) {
    myApp.scores[playerid].strokes[myApp.holeIndex] = stroke;
    myApp.services.updatePage();
  },

  updateOb: function(playerid, ob) {
    myApp.scores[playerid].obs[myApp.holeIndex] = ob;
    myApp.services.updatePage();
  },

  updatePage: function() {
    $("#toolbarHoleNum").html(myApp.holeIndex + 1);
    $("#toolbarPar").html(course.holes[myApp.holeIndex].par);

    for (var i = 0; i < players.length; i++) {
      // Update inputs
      $("#" + players[i].playerid + "stroke").val(myApp.scores[players[i].playerid].strokes[myApp.holeIndex]);
      $("#" + players[i].playerid + "ob").val(myApp.scores[players[i].playerid].obs[myApp.holeIndex]);

      // Update to par
      $("#" + players[i].playerid + "topar").html(toPar(players[i].playerid));
    }

    console.log("page updated");
  },

  postGame: function() {
    // Collect all game information into an array and send it as a post request
    var game = {};

    // Strokes and OBs
    for (var i = 0; i < players.length; i++) {
      var playerid = players[i].playerid;
      for (var j = 0; j < myApp.scores[playerid].strokes.length; j++) {
        // e.g. "player1-hole1"
        game["player" + playerid + "-hole" + (j+1)] = myApp.scores[playerid].strokes[j];
        // e.g. "player1-obhole1"
        game["player" + playerid + "-obhole" + (j+1)] = myApp.scores[playerid].obs[j];
      }
    }

    // Conditions
    $("ons-checkbox").each(function() {
      console.log($(this).attr("input-id") + ": " + $(this).prop("checked"));
      game[$(this).attr("input-id")] = $(this).prop("checked") ? 1 : 0;
    })

    // Game legality
    $("ons-switch").each(function() {
      console.log($(this).attr("input-id") + ": " + $(this).prop("checked"));
      game[$(this).attr("input-id")] = $(this).prop("checked") ? 1 : 0;
    });

    // Rest
    game["temp"] = $("#temp").val();
    game["date"] = $("#date").val();
    game["time"] = $("#time").val();
    game["comment"] = $("#comment").val();
    game["courseid"] = course.courseid;

    $.post("/disc-golf-stats/game", $.param(game), function(data) {
      alert(data);
    });
  }
};



function toPar(playerid) {
  var par = 0;

  for (var i = 0; i < course.holes.length; i++) {
    if (myApp.scores[playerid].strokes[i] != 0) {
      par += myApp.scores[playerid].strokes[i] - course.holes[i].par;
    }
  }

  if (par > 0) {
    par = "+" + par;
  }

  return par;
}
