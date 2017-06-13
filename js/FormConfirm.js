// At least 1 letter AND (1 number OR spec char) AND length > 5
$(document).ready(function() {

    var pass = $('#password');
    var conf = $('#password_confirm');
    var send = $('#send');
    var regex = new RegExp("^[A-Za-z]+([0-9]|[,;:!\/\*$&-+])+$");

    pass.on("keyup", function(e) {
		 verifyPass();
    });

    conf.on("keyup", function(e) {
		 verifyConf();
    });

    function verifyButton() {
		if (pass.isValid == true && conf.isValid == true)
			send.removeClass('disabled');
		else
			send.addClass('disabled');
    }

	function verifyPass() {
		var value = pass.val();
		if (regex.test(value) && value.length > 5)
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
		   e.removeClass("invalid").removeClass("valid").addClass('validate');
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

    function invalidPass() {
		invalidInput(pass);
    }

    function invalidConf() {
      invalidInput(conf);
    }

    function invalidInput(e) {
		e.removeClass('valid').removeClass('validate').addClass('invalid');
		e.isValid = false;
		verifyButton();
    }

    function validInput (e) {
    	e.removeClass('invalid').removeClass('validate').addClass('valid');
		e.isValid = true;
		verifyButton();
    }
    /* TODO: Ajouter des tooltips sur tous les champs du formulaire :focus
    $('.tooltipped').on("focusin", function(){
       $('.tooltipped').tooltip();
    });
	 */
});
