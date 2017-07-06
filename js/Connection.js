functions = (function()
{
	var mail = '';
	var pass = '';

	return [
		function connectUser()
		{
			mail = $('#email').val().toLowerCase();
			pass = $('#password').val();

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
					//$('main').append(response.responseText);
					$('#notification').slideUp("fast")
					var location = 'Home';
					if (response.responseText != '')
					{
						var json = JSON.parse(response.responseText);
						if (json.hasOwnProperty('url'))
						{
							console.log(json['url']);
							location = json['url'];
						}
						// FIXME: Ajouter une exception pour les différentes erreurs dans une fonction générique
					}
					if (response.status == 200)
					{
						window.location = location;
					} else
					{
						$('#notification_text').text('Combinaison Email/Mot de passe inexistante')
						$('#notification').slideDown("slow", function()
						{
							// TODO: Changer la couleur des champs
							verifyFields();
						});
					}
				}
			});

		},

		function verifyFields()
		{
			var password = $('#password').val();
			var email = $('#email').val().toLowerCase();
			if (password != '' && email != '' && (password != pass || email != mail))
			{
				$('#send').removeClass('disabled');
				$('.required').removeClass('invalid').addClass('valid');
			} else
			{
				$('#send').addClass('disabled');
				$('.required').removeClass('valid').addClass('invalid');
			}
		},

		function onDocumentReady()
		{
			verifyFields(); // TODO: Vérifier que tous les formulaires ont un verify dès le début et sont disabled de base
			$('.required').on('keyup', verifyFields);
		},
		
		function onKeyUp(event)
		{
			var keyCode = event.keyCode || event.which;
			if (keyCode == 13 && !$('#send').hasClass('disabled'))
				connectUser();
		}
	];

})();


connectUser = functions[0];
verifyFields = functions[1];
$(document).ready(functions[2]);
$(window).on("keyup", functions[3]);
