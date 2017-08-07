functions = (function()
{
	var mail = '';
	var regex_mail = new RegExp('^([\\w\\-\\._]+\\@[\\w\\-_]+\\.[\\w\\-\\._]+)$');
	
	return {
		/**
		* Gather form's data and send it to the server using Ajax request
		* Also handling errors that server could send back and push them to notification pane
		*/
		sendRecoveryRequest: function sendRecoveryRequest()
		{
			var mail = $('#recovery_email').val().toLowerCase();
			var displayed = false;


			var jsonData = {
				"recovery_email": mail
			};

			$.ajax({
				url        : 'Recovery',
				dataType   : 'json',
				contentType: 'application/json; charset=UTF-8',
				data       : JSON.stringify(jsonData),
				type       : 'POST',
				complete   : function (response)
				{
			
			var notification = $('#notification');
					notification.slideUp("fast");
					//console.log(response);
					//logError(response.responseText);
					
					switch (response.status)
					{
						case 200:
							message = 'Un email vous a été envoyé';
							notification.slideDown("slow", verifyFields);
							$('#notification').removeClass('red').addClass('green');
							break;
							
						case 424:	// TODO: Change notification's color and bring shawdow to it
							message = 'Vous ne possedez pas de compte. Veuillez vous inscrire.';
							$('#notification').removeClass('green').addClass('red');
							notification.slideDown("slow", verifyFields);
							break;
							
						case 403:
							message = 'Votre réinitialisation de mot de passe a éxpiré, veuillez soumettre à nouveau votre adresse email.';
							$('#notification').removeClass('green').addClass('red');
							notification.slideDown("slow", verifyFields);
							break;
						
						case 409:
							message = 'Veuillez remplir tous les champs.';
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
		* Used to unvalid form when error is received 
		*/
		
		verifyFields: function verifyFields()
		{
			var email = $('#recovery_email').val().toLowerCase();
			if (email != '' && regex_mail.test(email))
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
		* Used to allow user to submit form using Enter key
		*/
		onKeyUp: function onKeyUp(event)
		{
			var keyCode = event.keyCode || event.which;
			if (keyCode == 13 && !$('#send').hasClass('disabled'))
				sendRecoveryRequest();
		}
	};

})();

var sendRecoveryRequest = functions.sendRecoveryRequest;
var verifyFields = functions.verifyFields;
var onKeyUp = functions.onKeyUp;
$('.required').on('keyup', verifyFields);
$(document).on('keyup', onKeyUp);
$(document).ready(verifyFields);