# Ecran connecté

Voici un guide expliquant le fonctionnement de l'écran connecté.  

Si vous souhaitez mieux comprendre les fonctions les plus importantes, veuillez lire le read me dédidé à ces dernières.  

## Principe

Ce projet permet d'afficher l'emploi du temps de la personne connectée.  
En plus de voir son emploi du temps, l'utilisateur pourra aussi recevoir des informations venant de son département et des alertes le concernant.  

Ce projet a aussi pour but d'être affiché sur des télévisions afin d'afficher l'emploi du temps des différentes promotions.  

Ce projet est composé de deux partie :  
    - Le plugin qui permet d'avoir nos fonctionnalités.  
    - Le thème pour avoir l'apparence / la structure que l'on désire.  

## Plugin

Il y a plusieurs plugins utilisés pour ce projet, voici une liste décrivant l'utilité de chaque plugin :  
    - Members // Permet de limiter les pages d'un site à certain membre  
    - Ecran connecté // Plugin principale du site, nous allons en parler plus en détails en dessous  
    - GithubMachin // Permet de faire la synchronisation entre notre plugin et son code sur GitHub  
    - WPS Hide Login // Change l'url de connexion.  
    - WP Crontrol // Permet de faire appel au cron de WordPress  

Nous allons traiter plus en détails le plugin que nous développons, le plugin "Ecran connecté".  

Ce plugin permet plusieurs fonctionnalités:  
    - Création de plusieurs type de compte (Etudiant, Enseignant, Directeur d'études, Secretaire, technicien, Télévision)  
    - Affichage de l'emploi du temps de la personne connectée  
    - Envoie et affichage d'information  
    - Envoie et affichage d'alerte  

### Utilisateurs

Il y a six rôles différents avec chacun leur droit :  

|  Utilisateur       | Voir son emploi du temps |   Poster des informations | Poster des alertes | Inscrire des utilisateurs |
|:------------------:|:------------------------:|:-------------------------:|:------------------:|:-------------------------:|
| Etudiant           |        Oui               |      Non                  |     Non            |     Non                   |
| Technicien         |        Oui               |      Non                  |     Non            |     Non                   |
| Télévision         |        Oui               |      Non                  |     Non            |     Non                   |
| Enseignant         |        Oui               |      Non                  |     Oui            |     Non                   |
| Directeur d'études |        Oui               |      Oui                  |     Oui            |     Non                   |
| Secretaire         |        Non               |      Oui                  |     Oui            |     Oui                   |
| informationPoster  |        Non               |      Oui                  |     Non            |     Non                   |

Dans ce tableau, on peut voir qu'étudiant, technicien et télévisions ont les mêmes droits.  
Mais quelle est la différence ? L'affichage de l'emploi du temps.  

L'étudiant ne voit que son emploi du temps alors que le technicien va avoir un mix de toutes les promotions afin de savoir quelle salle est / sera utilisé.  
Quant à la télévision, la télévision affiche autant d'emploi du temps désiré.  

Si vous voulez autoriser un professeur à poster des informations, ajoutez-lui le rôle informationPoster

### Emploi du temps

L'emploi du temps provient de l'ADE.  
Pour le récupérer rendez-vous sur le read me d'installation du projet.  

Il est télécharger tous les matins via "WP Crontrol" et de la fonction "", en cas de problème de téléchargement, le plugin prend l'emploi du temps téléchargé la veille.  
L'emploi du temps télécharge une période d'une semaine en cas de problème venant de l'ADE permettant de continuer à fonctionner.  
L'affichage de l'emploi du temps est sur la journée pour les étudiants et les techniciens.  
Les enseignants et directeur d'études ont quant à eux accès aux dix prochains cours.  

Les emplois du temps des différentes promotions sont disponibles pour toutes les personnes connectées.  


### Informations

Les informations sont visibles par tout les utilisateurs.
Elles sont affichées dans un diaporama sur le côté de l'écran.

Il y a plusieurs types d'informations possibles a poster (image, texte, PDF, tableau excel, événement).

Les tableaux sont recommandés à n'avoir que trois colonnes maximum avec peu de contenu (A améliorer).  
Les PDF sont affichés grâce à la librairie "PDF.js" qui permet de créer son propre lecteur de PDF. Voir "slideshow.js"

Les événements sont des informations spéciales. Lorsqu'une information événement est posté, les télévisions n'affiche que les informations en plein écran.  
Ces informations sont donc destinés pour les journées sans cours du style "journée porte ouverte".  


### Alerte

Les alertes sont visibles par les personnes concernées.
Avant de poster une alerte, la personne doit indiquer les personnes concernées. Elle peut envoyer l'alerte à tout le monde ou seulement à un groupe voir plusieurs groupes.

Normalement, les personnes qui se sont abonnées aux alertes du site reçoivent l'alerte en notification.
Les alertes défilents les une après les autres en bas de l'écran dans un bandeau rouge.
Les alertes ne sont que du texte.

### Météo

La météo vient d'une API qui est appelé pour nous donner la météo en fonction de notre posistion GPS.
Voir "weather.js"

## Thème

Le thème permet de créer la structure du site. Cela nous permet de modeler le site à notre convenance.
Le site est dans les couleur de l'AMU. Nous avons le site séparé en quatre parties principales :
    - Le Header où ce trouve le logo du site et le menu
    - Le Main où ce trouve l'emploi du temps
    - La sidebar avec les informations
    - Le footer avec les alertes, la date et la météo


### Customisation

Ce thème peut être modifiable directement en allant dans la catégorie "Personnalisé" disponible sur la barre WordPress.  
Dans l'onglet "Ecran connecté", vous pourriez modifier :  
    - L'affichage des informations (positionner les infos à droite, à gauche ou ne pas les afficher)  
    - L'affichage des alertes (activer/désactiver les alertes)  
    - L'affichage de la météo (activer/désactiver, positionner à gauche ou à droite)  
    - L'affichage de l'emploi du temps (Défiler les emplois du temps un par un ou en continue)  

Vous pouvez aussi modifier les couleurs du site, changer le logo du site.  
