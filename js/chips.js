$('.chips').material_chip();
$('.chips-initial').material_chip({
   data: [{
      tag: 'Apple',
   }, {
      tag: 'Microsoft',
   }, {
      tag: 'Google',
   }],
});
$('.chips-placeholder').material_chip({
   placeholder: 'Enter a tag',
   secondaryPlaceholder: '+Tag',
});
$('.chips-autocomplete').material_chip({
   autocompleteOptions: {
      data: {
        'Apple': null,
        'Microsoft': null,
        'Google': null
      },
      limit: Infinity,
      minLength: 1
    }
});

// TODO: Ajouter la gestion des chips avec la BDD
/*
Création des données :
var chip = {
    tag: 'chip content',
    image: '', //optional
    id: 1, //optional
  };

Récupération des données :
 $('.chips-initial').material_chip('data');
 */
