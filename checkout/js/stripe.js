"use strict";

var stripe_key = $('#publishable_key').val();
var publishable_key = stripe_key;
// Create a Stripe client.
var stripe = Stripe(publishable_key);

// Create an instance of Elements.
 var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
    base: {
      color: '#333',
      fontWeight: 500,
      fontFamily: 'Quicksand, Open Sans, Segoe UI, sans-serif',
      fontSize: '16px',
      fontSmoothing: 'antialiased',

      ':focus': {
        color: '#333333',
      },

      '::placeholder': {
        color: '#9BACC8',
      },

      ':focus::placeholder': {
        color: '#CFD7DF',
      },
    },
    invalid: {
      color: '#fff',
      ':focus': {
        color: '#FA755A',
      },
      '::placeholder': {
        color: '#FFCCA5',
      },
    },
  };
var elementClasses = {
    base:'payment-input',
    focus: 'focus',
    empty: 'empty',
    invalid: 'invalid',
  };
// Create an instance of the card Element.
 //var card = elements.create('card', {style: style});
 //var cardNameElement=elements.create("cardName");
 var cardNumberElement=elements.create("cardNumber",{
    style: style,
    classes: elementClasses,
  });
 var cardExpiryElement=elements.create("cardExpiry",{
    style: style,
    classes: elementClasses,
  });
 var cardCvcElement=elements.create("cardCvc",{
    style: style,
    classes: elementClasses,
  });
 var postalCodeElement=elements.create("postalCode",{
    style: style,
    classes: elementClasses,
  });
// Add an instance of the card Element into the `card-element` <div>.
//cardNameElement.mount("#cardname");
cardNumberElement.mount("#cardnumber");
cardExpiryElement.mount("#cardexpiry");
cardCvcElement.mount("#cardcvv");
postalCodeElement.mount("#cardpostal");
// card.mount('#card-element');

function fieldvalidation() {
  var fieldError = [];
  var requiredFields = document.getElementsByClassName('required');

  Array.from(requiredFields).forEach(function (f) {
    if (f.value === '') {
      var formGroup = f.closest('.form-group');
      if (formGroup) {
        formGroup.classList.add('error');
      }
      fieldError.push(f.getAttribute('data-msg'));
    }
  });

  return fieldError;
}




function triggerBrowserValidation() {
    // The only way to trigger HTML5 form validation UI is to fake a user submit
    // event.
    var submit = document.createElement('input');
    submit.type = 'submit';
    submit.style.display = 'none';
    form.appendChild(submit);
    submit.click();
    submit.remove();
  }



// Handle form submission.
// Handle form submission
var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();

  // Trigger HTML5 validation UI on the form if any of the inputs fail validation
  if (!form.checkValidity()) {
    triggerBrowserValidation();
    return;
  }

  var errorMsgContainer = document.getElementById('error-msg');
  var fieldErrors = fieldvalidation();

  if (fieldErrors.length) {
    displayErrors(errorMsgContainer, fieldErrors);
    return;
  }

  errorMsgContainer.innerHTML = '';

  stripe.createToken(cardNumberElement).then(function(result) {
    if (result.error) {
      displayErrors(errorMsgContainer, [result.error.message]);
    } else {
      stripeTokenHandler(result.token);
    }
  });
});

// Function to display errors
function displayErrors(container, errors) {
  var msg = `<div class="autoDismissAlert alert alert-danger alert-dismissible fade show" role="alert">`;
  msg += `Whoops, the following fields are required but not filled in correctly:<br>`;
  msg += errors.map(function(error) {
    return `${error}<br>`;
  }).join('');
  msg +=`</div>`;
  container.innerHTML = msg;
}

// Submit the form with the token ID.
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);
    // Submit the form
    form.submit();
}




