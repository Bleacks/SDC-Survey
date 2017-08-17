// At least 1 letter AND 1 number AND length > 5
// TODO : changer en tableau de fonctions
//$(document).ready(function() {

$('.required').prop('disabled', true);
//$('.select-dropdown').prop("disabled", true);
//$('.personnal-input').prop("disabled", true).prop("selected", true);

var email = $('#email');
var age = $('#age');
var city = $('#city');
var group = $('#group');

var base_email;
var base_age;
var base_city;
var base_group;


var regex_mail = new RegExp('^([\\w\\-\\._]+\\@[\\w\\-_]+\\.[\\w\\-\\._]+)$');
var regex_age = new RegExp('^[1-9][0-9]?$|^100$');

// TODO: faire une fonction EnableChanges avec un bool

function allowChanges()
{
    //$('.personnal-input').prop("disabled", false).prop("selected", false);
    //$('.select-dropdown').prop("disabled", false);
    $('#modify').hide();
    $('#password').hide();
    $('#title').hide();
    $('#title_modify').show();
    $('#send').removeClass('hide');
    $('.required').prop('disabled', false);
    $('select').material_select();
}
function sendChangeInformation()
{
    var jsonData = {
       "email": email.val().toLowerCase(),
       "city": city.val(),
       "age" : age.val(),
       "group" : group.val()
    };
    sendAjax(jsonData, onChangeInformation);
}

function sendAjax(jsonData, onComplete)
{
    $.ajax({
        url        : 'Profile',
        dataType   : 'json',
        contentType: 'application/json; charset=UTF-8',
        data       : JSON.stringify(jsonData),
        type       : 'POST',
        complete   : onComplete
    });
}

function onChangeInformation(response)
{
    var notification = $('#notification');
   notification.slideUp("fast");
   console.log(response);
   logError(response.responseText);

   switch (response.status)
   {
       case 200:
           message = 'Vos informations ont bien été modifiées. Vous allez être déconnecté.';
           notification.slideDown("slow", null);
           $('#notification').removeClass('red').addClass('green');
           setTimeout(function(){ window.location = 'Disconnect'; }, 2000);
           break;

       case 409:	// TODO: Change notification's color and bring shawdow to it
           message = 'L\'adresse email existe déjà, veuillez entrer une autre adresse mail. ';
           $('#notification').removeClass('green').addClass('red');
           notification.slideDown("slow", null);
           break;

       case 424:	// TODO: Change notification's color and bring shawdow to it
           message = 'Une erreur s\'est produite, veuillez recommencer';
           $('#notification').removeClass('green').addClass('red');
           notification.slideDown("slow", null);
           break;

       default:
           message = 'Unhandled exception';
           break;
   }
   $('#notification_text').text(message);
}

email.on("input", function(e) {
     verifyEmail();
});

age.on("input", function(e) {
     verifyAge();
});

function verifyButton() {
    $('#send').prop('disabled', !(email.isValid && age.isValid));
}

function verifyEmail() {
    var new_val = email.val().toLowerCase();
    if (new_val != '' && regex_mail.test(new_val))
       validEmail();
    else
       invalidEmail();
}

function verifyAge() {
    var new_val = age.val();
    if(regex_age.test(new_val))
        validAge();
    else
        invalidAge();
//    clean(age);
}

function clean(e) {
    if (e.val() == '') {
        e.removeClass("invalid").removeClass("valid");
        e.isValid = false;
    }
}

function validEmail() {
    validInput(email);
}

function validAge() {
    validInput(age);
}

function invalidEmail() {
    invalidInput(email);
}
function invalidAge() {
    invalidInput(age);
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

$(document).ready(function() {

    $('select').material_select();

    base_email = email.val().toLowerCase();
    base_age = age.val();
    base_city = city.val();
    base_group = group.val();

	// FIXME: Retirer les init de valid pour la mise en production
	email.isValid = true;
	age.isValid = true;

//});
})
