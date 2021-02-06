$(document).ready(function () {

    $("#ordertablesearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });

    // retour en haut
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });

    $('.acartbutton').click(function (e) {
        if ($.trim($(this).text()) == "") {
            e.preventDefault();
            console.log($(this).text());
        }
    });

    //Click event to scroll to top
    $('.scrollToTop').click(function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

    // On met la quantité par defaut
    $('.addtocart').attr("data-quantity", 1);
    $('#quantityofproducttocart').attr("value", 1);
    
    // Nombre de produits a ajouter dans le panier
    $('.plus, .minus').click(function () {
        $('.addtocart').attr("data-quantity", $('#quantityofproducttocart').val());
    });
    $('#quantityofproducttocart').change(function () {
        $('.addtocart').attr("data-quantity", $('#quantityofproducttocart').val());
    });

    // quand le fichier image change
    $("input[type=file]").change(function () {

        // changement du nom d'aperçu
        let fieldVal = $(this).val();
        fieldVal = fieldVal.replace(/^.*\\/, "");
        if (fieldVal != undefined || fieldVal != "") {
            $(this).next(".custom-file-label").text(fieldVal);
        }

        // Preview de l'image
        let file = $(this).get(0).files[0];
        if(file){
            var reader = new FileReader();
            let newPath = $(this).parent().parent();
            
            reader.onload = function(){
                newPath.children('a').remove();
                newPath.append("<a><img src="+reader.result+"></a>");
            }
            reader.readAsDataURL(file);
        }
    });

    $('.add-another-collection-widget').click(function (e) {
        var list = $($(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') | list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);

        // on enleve le fakepath
        $("input[type=file]").change(function () {
            let fieldVal = $(this).val();
            fieldVal = fieldVal.replace(/^.*\\/, "");
            if (fieldVal != undefined || fieldVal != "") {
                $(this).next(".custom-file-label").text(fieldVal);
            }

            // Preview de l'image
            let file = $(this).get(0).files[0];
            if(file){
                var reader = new FileReader();
                let newPath = $(this).parent().parent();
                
                reader.onload = function(){
                    newPath.children('a').remove();
                    newPath.append("<a><img src="+reader.result+"></a>");
                }
        
                reader.readAsDataURL(file);
            }
            });
    });


// on desactive le fait que cliquer dans le panier cache le panier
$('.dropdown-menu div.dropdown-item').click(function(e) {
    e.stopPropagation();
});


});

 // Ajout d'un produit dans le panier en ajax et Rechargement des éléments concerné avoir un affichage a jour
$(".addtocart").on("click", function(){
    $productid = $(this).attr('data-id');
    $productquantity = $(this).attr('data-quantity');
    $.post('/products/addtocart/'+$productid, { quantity: $productquantity } ,function(data) {
        $( "#cart-preview" ).load(window.location.href + " #cart-preview" );
        $( "#cartpayementarticles" ).load(window.location.href + " #cartpayementarticles > *" );
    });
 }); 

 // Suppression d'un produit dans le panier en ajax et Rechargement des éléments concerné avoir un affichage a jour
 function removefromcart($productid){
        $.post('/products/removefromcart/'+$productid, function() {
        $( "#cart-preview" ).load(window.location.href + " #cart-preview" );
            $("#cartpayementarticles").load(window.location.href + " #cartpayementarticles > *");
            if (window.location.pathname == '/cart/') {
                location.reload();
        }
    });
 }