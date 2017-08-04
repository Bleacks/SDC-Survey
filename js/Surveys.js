$('.survey-target').on('click', function(){
    url = 'Surveys/' + $(this)[0].id
    jsonData = {
        'Survey': 'info'
    }
    sendSurvey(url, jsonData);
})

function sendSurvey(url, jsonData)
{
    $.ajax({
        url        : url,
        dataType   : 'json',
        contentType: 'application/json; charset=UTF-8',
        data       : JSON.stringify(jsonData),
        type       : 'POST',
        complete   : onComplete
    })
}

function onComplete(response)
{
    //logError(response.responseText);
    console.log(response);
    window.location = 'Surveys';
}

function logError(message)
{
    $('main').append(message);
}
