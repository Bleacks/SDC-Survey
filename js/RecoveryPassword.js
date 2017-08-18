// At least 1 letter AND 1 number AND length > 5
// TODO : changer en tableau de fonctions
//$(document).ready(function() {

	// TODO: message d'erreur pour le mot de passe et mail à revoir
    var oldPass = $('#old_pw');
	var pass = $('#change_pw');
    var conf = $('#change_pwc');
    var send = $('#send');
	var i = 0;
	var regex_pass = new RegExp("^(?=.*[a-zA-Z])(?=.*[0-9]).{5,25}$");
    //var regex_pass = new RegExp("^[A-Za-z0-9,;:!\/\*$&-+]{5,25}$");
	//var regex_pass2 = new RegExp("^.*[0-9].*$");
	var code = window.location.href.split('/').slice(-1)[0];

	// FIXME: Retirer les init de valid pour la mise en production
	pass.isValid = true;
	conf.isValid = true;
	oldPass.isValid = true;

	// NOTE: Supprimer les verify qui suivent pour la mise en production
	verifyButton();
	verifyPass();
	verifyConf();
	verifyOldPass();

//});

    pass.on("input", function(e) {
		 verifyPass();
    });

    conf.on("input", function(e) {
		 verifyConf();
    });

	oldPass.on("input", function(e) {
		 verifyOldPass();
    });


	function sendAjax(targetUrl, jsonData, onComplete)
	{
		$.ajax({
			url        : targetUrl,
			dataType   : 'json',
			contentType: 'application/json; charset=UTF-8',
			data       : JSON.stringify(jsonData),
			type       : 'POST',
			complete   : onComplete
		});
	}

    function verifyButton() {
        $('#send').prop('disabled', !(pass.isValid && conf.isValid && oldPass.isValid));
    }

	function onRecoveryComplete(response)
	{
		var notification = $('#notification');
		notification.slideUp("fast");
		console.log(response);
		logError(response.responseText);

		switch (response.status)
		{
			case 200:
				message = 'Votre mot de passe a bien été modifié. Vous allez être redirigé vers la page de connexion. ';
				notification.slideDown("slow", verifyButton);
				$('#notification').removeClass('red').addClass('green');
                setTimeout(function(){ window.location = JSON.parse(response.responseText); }, 2000);
				break;

			case 409:	// TODO: Change notification's color and bring shawdow to it
				message = 'Une erreur s\'est produite.';
				$('#notification').removeClass('green').addClass('red');
				notification.slideDown("slow", verifyButton);
				break;

			case 424:	// TODO: Change notification's color and bring shawdow to it
				message = 'Une erreur s\'est produite, veuillez recommencer.';
				$('#notification').removeClass('green').addClass('red');
				notification.slideDown("slow", verifyButton);
				break;

			default:
				message = 'Unhandled exception';
				break;
		}
		$('#notification_text').text(message);
	}
	// TODO: Optimiser les deux fonctions onComplete
	function onChangePasswordComplete(response)
	{
		var notification = $('#notification');
		notification.slideUp("fast");
		console.log(response);
		logError(response.responseText);

		switch (response.status)
		{
			case 200:
				message = 'Votre mot de passe a bien été modifié. ';
				notification.slideDown("slow", verifyButton);
				$('#notification').removeClass('red').addClass('green');
				//setTimeout(function(){ window.location = 'Connect'; }, 2000);
				break;

			case 409:	// TODO: Change notification's color and bring shawdow to it
				message = 'Votre ancien mot de passe est incorrect.';
				$('#notification').removeClass('green').addClass('red');
				notification.slideDown("slow", verifyButton);
				break;

			case 424:	// TODO: Change notification's color and bring shawdow to it
				message = 'Une erreur s\'est produite, veuillez recommencer.';
				$('#notification').removeClass('green').addClass('red');
				notification.slideDown("slow", verifyButton);
				break;

			default:
				message = 'Unhandled exception';
				break;
		}
		$('#notification_text').text(message);
	}

	function verifyPass() {
		var value = pass.val();
		if (regex_pass.test(value) && regex_pass.test(value))
		   validPass();
		else
		   invalidPass();
		clean(pass);
		verifyConf();
    }

    function verifyConf() {
		if (pass.val() == conf.val())
		   validConf();
		else
		   invalidConf();
		clean(conf);
    }

	function verifyOldPass() {
		var value = oldPass.val();
		if (regex_pass.test(value))
		   validOldPass();
		else
		   invalidOldPass();
		clean(oldPass);
    }

    function clean(e) {
		if (e.val() == '') {
		   e.removeClass("invalid").removeClass("valid");
			e.isValid = false;
		}
    }

    function validPass() {
		validInput(pass);
    }

    function validConf() {
		if (conf.val() == '')
		   clean(conf);
		else
		   validInput(conf);
    }

	 function validOldPass() {
		validInput(oldPass);
    }

    function invalidPass() {
		invalidInput(pass);
    }

    function invalidConf() {
      	invalidInput(conf);
    }

	function invalidOldPass() {
		invalidInput(oldPass);
    }

    function invalidInput(e) {
		e.removeClass('valid').addClass('invalid');
		e.isValid = false;
		verifyButton();
    }

    function validInput (e) {
    	e.removeClass('invalid').addClass('valid');
		e.isValid = true;
		verifyButton();
    }
	$(window).on("keyup", function(event) {
		var keyCode = event.keyCode || event.which;
		if (keyCode == 13 && !$('#send').hasClass('disabled'))
		{
			sendRecoveryRequest();
			//sendChangePasswordRequest();
		}

	});

// TODO: Add tooltips on hover for each fields of the form, displaying validation condition
   /*
   $('.tooltipped').on("focusin", function(){
   		$('.tooltipped').tooltip().show();
});*/
	function sendRecoveryRequest()
	{
		var jsonData = {
			"change_pw": pass.val(),
			"change_pwc": conf.val()
		};
		sendAjax('Recovery/' + code, jsonData, onRecoveryComplete);
	}

	function sendChangePasswordRequest()
	{
		var jsonData = {
			"old_pw": oldPass.val(),
			"change_pw": pass.val(),
			"change_pwc": conf.val()
		};
		sendAjax('ChangePassword', jsonData, onChangePasswordComplete);
	}
