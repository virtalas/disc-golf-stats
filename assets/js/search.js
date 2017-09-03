$(document).ready(function(){
  $("#result").html("Haetaan pelejä...");

  var refresh = function() {
    $("#result").html("Haetaan pelejä...");

    $.get('search/game?' + $('#conditions').serialize(), function(data) {
      console.log('search/game?' + $('#conditions').serialize());
      $('#result').html(data);
    });
  }

  $('select').change(function() {
    refresh();
  });

  $('ul li a').click(function(e) {
    var txt = e.html();
    console.log("gf"+txt);
  });

  refresh();
});
