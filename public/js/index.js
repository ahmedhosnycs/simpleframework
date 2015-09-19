// Toggle Function
$('.toggle').click(function(){
  // Switches the Icon
  $(this).children('i').toggleClass('fa-pencil');
  // Switches the forms  
  $('.form').animate({
    height: "toggle",
    'padding-top': 'toggle',
    'padding-bottom': 'toggle',
    opacity: "toggle"
  }, "slow");
});

$('#register-form').isHappy({
    fields: {
      // reference the field you're talking about, probably by `id`
      // but you could certainly do $('[name=name]') as well.
      '#username': {
        required: true,
        message: 'Please Enter your username'
      },
      '#email': {
        required: true,
        message: 'Please enter your email',
        test: happy.email
      },
      '#password': {
        required: true,
        message: 'Please enter password and should be more than 5 charachters',
        test: function(value) {
            return value.length > 5;
        }
      },
      '#phone': {
        required: true,
        message: 'Please enter a phone number, accepts numbers only',
        test: happy.numbers
      },
      '#dob': {
        required: true,
        message: 'Please enter your date of birth, you should be 18+',
        test: function(value) {
          var birthdate = new Date(value);
          birthdate.setFullYear(birthdate.getFullYear()+18);
          var today = new Date();
          return birthdate < today;
        }
      },
    }
});

$('#login-form').isHappy({
    fields: {
      '#loginemail': {
        required: true,
        message: 'Please enter your email',
        test: happy.email
      },
      '#loginpassword': {
        required: true,
        message: 'Please enter a valid password',
        test: function(value) {
            return value.length > 5;
        }
      },
    }
});