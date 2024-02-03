/* Pagination affichage consoles */
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
  const cards = document.querySelectorAll(".product-card-consoles"); // Sélectionnez les éléments .product-card
  const totalPages = Math.ceil(cards.length / itemsPerPage);

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
