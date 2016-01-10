$(document).ready(function(){

  /*
  *  Number of games played per month / Own games player per month
  */

  $("#gamecountpermonth").hide();

  $("#omatpelit").click(function(eventObject) {
    $("#omatpelit").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#pelit").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#gamecountpermonth").hide();
    $("#playergamecountpermonth").show();
  });

  $("#pelit").click(function(eventObject) {
    gameCountPerMonth.invalidateSize(); // fixes a problem caused by rendering the chart in a hidden element
    $("#pelit").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#omatpelit").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#playergamecountpermonth").hide();
    $("#gamecountpermonth").show();
  });

  /*
  *  Number of games player per month
  */

  var gamedates = [];

  $(".gamedateforjs").each(function(index) {
    var month = $(this).text().split(",")[0];
    var value = $(this).text().split(",")[1];

    gamedates.push({
        "month": month,
        "value": value
    });
  });

  $(".gamedateforjs").remove();

  var gameCountPerMonth = AmCharts.makeChart( "gamecountpermonth", {
    "type": "serial",
    "language": "fi",
    "theme": "light",
    "dataDateFormat": "YYYY-MM",
    "dataProvider": gamedates,
    "valueAxes": [ {
      "gridColor": "grey",
      "gridAlpha": 0.2,
      "dashLength": 0,
      "title": "Pelit"
    } ],
    "gridAboveGraphs": true,
    "startDuration": 1,
    "graphs": [ {
      "id": "g1",
      "balloonText": "[[category]]: <b>[[value]]</b>",
      "fillAlphas": 0.8,
      "lineAlpha": 0.2,
      "type": "column",
      "valueField": "value"
    } ],
    "chartScrollbar": {
        "oppositeAxis":false,
        "offset":30,
        "scrollbarHeight": 80,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "graphFillAlpha": 0,
        "graphLineAlpha": 0.5,
        "selectedGraphFillAlpha": 0,
        "selectedGraphLineAlpha": 1,
        "autoGridCount":true,
        "color":"#AAAAAA"
    },
    "chartCursor": {
      "categoryBalloonEnabled": false,
      "cursorAlpha": 0,
      "zoomable": false,
      "categoryBalloonDateFormat": "MMMM, YYYY"
    },
    "categoryField": "month",
    "categoryAxis": {
      "parseDates": true,
      "gridPosition": "start",
      "gridAlpha": 0,
      "tickPosition": "start",
      "tickLength": 20,
      "minPeriod": "MM",
      "minHorizontalGap": 35
    },
    "export": {
      "enabled": true
    }

  });

  gameCountPerMonth.addListener("rendered", zoomGCPMChart);

  zoomGCPMChart();

  function zoomGCPMChart() {
    // Show at least 15 months (that have games)
    var length = gameCountPerMonth.dataProvider.length;
    gameCountPerMonth.zoomToIndexes(length - 15, length - 1);
  }

  /*
  *  Own games player per month
  */

  var playerGamedates = [];

  $(".playergamedateforjs").each(function(index) {
    var month = $(this).text().split(",")[0];
    var value = $(this).text().split(",")[1];

    playerGamedates.push({
        "month": month,
        "value": value
    });
  });

  $(".playergamedateforjs").remove();

  var playerGameCountPerMonth = AmCharts.makeChart( "playergamecountpermonth", {
    "type": "serial",
    "language": "fi",
    "theme": "light",
    "dataDateFormat": "YYYY-MM",
    "dataProvider": playerGamedates,
    "valueAxes": [ {
      "gridColor": "grey",
      "gridAlpha": 0.2,
      "dashLength": 0,
      "title": "Pelit"
    } ],
    "gridAboveGraphs": true,
    "startDuration": 1,
    "graphs": [ {
      "id": "g1",
      "balloonText": "[[category]]: <b>[[value]]</b>",
      "fillAlphas": 0.8,
      "lineAlpha": 0.2,
      "type": "column",
      "valueField": "value"
    } ],
    "chartScrollbar": {
        "oppositeAxis":false,
        "offset":30,
        "scrollbarHeight": 80,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "graphFillAlpha": 0,
        "graphLineAlpha": 0.5,
        "selectedGraphFillAlpha": 0,
        "selectedGraphLineAlpha": 1,
        "autoGridCount":true,
        "color":"#AAAAAA"
    },
    "chartCursor": {
      "categoryBalloonEnabled": false,
      "cursorAlpha": 0,
      "zoomable": false,
      "categoryBalloonDateFormat": "MMMM, YYYY"
    },
    "categoryField": "month",
    "categoryAxis": {
      "parseDates": true,
      "gridPosition": "start",
      "gridAlpha": 0,
      "tickPosition": "start",
      "tickLength": 20,
      "minPeriod": "MM",
      "minHorizontalGap": 35
    },
    "export": {
      "enabled": true
    }

  });

  playerGameCountPerMonth.addListener("rendered", zoomPGCPMChart);

  zoomPGCPMChart();

  function zoomPGCPMChart() {
    // Show at least 15 months (that have games)
    var length = playerGameCountPerMonth.dataProvider.length;
    playerGameCountPerMonth.zoomToIndexes(length - 15, length - 1);
  }

  /*
  *  How many players per round
  */

  var playerCount = [];

  $(".playercountdistforjs").each(function(index) {
    var numberOfPlayers = $(this).text().split(",")[0];
    var occurance = $(this).text().split(",")[1];
    var title = "";

    if (numberOfPlayers == 1) {
      title = "1 pelaaja";
    } else {
      title = numberOfPlayers + " pelaajaa";
    }

    playerCount.push({
        "players": title,
        "occurance": occurance
    });
  });

  $(".playercountdistforjs").remove();

  var playerCountChart = AmCharts.makeChart("playercount", {
  	"type": "pie",
    "showBalloon": false,
  	"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
  	"labelRadius": 20,
  	"hoverAlpha": 0.47,
  	"labelTickAlpha": 0.2,
  	"marginLeft": -50,
  	"marginRight": 0,
  	"outlineThickness": 0,
  	"pullOutOnlyOne": true,
  	"startEffect": "easeOutSine",
  	"titleField": "players",
  	"valueField": "occurance",
  	"fontFamily": "Helvetica",
  	"fontSize": 12,
  	"handDrawScatter": 0,
  	"handDrawThickness": 0,
  	"theme": "light",
  	"allLabels": [],
  	"legend": {
  		"enabled": true,
  		"bottom": 0,
  		"position": "right",
  		"rollOverGraphAlpha": 0
  	},
  	"titles": [],
  	"dataProvider": playerCount
  });

  // hideGraphsBalloon(playerCountChart);
  playerCountChart.addListener("init", handleInit);

  playerCountChart.addListener("rollOverSlice", function(e) {
    handleRollOver(e);
  });

  function handleInit(){
    playerCountChart.legend.addListener("rollOverItem", handleRollOver);
  }

  function handleRollOver(e){
    var wedge = e.dataItem.wedge.node;
    wedge.parentNode.appendChild(wedge);
  }

  /*
  *  Game hours and days of week
  *  !! Not implemented yet !!
  */

  var gameHours = [];

  $(".gamehoursforjs").each(function(index) {
    var day = $(this).text().split(",")[0];
    var hour = $(this).text().split(",")[1];
    var found = false;

    switch (day) {
      case "0":
          day = "Sunnuntai";
          break;
      case "1":
          day = "Maanantai";
          break;
      case "2":
          day = "Tiistai";
          break;
      case "3":
          day = "Keskiviikko";
          break;
      case "4":
          day = "Torstai";
          break;
      case "5":
          day = "Perjantai";
          break;
      case "6":
          day = "Lauantai";
          break;
    }

    for (var i = 0; i < gameHours.length; i++) {
      if (gameHours[i].day == day && gameHours[i].hour == hour) {
        gameHours[i].value++;
        found = true;
      }
    }

    if (!found) {
      gameHours.push({
          "day": day,
          "hour": hour,
          "value": 1
      });
    }
  });

  $(".gamehoursforjs").remove();
});
