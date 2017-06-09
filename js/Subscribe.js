function createAccount() {
   $.post("Subscribe", { json_string:JSON.stringify({email:"otto@polo.fr"}) } );

   var email = $('#email').val();
   var password = $('#password').val();

   var json_data = {
      "email": email,
      "password": password
   };

   $.ajax({
       url        : 'Subscribe',
       dataType   : 'json',
       contentType: 'application/json; charset=UTF-8', // This is the money shot
       data       : JSON.stringify(json_data),
       type       : 'POST',
       complete   : function (response) {
          if (response.status == 200)
            alert('success');
          else
            console.log(response.responseText);
       }
    });
    // TODO: Fusionner avec le script de vérification des mot de passes ?
}
