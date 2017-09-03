var search = false;

$("#search-toggle").click(function() {
  if (search) {
    // redirect to /game
    location.reload();
  }

  search = true;
  $("#result").html("Haetaan pelejä...");
  $("#title").html("Hae pelejä");
  $("#search-toggle").html("Lopeta haku");
  $("#search-toggle").attr("href", "/game");
  $("#search-attributes").css("display", "inline");

  $('select[name=rain]').change(function() {
    console.log($("select[name=rain]").val());
    refresh();
  });

  $.get('game/search', function(data) {
    $('#result').html(data);
  });

  var refresh = function() {
    $("#result").html("Haetaan pelejä...");
    $.get( 'game/search?' + $('#conditions').serialize(), function(data) {
      $('#result').html(data);
    });
    console.log('game/search?' + $('#conditions').serialize());
  }
});
