confirmSubscription = (function() {
	var i = 0;
	var first = '';
	var city = '';
	var last = '';
	var age = '';

	// TODO: Voir pour factoriser le code commun dans des fonctions communes

	return function(token) {

		if (first !== $('#first_name').val() || last !== $('#last_name').val() ||
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
							 setTimeout(tempo, 20);
						 } else
							 // Stores the response.status in order to hide ancient answer for next modal
							 i = response.status;
					 }

					 $('#modal_progression').removeClass('indeterminate').addClass('determinate');
					 $('#modal_title').text("Envoi en cours...");
					 tempo();
					 setTimeout(feedback, 240);

					 // FIXME: Remettre les timeout sur *10

					 // TODO: Rename les JS
					 // TODO: Fusionner les JS ajax
					 /**
					 * Method called after loading-like delay of 'tempo'
					 * Changes modal's content to inform user of the request answer
					 */
					 function feedback() {
						 $('#modal_title').hide();
						 var status = response.status;
						 if (status == 200)
						 {
							 // In case of successfull submition
							 $('#btn_footer_mail').show();
						 }
						 // In case of unsuccessfull submition we check if error is already handled, if not generic error message is displayed
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
	}
})();
