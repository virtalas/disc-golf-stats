window.myApp = {};
myApp.holeIndex = -1;
myApp.scores = {};
myApp.lastUpdateTime = 0;
myApp.lastUpdateAttemptTime = 0;
myApp.scoresChanged = false;

document.addEventListener('init', function(event) {
    var page = event.target;

    if (myApp.controllers.hasOwnProperty(page.id)) {
        myApp.controllers[page.id](page);
    }

    // $("#toolbarbutton").click(function() {
    //   var menu = document.getElementById('menu');
    //   console.log("g");
    //   menu.open();
    // });

    // Set button functionality to open/close the menu.
    // page.querySelector('[component="button/menu"]').onclick = function() {
    //   document.querySelector('#mySplitter').right.open();
    // };

    // $("#splitter_score_input").click(function() {
    //   var content = document.getElementById('content');
    //   var menu = document.getElementById('menu');
    //   content.load("score_input.html")
    //   // .then(myApp.controllers.scoreInputPage(null));
    //     // .then(menu.close.bind(menu)); // Causes "Unhandled Promise Rejection: Splitter side is locked."
    //   console.log("score_input page");
    // });
    //
    // $("#splitter_hole_info").click(function() {
    //   var content = document.getElementById('content');
    //   var menu = document.getElementById('menu');
    //   content.load("../../app/views/game/mobilegui/hole_info.html")
    //     // .then(menu.close.bind(menu)); // Causes "Unhandled Promise Rejection: Splitter side is locked."
    //     console.log("hole_info page");
    // });

    if (myApp.holeIndex == -1) {
        loadScores();
        myApp.services.nextHole();
        myApp.lastUpdateTime = new Date().getTime();
    }
});

function loadScores() {
    for (var i = 0; i < players.length; i++) {
        var playerid = players[i].playerid;

        myApp.scores[playerid] = {
            "player": players[i],
            "strokes": [],
            "obs": []
        }

        // Load scores from preparedGame.scores
        for (var holeIndex = 0; holeIndex < course.holes.length; holeIndex++) {
            var stroke = preparedGame.scores["player" + playerid][holeIndex].stroke;
            myApp.scores[playerid].strokes.push(stroke);

            var ob = preparedGame.scores["player" + playerid][holeIndex].ob;
            myApp.scores[playerid].obs.push(ob);
        }
        console.log("scores loaded for " + players[i].firstname);
    }
}

window.fn = {};

window.fn.open = function() {
    console.log("toolbar menu button pressed");
    var menu = document.getElementById('menu');
    menu.open();
};

window.fn.load = function(page) {
    var content = document.getElementById('content');
    var menu = document.getElementById('menu');
    content.load(page)
        .then(menu.close.bind(menu));
};
