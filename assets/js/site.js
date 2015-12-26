$(document).ready(function(){
  //alert('Hello World!');
});

// Kun sivu on latautunut kutsutaan ready-funktion parametrina annettua funktiota
$(document).ready(function(){
  // Valitaan kaikki form-elementit, joihin liittyy destroy-form-luokka ja lisätään niihin kuuntelija, joka kutsuu parametrina annettua funktiota, kun lomake lähetetään
  $('form.destroy-form').on('submit', function(submit){
    // Otetaan kohdelomakkeesta data-confirm-attribuutin arvo
    var confirm_message = $(this).attr('data-confirm');
    // Pyydetään käyttäjältä vahvistusta
    if(!confirm(confirm_message)){
      // Jos käyttäjä ei anna vahvistusta, ei lähetetä lomaketta
      submit.preventDefault();
    }
  });
});

// Disabled link
$( document ).ready(function() {
	$('a.disabled_link').click(function(event){
   		event.preventDefault();
	});
});

// Set sidebar's grey background equal to window's scrollable area height
$(window).load(function() {
  var scroll_height = document.getElementById('fake-wrapper').scrollHeight;
  var sidebar = document.getElementById('sidebar-wrapper');
  
  if (sidebar) {
    sidebar.setAttribute('style','height:'+scroll_height+'px;');
  }
});
