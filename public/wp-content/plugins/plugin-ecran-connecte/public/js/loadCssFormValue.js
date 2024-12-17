// Fonction pour récupérer le contenu brut du fichier CSS
async function fetchCssFile(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Erreur lors du chargement du fichier CSS : ${response.statusText}`);
        }
        const cssText = await response.text();
        console.log("Fichier CSS récupéré avec succès");
        return cssText;
    } catch (error) {
        console.error(error);
        return null;
    }
}

// Fonction pour extraire les variables CSS du contenu brut
function extractCssVariables(cssText) {
    const variables = {};
    const variableRegex = /--([\w-]+)\s*:\s*([^;]+);/g;
    let match;

    while ((match = variableRegex.exec(cssText)) !== null) {
        variables[`--${match[1]}`] = match[2].trim();
    }

    return variables;
}

// Fonction principale
async function populateFormWithCssVariables() {
    const cssFileUrl = "https://testonsleschoses.alwaysdata.net/wp-content/themes/theme-ecran-connecte/assets/css/global/global-" + document.getElementById('cssFileSelector').value +".css";

    // Étape 1 : Récupérer le contenu du fichier CSS
    const cssText = await fetchCssFile(cssFileUrl);

    if (!cssText) {
        console.error("Impossible de récupérer les variables CSS");
        return;
    }

    // Étape 2 : Extraire les variables CSS
    const cssVariables = extractCssVariables(cssText);

    // Étape 3 : Afficher les variables récupérées dans la console
    console.log("Variables CSS récupérées :", cssVariables);

    // Étape 4 : Affecter les valeurs aux champs du formulaire
    document.getElementById('background1').value = cssVariables['--primary-background-color'] || '';
    document.getElementById('background2').value = cssVariables['--secondary-background-color'] || '';
    document.getElementById('layout').value = cssVariables['--primary-layout-background-color'] || '';
    document.getElementById('layoutColor').value = cssVariables['--primary-layout-color'] || '';
    document.getElementById('title').value = cssVariables['--primary-title-color'] || '';
    document.getElementById('link').value = cssVariables['--primary-link-color'] || '';
    document.getElementById('buttonBorder').value = cssVariables['--primary-button-border-color'] || '';
    document.getElementById('button').value = cssVariables['--primary-button-color'] || '';
    document.getElementById('sideBar').value = cssVariables['--primary-sidebar-color'] || '';
}

// Exécuter la fonction au chargement du DOM
document.getElementById('cssFileSelector').addEventListener('change', populateFormWithCssVariables);
window.addEventListener('DOMContentLoaded', populateFormWithCssVariables);
