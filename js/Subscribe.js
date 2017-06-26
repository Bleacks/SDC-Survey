 createAccount = (function() {
	var i = 0;
	var email = '';
	var password = '';

	// TODO: Faire détecter les informations au navigateur (form detect)
	// TODO: Adatper le fonctionnement sur mobile

	return function() {
		if (email !== $('#email').val() || password !== $('#password').val()) {
			// Enable modal
			$('.modal').modal();
			$('#modal_title').show();
			$('#submitted').modal('open');

			// Initializes modal
			$('#modal_title').text("Connexion à la base de données...");
			$('#modal_progression').css('width', '0%');
			$('#modal_progression').removeClass('determinate').addClass('indeterminate');

			// Hides last modal elements
			$('#modal_title_'+i).hide();
			$('#modal_message_'+i).hide();
			$('#btn_footer_mail').hide();

			// Initializes usefull var
			i = 0;
			email = $('#email').val().toLowerCase();
			password = $('#password').val();

		   var json_data = {
		      "email": email,
		      "password": password
		   };
		   $.ajax({
		       url        : 'Subscribe',
		       dataType   : 'json',
		       contentType: 'application/json; charset=UTF-8',
		       data       : JSON.stringify(json_data),
		       type       : 'POST',
		       complete   : function (response) {
		          // TODO: Ajouter une barre de progression non régulière pour mieux simuler le compute time
					 // Recursif avec méthode de callback
					 function tempo () {
						 if (i <= 10) {
							 $('#modal_progression').css('width', (i*10) + '%');
							 //console.log((i*10));
							 i++;
							 setTimeout(tempo, 200);
						 } else
							 // Stores the response.status in order to hide ancient answer for next modal
							 i = response.status;
					 }

					 $('#modal_progression').removeClass('indeterminate').addClass('determinate');
					 $('#modal_title').text("Envoi en cours...");
					 tempo();
					 setTimeout(feedback, 2400);

					 function feedback() {
						 $('#modal_title').hide();
						 var status = response.status;
						 if (status == 200)
						 {
							 $('#btn_footer_mail').show();
						 }
						 else if (undefined == $('#modal_title_' + status)[0])
						 {
							 // TODO: Ajouter l'adresse mail du futur administrateur
							 // TODO: Log automatique des erreurs sur l'adresse mail de l'administrateur (disclaimer pour l'adresse perso)
							 $('#modal_title_err').text('Error ['+ status +'] : '+ response.statusText);
							 $('#modal_message_err').text('Contact system admin, and provide him thoses informations : \n' + 'Error ['+ status +'] : '+ response.statusText + ' : '+ response.responseText);
							 $('#modal_title_err').attr('id', 'modal_title_' + status);
							 $('#modal_message_err').attr('id', 'modal_message_' + status);
						 }

						 $('#modal_title_' + status).show();
						 $('#modal_message_' + status).show();
					 }
		       }
		    });
		 } else
			 $('#submitted').modal('open');
	    // TODO: Afficher le contenu seulement après le chargement de toutes les ressources de la page après un loading gif
	}
})();
