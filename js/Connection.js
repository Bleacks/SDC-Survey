// Using IIFE to wrap content and "privatize" gobals var
functions = (function()
{
	var mail = '';
	var pass = '';
	var regex_mail = new RegExp('^([\\w\\-\\._]+\\@[\\w\\-_]+\\.[\\w\\-\\._]+)$');

	return {
		/**
		* Gather form's data and send it to the server using Ajax request
		* Also handling errors that server could send back and push them to notification pane
		*/
		connectUser: function connectUser()
		{
			var mail = $('#email').val().toLowerCase();
			var pass = $('#password').val();
			var displayed = false;


			var jsonData = {
				"email": mail,
				"password": pass,
				"remember": $('#remember').prop('checked')
			};

			$.ajax({
				url        : 'Connect',
				dataType   : 'json',
				contentType: 'application/json; charset=UTF-8',
				data       : JSON.stringify(jsonData),
				type       : 'POST',
				complete   : function (response)
				{
					console.log(response);
					//$('main').append(response.responseText);
					var notification = $('#notification');
					notification.slideUp("fast");
					var location = 'Home';
					if (response.responseText != '')
					{
						var json = JSON.parse(response.responseText);
						if (json.hasOwnProperty('url'))
						{
							location = json['url'];
						}
					}
					switch (response.status)
					{
						case 200:
							window.location = location;
							break;

						case 422:	// TODO: Change notification's color and bring shawdow to it
							message = 'Combinaison Email/Mot de passe inexistante';
							notification.slideDown("slow", verifyFields);
							break;

						case 500:
							message = 'Une erreur s\'est produite';
							break;

						default:
							message = 'Unhandled exception';
							break;
					}
					$('#notification_text').text(message);
				}
			});

		},

		/**
		* Used to unvalid form when error is received and also to valid it back when changes are made
		*/
		verifyFields: function verifyFields()
		{
			var password = $('#password').val();
			var email = $('#email').val().toLowerCase();
			if (password != '' && email != '' && regex_mail.test(email) && (password != pass || email != mail))
			{
				$('#send').removeClass('disabled');
				$('.required').removeClass('invalid').addClass('valid');
			} else
			{
				$('#send').addClass('disabled');
				$('.required').removeClass('valid').addClass('invalid');
			}
		},

		/**
		* Used to initialize form behavior and validation
		*/
		onDocumentReady: function onDocumentReady()
		{
			verifyFields(); // TODO: Vérifier que tous les formulaires ont un verify dès le début et sont disabled de base
			$('.required').on('keyup', verifyFields);
		},

		/**
		* Used to allow user to submit form using Enter key
		*/
		onKeyUp: function onKeyUp(event)
		{
			var keyCode = event.keyCode || event.which;
			if (keyCode == 13 && !$('#send').hasClass('disabled'))
				connectUser();
		}
	};

})();


connectUser = functions.connectUser;
verifyFields = functions.verifyFields;
$(document).ready(functions.onDocumentReady);
$(window).on("keyup", functions.onKeyUp);
