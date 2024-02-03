// Affichage / disparition croix + affichage / disparition main-nav + affichage / disparition search-bar
const image = document.getElementById('logo-search-header');
const searchIcon = document.querySelector('.search-container-header');
const mainNav = document.querySelector('.main-nav-header');

const searchBar = document.querySelector('.search-bar-header');

image.addEventListener('click', function() {
    if (image.getAttribute('src') === '/images/search.png') {
        image.setAttribute('src', '/images/cross.png');
        image.classList.add('img-cross-header');
        document.querySelector(".main-nav-header").style.opacity = "0";
        document.querySelector(".search-bar-header").style.opacity = "1";
        searchBar.classList.remove('hidden');
        searchBar.classList.add('active');

    } else {
        image.setAttribute('src', '/images/search.png');
        image.classList.remove('img-cross-header'); 
        document.querySelector(".main-nav-header").style.opacity = "1";
        document.querySelector(".search-bar-header").style.opacity = "0";
        searchBar.classList.add('hidden');
        searchBar.classList.remove('active');
    }
});

/* Apparition / disparition main-nav en colonne */
const hamburgerIcon = document.querySelector('.logo-menu-hamburger-header');
const closeIcon = document.querySelector('.logo-menu-close-header');
const navMenu = document.querySelector('.main-nav-header');

hamburgerIcon.addEventListener('click', function() {
    hamburgerIcon.classList.add('hidden');
    closeIcon.classList.remove('hidden');
    navMenu.style.display = 'flex';
});

closeIcon.addEventListener('click', function() {
    closeIcon.classList.add('hidden');
    hamburgerIcon.classList.remove('hidden');
    navMenu.style.display = 'none';
});


/*vérification de la largeur de la fenêtre et ajustement de l'affichage du menu hamburger*/
window.addEventListener("resize", function () {
  const windowWidth = window.innerWidth;
  const menuHamburger = document.querySelector(".menu-hamburger-header");
  const searchBar = document.getElementById("search-bar-header");

  if (windowWidth > 1100) {
    menuHamburger.style.display = "none";
    if (searchBar.classList.contains("active")) {
      searchBar.classList.remove("active");
    }
  } else {
    menuHamburger.style.display = "flex";
  }
})


// Sélectionner tous les liens de navigation
const navLinks = document.querySelectorAll('.main-nav-header a');

// Obtenir l'URL actuelle
const currentUrl = window.location.pathname;

function applyActiveClass() {
  const currentUrl = window.location.pathname;

  navLinks.forEach(link => {
    const linkPath = "/" + link.getAttribute('href');

    if (currentUrl === linkPath || (linkPath === '/consoles' && currentUrl.startsWith('/produits-consoles')) || (linkPath === '/ordinateurs' && currentUrl.startsWith('/produits-ordinateurs')) || (linkPath === '/jeux-videos' && currentUrl.startsWith('/produits-jeux-video')) || (linkPath === '/produits-derives' && currentUrl.startsWith('/produits-produits-derives')) || (linkPath === '/le-projet' && currentUrl.startsWith('/livre-blanc')) || (linkPath === '/contact' && currentUrl.startsWith('/faq')) || (linkPath === '/profil' && currentUrl.startsWith('/porte-monnaie')) || (linkPath === '/profil' && currentUrl.startsWith('/changement-mail')) || (linkPath === '/profil' && currentUrl.startsWith('/changement-mot-de-passe')) || (linkPath === '/profil' && currentUrl.startsWith('/favoris')) || (linkPath === '/profil' && currentUrl.startsWith('/supprimer-profil')) || (linkPath === '/profil' && currentUrl.startsWith('/connection-profil')) || (linkPath === '/vendre' && currentUrl.startsWith('/connection-vendre')) || (linkPath === '/profil' && currentUrl.startsWith('/mise-a-jour-produits'))){
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}


// Appeler la fonction au chargement de la page
document.addEventListener('DOMContentLoaded', applyActiveClass);
