
// This file basically consists of some light javascript which consists of
// 
// 
// i.) Validating the Form
// ii.) Getting the transaction ID
// iii.) Passing on that transaction ID as a hidden field to create a successful object ready to be posted back on the server
//
// After reading this file, please head over to ../app/submitform.php to learn more

// Execute JS when the document is ready
$(document).ready(function() {
// Example post object ****You do not need this, thiis basically shows how the json Object can look which has to be posted onto ABC Financial's servers****
   var jsonData = {
"paymentPlanId": "",
"planValidationHash": "",
"salesPersonId": "",
"campaignId": "",
"macAddress": "",
"activePresale": "",
"sendAgreementEmail": "",
"agreementContactInfo": {
  "firstName": "",
"middleInitial": "",
"lastName": "",
"email": "",
"gender": "",
"homePhone": "",
"cellPhone": "",
"workPhone": "",
"workExt": "",
"birthday": "",
"driversLicense": "",
"employer": "",
"wellnessProgramId": "",
"barcode": "",
"agreementAddressInfo": {
"addressLine1": "",
"addressLine2": "",
"city": "",
"state": "",
"country": "",
"zipCode": ""
},
"emergencyContact": {
"ecFirstName": "",
"ecLastName": "",
"ecPhone": "",
"ecPhoneExtension": ""
}
},
"thirdPartyPaymentMethod": "",
"todayBillingInfo": {
"isTodayBillingSameAsDraft": "",
"todayCcFirstName": "",
"todayCcLastName": "",
"todayCcType": "visa",
"todayCcAccountNumber": "",
"todayCcExpMonth": "",
"todayCcExpYear": "",
"todayCcCvvCode": "",
"todayCcBillingZip": ""
},
"draftBillingInfo": {
"draftCreditCard": {
"creditCardFirstName": "",
"creditCardLastName": "",
"creditCardType": "",
"creditCardAccountNumber": "",
"creditCardExpMonth": "",
"creditCardExpYear": ""
},
"draftBankAccount": {
"draftAccountFirstName": "",
"draftAccountLastName": "",
"draftAccountRoutingNumber": "",
"draftAccountNumber": "",
"draftAccountType": ""
}
},
"payPageBillingInfo": {
"payPageDraftCreditCard": {
"draftCreditCardTransactionId": ""
},
"payPageDraftBankAccount": {
"draftAccountTransactionId": ""
},
"payPageDueTodayCreditCard": {
"todayCreditCardTransactionId": ""
}
},
"schedules": [
"string"
]
}
// Finish example post object.


// Validating the form and dividing it into a two step form


// Storing necessary variables
var payPage = $('#payPage');
var prevButton = $('#prev');
var myForm = $('#contact');
var required = $('.required');
var eMailInput = $('.eMail');
var eMailVerified = $('.eMailMatch');
var dobInput = $('.dob-input');
var smallDOB = $('.small-dob');

// Let's first stop the browser to submit the form on pressing enter.
 $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

// The 'required' class basically represents all the fields which are required
// This is a function which can be used later on, which adds a class warning if the value is empty on an event of blur.
required.blur(function()
{
      if( !this.value ) {
        // If it's empty, add the class warning
            $(this).addClass('warning');
      } else {
        // Else, remove the class warning.
        $(this).removeClass('warning');
      }
});


// This function checks if the user has entered a valid email address
function checkEmail(str)
{
  // creating regex
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!re.test(str))
      // if it does not follow the regex generated, add the class warning
    eMailInput.addClass('warning');
    if (eMailInput.hasClass('warning')) {
      // If the current field has the class warning, show the small description message of what's wrong
          $('.smallEmail').show();
    } else {
      // Else, hide the small description message.
      $('.smallEmail').hide();
    }
}

// Calling the function on an event of blur.
eMailInput.blur(function() {
  checkEmail(this.value);
});


// This checks if the both the email addresses match.
eMailVerified.blur(function() {
  if(eMailVerified.val() != eMailInput.val()) {
    // If they do not match, add the class warning
    eMailVerified.addClass('warning');
  }

    if ( eMailVerified.hasClass('warning')) {
      // If this has the class warning, add a small description message of what's wrong
      $('.small-verify-email').show();
    } else {
      // else hide the small description message.
      $('.small-verify-email').hide();;
    }

})

// This function validates the date and its format
function isValidDate(date) {
  // creating the optimistic format.
    var matches = /^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/.exec(date);
    // if matches is null, return false
    if (matches == null) return false;
    // dividing matches in three different parts. 
    var d = matches[2];
    var m = matches[1] - 1;
    var y = matches[3];
    // creating the new date in the right format
    var composedDate = new Date(y, m, d);
    // returning the day, month and year
    return composedDate.getDate() == d &&
            composedDate.getMonth() == m &&
            composedDate.getFullYear() == y;
} 

// On blur, call the isValidDate function
dobInput.blur(function() {
  // If it doesn't validate add the class warning
  if (isValidDate($(this).val()) === false) {
    $(this).addClass('warning'); 
  }

  if ($(this).hasClass('warning')) {
// If it has the class warning, display the error message
    smallDOB.show();
  } else {
    // Else, hide it.
    smallDOB.hide();
  }

});



// #next is the button which takes the user to the next page.
$("#next").click(function() {
// If any of the required fields have classes warning or if any of the required fields are empty
      if (required.hasClass('warning') || !$('.required').value ) {
           $('.required').each(function(e,i){
            // Loop around all those fields, and add a class warning to them.
                          if(this.value == "") $(this).addClass('warning');
                        });
           // After the loop ends,
                if (required.hasClass('warning')) {
                  // if any of the fields have a class warning then scroll to that specific element.
                      $([document.documentElement, document.body]).animate({
                          scrollTop: $(".warning").offset().top
                      }, 500);
                } else {
                  // Else, trigger the fake loader.
        $('.effect-hidden').addClass('effect-visible').removeClass('effect-hidden');
        $('.loader-parent').addClass('loader-translate').removeClass('effect-hidden');

        // Setting a timeout function so that the fake loader disappears after 1000ms
        // and the next part of the form is visible.
          setTimeout(
            function() {
              // show the hidden content, that is, the iframe and the go back button
                $('.hidden').addClass('show-me');
                // Hide the personal details form
                myForm.hide();
                // Hide the loader
                $('.effect-visibile').addClass('effect-hidden').removeClass('effect-visible');
                $('.overlay').addClass('effect-hidden').removeClass('effect-visible');
                $('.loader-parent').removeClass('loader-translate').addClass('effect-hidden').removeClass('effect-visible');
            }, 1000);
                }
      } 

  });

// Prev is the 'goback button', the onclick event would trigger the same fake loader and display the form after 1000ms
$('#prev').click(function() {

 $('.effect-hidden').addClass('effect-visible').removeClass('effect-hidden');
$('.loader-parent').addClass('loader-translate').removeClass('effect-hidden');


        setTimeout(
            function() {
              myForm.show();
              $('.show-me').addClass('hidden').removeClass('show-me');
                $('.effect-visibile').addClass('effect-hidden').removeClass('effect-visible');
                $('.overlay').addClass('effect-hidden').removeClass('effect-visible');
                $('.loader-parent').removeClass('loader-translate').addClass('effect-hidden').removeClass('effect-visible');
            }, 1000);


});


// **************** SUBMITTING THE FORM TO THE SERVER (please note: this is just a simple function which submits the form to the server) *********************
// I recommend reading the bottom part to see the retrievement of the transaction ID.

// Submitting the form using simple ajax.
  myForm.submit(function(e) {
    // getting the form and storing it in a variable
    var form = $(this);
    // getting the action's url
    var url = form.attr('action');
    // making a post request
    $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(data)
           {
              (data); // show response from the php script.
           }
         });
});



