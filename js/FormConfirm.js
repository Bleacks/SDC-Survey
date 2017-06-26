// At least 1 letter AND 1 number AND length > 5
$(document).ready(function() {

	// TODO: message d'erreur pour le mot de passe et mail à revoir
    var pass = $('#password');
    var conf = $('#password_confirm');
    var send = $('#send');
	var mail = $('#email');
    var regex_pass = new RegExp("^[A-Za-z0-9,;:!\/\*$&-+]{5,25}$");
	var regex_pass2 = new RegExp("^.*[0-9].*$");
	var regex_mail = new RegExp('^([\\w\\-\\._]+\\@[\\w\\-_]+\\.[\\w\\-\\._]+)$');
	// TODO: °Voir avec Xaviera si on utilise les mails étudiants (mieux) ^[\w-._]+\@(etu\.)?univ-lorraine\.fr

	// FIXME: Retirer les init de valid pour la mise en production
	pass.isValid = true;
	conf.isValid = true;
	mail.isValid = true;

	mail.on("input", function(e) {
		verifyMail();
	});

    pass.on("input", function(e) {
		 verifyPass();
    });

    conf.on("input", function(e) {
		 verifyConf();
    });

    function verifyButton() {
		if (pass.isValid == true && conf.isValid == true && mail.isValid == true)
			send.removeClass('disabled');
		else
			send.addClass('disabled');
    }

	 function verifyMail() {
		if (regex_mail.test(mail.val()))
			validMail();
		else
		 	invalidMail();
		clean(mail);
	 }

	function verifyPass() {
		var value = pass.val();
		if (regex_pass.test(value) && regex_pass2.test(value))
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

    function clean(e) {
		if (e.val() == '') {
		   e.removeClass("invalid").removeClass("valid");
			e.isValid = false;
		}
    }

	 function validMail() {
		if (mail.val() == '')
			clean(mail);
		else
			validInput(mail);
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

    function invalidPass() {
		invalidInput(pass);
    }

    function invalidConf() {
      	invalidInput(conf);
    }

	 function invalidMail() {
		invalidInput(mail);
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
	$('#send').on("keyup keypress", function(event) {
		var keyCode = event.keyCode || event.which;
		if (keyCode == 13)
			createAccount();
	});
   
   // TODO: Ajouter des tooltips sur tous les champs du formulaire :focus
   /*
   $('.tooltipped').on("focusin", function(){
   		$('.tooltipped').tooltip().show();
});*/
});
