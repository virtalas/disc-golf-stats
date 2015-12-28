$(document).ready(function(){

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
      "marginRight": 40,
      "marginLeft": 40,
      "autoMarginOffset": 20,
      "dataDateFormat": "YYYY-MM-DD",
      "valueAxes": [{
          "id": "v1",
          "axisAlpha": 0,
          "position": "left",
          "ignoreAxisWidth":true
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
          "minHorizontalGap": 27
      },
      "export": {
          "enabled": true
      },
      "dataProvider": highScores
  });

  courseHighScoresChart.addListener("rendered", zoomChart);

  zoomChart();

  function zoomChart() {
    // Show at least 15 months (that have games)
    var length = courseHighScoresChart.dataProvider.length;
    courseHighScoresChart.zoomToIndexes(length - 15, length - 1);
  }

});
