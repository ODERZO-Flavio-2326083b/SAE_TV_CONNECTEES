# Ecran Connecté - Installation

## Prérequis

Avant de commencer l'installation du site, vous devez avoir :

- WordPress : <https://fr.wordpress.org/download/>  

- Le dossier zippé contenant le dossier "wp-content".  

- Une base de données avec un utilisateur.  

- Le fichier contenant les pages de WordPress.

## WordPress

La première étape consiste à mettre en place WordPress.  

- Décompressez le fichier zip de WordPress.  

- Lancez votre site.  

- Lorsque vous lancerez votre site pour la première fois, WordPress vous demandera d'indiquer :  
    - Le nom de la base de données.   
    - L'hôte de la base de données.  
    - Le login de l'utilisateur de la base de données.  
    - Le mot de passe de l'utilisateur de la base de données.  

Après avoir validé les identifiants pour la base de données, vous serez amené à vous créer un compte administrateur et de donner un titre à votre site.  

## Installation du plugin & du thème

Maintenant que WordPress est en place, on peut ajouter nos plugins et thèmes.  

Pour ce faire, remplacer le dossier "wp-content" par notre dossier zippé "wp-content".  

Une fois le contenu installé, il faut activer les plugins (extensions) et le thème :

Dans "nomdevotresite/wp-admin" :
- Activez l'extension Ecran Connecté AMU.
- Par la suite, dans l'onglet Apparence, puis Thèmes, activez le thème "Ecran connecté".

Les plugins et le thème sont maintenant activés.


## Pages

Pour finir, il ne reste plus qu'à ajouter les pages du site.

Allez dans Outils, Importer puis sélectionner WordPress et lancez l'outil d'importation. Importer le fichier XML pages.xml.


## Enregistrer des groupes

Dans cette partie, nous verrons comment enregistrer des groupes : une année, une classe ou un demi-groupe.  
Pour ce faire, connectez-vous sur votre ENT et allez dans Planning des cours (ADE).  

Sélectionnez le groupe de votre choix.
Cliquez sur le bouton "Export to agenda..." en bas à gauche de la page puis "Générer URL"

Vous aurez alors une URL de ce style "https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=8395&calType=ical&firstDate=2025-01-06&lastDate=2025-01-10"

Récupérer la valeur dans resources=... (Dans notre lien d'exemple, il s'agit de 8395).  

Dans votre site WordPress, allez dans la partie "Code ADE" et remplissez le formulaire pour ajouter le code.  

## Création de départements
La création de département est essentielle au bon fonctionnement du site.
Allez sur la page "Départements" et remplissez le formulaire.

Vous pourrez par la suite compléter les différents formulaires du site selon vos besoins, notamment dans les pages Informations,
Utilisateurs.


