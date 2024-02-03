document.addEventListener("DOMContentLoaded", function() {
    initImageContainers();
    attachEventHandlers();
});

function initImageContainers() {
    var imageContainers = [
        { imageId: 'image-principale', pathId: 'image_path1', oldTextId: 'ancien-texte1', newTextId: 'nouveau-texte1', deleteButtonId: 'delete-button1', deleteFlagId: 'delete-flag1' },
        { imageId: 'image-secondaires2', pathId: 'image_path2', oldTextId: 'ancien-texte2', newTextId: 'nouveau-texte2', deleteButtonId: 'delete-button2', deleteFlagId: 'delete-flag2' },
        { imageId: 'image-secondaires3', pathId: 'image_path3', oldTextId: 'ancien-texte3', newTextId: 'nouveau-texte3', deleteButtonId: 'delete-button3', deleteFlagId: 'delete-flag3' },
        { imageId: 'image-secondaires4', pathId: 'image_path4', oldTextId: 'ancien-texte4', newTextId: 'nouveau-texte4', deleteButtonId: 'delete-button4', deleteFlagId: 'delete-flag4' },
        { imageId: 'image-secondaires5', pathId: 'image_path5', oldTextId: 'ancien-texte5', newTextId: 'nouveau-texte5', deleteButtonId: 'delete-button5', deleteFlagId: 'delete-flag5' },
    ];

    imageContainers.forEach(function(container) {
        initImageContainer(container.imageId, container.pathId, container.oldTextId, container.newTextId, container.deleteButtonId, container.deleteFlagId);
    });
}

function initImageContainer(imageId, fileInputId, oldTextId, newTextId, deleteButtonId, deleteFlagId) {
    var image = document.getElementById(imageId);
    var fileInput = document.getElementById(fileInputId);
    var oldText = document.getElementById(oldTextId);
    var newText = document.getElementById(newTextId);
    var deleteButton = document.getElementById(deleteButtonId);

    var hasImage = image.src && image.complete && image.naturalHeight !== 0;
    image.style.display = hasImage ? 'block' : 'none';
    oldText.style.display = hasImage ? 'block' : 'none';
    newText.style.display = hasImage ? 'none' : 'none';
    deleteButton.style.display = hasImage ? 'block' : 'none';
    fileInput.style.display = hasImage ? 'none' : 'block';
    document.getElementById(deleteFlagId).value = hasImage ? 'false' : 'true';
}


function attachEventHandlers() {
    var imagePaths = [
        { pathId: 'image_path1', imageId: 'image-principale', oldTextId: 'ancien-texte1', newTextId: 'nouveau-texte1', deleteButtonId: 'delete-button1', deleteFlagId: 'delete-flag1' },
        { pathId: 'image_path2', imageId: 'image-secondaires2', oldTextId: 'ancien-texte2', newTextId: 'nouveau-texte2', deleteButtonId: 'delete-button2', deleteFlagId: 'delete-flag2' },
        { pathId: 'image_path3', imageId: 'image-secondaires3', oldTextId: 'ancien-texte3', newTextId: 'nouveau-texte3', deleteButtonId: 'delete-button3', deleteFlagId: 'delete-flag3' },
        { pathId: 'image_path4', imageId: 'image-secondaires4', oldTextId: 'ancien-texte4', newTextId: 'nouveau-texte4', deleteButtonId: 'delete-button4', deleteFlagId: 'delete-flag4' },
        { pathId: 'image_path5', imageId: 'image-secondaires5', oldTextId: 'ancien-texte5', newTextId: 'nouveau-texte5', deleteButtonId: 'delete-button5', deleteFlagId: 'delete-flag5' },
        
    ];

    imagePaths.forEach(function(path) {
        // Gestionnaire pour le chargement des images
        document.getElementById(path.pathId).addEventListener('change', function(event) {
            createFileUploadHandler(path.imageId, 'image-defaut', path.newTextId, path.oldTextId, path.deleteButtonId, path.deleteFlagId)(event);
        });

        // Gestionnaire pour la suppression des images
        var deleteButton = document.getElementById(path.deleteButtonId);
        if (deleteButton) {
            deleteButton.addEventListener('click', function(event) {
                removeImage(event, path.imageId, 'image-defaut', path.newTextId, path.oldTextId, path.deleteButtonId, path.deleteFlagId);
            });
        }
    });

}



function createFileUploadHandler(imageId, defaultImageId, newTextId, oldTextId, deleteButtonId, deleteFlagId) {
    return function(event) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Afficher la nouvelle image
            var image = document.getElementById(imageId);
            image.src = e.target.result;
            image.style.display = 'block';

            // Masquer le texte "image actuelle" et afficher le texte "nouvelle image"
            var oldText = document.getElementById(oldTextId);
            var newText = document.getElementById(newTextId);
            oldText.style.display = 'none';
            newText.style.display = 'block';

            // Masquer le champ de saisie du fichier
            var fileInput = document.getElementById(deleteFlagId.replace('delete-flag', 'image_path'));
            fileInput.style.display = 'none';

            // Masquer l'image par défaut si elle existe
            var defaultImage = document.getElementById(defaultImageId);
            defaultImage.style.display = 'none';

            // Afficher le bouton de suppression
            var deleteButton = document.getElementById(deleteButtonId);
            // Création dynamique du bouton de suppression si nécessaire
            var deleteButton = document.getElementById(deleteButtonId);
            if (!deleteButton) {
                deleteButton = document.createElement('button');
                deleteButton.id = deleteButtonId;
                deleteButton.className = 'delete-button';
                deleteButton.innerHTML = '<img src="/images/delete.png" alt="Supprimer" />';
                deleteButton.onclick = function(event) {
                    removeImage(event, imageId, defaultImageId, newTextId, oldTextId, deleteButtonId, deleteFlagId);
                };
                // Insérer le bouton dans le DOM
                image.parentNode.insertBefore(deleteButton, image.nextSibling);
            }
            deleteButton.style.display = 'block';

            // Réinitialiser le drapeau de suppression
            document.getElementById(deleteFlagId).value = 'false';
        };
        reader.readAsDataURL(event.target.files[0]);
    };
}



function removeImage(event, imageId, defaultImageId, newTextId, oldTextId, deleteButtonId, deleteFlagId, fileInputId) {
    event.preventDefault();

    // Cachez tous les éléments sauf le champ de saisie du fichier
    var elementsToHide = [imageId, defaultImageId, newTextId, oldTextId, deleteButtonId].map(function(id) {
        return document.getElementById(id);
    });

    elementsToHide.forEach(function(element) {
        if (element) {
            element.style.display = 'none';
        }
    });

    // Réinitialisez et affichez le champ de saisie du fichier
    var fileInput = document.getElementById(fileInputId);
    if (!fileInput) {
        // Créez un nouveau champ de saisie de fichier s'il n'existe pas
        fileInput.value = '';
        fileInput.style.display = 'block';
      
        // Insérez le champ de saisie de fichier dans le DOM
        var container = document.getElementById(imageId).parentNode;
        container.appendChild(fileInput);
    }
    // Si le champ de saisie du fichier existe, réinitialisez-le simplement
    fileInput.value = '';
    fileInput.style.display = 'block';
    
    // Indiquez que l'image a été supprimée
    document.getElementById(deleteFlagId).value = 'true';
}