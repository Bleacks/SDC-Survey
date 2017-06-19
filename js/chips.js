$( document ).ready(function() {

   // Initializing chips
   var Rachel = {
      tag: 'Rachel S',
      image: './bitmoji-face.png',
      id: 1
   };
   var chips = {
      Rachel: Rachel,
      Lucidchart: null,
      Architek: null,
      Blender: null,
	  SketchUp: null
   };

   // Treating different chips types
   $('.chips').material_chip();
   $('.chips-initial').material_chip({
      data: [{Rachel}]
   });
   $('.chips-placeholder').material_chip({
      placeholder: 'Enter a tag',
      secondaryPlaceholder: '+Tag',
   });
   $('.chips-autocomplete').material_chip({
      autocompleteOptions: {
         data: chips,
         limit: Infinity,
         minLength: 1
       }
   });
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
