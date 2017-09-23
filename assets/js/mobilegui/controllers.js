myApp.controllers = {
  scoreInputPage: function(page) {

    $("#next").click(function() {
      myApp.services.nextHole();
    });
  }
};
