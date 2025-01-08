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
- Activez l'extension Ecran Connecté AMU
- 
Allez ensuite dans l'onglet des plugins et activez-les tous.  

Allez ensuite à l'onglet thème et activer le thème "Ecran connecté".  

Les plugins et le thème sont maintenant activés.

## Mise à jour automatique de l'EDT

Pour que les pages se mettent a jour automatiquement, il vous faut rajouter une tâche récurrente manuellement. 

Pour ce faire, allez dans la partie administrateur du site "nomdusite/wp-admin".  

Dans la barre latérale, cliquez sur "Réglages", puis "Planifications Cron". 

Allez ensuite dans l'onglet "Ajouter un évènement cron". 

Dans la section "Nom du crochet", renseignez "downloadFileICS" (sans les guillemets). 

Dans la section "Prochaine exécution", sélectionnez "Demain". 

Dans la section "Fréquence", sélectionnez "Une fois par jour (Daily)". 

Vous pouvez ensuite confirmer l'ajout avec le bouton "Add Event". 

## Pages

Pour finir, il ne reste plus qu'à ajouter les pages du site.

Allez dans importer, WordPress et donné le fichier xml contenant les pages.  

Le site est maintenant prêt, il faut néanmoins enregistré les différents groupe afin de pouvoir les utiliser.

## Enregistrer des groupes

Dans cette partie, nous verrons comment enregistrer des groupes.  
Pour ce faire connectez vous sur votre ent et allez dans l'ADE.  

Sélectionnez le groupe de votre choix.
Cliquez sur le bouton "Export to agenda..." puis "générer URL"

Vous aurez alors une URL de ce style "https://ade-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=6445&calType=ical&firstDate=2020-03-02&lastDate=2020-03-08"

Récupérer la valeur à ressources=.... (Dans notre lien d'exemple, il s'agit de 6445).  

Dans votre site WordPress, allez dans la partie "Code ADE" et remplissez le formulaire.  

## Custimisation

Vous pouvez customisé ce site comme vous le souhaitez.

Il y a plusieurs partie modifiable sur le site.
Pour cela cliquez sur "Personnalisé" sur la barre wordpress

Votre site est dorénavant prêt à être utilisé.