// **************** RETRIEVING THE TRANSACTION ID *********************


// These are the functions that can be used to receive the postMessage in order to retrieve the
//transaction id.
// It tracks the events and would log in a successful transaction ID when the payment is sucessfully entered.
function bindEvent(element, eventName, eventHandler) {
if (element.addEventListener){
  element.addEventListener(eventName, eventHandler, false);
  } else if (element.attachEvent) {
  element.attachEvent('on' + eventName, eventHandler);
  }
}

window.addEventListener("message", receiveMessage, false);
function receiveMessage(event) {
// logging the data in the console.
console.log("Got the message: " + event.data);
// saving the data in a variable to fetch later on.
var myData = event.data;
// checking to see if the data consists the word 'transactionId' :::Please Note::: the function abive is binded with each event, and is checking consistently if the payment was sucessfully submitted, therefore it's important to check on each event if transactionID was generated.
  if( myData.indexOf('transactionId') >= 0){
    // If yes, then log the data.
    console.log(myData);
    // convert the data in JSON.
    var myDataJson = JSON.parse(myData);
    // get the transactionId field within the personal form written in HTML within the file 'plan-detail.php'.
    var transactionIdField = $('#transactionID');
    // call the JSON property needed to get the transaction id.
    var transactionID = myDataJson['transactionId'];
    // log the newly generated transactionID.
    console.log(transactionID);
    // insert the newly generated transactionID's value in our transactionID field.
    transactionIdField.val(transactionID);
    // Finally, submit the form.
    $('#contact-submit').click();
    // Add the fake loader until the page redirects
        $('.effect-hidden').addClass('effect-visible').removeClass('effect-hidden');
        $('.loader-parent').addClass('loader-translate').removeClass('effect-hidden');
        $('.overlay').css('background-color', 'rgba(255,255,255,1)');
  }

}


// document.ready closing brackets.

});
