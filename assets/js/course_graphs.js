$(document).ready(function(){

  /*
  *  Course high scores / own high scores changing
  */

  $("#coursehighscores").hide();

  $("#omatennatykset").click(function(eventObject) {
    $("#omatennatykset").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#rataennatykset").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#coursehighscores").hide();
    $("#courseplayerhighscores").show();
  });

  $("#rataennatykset").click(function(eventObject) {
    $("#rataennatykset").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#omatennatykset").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#courseplayerhighscores").hide();
    $("#coursehighscores").show();
  });

  /*
  *  Course high scores
  */

  var highScores = [];

  $(".highscoresforjs").each(function(index) {
    var date = $(this).text().split(",")[0];
    var value = $(this).text().split(",")[1];

    highScores.push({
        "date": date,
        "value": value
    });
  });

  $(".highscoresforjs").remove();

  var courseHighScoresChart = AmCharts.makeChart("coursehighscores", {
      "type": "serial",
      "theme": "light",
      "language": "fi",
      "marginRight": 30,
      "marginLeft": 50,
      "autoMarginOffset": 20,
      "dataDateFormat": "YYYY-MM-DD",
      "valueAxes": [{
          "id": "v1",
          "axisAlpha": 0,
          "position": "left",
          "ignoreAxisWidth": true,
          "title": "Tulos"
      }],
      "balloon": {
          "borderThickness": 1,
          "shadowAlpha": 0
      },
      "graphs": [{
          "id": "g1",
          "balloon":{
            "drop":true,
            "adjustBorderColor":false,
            "color":"#ffffff"
          },
          "bullet": "round",
          "bulletBorderAlpha": 1,
          "bulletColor": "#FFFFFF",
          "bulletSize": 5,
          "hideBulletsCount": 50,
          "lineThickness": 2,
          "title": "red line",
          "useLineColorForBulletBorder": true,
          "valueField": "value",
          "balloonText": "<span style='font-size:18px;'>[[value]]</span>",
          "type": "smoothedLine",
          "fillAlphas": 0.2,
      }],
      "chartScrollbar": {
          "graph": "g1",
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
          "pan": true,
          "valueLineEnabled": false,
          "valueLineBalloonEnabled": false,
          "cursorAlpha":1,
          "cursorColor":"#258cbb",
          "limitToGraph":"g1",
          "valueLineAlpha":0.2,
          "categoryBalloonDateFormat": "MMM DD, YYYY"
      },
      "categoryField": "date",
      "categoryAxis": {
          "parseDates": true,
          "dashLength": 1,
          "minorGridEnabled": true,
          "minHorizontalGap": 50
      },
      "export": {
          "enabled": true
      },
      "dataProvider": highScores
  });

  courseHighScoresChart.addListener("rendered", zoomCHSChart);

  zoomCHSChart();

  function zoomCHSChart() {
    // Show at least 15 months (that have games)
    var length = courseHighScoresChart.dataProvider.length;
    courseHighScoresChart.zoomToIndexes(length - 15, length - 1);
  }

  /*
  *  Own course high scores
  */

  var playerHighScores = [];

  $(".playerhighscoresforjs").each(function(index) {
    var date = $(this).text().split(",")[0];
    var value = $(this).text().split(",")[1];

    playerHighScores.push({
        "date": date,
        "value": value
    });
  });

  $(".playerhighscoresforjs").remove();

  var playerCourseHighScoresChart = AmCharts.makeChart("courseplayerhighscores", {
      "type": "serial",
      "theme": "light",
      "language": "fi",
      "marginRight": 30,
      "marginLeft": 50,
      "autoMarginOffset": 20,
      "dataDateFormat": "YYYY-MM-DD",
      "valueAxes": [{
          "id": "v1",
          "axisAlpha": 0,
          "position": "left",
          "ignoreAxisWidth": true,
          "title": "Tulos"
      }],
      "balloon": {
          "borderThickness": 1,
          "shadowAlpha": 0
      },
      "graphs": [{
          "id": "g1",
          "balloon":{
            "drop":true,
            "adjustBorderColor":false,
            "color":"#ffffff"
          },
          "bullet": "round",
          "bulletBorderAlpha": 1,
          "bulletColor": "#FFFFFF",
          "bulletSize": 5,
          "hideBulletsCount": 50,
          "lineThickness": 2,
          "title": "red line",
          "useLineColorForBulletBorder": true,
          "valueField": "value",
          "balloonText": "<span style='font-size:18px;'>[[value]]</span>",
          "type": "smoothedLine",
          "fillAlphas": 0.2,
      }],
      "chartScrollbar": {
          "graph": "g1",
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
          "pan": true,
          "valueLineEnabled": false,
          "valueLineBalloonEnabled": false,
          "cursorAlpha":1,
          "cursorColor":"#258cbb",
          "limitToGraph":"g1",
          "valueLineAlpha":0.2,
          "categoryBalloonDateFormat": "MMM DD, YYYY"
      },
      "categoryField": "date",
      "categoryAxis": {
          "parseDates": true,
          "dashLength": 1,
          "minorGridEnabled": true,
          "minHorizontalGap": 50
      },
      "export": {
          "enabled": true
      },
      "dataProvider": playerHighScores
  });

  playerCourseHighScoresChart.addListener("rendered", zoomPCHSChart);

  zoomPCHSChart();

  function zoomPCHSChart() {
    // Show at least 15 months (that have games)
    var length = playerCourseHighScoresChart.dataProvider.length;
    playerCourseHighScoresChart.zoomToIndexes(length - 15, length - 1);
  }

  /*
  *  Birdie occurance per hole
  */

  var scoreDistribution = [];

  $(".scoredistributionforjs").each(function(index) {
    var holeNum = $(this).text().split(",")[0];
    var holeInOnes = $(this).text().split(",")[1];
    var birdies = $(this).text().split(",")[2];
    var pars = $(this).text().split(",")[3];
    var bogies = $(this).text().split(",")[4];
    var overBogies = $(this).text().split(",")[5];

    scoreDistribution.push({
        "hole": holeNum,
        "holeInOnes": holeInOnes,
        "birdies": birdies,
        "pars": pars,
        "bogies": bogies,
        "overBogies": overBogies
    });
  });

  $(".scoredistributionforjs").remove();

  var chart = AmCharts.makeChart("scoredistribution", {
    "type": "serial",
  	"theme": "light",
    "legend": {
      "horizontalGap": 10,
      "maxColumns": 1,
      "position": "right",
  		"useGraphSettings": true,
  		"markerSize": 10
    },
    "dataProvider": scoreDistribution,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0,
        "title": "Tulos"
    }],
    "graphs": [{
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>Väylä [[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0,
        "title": "Hole in one",
        "type": "column",
	      "color": "#000000",
        "fillColors": "red",
        "valueField": "holeInOnes"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>Väylä [[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0,
        "title": "Birdie",
        "type": "column",
	      "color": "#000000",
        "fillColors": "#00FF00",
        "valueField": "birdies"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>Väylä [[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0,
        "title": "Par",
        "type": "column",
	      "color": "#000000",
        "fillColors": "#A7C942",
        "valueField": "pars"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>Väylä [[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0,
        "title": "Bogey",
        "type": "column",
	      "color": "#000000",
        "fillColors": "orange",
        "valueField": "bogies"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>Väylä [[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0,
        "title": "Over bogey",
        "type": "column",
	      "color": "#000000",
        "fillColors": "#8E668E",
        "valueField": "overBogies"
    }],
    "categoryField": "hole",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
        "title": "Väylä",
        "minHorizontalGap": 15
    },
    "export": {
    	"enabled": true
     }
  });

});
