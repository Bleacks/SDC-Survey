confirmSubscription = (function() {
	var i = 0;
	var first = '';
	var city = '';
	var last = '';
	var age = '';

	// TODO: Voir pour factoriser le code commun dans des fonctions communes

	return function(token) {

		if (first !== $('#first_name').val() || last_name !== $('#last_name').val() ||
		age !== $('#age').val() || city !== $('#city').val())
		{
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
			first = $('#first_name').val();
			last = $('#last_name').val();
			age = $('#age').val();
			city = $('#city').val();

		   	var json_data = {
		      	"first_name": first,
		      	"last_name": last,
				"age": age,
				"city": city
		   	};

		   	$.ajax({
		       url        : 'Subscribe/' + token,
		       dataType   : 'json',
		       contentType: 'application/json; charset=UTF-8',
		       data       : JSON.stringify(json_data),
		       type       : 'POST',
		       complete   : function (response) {
		          // TODO: Ajouter une barre de progression non régulière pour mieux simuler le compute time
					 function tempo () {
						 if (i <= 10) {
							 $('#modal_progression').css('width', (i*10) + '%');
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
						 switch (response.status) {
							 case 200:
						 			$('#modal_title_200').show();
									$('#modal_message_200').show();
									$('#btn_footer_mail').show();
							 		break;

							 case 409:
									$('#modal_title_409').show();
									$('#modal_message_409').show();
									break;

							 case 424:
									$('#modal_title_424').show();
									$('#modal_message_424').show();
									break;

							 case 429:
									$('#modal_title_429').show();
									$('#modal_message_429').show();
									break;

				  			 default:
									// TODO: Ajouter l'adresse mail du futur administrateur
									// TODO: Log automatique des erreurs sur l'adresse mail de l'administrateur (disclaimer pour l'adresse perso)
									// TODO: Voir si les h4 vides prennent de la place ou non
									$('modal_title').show();
									$('#modal_title').text('Error ['+ response.status +'] : '+ response.statusText);
									$('#modal_message_err').text('Contact system admin, and provide him thoses informations : \n' + 'Error ['+ response.status +'] : '+ response.statusText + ' : '+ response.responseText);
						 }
					 }
		       }
		    });
		 } else
			 $('#submitted').modal('open');
	}
})();
