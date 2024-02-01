// Init the widget on ready state
$(document).ready(function() {
  // Loading the Parcelshop picker widget into the <div> with id "Zone_Widget" with such settings:
  $("#Zone_Widget").MR_ParcelShopPicker({
    //
    // Settings relating to the HTML.
    //
    // JQuery selector of the HTML element receiving the Parcelshop Number (ex: here, input type text, but should be input hidden)
    Target: "#ParcelCode",
    //
    // Settings for Parcelshop data access
    //
    // Code given by Mondial Relay, 8 characters (padding right with spaces)
    // BDTEST is used for development only => a warning appears - "CC22Y0PW ",
    Brand: "CC22Y0PW",
    //
    // Default Country (2 letters) used for search at loading
    Country: "FR",
    // Display settings
    //
    // Enable Responsive (nb: non responsive corresponds to the Widget used in older versions=
    Responsive: true,
    //
    OnParcelShopSelected:
    // Fonction de traitement à la sélection du point relais.
    // Remplace les données de cette page par le contenu de la variable data.
    // data: les informations du Point Relais
    function(data) {
      // Mettre à jour le contenu des éléments <span>
      $("#cb_ID").html(data.ID);
      $("#cb_Nom").html(data.Nom);
      $("#cb_Adresse").html(data.Adresse1 + ' ' + data.Adresse2);
      $("#cb_CP").html(data.CP);
      $("#cb_Ville").html(data.Ville);
      $("#cb_Pays").html(data.Pays);

      // Mettre à jour les champs cachés
      $('input[name="cb_Nom"]').val(data.Nom);
      $('input[name="cb_Adresse"]').val(data.Adresse1 + ' ' + data.Adresse2);
      $('input[name="cb_CP"]').val(data.CP);
      $('input[name="cb_Ville"]').val(data.Ville);
      $('input[name="cb_Pays"]').val(data.Pays);
    },
    
  });

});

// Animation descente page 
// Création d'un observer pour détecter les changements dans le span
var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.target.id === 'cb_Nom') {
            document.querySelector('.expediteur').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});

// Configuration de l'observer
var config = { childList: true, characterData: true, subtree: true };

// Démarrage de l'observation
observer.observe(document.getElementById('cb_Nom'), config);


document.querySelector('.checkbox-exp').addEventListener('change', function() {
    document.querySelector('.destinataire').scrollIntoView({ behavior: 'smooth', block: 'center' });
});

document.querySelector('.checkbox-des').addEventListener('change', function() {
    document.querySelector('.Dprix').scrollIntoView({ behavior: 'smooth', block: 'center' });
});





$(document).ready(function() {

    $('#stripePaymentButton').on('click', function(e) {
        // Assurer que les conditions sont remplies (remplacer par vos propres validations si nécessaire)
        var cbNomRempli = $('#ParcelCode').val() !== '';
        var checkboxExpRempli = $('#exp').is(':checked');
        var checkboxDesRempli = $('#des').is(':checked');

        if (!cbNomRempli || !checkboxExpRempli || !checkboxDesRempli) {
            e.preventDefault(); // Empêcher la soumission si une condition n'est pas remplie
            // Messages d'erreur spécifiques à chaque cas
            if (!cbNomRempli) alert("Veuillez sélectionner un point relais.");
            if (!checkboxExpRempli) alert("Veuillez cocher la case 'Expéditeur'.");
            if (!checkboxDesRempli) alert("Veuillez cocher la case 'Destinataire'.");
        }
        // Si tout est bon, le formulaire sera soumis et traité par votre script PHP
    });
});
