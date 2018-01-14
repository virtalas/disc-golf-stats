myApp.controllers = {
  scoreInputPage: function(page) {

    // Button functionality

    $("#next").click(function() {
      // Send in-progress game to server if scores changed, then clear changed -flag
      myApp.services.updateGame();
      myApp.services.nextHole();
    });

    $("#previous").click(function() {
      // Send in-progress game to server if scores changed, then clear changed -flag
      myApp.services.updateGame();
      myApp.services.previousHole();
    });

    $("#set_to_par").click(function() {
      myApp.services.setHoleScoresToPar();
    });

    // Listeners for score input fields

    for (var i = 0; i < players.length; i++) {

      // After the field loses focus, update scores (apparently not called if the field is empty)
      $("#" + players[i].playerid + "stroke").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateStroke(playerid, $(this).val());
      });
      $("#" + players[i].playerid + "ob").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateOb(playerid, $(this).val());
      });

      // Clear input field when user taps on it
      $("#" + players[i].playerid + "stroke").focus(function() {
        $(this).val("");
      });
      $("#" + players[i].playerid + "ob").focus(function() {
        $(this).val("");
      });

      // If the field is empty when it loses focus, set the value to 0
      $("#" + players[i].playerid + "stroke").blur(function() {
        if ($(this).val() == "") {
          var playerid = this.id.replace(/\D/g,'');
          $(this).val("0");
          myApp.services.updateStroke(playerid, $(this).val());
        }
      });
      $("#" + players[i].playerid + "ob").blur(function() {
        if ($(this).val() == "") {
          var playerid = this.id.replace(/\D/g,'');
          $(this).val("0");
          myApp.services.updateOb(playerid, $(this).val());
        }
      });

      $("#" + players[i].playerid + "stroke").on("input", function() {
        hideKeyboard();
      })
      $("#" + players[i].playerid + "ob").on("input", function() {
        hideKeyboard();
      })
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
      myApp.services.finishGame();
    });

    // Set date and time to now
    $("#date").val(myApp.services.dateNow());
    $("#time").val(myApp.services.timeNow());

    // Validators for date and time
    $("#date").change(function() {
      if (validateDate($(this).val())) {
        $(this).css('color', 'black');
      } else {
        $(this).css('color', 'red');
      }
    })
    $("#time").change(function() {
      if (validateTime($(this).val())) {
        $(this).css('color', 'black');
      } else {
        $(this).css('color', 'red');
      }
    })

    // Fetch course weather
    $.getScript("../../assets/js/weather.js");
  }
};

function hideKeyboard() {
  document.activeElement.blur()
  $(this).blur();
}

function validateDate(dateString) {
  return /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/.test(dateString);
};

function validateTime(timeString) {
  return /^[0-5][0-9]:[0-5][0-9]$/.test(timeString);
}
