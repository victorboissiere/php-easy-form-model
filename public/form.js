$(function() {

  function getInputValue(name)
  {
    return $('input[name="' + name + '"]').val();
  }

  $('#form').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
      url: $(this).attr('action'),//  Get form URL (action attribute) 
      type: $(this).attr('method'),// Get form method (method attribute)
      data: {
        'firstname': getInputValue('firstname'),
        'lastname': getInputValue('lastname'),
        'email': getInputValue('email')
      }
    }).done(function(response) {
      console.log('Server responded with :' + response);
    }).fail(function(error) {
      console.log('Request could not be send correctly');
      console.error('Error' + error);
    });
    return false;
  });
});
