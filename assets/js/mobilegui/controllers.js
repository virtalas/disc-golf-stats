myApp.controllers = {
  scoreInputPage: function(page) {

    $("#next").click(function() {
      console.log("next clicked");
      myApp.services.nextHole();
    });

    $("#previous").click(function() {
      myApp.services.previousHole();
    });

    for (var i = 0; i < players.length; i++) {
      $("#" + players[i].playerid + "stroke").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateStroke(playerid, $(this).val());
      });
      $("#" + players[i].playerid + "ob").change(function() {
        var playerid = this.id.replace(/\D/g,'');
        myApp.services.updateOb(playerid, $(this).val());
      });
    }

    myApp.services.updatePage();
  }
};

// https://stackoverflow.com/a/24035537
function hideKeyboard() {
  //this set timeout needed for case when hideKeyborad
  //is called inside of 'onfocus' event handler
  setTimeout(function() {

    //creating temp field
    var field = document.createElement('input');
    field.setAttribute('type', 'text');
    //hiding temp field from peoples eyes
    //-webkit-user-modify is nessesary for Android 4.x
    field.setAttribute('style', 'position:absolute; top: 0px; opacity: 0; -webkit-user-modify: read-write-plaintext-only; left:0px;');
    document.body.appendChild(field);

    //adding onfocus event handler for out temp field
    field.onfocus = function(){
      //this timeout of 200ms is nessasary for Android 2.3.x
      setTimeout(function() {

        field.setAttribute('style', 'display:none;');
        setTimeout(function() {
          document.body.removeChild(field);
          document.body.focus();
        }, 14);

      }, 200);
    };
    //focusing it
    field.focus();

  }, 50);
}
