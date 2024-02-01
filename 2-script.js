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


/* Pagination affichage consoles */
document.addEventListener("DOMContentLoaded", function () {
  let currentPage = 1;
  let itemsPerPage;

  // Déterminez le nombre d'articles par page en fonction de la largeur de la fenêtre
  const width = window.innerWidth;
  if (width > 1100) {       // Ordinateur
    itemsPerPage = 6;
  } else if (width > 550) { // Tablette
    itemsPerPage = 3;
  } else {                  // Téléphone
    itemsPerPage = 2;
  }

  const cards = document.querySelectorAll(".product-card-consoles"); // Sélectionnez les éléments .product-card
  const totalPages = 5;

  function showPage(page) {
    cards.forEach((card) => {
      card.classList.remove("visible");
      card.style.display = "none"; // Cachez l'élément
      updateProgressBarConsoles(currentPage, totalPages);
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

  document.getElementById("nextPagec").addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
        updateProgressBarConsoles(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  document.getElementById("prevPagec").addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
        updateProgressBarConsoles(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  showPage(currentPage); // Affichez la première page au chargement initial
});


/* Barre de chargement carrousel consoles */
function updateProgressBarConsoles(currentPage, totalPages) { // Renommez cette fonction
    const progressBar = document.querySelector('.progress-bar-consoles');
    const percentage = (currentPage / totalPages) * 98;
    progressBar.style.width = percentage + '%';
    progressBar.innerHTML = currentPage + ' / ' + totalPages;
}
















/* Pagination affichage ordinateurs */
document.addEventListener("DOMContentLoaded", function () {
  let currentPage = 1;
  let itemsPerPage;

  // Déterminez le nombre d'articles par page en fonction de la largeur de la fenêtre
  const width = window.innerWidth;
  if (width > 1100) {       // Ordinateur
    itemsPerPage = 6;
  } else if (width > 550) { // Tablette
    itemsPerPage = 3;
  } else {                  // Téléphone
    itemsPerPage = 2;
  }

  const cards = document.querySelectorAll(".product-card-ordinateurs"); // Sélectionnez les éléments .product-card
  const totalPages = 5;

  function showPage(page) {
    cards.forEach((card) => {
      card.classList.remove("visible");
      card.style.display = "none"; // Cachez l'élément
      updateProgressBarOrdinateurs(currentPage, totalPages);
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

  document.getElementById("nextPageo").addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
        updateProgressBarOrdinateurs(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  document.getElementById("prevPageo").addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
        updateProgressBarOrdinateurs(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  showPage(currentPage); // Affichez la première page au chargement initial
});


/* Barre de chargement carrousel consoles */
function updateProgressBarOrdinateurs(currentPage, totalPages) { // Renommez cette fonction
    const progressBar = document.querySelector('.progress-bar-ordinateurs');
    const percentage = (currentPage / totalPages) * 98;
    progressBar.style.width = percentage + '%';
    progressBar.innerHTML = currentPage + ' / ' + totalPages;
}


//apparition ordinateurs
document.addEventListener("scroll", function () {
  const sections = document.querySelectorAll(".home-ordinateurs");

  sections.forEach(function (section) {
    const rect = section.getBoundingClientRect();
    const screenHeight = window.innerHeight || document.documentElement.clientHeight;

    // Calcul pour déterminer si 2% du conteneur est visible
    const threshold = rect.height * 0.02;

    if (
      rect.bottom >= threshold && // Le bas du conteneur est plus bas que le seuil
      rect.top <= screenHeight - threshold // Le haut du conteneur est plus haut que le seuil (en partant du bas de l'écran)
    ) {
      // Section est suffisamment visible
      section.style.opacity = "1";
      section.style.transform = "scale(100%)";
    }
  });
});













/* Pagination affichage jeux */
document.addEventListener("DOMContentLoaded", function () {
  let currentPage = 1;
  let itemsPerPage;

  // Déterminez le nombre d'articles par page en fonction de la largeur de la fenêtre
  const width = window.innerWidth;
  if (width > 1100) {       // Ordinateur
    itemsPerPage = 6;
  } else if (width > 550) { // Tablette
    itemsPerPage = 3;
  } else {                  // Téléphone
    itemsPerPage = 2;
  }

  const cards = document.querySelectorAll(".product-card-jeux"); // Sélectionnez les éléments .product-card
  const totalPages = 5;

  function showPage(page) {
    cards.forEach((card) => {
      card.classList.remove("visible");
      card.style.display = "none"; // Cachez l'élément
      updateProgressBarJeux(currentPage, totalPages);
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

  document.getElementById("nextPagej").addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
        updateProgressBarJeux(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  document.getElementById("prevPagej").addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
        updateProgressBarJeux(currentPage, totalPages); // Mettez à jour cet appel
      }
  });


  showPage(currentPage); // Affichez la première page au chargement initial
});


/* Barre de chargement carrousel jeux */
function updateProgressBarJeux(currentPage, totalPages) { // Renommez cette fonction
    const progressBar = document.querySelector('.progress-bar-jeux');
    const percentage = (currentPage / totalPages) * 98;
    progressBar.style.width = percentage + '%';
    progressBar.innerHTML = currentPage + ' / ' + totalPages;
}


//apparition jeux
document.addEventListener("scroll", function () {
  const sections = document.querySelectorAll(".home-jeux");

  sections.forEach(function (section) {
    const rect = section.getBoundingClientRect();
    const screenHeight = window.innerHeight || document.documentElement.clientHeight;

    // Calcul pour déterminer si 2% du conteneur est visible
    const threshold = rect.height * 0.02;

    if (
      rect.bottom >= threshold && // Le bas du conteneur est plus bas que le seuil
      rect.top <= screenHeight - threshold // Le haut du conteneur est plus haut que le seuil (en partant du bas de l'écran)
    ) {
      // Section est suffisamment visible
      section.style.opacity = "1";
      section.style.transform = "scale(100%)";
    }
  });
});




/* Pagination affichage produits-derives */
document.addEventListener("DOMContentLoaded", function () {
  let currentPage = 1;
  let itemsPerPage;

  // Déterminez le nombre d'articles par page en fonction de la largeur de la fenêtre
  const width = window.innerWidth;
  if (width > 1100) {       // Ordinateur
    itemsPerPage = 6;
  } else if (width > 550) { // Tablette
    itemsPerPage = 3;
  } else {                  // Téléphone
    itemsPerPage = 2;
  }

  const cards = document.querySelectorAll(".product-card-produits"); // Sélectionnez les éléments .product-card
  const totalPages = 5;

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


/* Barre de chargement carrousel produits */
function updateProgressBarProduits(currentPage, totalPages) { // Renommez cette fonction
    const progressBar = document.querySelector('.progress-bar-produits');
    const percentage = (currentPage / totalPages) * 98;
    progressBar.style.width = percentage + '%';
    progressBar.innerHTML = currentPage + ' / ' + totalPages;
}


//apparition produits derives
document.addEventListener("scroll", function () {
  const sections = document.querySelectorAll(".home-produits");

  sections.forEach(function (section) {
    const rect = section.getBoundingClientRect();
    const screenHeight = window.innerHeight || document.documentElement.clientHeight;

    // Calcul pour déterminer si 2% du conteneur est visible
    const threshold = rect.height * 0.02;

    if (
      rect.bottom >= threshold && // Le bas du conteneur est plus bas que le seuil
      rect.top <= screenHeight - threshold // Le haut du conteneur est plus haut que le seuil (en partant du bas de l'écran)
    ) {
      // Section est suffisamment visible
      section.style.opacity = "1";
      section.style.transform = "scale(100%)";
    }
  });
});








//apparition contact
document.addEventListener("scroll", function () {
  const sections = document.querySelectorAll(".main-banner-contact");

  sections.forEach(function (section) {
    const rect = section.getBoundingClientRect();
    const screenHeight = window.innerHeight || document.documentElement.clientHeight;

    // Calcul pour déterminer si 2% du conteneur est visible
    const threshold = rect.height * 0.02;

    if (
      rect.bottom >= threshold && // Le bas du conteneur est plus bas que le seuil
      rect.top <= screenHeight - threshold // Le haut du conteneur est plus haut que le seuil (en partant du bas de l'écran)
    ) {
      // Section est suffisamment visible
      section.style.opacity = "1";
      section.style.transform = "scale(100%)";
    }
  });
});