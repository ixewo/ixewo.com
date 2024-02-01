/*Icone favoris*/
document.addEventListener("DOMContentLoaded", function () {
    // Gestionnaire d'événements pour les icônes de favoris
    const favoriteIcons = document.querySelectorAll(".favorite-icon");
    favoriteIcons.forEach((icon) => {
        icon.addEventListener("click", function () {
            this.classList.toggle("favorited");

            const productId = this.getAttribute("data-product-id");
              
            fetch("27-add-remove-favorite.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "error") {
                    document.getElementById("errorMessage").innerHTML = data.message;
                    showErrorMessagePopup();
                    this.classList.toggle("favorited"); // Remettre l'état précédent si erreur
                }
                // Vous pouvez ajouter ici une logique supplémentaire pour gérer le succès
            });
        });
    });
});


/* Affichage pop-up */
function showErrorMessagePopup() {
    const errorMessageElement = document.getElementById("errorMessage");
    const backdropElement = document.createElement('div');
    backdropElement.id = 'errorMessage-backdrop';

    // Affichez le pop-up et l'arrière-plan semi-transparent
    errorMessageElement.style.display = 'block';
    backdropElement.style.display = 'block';
    document.body.appendChild(backdropElement);

    // Ajoutez un événement click pour fermer le pop-up lorsqu'on clique en dehors
    backdropElement.addEventListener('click', function() {
        errorMessageElement.style.display = 'none';
        this.remove();
    });
}


/* Pagination affichage produits */
document.addEventListener("DOMContentLoaded", function () {
  let currentPage = 1;
  let itemsPerPage;

  // Déterminez le nombre d'articles par page en fonction de la largeur de la fenêtre
  const width = window.innerWidth;
  if (width > 1100) {       // Ordinateur
    itemsPerPage = 35;
  } else if (width > 750) { // Tablette
    itemsPerPage = 18;
  } else {                  // Téléphone
    itemsPerPage = 20;
  }
  const cards = document.querySelectorAll(".product-card-produits"); // Sélectionnez les éléments .product-card
  const totalPages = Math.ceil(cards.length / itemsPerPage);

  function showPage(page) {
    cards.forEach((card) => {
      card.classList.remove("visible");
      card.style.display = "none"; // Cachez l'élément
      updateProgressBarProduits(currentPage, totalPages);
    });

    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    cards.forEach((card, index) => {
      if (index >= start && index < end) {
        card.classList.add("visible");
        card.style.display = "block"; // Montrez l'élément
      }
    });
  }

  document.getElementById("nextPagep").addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
        updateProgressBarProduits(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  document.getElementById("prevPagep").addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
        updateProgressBarProduits(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  showPage(currentPage); // Affichez la première page au chargement initial
});


/* Barre de chargement carrousel Produits */
function updateProgressBarProduits(currentPage, totalPages) { // Renommez cette fonction
    const progressBar = document.querySelector('.progress-bar-produits');
    const percentage = (currentPage / totalPages) * 98;
    progressBar.style.width = percentage + '%';
    progressBar.innerHTML = currentPage + ' / ' + totalPages;
}
