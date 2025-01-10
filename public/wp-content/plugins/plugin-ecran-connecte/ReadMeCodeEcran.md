# Ecran connecté code

Ce guide va expliquer les parties les plus importantes du plugin et du thème "écran connecté".

## Plugins

Un plugin se créé avec un fichier PHP contenant :  

- Un dossier "src" contenant le MVC du projet.  
- Un dossier "blocks" avec tous les blocks, les blocks permettent de placer notre code dans une page WordPress.  
- Un dossier "public" contenant tout le contenu multimédia (CSS / JS / Img / Fichier). 
- Un dossier "vendor" du code qu'on utilise, mais qui ne nous appartient pas (Contient composer, R34ICS (permet de lire les fichiers ICS / l'emploi du temps)). 
- Un dossier "widgets" contient les widgets générés pour WordPress, devenus obsolètes au vu de son utilisation actuelle.

Toutes les fonctionnalités sont générées via le dossier "src".  

### Utilisateurs

Il y a 5 classes utilisées pour les utilisateurs :  

User qui est la classe principale puis les classes qui héritent de cette dernière 
(Television, Secretary, Technician et Subadmin).  

Toutes ces classes sont toutes liées à la même entité (model) : User.

Un utilisateur Television affiche l'emploi du temps lié au code ADE et au département.

Un utilisateur Secretary peut poster des alertes, créer des utilisateurs et ajouter des informations.

Un utilisateur Technician peut accéder aux emplois du temps.

Un utilisateur Subadmin possède les mêmes permissions qu'un administrateur, il ne peut créer d'autre 
sous-administrateur.

### Emploi du temps

Les emplois du temps sont téléchargés au format ICS.  

Les classes utilisées sont : R34ICS et UserController.  

Lorsqu'un utilisateur est connecté, il appelle R34ICS pour pouvoir afficher son emploi du temps, R34ICS permet de 
lire les fichiers ICS.  

### Informations

Une information correspond au type de fichier suivant : Texte, Image, PDF, Vidéo, Short.

Les classes utilisées sont : InformationController, Information & InformationView.  
Les librairies "PhpOffice" et "PDF.js" sont aussi utilisées.  
Fichier javascript : slideshow.js, qui gère les diaporamas.

Les informations sont affichées dans un diaporama dans la partie droite de la télévision.

Les vidéos sont affichées par-dessus l'emploi du temps dans un autre diaporama, puis disparaissent pendant
un certain temps pour laisser place à l'emploi du temps.

### Alertes
Les alertes sont affichés en dessous de l'emploi du temps dans la télévision.

Les classes utilisées sont : AlertController, Alert & AlertView.  
La librairie "JQuery Ticker" est aussi utilisée.  
Fichier javascript : alertTicker.js.

Les alertes sont uniquement des textes.

### Scrapping
Le scrapping, méthode de récupération de données de site web, est également affiché dans le diaporama d'informations.
Il est statique, ce qu'il signifie que pour changer ces informations, il faut se rendre dans le fichier Scrapper.php, 
localisé dans le dossier models.
On modifie "$this→url→'liendusite.net';".
Il est recommandé d'éviter un site utilisant des cookies.