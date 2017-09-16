$(document).ready(function(){
  $("#result").html("Haetaan pelejä...");

  $('select').change(function() {
    fetchGames(1);
  });

  fetchGames(1);
});

function fetchGames(page) {
  $("#result").html("Haetaan pelejä...");
  var params = $('#conditions').serialize();
  params += "&page=" + page;

  $.get('search/game?' + params, function(data) {
    console.log('search/game?' + $('#conditions').serialize());
    $('#result').html(data);
    listenForLinks();
  });
}

function listenForLinks() {
  $('a.active_js_link').click(function(e) {
    var page = $(e.target).text();
    fetchGames(page);
  });
}
