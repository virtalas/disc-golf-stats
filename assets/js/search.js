var search = false;

$("#search-toggle").click(function() {
  if (!search) {
    search = true;
    $("#result").html("Haetaan pelejä...");
    $("#title").html("Hae pelejä");
    $("#search-toggle").html("Lopeta haku");
    $("#search-attributes").css("display", "inline");

    $.get('game/search', function(data) {
      $('#result').html(data);
    });
  } else {
    // redirect to /game
  }
});
