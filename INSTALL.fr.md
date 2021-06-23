# Installation de la plateforme de démocratie liquide Agora Ex Machina (AEM)

## Copie des fichiers

### Si vous n’êtes pas familier avec un repository git

* Télécharger et décompresser l’archive en local
* Ajouter les infos dans le fichier .env, en fin de fichier, ajoutez les informations relatives au serveur de bases de données (adresse, identifiant, mot de passe, nom de la base de données) selon le modèle prévu à cet effet.
* Copiez l’ensemble des fichiers dans un répertoire nommé agoraexmachina

### Si vous êtes familier avec un repository git

* Utilisez `git clone` pour télécharger l'ensemble des fichiers nécessaires à l'installation. 

## Installation

### Préambule

**AgoraExMachina** est développé à l'aide du Framework Symfony. Il est nécessaire :

* soit d'installer le gestionnaire de paquets `Composer` à votre serveur php/MySQL
* soit d'utiliser `composer.phar` pour permettre d'acquisition des paquets.

### Avec composer

`composer install --no-dev --optimize-autoloader`

`composer require symfony/dotenv`

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:execute --up 1`


### Avec composer.phar

`php composer.phar install --no-dev --optimize-autoloader` dans le répertoire agoraexmachina

`composer require symfony/dotenv`

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:execute --up 1`

### Ubuntu

`sudo composer install --no-plugins --no-scripts --no-dev --optimize-autoloader`

`sudo composer require symfony/dotenv --no-plugins --no-scripts`

`sudo php bin/console doctrine:database:create`

`sudo php bin/console doctrine:migrations:execute --up 1`

`sudo chmod 777 public/img/upload`

`sudo chmod 777 public/pdf/upload`

### Procédure post-installation

* Dans un navigateur, se placer dans l’interface d’administration de AEM (http://mondomaine.com/agoraexmachina/public)
* S'enregistrer en admin

### Et ensuite

* Il est possible de modifier un certain nombre de variables dans le fichier `config/services.yaml`. par mi celles-ci :
  * le nom de votre site (affiché dans le menu et dans la balise title de la page `html`
  * Le non de la structure qui abrite le site
  * Le langage, la lang et l'écriture (pour certaines balises, voir dans le fichier `templates/base.html.twig` pour plus de détails).
  
### Installation du service d'email et passage en production

https://symfony.com/doc/current/mailer.html

## Remarques
La solution de démocratie liquide est en développement, et quelques bugs d'installation restent. Ne désespérez pas lors de l'installation. Ces quelques conseils vous aideront surement : 

* Effectuez d'abord une installation en mode local. Une fois cette opération réussie, vous aurez accès à l'ensemble des paquets Symfony qui se trouvent dans le répertoire vendor. 
* Si vous tentez d'installer AEM à l'aide des commandes `composer install --nodev --optimize --autoloader`  et que vous recevez des messages d'erreur, vous pourrez copier le répertoire `vendor` à la racine de votre répertoire distant. En recommençant ensuite les mêmes lignes de commande, votre installation se déroulera sans accroc.
* Lors de l'installation de la base de données, si vous avez des messages d'erreur faisant cas d'`utf8mb4_unicode_ci` non valide, il vous faudra faire cette installation manuellement en exportant le code des tables de la base (à partir de `user`) et de faire l'installation des tables directement, par exemple depuis votre interface phpMyAdmin, en corrigeant, pour la table `user` la chaine de caractères *utf8mb4_unicode_ci* par *utf8_unicode_ci*. Cette procédure peut être appliquée à l'ensemble des tables `user', et suivantes.

Pour tout bug relatif à l'installation, veuillez vous adresser à l'auteur.**