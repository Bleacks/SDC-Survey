/** Containing one boolean for each question
* true if valid or not required
* false if not
*/
var validFields = [];

// Initializes form and listener for eventual 'other' section
$('[id^=question-]').each(function()
{
    validFields.push(!$(this).attr('required'));
    var question = $(this);
    var other = question.siblings().children().filter('[id^=other-]');
    if (other.size() == 1)
        addOtherListener(question, other)
});

// Initilizes the form state
verifyForm();

// Adds listener to all answers of each question in the page
$('.question').each(function()
{
    var question = $(this);
    var questionId = question.attr('id').slice(-1);
    switch (question.attr('type'))
    {
        // TODO: Find a way to refactor case 1 with case 3 using addElementListener and common code
        case '1':   // Multiple choice (Checkbox)
            $('[answersto='+ questionId +']').each(function() {
                addCheckboxListener(question, $(this), questionId)
            });
            break;

        case '2':   // Unique choice (Select)
            $('#select-'+ questionId).on('change', function(e) {
                console.log('Select changed for question '+questionId);
                setField(question, true)
            });
            break;

        case '3':   // Multiple choice (Chips)
            $('.chips').on('chip.add', function(e, chip){
                console.log('Chip added for question '+questionId)
                setField(question, true);
            });
            $('.chips').on('chip.delete', function(e, chip){
                console.log('Chip deleted for question '+questionId)
                setField(question, $('.chips').children().filter('div.chip').size() > 0);
            });
            break;

        case '4':   // Unique choice (Radio)
            $('[answersto='+ questionId +']').each(function() {
                addRadioListener(question, $(this), questionId);
            });
            break;

        default:
            break;
    }
});

/** Adds listener for 'other' elements at the end of answer list */
function addOtherListener(question, element)
{
    var newIndex = Number(element.attr('id').split('-')[1]);
    var otherCount = newIndex + 1;
    var newId = 'other-'+ newIndex;
    var lastId = 'other-'+ otherCount;
    var questionId = question.attr('id').slice(-1);
    var questionType = question.attr('type');
    element.parent().on('click', function()
    {
        element.prop('checked', false);
        question.parent().append( generateElement(questionId, questionType, lastId, 'Autre..') );
        $(this).replaceWith( generateElement(questionId, '5', newId, '') );
        $('#'+ newId).on('change', function()
        {
            var value = $(this).val();
            $(this).parent().replaceWith( generateElement(questionId, questionType, newId, value) );
            $('#'+ lastId).on('click', function() { addOtherListener(question, $(this)) });
            $('#'+ lastId).prop('checked', false);
            addElementListener(question, $('#'+ newId));
            setField(question, true);
        })
    });
}

/** Generate new element (HTML code) base on the given informations */
function generateElement(questionId, type, id, name)
{
    var elementHTML;
    switch (type)
    {
        case '1':   // Multiple choice (Checkbox)
            elementHTML =
            '<p class="col s6">\
                <input id="'+ id +'" answersto="'+ questionId +'" type="checkbox" name="'+ name +'" class="filled-in">\
                <label for="'+ id +'">'+ name +'</label>\
            </p>';
            break;

        case '2':   // Unique choice (Select)
            break;  // No need of 'other' section in select, use Radio instead

        case '3':   // Multiple choice (Chips)
            break;  // No need of 'other' section, just type in input

        case '4':   // Unique choice (Radio)
            elementHTML =
            '<p>\
                <input class="with-gap" name="'+ questionId +'" answersto="'+ questionId +'" type="radio" id="'+ id +'" />\
                <label for="'+ id +'">'+ name +'</label>\
            </p>';
            break;

        case '5':   // Text input
            elementHTML =
            '<p class="col s6">\
                <input id="'+ id +'" type="text" autofocus name="'+ id +'" class="col s10" style="margin-top:-1.5em;">\
            </p>';
            break;

        default:
            break;
    }
    return elementHTML;
}

/** Adds listener on the given element associated to the given question */
function addElementListener(question, element)
{
    var questionId = question.attr('id').slice(-1);
    if (question.attr('type') == 1)
        addCheckboxListener(question, element, questionId);
    else
        addRadioListener(question, element, questionId);
    element.prop('checked', true);
}

/** Adds listener for the given checkbox */
function addCheckboxListener(question, checkbox, questionId)
{
    checkbox.on('change', function() {
        console.log('Checkbox changed for question '+questionId);
        var valid;
        $('[answersto='+ questionId +']').each(function() { valid |= checkbox.prop('checked')});
        setField(question, valid);
    });
}

/** Adds listener for the given checkbox */
function addRadioListener(question, radio, questionId)
{
    radio.on('click', function() {
        console.log('Radio changed for question '+questionId);
        setField(question, true);
    });
}

/** Changes the given question state for form validation */
function setField(question, value)
{
    var questionId = question.attr('id').slice(-1);
    var valid = question.attr('required');
    valid = (valid && value) || !valid;
    validFields[questionId-1] = valid;
    verifyForm();
}

/** Verifies form validity and apply it */
function verifyForm()
{
    valid = true;
    validFields.forEach(function(element) { valid &= element });
    changeFormState(valid);
}

/** Changes the form state to the given state */
function changeFormState(state)
{
    $('#send').prop('disabled', !state);
    $('form').unbind('submit', state);
}



// Initializes materilize's select dropdown
$('select').material_select();

// Adds all answers related to this question in the related chip's autocomplete
var autocompleteElements = $('.chips-autocomplete');
var idGS = window.location.href.split('/').slice(-1)[0];
var jsonData = {};
for (var i = 0; i < autocompleteElements.size(); i++)
    sendAjax('Surveys/' + idGS + '?chips-data=' + autocompleteElements.attr('chips-data'));

// TODO: Change click behavior or remove this listener
// Auto-submit form on click
$('.survey-target').on('click', function()
{
    url = 'Surveys/' + $(this)[0].id
    jsonData = {
        'Survey': 'info'
    }
    sendSurvey(url, jsonData);
});

/** Sends Ajax request to submit this survey */
function sendSurvey(url, jsonData)
{
    $.ajax({
        url        : url,
        dataType   : 'json',
        contentType: 'application/json; charset=UTF-8',
        data       : JSON.stringify(jsonData),
        type       : 'POST',
        complete   : function (response)
        {
            window.location = 'Surveys';
        }
    })
}

/** Sends Ajax request in order to retrieve chips-data (autocomplete answers) from the database */
function sendAjax(url)
{
    $.ajax({
        url        : url,
        dataType   : 'json',
        contentType: 'application/json; charset=UTF-8',
        type       : 'GET',
        complete   : function (response)
        {
            if (response.status == 200)
            {
                jsonData = response.responseJSON;

                for (i = 0; i < response.responseJSON.length; i++)
                    jsonData[response.responseJSON[i]] = null;
                $('.chips-autocomplete').material_chip({
                    autocompleteOptions: {
                        data: jsonData,
                        limit: 5,
                        minLength: 1
                    }
                });
            }
        }
    })
}
