---

# Guide d'installation du site web sur Raspberry Pi - SAE TV Connectée

### Auteur :  
Bertin Mathéo, Gallou Loïc, Oderzo Flavio, Tamine Cyril

---

## Table des matières

1. [Mise en place de Raspberry Pi OS](#mise-en-place-de-raspberry-pi-os)
    1. [Installation de l'OS sur carte microSD](#installation-de-los-sur-carte-microsd)
    2. [Premier pas sur Raspberry Pi OS](#premier-pas-sur-raspberry-pi-os)
2. [Mise en place du serveur Apache](#mise-en-place-du-serveur-apache)
    1. [Installation d'Apache](#installation-dapache)
    2. [Installation de PHP](#installation-de-php)
3. [Mise en place de la base de données](#mise-en-place-de-la-base-de-données)
    1. [Installation de MariaDB](#installation-de-mariadb)
    2. [Création d'un compte utilisateur pour MariaDB](#création-dun-compte-utilisateur-pour-mariadb)
    3. [Mettre en place phpMyAdmin](#mettre-en-place-phpmyadmin)
4. [Mise en place de Wordpress](#mise-en-place-de-wordpress)
    1. [Installation de Wordpress](#installation-de-wordpress)
    2. [Liaison de Wordpress avec la base de données](#liaison-de-wordpress-avec-la-base-de-données)
5. [Migration de votre site sur Raspberry Pi](#migration-de-votre-site-sur-raspberry-pi)
    1. [Récupération de votre site distant](#récupération-de-votre-site-distant)
    2. [Configuration Wordpress](#configuration-wordpress)
6. [Ressources](#ressources)

---

## Mise en place de Raspberry Pi OS

### Installation de l’OS sur carte microSD

1. Avoir une carte microSD d’au moins 8Go et un adaptateur microSD/USB pour pouvoir télécharger l’OS directement sur la carte via un port USB de votre ordinateur.
2. Rendez-vous sur la page de téléchargement de Raspberry Pi Imager.
3. Après téléchargement, insérez la carte microSD dans l’adaptateur USB et connectez-le à votre ordinateur.
4. Ouvrez le fichier téléchargé, choisissez la version 64 bit de Raspberry Pi OS (recommandée).
5. Sélectionnez la microSD comme espace de stockage.
6. Cliquez sur “Next”, puis sur “No” (nous configurerons les paramètres de l'OS plus tard).
7. Une fois le téléchargement terminé, retirez la carte microSD.

### Premier pas sur Raspberry Pi OS

1. Insérez la microSD dans votre Raspberry Pi, branchez-le, puis connectez un clavier et une souris.
2. Après l’installation de l’OS, renseignez un nom d’utilisateur et un mot de passe.
3. Vous arriverez sur le bureau de Raspberry Pi OS.
4. Allez dans “Préférences” > “Raspberry Pi Configuration”.
5. Allez dans l’onglet “Interfaces” et cochez les cases SSH et VNC pour l’accès à distance.

---

## Mise en place du serveur Apache

### Installation d'Apache

1. Mettez à jour les paquets avec la commande suivante :

   ```bash
   sudo apt update
   sudo apt upgrade
   ```

2. Installez Apache avec cette commande :

   ```bash
   sudo apt install apache2
   ```

3. Testez le serveur en entrant l’adresse IP de votre Raspberry dans un navigateur.

   ```bash
   hostname -I
   ```

   Vous devriez voir une page indiquant que le serveur fonctionne.

### Installation de PHP

1. Installez PHP avec la commande :

   ```bash
   sudo apt install php libapache2-mod-php
   ```

2. Testez le fonctionnement de PHP en créant un fichier dans `/var/www/html/` :

   ```bash
   sudo nano /var/www/html/test.php
   ```

   Collez le code suivant :

   ```php
   <?php
   echo "La date d'aujourd'hui est ".date('Y-m-d H:i:s');
   ?>
   ```

   Enregistrez avec `Ctrl + S` puis quittez avec `Ctrl + X`.
   
3. Rafraîchissez votre navigateur pour voir le message avec la date actuelle.

---

## Mise en place de la base de données

### Installation de MariaDB

1. Mettez à jour les paquets :

   ```bash
   sudo apt update
   sudo apt upgrade
   ```

2. Installez MariaDB avec la commande :

   ```bash
   sudo apt install mariadb-server
   ```

### Création d’un compte utilisateur pour MariaDB

1. Lancez MariaDB avec la commande :

   ```bash
   sudo mysql
   ```

2. Créez un utilisateur avec la commande suivante :

   ```bash
   CREATE USER 'utilisateur'@'localhost' IDENTIFIED BY 'motdepasse';
   ```

3. Donnez les permissions nécessaires à cet utilisateur :

   ```bash
   GRANT ALL PRIVILEGES ON *.* TO 'utilisateur'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Mettre en place phpMyAdmin

1. Installez phpMyAdmin :

   ```bash
   sudo apt install phpmyadmin
   ```

2. Sélectionnez `apache2` lors de l'installation.
3. Accédez à phpMyAdmin via votre navigateur à l'adresse `http://<votre_IP>/phpmyadmin`.

---

## Mise en place de Wordpress

### Installation de Wordpress

1. Téléchargez l’archive de Wordpress avec cette commande :

   ```bash
   wget https://wordpress.org/latest.tar.gz
   ```

2. Extrayez l’archive dans le répertoire `/var/www/html` :

   ```bash
   sudo tar -xvzf latest.tar.gz -C /var/www/html
   ```

3. Modifiez les permissions pour éviter d’utiliser `sudo` à chaque fois :

   ```bash
   sudo chmod -R 755 /var/www/html/wordpress
   ```

### Liaison de Wordpress avec la base de données

1. Créez une base de données vide dans phpMyAdmin.
2. Accédez à `http://<votre_IP>/wordpress` et suivez les instructions.
3. Remplissez les informations de la base de données (nom, utilisateur, mot de passe) et cliquez sur “Let’s Go”.
4. Finalisez l’installation en créant un compte administrateur pour votre site Wordpress.

---

## Migration de votre site sur Raspberry Pi

### Récupération de votre site distant

1. Installez FileZilla avec la commande :

   ```bash
   sudo apt install filezilla
   ```

2. Renseignez les informations FTP de votre site distant et transférez les fichiers du dossier `wp-content`.

### Configuration Wordpress

1. Importez le fichier XML de votre site dans `Outils` > `Importer` dans le tableau de bord de Wordpress.
2. Activez le thème de votre site.

Votre site Wordpress est maintenant migré et fonctionne sur votre Raspberry Pi.

---

## Ressources

- [Tuto Raspberry Pi OS](https://raspberrytips.fr/installer-raspberry-pi-os/)
- [Tuto Apache et PHP](https://www.raspberrypi-france.fr/mise-en-place-dun-serveur-web-apache-sur-raspberry-pi/)
- [Tuto MariaDB](https://raspberrytips.fr/installer-mysql-raspberry-pi-mariadb/)
- [Tuto PhpMyAdmin](https://raspberrytips.fr/installer-serveur-web-raspberry-pi/)
- [Tuto Wordpress](https://raspberrytips.fr/installer-wordpress-sur-raspberry-pi/)

---

Vous avez maintenant toutes les étapes nécessaires pour installer et migrer un site Wordpress sur un Raspberry Pi !

