// Fonction pour gérer la visibilité des éléments
function updateVisibility(elementId, displayStyle) {
    var element = document.getElementById(elementId);
    if (element) {
        element.style.display = displayStyle;
    }
}

// Fonction pour gérer la prévisualisation de l'image et la visibilité du file input
function handleImagePreview(fileInputId, imageId, deleteButtonId, newTextId) {
    var fileInput = document.getElementById(fileInputId);
    var reader = new FileReader();

    reader.onload = function(e) {
        var imagePreview = document.getElementById(imageId);
        imagePreview.src = e.target.result;

        // Afficher l'aperçu de l'image et masquer le file input
        updateVisibility(imageId, 'block');
        updateVisibility(deleteButtonId, 'block');
        updateVisibility(newTextId, 'block');
        updateVisibility(fileInputId, 'none');
    };

    reader.readAsDataURL(fileInput.files[0]);
}

// Fonction pour supprimer l'aperçu de l'image et afficher le file input
function removeImage(event, imageId, newTextId, fileInputId, deleteButtonId) {
    event.preventDefault();

    // Masquer l'aperçu de l'image et afficher le file input
    updateVisibility(imageId, 'none');
    updateVisibility(newTextId, 'none');
    updateVisibility(deleteButtonId, 'none');
    updateVisibility(fileInputId, 'block');

    // Réinitialiser le file input
    var fileInput = document.getElementById(fileInputId);
    if (fileInput) {
        fileInput.value = '';
    }
}

// Ajout des gestionnaires d'événements pour chaque file input
var fileInputs = [
    {fileInputId: 'fileToUpload', imageId: 'image-principale', deleteButtonId: 'delete-button1', newTextId: 'nouveau-texte1'},
    {fileInputId: 'fileToUpload2', imageId: 'image-secondaires2', deleteButtonId: 'delete-button2', newTextId: 'nouveau-texte2'},
    {fileInputId: 'fileToUpload3', imageId: 'image-secondaires3', deleteButtonId: 'delete-button3', newTextId: 'nouveau-texte3'},
    {fileInputId: 'fileToUpload4', imageId: 'image-secondaires4', deleteButtonId: 'delete-button4', newTextId: 'nouveau-texte4'},
    {fileInputId: 'fileToUpload5', imageId: 'image-secondaires5', deleteButtonId: 'delete-button5', newTextId: 'nouveau-texte5'},
];

fileInputs.forEach(function(fileInput) {
    document.getElementById(fileInput.fileInputId).addEventListener('change', function() {
        handleImagePreview(fileInput.fileInputId, fileInput.imageId, fileInput.deleteButtonId, fileInput.newTextId);
    });
});
