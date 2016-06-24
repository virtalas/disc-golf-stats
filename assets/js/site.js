// Ask for confirmation before deleting anything
$(document).ready(function () {
  $('form.destroy-form').on('submit', function (submit) {
    var confirm_message = $(this).attr('data-confirm');

    if (!confirm(confirm_message)) {
      submit.preventDefault();
    }
  });
});

// Disabled link
$(document).ready(function () {
  $('a.disabled_link').click(function(event){
    event.preventDefault();
  });
});

// Set sidebar's grey background equal to window's scrollable area height
$(window).load(function () {
  var scroll_height = document.getElementById('fake-wrapper').scrollHeight;
  var sidebar = document.getElementById('sidebar-wrapper');

  if (sidebar) {
    sidebar.setAttribute('style','height:'+scroll_height+'px;');
  }
});

// Player page's link
$(document).ready(function () {
  // Set the link to direct to the user's own page
  var href = $("#playerpagelink").attr("href");
  $("#playerpagelink").attr("href", href + "?player=" + getCookie("user"));

  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1);
      if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
  }
})
