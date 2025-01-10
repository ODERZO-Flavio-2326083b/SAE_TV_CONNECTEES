# Ecran connecté

Voici un guide expliquant le fonctionnement de l'écran connecté.  

Si vous souhaitez mieux comprendre les fonctions les plus importantes, veuillez lire le ReadMe dédié à ces dernières : [ReadMeInstallationEcran.md](ReadMeInstallationEcran.md).

## Principe

Ce projet permet d'afficher l'emploi du temps des différents groupes d'étudiants par département via des télévisions.
En plus de voir leur emploi du temps, les étudiants pourront aussi recevoir des informations venant de leur département et des alertes les concernant.  

Ce projet a aussi pour but d'informer les agents d'entretien des salles qui sont occupées, afin de qu'ils puissent s'occuper des salles vides.

Ce projet est composé de deux parties :  
    - Le plugin qui permet d'avoir nos différentes fonctionnalités.
    - Le thème pour avoir l'apparence/la structure que l'on désire. 

## Plugins

Il y a plusieurs plugins utilisés pour ce projet, voici une liste décrivant l'utilité de chaque plugin :  
    - Ecran connecté : Plugin principal du site, nous allons en parler plus en détails en dessous.
    - WPCrontrol : Permet de faire appel au cron de WordPress.

Nous allons traiter plus en détail le plugin que nous développons, le plugin "Ecran connecté".  

Ce plugin permet plusieurs fonctionnalités :  
    - Création de plusieurs types de compte (Secrétaire, Agent d'entretien/Technicien, Télévision).
    - Affichage de l'emploi du temps de la personne connectée.
    - Ajout, envoi et affichage d'information  
    - Ajout, envoi et affichage d'alerte 
    - Création des départements
    - Personnalisation des couleurs de la télévision, selon le département.


### Utilisateurs

Il y a trois rôles différents avec chacun leur droit :

|     Utilisateur     | Voir son emploi du temps |   Poster des informations | Poster des alertes | Inscrire des utilisateurs | Personnaliser la télévision |
|:-------------------:|:------------------------:|:-------------------------:|:------------------:|:-------------------------:|:---------------------------:|
|     Technicien      |           Oui            |            Non            |        Non         |            Non            |             Non             |
|     Télévision      |           Oui            |            Non            |        Non         |            Non            |             Non             |
|     Secrétaire      |           Non            |            Oui            |        Oui         |            Oui            |             Non             |
| Sous-administrateur |           Non            |            Oui            |        Oui         |            Oui            |             Oui             |


Les techniciens sont des agents d'entretien, qui vont avoir accès à l'emploi du temps pour leur permettre de savoir quelles salles sont occupées.
Les sous-administrateurs sont les administrateurs de leur département respectif.

### Emploi du temps

L'emploi du temps provient du Planning des cours de l'ENT (ADE).  
Pour le récupérer rendez-vous sur le ReadMe d'installation du projet.  

Il est téléchargé tous les matins via "WP Crontrol", en cas de problème de téléchargement, le plugin prend l'emploi du temps téléchargé la veille.  
L'emploi du temps télécharge une période d'une semaine en cas de problème venant de l'ADE permettant de continuer à fonctionner.  
L'affichage de l'emploi du temps est sur la journée pour les étudiants et les techniciens.  

Les emplois du temps des différentes promotions sont disponibles pour tous les utilisateurs connectés.


### Informations

Les informations sont visibles par tous les utilisateurs selon leur département.
Elles sont affichées dans un diaporama à la droite de l'écran.

Il y a plusieurs types d'informations possibles à poster (image, texte, PDF, événement, vidéo, short).
Les images sont affichées au format 

Les PDF sont affichés grâce à la librairie "PDF.js" qui permet de créer son propre lecteur de PDF. Voir "slideshow.js"

Les vidéos et shorts (vidéo courte au format vertical) sont au format .mp4 ou .webm et sont affichés dans un diaporama par-dessus l'emploi du temps
dans un diaporama. Une fois le diaporama terminé, les vidéos disparaissent pendant une certaine durée pour laisser place à l'emploi du temps.

Les shorts sont affichés dans le diaporama d'informations à droite comme les autres.

Les événements sont des informations spéciales. Lorsqu'une information événement est postée, les télévisions n'affichent que les informations en plein écran.  
Ces informations sont donc destinées pour les journées sans cours du style "Journée portes ouvertes".  
Un événement est soit une image, soit un PDF.



### Alerte

Les alertes sont visibles par les personnes concernées.
Avant de poster une alerte, la personne doit indiquer les personnes concernées. Elle peut envoyer l'alerte à tout le monde ou seulement à un groupe, voire plusieurs groupes.

Normalement, les personnes qui se sont abonnées aux alertes du site reçoivent l'alerte en notification.
Les alertes défilent les une après les autres en bas de l'écran dans un bandeau rouge.
Les alertes ne sont que du texte.

### Départements
Les départements sont créés à l'aide du formulaire à la page dédiée, en ajoutant le nom du département, sa création amène à l'utilisation des autres pages : 
- Chaque utilisateur crée est associé à un département.
- Chaque information créée est associé à un département.
- Pour la personnalisation de la télévision, chaque télévision est associée à un département.


### Météo

La météo vient d'une API qui est appelé pour nous donner la météo en fonction de notre position GPS.
Voir "weather.js".

### Thème

Le thème permet de créer la structure du site. Cela nous permet de modeler le site à notre convenance.
Le site respecte la charte graphique de l'AMU. Nous avons le site séparé en quatre parties principales :
    - Le Header où se trouve le logo du site et le menu
    - Le Main où se trouve l'emploi du temps
    - La sidebar avec les informations
    - Le footer avec les alertes, la date et la météo


### Personnalisation via WordPress

Ce thème peut être modifiable directement en allant dans la catégorie "Personnalisé" disponible sur la barre WordPress.  
Dans l'onglet "Ecran connecté", vous pourrez modifier :  
    - L'affichage des informations (positionner les infos à droite, à gauche ou ne pas les afficher). 
    - L'affichage des alertes (activer/désactiver les alertes). 
    - L'affichage de la météo (activer/désactiver, positionner à gauche ou à droite).
    - L'affichage de l'emploi du temps (Défiler les emplois du temps un par un ou en continu).

Vous pouvez aussi modifier les couleurs du site, changer le logo du site, dans le cas d'un changement de charte graphique
par AMU.

## Personnalisation via le CSS

Vous pouvez également personnaliser les télévisions comme vous le souhaitez grâce à la page "Gestion du CSS".

Sélectionnez le département dont vous souhaitez modifier les couleurs de la télévision.

Arrière-plan 1 :

La couleur d'arrière-plan de l'entièreté de la page.

Arrière-plan 2 :

La couleur d'arrière-plan du container au milieu de la page.

Couleur de la mise en page :

La couleur des rubans d'en-tête et de pied-de-page.

Couleur de l'écriture :

La couleur du texte présent dans le ruban de pied-de-page.

Couleur du titre :

La couleur du titre de chaque page.

Couleur des liens :

La couleur des liens, comme par exemple le bouton "modifier" des télévisions (voir page 19 pour plus d'informations).

Couleur de la bordure du bouton :

La couleur des bordures des différents boutons présents sur le site, s'il y en a une.

Couleur du bouton :

La couleur des différents boutons présents sur le site.

Couleur de la barre latérale :

La couleur de la partie gauche de l'affichage des télévisions, dans laquelle sont affichées les informations.

Une fois la modification effectuée, connectez-vous à la télévision du département afin d'en voir les résultats.


