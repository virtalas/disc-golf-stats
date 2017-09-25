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
