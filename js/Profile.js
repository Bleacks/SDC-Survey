functions = (function()
{
  var email = '';
  var regex_mail = new RegExp('^([\\w\\-\\._]+\\@[\\w\\-_]+\\.[\\w\\-\\._]+)$');

	$('select').material_select();

	return {
		/**
		* Gather form's data and send it to the server using Ajax request
		* Also handling errors that server could send back and push them to notification pane
		*/
		changeInformation: function changeInformation()
		{

			var jsonData = {
                "email": email

            };


			$.ajax({
				url        : 'Profile',
				dataType   : 'json',
				contentType: 'application/json; charset=UTF-8',
				data       : JSON.stringify(jsonData),
				type       : 'PUT',
				complete   : function (response)
				{

					//console.log(response);
					//logError(response.responseText);
				}
			});

		},

    allowChanges: function allowChanges()
    {
      $('.personnal-input').prop("disabled", false).prop("selected", false);
      $('.select-dropdown').prop("disabled", false);
      $('#modify').hide();
      $('#password').hide();
      $('#send').removeClass('hide');

    },

		/**
		* Used to unvalid form when error is received
		*/

		verifyFields: function verifyFields()
		{
			var email = $('#email').val().toLowerCase();
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

var changeInformation = functions.changeInformation;
var allowChanges = functions.allowChanges;
var verifyFields = functions.verifyFields;
var onKeyUp = functions.onKeyUp;
$('.required').on('keyup', verifyFields);
$(document).on('keyup', onKeyUp);
$(document).ready(function() {
  $('.select-dropdown').prop('disabled', true);
  verifyFields
});
