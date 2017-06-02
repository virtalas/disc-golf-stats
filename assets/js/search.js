var search = false;

$("#search-toggle").click(function() {
  if (!search) {
    search = true;
    $("#result").html("Haetaan pelejä...");
    $("#title").html("Hae pelejä");
    $("#search-toggle").html("Lopeta haku");
    $("#search-toggle").attr("href", "/game");
    $("#search-attributes").css("display", "inline");

    $('#rain').on('click', function() {
      console.log("clsl");
    });

    $.get('game/search', function(data) {
      $('#result').html(data);
    });

    var refreash = function() {
      $.post( 'game/search', $('form#conditions').serialize(), function(data) {
        $('#result').html(data);
      });
      console.log("change");
    }

    $('#rain').on('click', function() {
      console.log("clsl");
    });

  } else {
    // redirect to /game
    location.reload();
  }
});
