# Ecran connecté code

Ce guide va expliquer les parties les plus importantes du plugin et du thème "écran connecté".

## Plugins

Un plugin se créé avec un fichier PHP contenant :  
    - Un dossier "src" contenant le MVC du projet.  
    - Un dossier "blocks" avec tous les blocks, les blocks permettent de placer notre code dans une page WordPress.  
    - Un dossier "public" contenant tout le contenu multimédia (CSS / JS / Img / Fichier). 
    - Un dossier "vendor" du code qu'on utilise mais qui nous appartient pas (Contient composer, R34ICS (permet de lire les fichiers ICS / l'emploi du temps)). 
    - Un dossier "widgets" contient les widgets générés pour WordPress, devenue obsolète vue notre utilisation actuelle. Peut être utile dans le futur.  

Toutes les fonctionnalités sont générées via le dossier "src".  

### Utilisateurs

Il y a 3 classes pour les utilisateurs :  

User qui est la classe principale puis les classes qui héritent de cette dernière (Television, Secretary, Technician).  

Toutes ces classes sont tous liées à la même entité (model) : User.

Un utilisateur Television affiche l'emploi du temps lié au code ADE et au département.

Un utilisateur Secretary peut poster des alertes, créer des utilisateurs et ajouter des informations.

Un utilisateur Techician peut accéder aux emplois du temps.

### Emploi du temps

Les emplois du temps sont téléchargé en format ICS.  

Les classes utilisées sont : R34ICS et UserController  

Lorsqu'un utilisateur est connecté, il appelle R34ICS pour pouvoir afficher son emploi du temps, R34ICS permet de lire les fichiers ICS.  

### Informations

Une information correspond au type de fichier suivant : Texte, Image, PDF, Vidéo, Short.

Les classes utilisées sont : InformationController, Information & InformationView.  
Les librairies "PhpOffice" et "PDF.js" sont aussi utilisées.  
Fichier javascript : slideshow.js  

Les informations sont affichées dans un diaporama dans la partie gauche de la télévision.

Les vidéos sont affichées par dessus l'emploi du temps dans un autre diaporama, puis disparaissent pendant
un certain temps pour laisser place à l'emploi du temps.
### Alertes
Les alertes sont affichés en dessous de l'emploi du temps dans la télévision.

Les classes utilisées sont : AlertController, Alert & AlertView.  
La librairie "JQuery Ticker" est aussi utilisée.  
Fichier javascript : alertTicker.js.