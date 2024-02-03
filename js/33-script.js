// Fontion d'affichage dispariton des réposnes
document.addEventListener('DOMContentLoaded', (event) => {
    // Sélectionner tous les boutons 'afficher'
    const afficherButtons = document.querySelectorAll('.afficher-button');

    afficherButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Trouver le conteneur .container-FAQ2 correspondant et l'afficher
            let faqContainer = this.previousElementSibling; // Changé à previousElementSibling
            faqContainer.style.display = "block"; // affiche le contenu FAQ
            this.style.display = "none"; // cache le bouton 'afficher'
            this.nextElementSibling.style.display = "inline-block"; // affiche le bouton 'cacher'
        });
    });

    // Sélectionner tous les boutons 'cacher'
    const cacherButtons = document.querySelectorAll('.cacher-button');

    cacherButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Trouver le conteneur .container-FAQ2 correspondant et le masquer
            let faqContainer = this.previousElementSibling.previousElementSibling; // Deux fois previousElementSibling pour remonter au conteneur FAQ
            faqContainer.style.display = "none"; // cache le contenu FAQ
            this.style.display = "none"; // cache le bouton 'cacher'
            this.previousElementSibling.style.display = "inline-block"; // affiche le bouton 'afficher'
        });
    });
});
