
    $(window).scroll(function () {   
        
      if($(window).scrollTop() > 200) {
      $('.paiementsidebar').css('position','fixed');
      $('.paiementsidebar').css('top','0'); 
      }
  
      else if ($(window).scrollTop() <= 200) {
      $('.paiementsidebar').css('position','');
      $('.paiementsidebar').css('top','');
      }  
      if ($('.paiementsidebar').offset().top + $(".paiementsidebar").height() > $("#footer").offset().top) {
          $('.paiementsidebar').css('top',-($(".paiementsidebar").offset().top + $(".paiementsidebar").height() - $("#footer").offset().top));
      }
  });
  
  
var stripe = Stripe("pk_live_51HoXvtBkzDMwRKFJbV0wLTuEC9duqtNCbUKRk17E5ev8CtVgFJ6oUTNCAq5fwXkBAfIjbERISUhA2rq5i9PXaAKC008CtoiUJr");

// The items the customer wants to buy

var purchase = {

  items: [{ id: "xl-tshirt" }]

};

// Disable the button until we have Stripe set up on the page
document.querySelector("button#submit").disabled = true;

fetch("checkout", {

  method: "POST",

  headers: {

    "Content-Type": "application/json"

  },

  body: JSON.stringify(purchase)

})

  .then(function(result) {

    return result.json();

  })

  .then(function(data) {

    var elements = stripe.elements();

    var style = {

      base: {

        color: "#32325d",

        fontFamily: 'Arial, sans-serif',

        fontSmoothing: "antialiased",

        fontSize: "16px",

        "::placeholder": {

          color: "#32325d"

        }

      },

      invalid: {

        fontFamily: 'Arial, sans-serif',

        color: "#fa755a",

        iconColor: "#fa755a"

      }

    };

    var card = elements.create("card", { style: style });

    // Stripe injects an iframe into the DOM

    card.mount("#card-element");

    card.on("change", function (event) {

      // Disable the Pay button if there are no card details in the Element

      document.querySelector("button#submit").disabled = event.empty;

      document.querySelector("#card-error").textContent = event.error ? event.error.message : "";

    });

    var form = document.getElementById("payment-form");

    form.addEventListener("submit", function(event) {

      event.preventDefault();

      // Complete payment when the submit button is clicked

      payWithCard(stripe, card, data.clientSecret);

    });

  });

// Calls stripe.confirmCardPayment

// If the card requires authentication Stripe shows a pop-up modal to

// prompt the user to enter authentication details without leaving your page.

var payWithCard = function(stripe, card, clientSecret) {

  loading(true);

  stripe

    .confirmCardPayment(clientSecret, {

      payment_method: {

        card: card

      }

    })

    .then(function(result) {

      if (result.error) {

        // Show error to your customer

        showError(result.error.message);

      } else {

        // The payment succeeded!
        console.log('paiement ok');
        orderComplete(result.paymentIntent.id);

      }

    });

};

/* ------- UI helpers ------- */

// Shows a success message when the payment is complete

var orderComplete = function(paymentIntentId) {

  loading(false);

  // envoie de l'adresse et lancement du controller de succes
  let deliveryAddress = $('#inputAddress').val();
  let deliveryCity = $('#inputCity').val();
  let deliveryPostalCode = $('#inputCP').val();

  $.post( "checkout/succes",{
    address : function () {return deliveryAddress;},
    city : function () {return deliveryCity;},
    postalCode : function () {return deliveryPostalCode;}
  });

  // Suppression du corps de la page
   $("#paiementbody").hide();

// affichage du message de résultat
  document.querySelector(".result-message").classList.remove("hidden");

  // redirection vers la page des produits
  setTimeout(function(){
    window.location.href = document.location.origin;
 }, 3000);

};

// Show the customer the error from Stripe if their card fails to charge

var showError = function(errorMsgText) {

  loading(false);

  var errorMsg = document.querySelector("#card-error");
  $("#card-error").removeClass("hidden");
  errorMsg.textContent = errorMsgText;

  setTimeout(function() {

    errorMsg.textContent = "";

  }, 4000);

};

// Show a spinner on payment submission

var loading = function(isLoading) {

  if (isLoading) {

    // Disable the button and show a spinner

    document.querySelector("button#submit").disabled = true;

    document.querySelector("#spinner").classList.remove("hidden");

    document.querySelector("#button-text").classList.add("hidden");

  } else {

    document.querySelector("button#submit").disabled = false;

    document.querySelector("#spinner").classList.add("hidden");

    document.querySelector("#button-text").classList.remove("hidden");

  }

};
