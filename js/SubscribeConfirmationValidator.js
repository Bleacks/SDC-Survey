/**
* Changes front-end informations when completing form fields
*/
$(document).ready(function() {

	// Initializes graphical elements of the associated page
	$('select').material_select();
	$('.indeterminate-checkbox').prop('indeterminate', true)
	$('.empty_label').css('cursor', 'default');
	$('.indeterminate-checkbox').css('cursor', 'default');

	// TODO: Ajust display of differents elements
	// FIXME: Add red asterisk on input.select-dropdown

	// Array of fields that has to be updated on
	var onkeyup = ['#first_name', '#last_name', '#age'];

	for (var i = 0; i < 3; i++) {
		handler(onkeyup[i], 'keyup');
	};

	// Array of fields that has to be updated on change
	var onchange = ['#city'];

	// Adds listener for on change event
	for (var i = 0; i < 2; i++) {
		handler(onchange[i], 'change');
	};

	/** Creates handler for the given element */
	function handler(element, event) {
		$(element).on(event, function() {
			validate(element, $(element).val() != '');
			verifyButton();
		});
	}

	/** Changes the state of the given checkbox to checked or indeterminate base on given boolean */
	function validate(element, valid) {
		$(element + '_status').prop('checked', valid).prop('indeterminate', !valid);
	}

	/** Refreshs button's disabled prop, based on all form's checkboxes state */
	function verifyButton() {
		if ($('#age_status').prop('checked') && $('#city_status').prop('checked')
		&& $('#last_name_status').prop('checked') && $('#first_name_status').prop('checked'))
			$('#send').removeClass('disabled');
		else
			$('#send').addClass('disabled');
	}

	// Disable on click actions for checkboxes as they're only used to inform field validation
	$('.indeterminate-checkbox').on('click',function() {
		$(this).prop('checked', false);
		if ($(this).prop('checked')) {
			$(this).prop('indeterminate', false);
		} else {
		    $(this).prop('indeterminate', true);
		}
	})
});
