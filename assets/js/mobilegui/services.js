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

    setHoleScoresToPar: function() {
        for (var i = 0; i < players.length; i++) {
            var playerid = players[i].playerid;
            myApp.services.updateStroke(playerid, course.holes[myApp.holeIndex].par);
            myApp.services.updateOb(playerid, 0);
        }
    },

    updateStroke: function(playerid, stroke) {
        myApp.scores[playerid].strokes[myApp.holeIndex] = stroke;
        myApp.scoresChanged = true;
        myApp.services.updatePage();
    },

    updateOb: function(playerid, ob) {
        myApp.scores[playerid].obs[myApp.holeIndex] = ob;
        myApp.scoresChanged = true;
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

            // Update hole number label
            $("#hole_num_label").html((myApp.holeIndex + 1) + ".");
        }

        var timeSinceLastUpdate = new Date().getTime() - myApp.lastUpdateTime;
        var minutesSinceLastUpdate = Math.round(timeSinceLastUpdate / 60000);
        $("#timesincelastupdate").html(minutesSinceLastUpdate);
        console.log("time since last update: "+timeSinceLastUpdate);

        console.log("page updated");
    },

    // Send in-progress game to server
    updateGame: function() {
        var now = new Date().getTime();

        if (now - myApp.lastUpdateAttemptTime > 5000 && myApp.scoresChanged) {
            // Time is updated here so no new requests are made until the first one finishes.
            // Then the time is also updated in the ajax success function.
            myApp.lastUpdateAttemptTime = new Date().getTime();
        } else {
            return;
        }

        var game = createGameWithStrokes();
        sendingScores();
        postNoRedirect("/disc-golf-stats/game/" + preparedGame.gameid + "/mobile/edit", game);
    },

    updateGameWithFeedback: function() {
        var game = createGameWithStrokes();
        postNoRedirectWithFeedback("/disc-golf-stats/game/" + preparedGame.gameid + "/mobile/edit", game);
    },

    finishGame: function() {
        var game = createGameWithStrokes();

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
        game["temp"] = $("#inputtemp").val();
        game["date"] = $("#date").val();
        game["time"] = $("#time").val();
        game["comment"] = $("#comment").val();

        // POST and redirect
        postAndRedirect("/disc-golf-stats/game/" + preparedGame.gameid + "/edit", game);
    },

    showScoreCardPopOver: function(target) {
        document.getElementById('popover').show(target);

        $.get('/disc-golf-stats/search/game/' + preparedGame.gameid, function(data) {
          $('#gamecard').html(data);
        });
    },

    dateNow: function() {
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
    },

    timeNow: function() {
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
};

function createGameWithStrokes() {
    // Collect all game information into an array and send it as a post request
    var game = {};

    // Loading indicator
    // $("#animated_progress_circle").show();

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

    // Players
    for (var i = 0; i < players.length; i++) {
        game["player" + players[i].playerid] = players[i].playerid;
    }

    // Game legality
    for (var i = 0; i < players.length; i++) {
        // Check game legality from the first hole score
        var legal = preparedGame.scores["player" + players[i].playerid][0].legal;
        game["legal-player" + players[i].playerid] = legal ? 1 : 0;
    }

    game["date"] = myApp.services.dateNow();
    game["time"] = myApp.services.timeNow();
    game["courseid"] = course.courseid;

    return game;
}

function toPar(playerid) {
    var par = 0;

    for (var i = 0; i < course.holes.length; i++) {
        // Stroke == 0 means a skipped hole
        if (myApp.scores[playerid].strokes[i] != 0) {
            par += parseInt(myApp.scores[playerid].strokes[i]) + parseInt(myApp.scores[playerid].obs[i]) - parseInt(course.holes[i].par);
        }
    }

    if (par > 0) {
        par = "+" + par;
    }

    return par;
}

function createForm(url, data) {
    $('form').remove();
    var form = document.createElement('form');
    form.method = 'post';
    form.action = url;
    for (var name in data) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = data[name];
        form.appendChild(input);
    }
    $(document.body).append(form);
    return form;
}

function postAndRedirect(url, data) {
    var form = createForm(url, data);
    form.submit();
}

function postNoRedirect(url, data) {
    var form = createForm(url, data);
    $.ajax({
        url: url,
        type: 'post',
        data: $('form').serialize(),
        success: function(){
            // $("#animated_progress_circle").hide();
            // $("#animated_progress_bar").delay(2000).hide();
            myApp.lastUpdateTime = new Date().getTime();
            myApp.scoresChanged = false;
            scoresSent();
            console.log("scores sent succesfully");
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
//          TODO: Invesitage error. Maybe notify a reload and then reload the page?
            alert("Virhe! Lataa sivu uudelleen.");
//            alert("Status: " + textStatus);
//            alert("Error: " + errorThrown);
        }
    });
}

function postNoRedirectWithFeedback(url, data) {
    var form = createForm(url, data);
    $.ajax({
        url: url,
        type: 'post',
        data: $('form').serialize(),
        success: function(){
            myApp.lastUpdateTime = new Date().getTime();
            myApp.scoresChanged = false;
            console.log("scores sent succesfully");
            alert("Tulokset lähetettiin onnistuneesti.");
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
//          TODO: Invesitage error. Maybe notify a reload and then reload the page?
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
        }
    });
}

// Tell the user the scores are being sent.
function sendingScores() {
    $("#timesincelastupdatetext").hide();
    $("#sending").show();
}

// Tell the user scores were sent succesfully.
function scoresSent() {
    $("#timesincelastupdate").html(0);
    $("#sending").hide();
    $("#timesincelastupdatetext").show();
}
