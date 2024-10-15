# Notifications Push

Voici un guide pour activer les notifications push lors de la création d'alertes.

## Préparatifs

Cette fonctionnalité nécessite un site internet servi avec HTTPS.  
Si vous utilisez AlwaysData pour héberger votre site, vous pouvez forcer l'utilisation d'HTTPS dans les paramètres du
site internet (Option ***"Force HTTPS Usage"***).

## Créer un compte OneSignal

Ce projet utilise OneSignal afin de pouvoir délivrer a ses utilisateurs des notifications push.  
Vous allez donc devoir paramétrer un compte OneSignal afin de pouvoir utiliser cette fonctionnalité.

Tout d'abord, rendez-vous sur [le site de OneSignal](https://onesignal.com) afin de créer un compte.

## Paramétrer l'application

Une fois le compte créé et confirmé par mail, vous allez arriver sur une page vous demandant d'écrire le nom de
l'application ainsi que les plateformes sur lesquelles les notifications seront utilisées.

Dans le nom de l'application (***"Name of your app or website"***), écrivez le nom de votre établissement/département.  
Dans les plateformes utilisables (***"What platform do you wish to use for this app? You can set up more later."***),
sélectionnez l'option **"Web Push"**.

Vous pouvez maintenant passer à l'étape suivante.

## Paramétrer le Web Push

Vous allez ensuite arriver sur une page vous demandant de paramétrer les notifications web.

### Section 1. "Choose Integration"

Dans cette section, choisissez l'option **"Custom Code"**.

### Section 2. "Site Setup"

Vous allez devoir remplir deux champs, ***"Site Name"*** et ***"Site URL"***.

Dans le champ ***"Site Name"***, réécrivez le nom de votre établissement/département.  
Dans le champ ***"Site URL"***, écrivez l'URL depuis laquelle la TV connectée est accessible.
(Exemple : https://matvconnectee.alwaysdata.net)

Vous pouvez maintenant passer à l'étape suivante.

## Paramétrer le Web Push (suite et fin)

Finalement, vous allez arriver sur une page intitulée ***"Configure Web Push"***.  
Cette page-là ne vous est pas utile, vous pouvez donc directement cliquer sur le bouton pour terminer la configuration
en bas de la page.

## Paramétrer la TV

Dans OneSignal, vous devriez avoir été redirigé vers la page de l'application que vous venez de créer
(si ce n'est pas le cas, rendez-vous y).  
Allez dans la section ***"Settings"*** puis dans la sous-section ***"Keys & IDs"***.

À côté, ouvrez le fichier ``config-notifs.php`` présent dans le même dossier que ce fichier.

Vous allez devoir remplacer la valeur ``<ONESIGNAL_APP_ID>`` présente Ligne 6 par la valeur présente sous
***"OneSignal App ID"***.  
Vous allez aussi devoir remplacer la valeur ``<REST_API_KEY>`` présente Ligne 11 par la valeur présente sous
***"REST API Key"***.

### Exemple

Dans OneSignal, vous trouvez les valeurs suivantes :
```
ONESIGNAL APP ID
5eb5a37e-b458-11e3-ac11-000c2940e62c

REST API KEY
NGEwMGZmMjItY2NkNy0xMWUzLTk5ZDUtMDAwYzI5NDBlNjJj
```

Dans le fichier ``config-notifs.php``, modifiez les lignes :
```PHP
define('ONESIGNAL_APP_ID',  '<ONESIGNAL_APP_ID>');
define('ONESIGNAL_API_KEY', '<REST_API_KEY>');
```

Par :
```PHP
define('ONESIGNAL_APP_ID',  '5eb5a37e-b458-11e3-ac11-000c2940e62c');
define('ONESIGNAL_API_KEY', 'NGEwMGZmMjItY2NkNy0xMWUzLTk5ZDUtMDAwYzI5NDBlNjJj');
```