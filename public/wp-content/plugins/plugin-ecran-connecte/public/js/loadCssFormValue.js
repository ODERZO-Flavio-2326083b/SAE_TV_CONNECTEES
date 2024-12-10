// Fonction pour récupérer une variable CSS spécifique
function getCssVariable(variableName) {
    // Utilise getComputedStyle pour obtenir les styles calculés de l'élément <html> (document.documentElement)
    // getPropertyValue retourne la valeur de la variable CSS passée en paramètre
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
}

// Fonction pour peupler un formulaire avec les valeurs des variables CSS
function populateFormWithCssVariables() {
    // 1. Récupération des valeurs de chaque variable CSS
    const primaryBackgroundColor = getCssVariable('--primary-background-color');
    const secondaryBackgroundColor = getCssVariable('--secondary-background-color');
    const primaryLayoutColor = getCssVariable('--primary-layout-background-color');
    const primaryLayoutBackgroundColor = getCssVariable('--primary-layout-color');
    const primaryTitleColor = getCssVariable('--primary-title-color');
    const primaryLinkColor = getCssVariable('--primary-link-color');
    const primaryButtonBorderColor = getCssVariable('--primary-button-border-color');
    const primaryButtonColor = getCssVariable('--primary-button-color');
    const primarySidebarColor = getCssVariable('--primary-sidebar-color');

    // Affiche dans la console une des valeurs (utile pour déboguer)
    console.log(primaryTitleColor);

    // 2. Affectation des valeurs récupérées aux champs du formulaire
    // Chaque champ du formulaire (identifié par son id) est rempli avec la valeur CSS correspondante
    document.getElementById('background1').value = primaryBackgroundColor;
    document.getElementById('background2').value = secondaryBackgroundColor;
    document.getElementById('layout').value = primaryLayoutBackgroundColor;
    document.getElementById('layoutColor').value = primaryLayoutColor;
    document.getElementById('title').value = primaryTitleColor;
    document.getElementById('link').value = primaryLinkColor;
    document.getElementById('buttonBorder').value = primaryButtonBorderColor;
    document.getElementById('button').value = primaryButtonColor;
    document.getElementById('sideBar').value = primarySidebarColor;
}

// 3. Exécution de la fonction après le chargement complet de la page
// Utilisation de l'événement DOMContentLoaded pour s'assurer que le DOM est prêt avant de manipuler les éléments
window.addEventListener('DOMContentLoaded', populateFormWithCssVariables);
