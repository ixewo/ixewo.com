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

/* Zoom sur l'image */
document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.photos-produits img');
    const backdrop = document.querySelector('.photos-produits-backdrop');
  
    images.forEach((img) => {
      img.addEventListener('click', function () {
        // Supposons que vous avez une div avec une classe 'zoomed' pour le style du zoom
        this.classList.toggle('zoomed');
        backdrop.style.display = 'block';
      });
    });
 
    // Ajoutez un événement click pour fermer le pop-up lorsqu'on clique en dehors
    backdrop.addEventListener('click', function() {
      images.forEach((img) => {
      img.classList.remove('zoomed');
    });
    this.style.display = 'none';
  });
});



/* Carrousel d'image */
document.addEventListener('DOMContentLoaded', function () {
  const images = document.querySelectorAll('.photos-produits img');
  const progressBar = document.querySelector('.progress-bar-img');
  let currentIndex = 0; // Start with the first image

  function updateCarousel() {
    images.forEach((img, index) => {
      img.classList.remove('active');
      if (index === currentIndex) {
        img.classList.add('active');
      }
    });
    
    // Update the progress bar
    const progressPercentage = (currentIndex + 1) / images.length * 98;
    progressBar.style.width = `${progressPercentage}%`;
  }

  images.forEach((img, index) => {
    img.addEventListener('click', () => {
      currentIndex = index;
      updateCarousel();
    });
  });

  document.getElementById('previmg').addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    updateCarousel();
  });

  document.getElementById('nextimg').addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % images.length;
    updateCarousel();
  });

  // Set the first image as active
  updateCarousel();
});











document.addEventListener('DOMContentLoaded', function () {
  // Recherche du bouton "Contacter" dans le document
  var contacterBtn = document.querySelector('.contacter-button');
  // Recherche des éléments de contact pour les afficher/masquer
  var contactElements = document.querySelectorAll('.contact');

  // Fonction pour basculer la visibilité des informations de contact
  function toggleContactInfo() {
    // Itération sur chaque élément de contact
    contactElements.forEach(function (element) {
    // Vérifiez si l'élément est actuellement non affiché
    if (element.style.display === 'none' || !element.style.display) {
      element.style.display = 'block'; // Change le style pour afficher l'élément
    } else {
      element.style.display = 'none'; // Change le style pour masquer l'élément
    }
    });
  }

  // Attacher la fonction toggleContactInfo au clic du bouton
  if (contacterBtn) {
      contacterBtn.addEventListener('click', toggleContactInfo);
    }
  });// Logique pour contacter le vendeur si l'utilisateur est connecté


// affichage message d'erreur bouton contacter
document.addEventListener('DOMContentLoaded', function () {
    var contacterBtn = document.querySelector('.contacter-button');
    if (contacterBtn) {
        contacterBtn.addEventListener('click', function () {
            // Supposons que la variable isUserLoggedIn soit définie globalement
            if (!isUserLoggedIn) { // Vous devez définir cette variable dans votre PHP
                document.getElementById("errorMessage").innerHTML = "<a href='connection-profil'>Connectez-vous</a> pour acceder aux contacts";
                showErrorMessagePopup(); // Utilisez la fonction existante pour afficher le pop-up
            } else {
                /* Affichage des informations de contact */
                
            }
        });
    }
});



// affichage message d'erreur bouton acheter
document.addEventListener('DOMContentLoaded', function () {
    var acheterBtn = document.querySelector('.acheter-button');
    if (acheterBtn) {
        acheterBtn.addEventListener('click', function (e) {
            // Supposons que la variable isUserLoggedIn soit définie globalement
            if (!isUserLoggedIn) { // Vous devez définir cette variable dans votre PHP
                // Empêcher le comportement par défaut du bouton (redirection)
                e.preventDefault();

                // Afficher le message d'erreur
                document.getElementById("errorMessage").innerHTML = "<a href='connection-profil'>Connectez-vous</a> pour acheter ce produit";
                showErrorMessagePopup(); // Utilisez la fonction existante pour afficher le pop-up
            } else {
                // Laisser l'action par défaut se produire pour les utilisateurs connectés
            }
        });
    }
});

