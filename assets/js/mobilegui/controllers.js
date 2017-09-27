myApp.controllers = {
  scoreInputPage: function(page) {

    $("#next").click(function() {
      console.log("next clicked");
      myApp.services.nextHole();
    });

    $("#previous").click(function() {
      myApp.services.previousHole();
    });

    $("#set_to_par").click(function() {
      myApp.services.setHoleScoresToPar();
    });

    for (var i = 0; i < players.length; i++) {
      $("#" + players[i].playerid + "stroke").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateStroke(playerid, $(this).val());
      });
      $("#" + players[i].playerid + "ob").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateOb(playerid, $(this).val());
      });
    }

    myApp.services.updatePage();
  },

  conditionsPage: function(page) {
    // Show progress bar for fetching the weather
    $("#animated_progress_bar").show();

    // Generate game legality switches for the first player
    $("#game_legality_switch span").html(players[0].firstname);
    $("#game_legality_switch ons-switch").attr("input-id", "legal-player" + players[0].playerid);

    // Switches for the rest of the players
    for (var i = 1; i < players.length; i++) {
      var legality = $("#game_legality_switch").clone();
      legality.find("span").html(players[i].firstname);
      legality.find("ons-switch").attr("input-id", "legal-player" + players[i].playerid);
      legality.appendTo("#legality_switches");
    }

    // Send button functionality
    $("#add_game_button").click(function() {
      myApp.services.postGame();
    });

    // Set date and time to now
    $("#date").val(dateNow());
    $("#time").val(timeNow());

    // Fetch course weather
    $.getScript("../../assets/js/weather.js");
  }
};

function dateNow() {
  var today = new Date();
  var dd = today.getDate();
  var mm = today.getMonth() + 1; // January is 0!
  var yyyy = today.getFullYear();

  if (dd < 10) {
      dd = '0' + dd
  }

  if (mm < 10) {
      mm = '0' + mm
  }

  return yyyy + "-" + mm + "-" + dd;
}

function timeNow() {
  var today = new Date();
  var h = today.getHours();
  var min = today.getMinutes();

  if (h < 10) {
    h = "0" + h;
  }

  if (min < 10) {
    min = "0" + min;
  }

  return h + ":" + min;
}

// https://stackoverflow.com/a/24035537
function hideKeyboard() {
  //this set timeout needed for case when hideKeyborad
  //is called inside of 'onfocus' event handler
  setTimeout(function() {

    //creating temp field
    var field = document.createElement('input');
    field.setAttribute('type', 'text');
    //hiding temp field from peoples eyes
    //-webkit-user-modify is nessesary for Android 4.x
    field.setAttribute('style', 'position:absolute; top: 0px; opacity: 0; -webkit-user-modify: read-write-plaintext-only; left:0px;');
    document.body.appendChild(field);

    //adding onfocus event handler for out temp field
    field.onfocus = function(){
      //this timeout of 200ms is nessasary for Android 2.3.x
      setTimeout(function() {

        field.setAttribute('style', 'display:none;');
        setTimeout(function() {
          document.body.removeChild(field);
          document.body.focus();
        }, 14);

      }, 200);
    };
    //focusing it
    field.focus();

  }, 50);
}
