functions = (function()
{
	var i = 0;

	return [
		function initModal()
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
		},

		// TODO: Change algorithm for a random progression, in order to keep it real for the end user
		function tempo ()
		{
		   if (i <= 10)
		   {
			   $('#modal_progression').css('width', (i*10) + '%');
			   i++;
			   setTimeout(tempo, 200);
		   } else
		   {
			   // Stores the response.status in order to hide ancient answer for next modal
			   //i = response.status;
			   return;
		   }
	   	},

		function feedback(response)
		{
			$('#modal_title').hide();
			i = response.status;
			var status = response.status;
			if (status == 200)
			{
				$('#btn_footer_mail').show();
			}
			else
				if (undefined == $('#modal_title_' + status)[0])
				{
					// TODO: Add administrator's mail, or link to contact him
					// TODO: Automatically log informations to the administrator email ? (disclaimer : no personnal adresses)
					$('#modal_title_err').text('Error ['+ status +'] : '+ response.statusText);
					$('#modal_message_err').text('Contact system admin, and provide him thoses informations : \n' + 'Error ['+ status +'] : '+ response.statusText + ' : '+ response.responseText);
					$('#modal_title_err').attr('id', 'modal_title_' + status);
					$('#modal_message_err').attr('id', 'modal_message_' + status);
				}

			$('#modal_title_' + status).show();
			$('#modal_message_' + status).show();
		},

		function sendAjax(targetUrl, jsonData)
		{
			$.ajax({
				url        : targetUrl,
				dataType   : 'json',
				contentType: 'application/json; charset=UTF-8',
				data       : JSON.stringify(jsonData),
				type       : 'POST',
				complete   : function (response)
				{
					$('#modal_progression').removeClass('indeterminate').addClass('determinate');
					$('#modal_title').text("Envoi en cours...");
					tempo();
					setTimeout(feedback.bind(null, response), 2400);
				}
			});
		},

		createAccount = (function()
		{
			var email = '';
			var password = '';

			// TODO: Faire détecter les informations au navigateur (form detect)
			// TODO: Adatper le fonctionnement sur mobile

			return function()
			{
				if (email !== $('#email').val() || password !== $('#password').val())
				{
					email = $('#email').val().toLowerCase();
					password = $('#password').val();

					initModal();

					var jsonData = {
						"email": email,
						"password": password
					};

					sendAjax('Subscribe', jsonData);

				} else
					$('#submitted').modal('open');
			}
		})(),

		confirmSubscription = (function()
		{
			var first = '';
			var last = '';
			var age = '';
			var city = '';

			return function(token)
			{
				var newFirst = $('#first_name').val();
				var newLast = $('#last_name').val();
				var newAge = $('#age').val();
				var newCity = $('#city').val();

				if (first !== newFirst || last !== newLast
				 || age !== newAge || city !== newCity) {

				first = newFirst;
				last = newLast;
				age = newAge;
				city = newCity;

				initModal();

				var jsonData = {
					"first_name": first,
					"last_name": last,
					"age": age,
					"city": city
				};

				sendAjax('Subscribe/' + token, jsonData);

				} else
					$('#submitted').modal('open');
			}
		})(),


		function(event)
		{
			var keyCode = event.keyCode || event.which;
			var button = $('#send');
			if (keyCode == 13 && !button.hasClass('disabled'))
				button.trigger('click');
		}
	];
})();

initModal = functions[0];
tempo = functions[1];
feedback = functions[2];
sendAjax = functions[3];
createAccount = functions[4];
confirmSubscription = functions[5];
$(window).on("keyup", functions[6]);
