createAccount = (function() {
	var subscribed = false;
	var i = 0;

	return function() {

		if (!subscribed) {
			$('#btn_footer_mail').hide();
			$('.modal').modal();
			$('#modal_title').text("Contacting server...");
			//$('#modal_message').text(modal.message);
			$('#submitted').modal('open');

			var modal = {};
		   var json_data = {
		      "email": $('#email').val(),
		      "password": $('#password').val()
		   };

		   $.ajax({
		       url        : 'Subscribe',
		       dataType   : 'json',
		       contentType: 'application/json; charset=UTF-8',
		       data       : JSON.stringify(json_data),
		       type       : 'POST',
		       complete   : function (response) {
		          console.log(response);
					 switch (response.status) {
						 case 200:
					 			modal.title = 'Subscription sent';
								modal.message = 'Please check your mail in order to confirm your subscription';
								modal.mail = true;
						 		break;

						 case 409:
								modal.title = 'Email adress already in use'
								modal.message = 'Any previous subscription attempt with this adress will be deleted within 24 hours if not confirmed. Please check your mails if you haven\'t yet';
								break;

						 case 500:
						 		// TODO: Ajouter l'adresse mail du futur administrateur
								// TODO: Log automatique des erreurs sur l'adresse mail de l'administrateur (disclaimer pour l'adresse perso)
						 		modal.title = 'Internal Server Error';
								modal.message = 'Contact system admin, and provide him thoses informations : \n' + response.responseText;
								break;

						 case 424:
								modal.title = 'A transaction method failed';//Une méthode de la transaction a échoué';
								modal.message = 'Something went wrong submitting your request, please try again later. If problem persist, please contact system administrator.';
								//'Un problème est survenu lors de l\'éxecution de votre requête, veuillez réessayer plus tard. Si le problème persiste, veuillez contacter l\'administrateur';
								break;

						 case 429:
								modal.title = 'Too many request were sent from your adress';//Trop de requêtes ont étés effectuées depuis votre adresse';
								modal.message = 'Please try again in a few minutes'; //Veuillez patientez avant de pouvoir réessayer.';
								break;

			  			 default:
						 		modal.title = 'Error ['+ response.status +'] : '+ response.statusText;
								modal.message = 'Contact system admin, and provide him thoses informations : \n' + 'Error ['+ response.status +'] : '+ response.statusText + ' : '+ response.responseText;
					 }


					 // TODO: Ajouter une barre de progression non régulière pour mieux simuler le compute time
					 // Recursif avec méthode de callback
					 function tempo () {
						 if (i <= 10) {
							 $('#modal_progression').css('width', (i*10) + '%');
							 //console.log((i*10));
							 i++;
							 setTimeout(tempo, 200);
						 }
					 }
					 $('#modal_progression').removeClass('indeterminate').addClass('determinate');
					 $('#modal_title').text("Sending...");
					 setTimeout(tempo, 200);
					 setTimeout(feedback, 2400);
					 subscribed = true;

					 function feedback() {
						 if (modal.mail)
						 	$('#btn_footer_mail').show();
						 $('.modal').modal();
						 $('#modal_title').text(modal.title);
						 $('#modal_message').text(modal.message);
						 $('#submitted').modal('open');
					 }
		       }
		    });
		 } else
		 		$('#submitted').modal('open');
	    // TODO: Afficher le contenu seulement après le chargement de toutes les ressources de la page après un loading gif
	}
})();
