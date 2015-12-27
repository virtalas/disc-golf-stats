$(document).ready(function(){

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
      "dashLength": 0
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
      "categoryBalloonDateFormat": "MMM, YYYY"
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

  // var chart1 = AmCharts.makeChart("chartdiv1", {
  //     "type": "serial",
  //     "theme": "light",
  //     "marginRight": 40,
  //     "marginLeft": 40,
  //     "autoMarginOffset": 20,
  //     "dataDateFormat": "YYYY-MM",
  //     "valueAxes": [{
  //         "id": "v1",
  //         "axisAlpha": 0,
  //         "position": "left",
  //         "ignoreAxisWidth":true
  //     }],
  //     "balloon": {
  //         "borderThickness": 1,
  //         "shadowAlpha": 0
  //     },
  //     "graphs": [{
  //         "id": "g1",
  //         "balloon":{
  //           "drop":true,
  //           "adjustBorderColor":false,
  //           "color":"#ffffff"
  //         },
  //         "bullet": "round",
  //         "bulletBorderAlpha": 1,
  //         "bulletColor": "#FFFFFF",
  //         "bulletSize": 5,
  //         "hideBulletsCount": 50,
  //         "lineThickness": 2,
  //         "title": "red line",
  //         "useLineColorForBulletBorder": true,
  //         "valueField": "value",
  //         "balloonText": "<span style='font-size:18px;'>[[value]]</span>",
  //         "type": "smoothedLine",
  //         "fillAlphas": 0.2,
  //     }],
  //     "chartScrollbar": {
  //         "graph": "g1",
  //         "oppositeAxis":false,
  //         "offset":30,
  //         "scrollbarHeight": 80,
  //         "backgroundAlpha": 0,
  //         "selectedBackgroundAlpha": 0.1,
  //         "selectedBackgroundColor": "#888888",
  //         "graphFillAlpha": 0,
  //         "graphLineAlpha": 0.5,
  //         "selectedGraphFillAlpha": 0,
  //         "selectedGraphLineAlpha": 1,
  //         "autoGridCount":true,
  //         "color":"#AAAAAA"
  //     },
  //     "chartCursor": {
  //         "pan": true,
  //         "valueLineEnabled": true,
  //         "valueLineBalloonEnabled": true,
  //         "cursorAlpha":1,
  //         "cursorColor":"#258cbb",
  //         "limitToGraph":"g1",
  //         "valueLineAlpha":0.2,
  //         "categoryBalloonDateFormat": "MMM, YYYY"
  //     },
  //     "categoryField": "month",
  //     "categoryAxis": {
  //         "parseDates": true,
  //         "dashLength": 1,
  //         "minorGridEnabled": true,
  //         "minPeriod": "MM",
  //         "minHorizontalGap": 27
  //     },
  //     "export": {
  //         "enabled": true
  //     },
  //     "dataProvider": gamedates
  // });

  // chart1.addListener("rendered", zoomChart);
  gameCountPerMonth.addListener("rendered", zoomChart);

  zoomChart();

  function zoomChart() {
    // Show at least 15 months (that have games)
    var length = gameCountPerMonth.dataProvider.length;
    gameCountPerMonth.zoomToIndexes(length - 15, length - 1);
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
  	"labelRadius": 10,
  	"hoverAlpha": 0.47,
  	"labelTickAlpha": 0,
  	"marginLeft": -30,
  	"marginRight": -30,
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
  	"balloon": {},
  	"legend": {
  		"enabled": true,
  		"bottom": 0,
  		"position": "right",
  		"rollOverGraphAlpha": 0
  	},
  	"titles": [],
  	"dataProvider": playerCount
  });

  hideGraphsBalloon(playerCountChart);
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
});
