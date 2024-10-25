function getCssVariable(variableName) {
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
}

function populateFormWithCssVariables() {
    // Récupération des valeurs de chaque variable CSS
    const primaryBackgroundColor = getCssVariable('--primary-background-color');
    const secondaryBackgroundColor = getCssVariable('--secondary-background-color');
    const primaryLayoutColor = getCssVariable('--primary-layout-background-color');
    const primaryLayoutBackgroundColor = getCssVariable('--primary-layout-color');
    const primaryTitleColor = getCssVariable('--primary-title-color');
    const primaryLinkColor = getCssVariable('--primary-link-color');
    const primaryButtonBorderColor = getCssVariable('--primary-button-border-color');
    const primaryButtonColor = getCssVariable('--primary-button-color');
    const primarySidebarColor = getCssVariable('--primary-sidebar-color');


    // Affectation des valeurs aux champs du formulaire
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

// Exécution de la fonction après le chargement complet de la page
window.addEventListener('DOMContentLoaded', populateFormWithCssVariables);
