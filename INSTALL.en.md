# Installation of the liquid democracy platform Agora Ex Machina (AEM)

## Copying files

### If you are not familiar with a git repository

* Download and unzip the archive locally
* Add the infos in the .env file, at the end of the file, add the information related to the database server (address, login, password, database name) according to the template provided for this purpose.
* Copy all the files in a directory named agoraexmachina.

### If you are familiar with a git repository

* Use `git clone` to download all the files needed for installation. 

## Installation

### Preamble

**AgoraExMachina** is developed using the Symfony Framework. It is necessary :

* either to install the package manager `Composer` to your php/MySQL server
* or use `composer.phar` to allow packet acquisition.

### With composer

`composer install`

`php bin/console doctrine:database:create`

`php bin/console doctrine:schema:update --force`

`php bin/console doctrine:fixtures:load`

*In .env file, change APP_ENV=dev to APP_ENV=prod*

`composer install --no-dev --optimize-autoloader`

### With composer.phar

`php composer.phar install` in the agoraexmachina directory

`php bin/console doctrine:database:create`

`php bin/console doctrine:schema:update --force`

`php bin/console doctrine:fixtures:load`

*In .env file, change APP_ENV=dev to APP_ENV=prod*

`php composer.phar install --no-dev --optimize-autoloader`

### Post-installation procedure

* In a browser, go to the AEM administration interface (http://mondomaine.com/agoraexmachina)
* Sign in (top right, **Signin**)
* Login : **admin@agora.com**
* Password: **agora**

### And then

* It is possible to modify a number of variables in the `config/services.yaml` file, among them :
  * the name of your site (displayed in the menu and in the title tag of the `html` page.
  * The name of the structure that houses the site
  * The language, lang and writing (for some tags, see the `templates/base.html.twig` file for more details).

## Remarks
The liquid democracy solution is under development, and some installation bugs remain. Don't despair during installation. These few tips will surely help you: 

* Perform a local mode installation first. Once this operation is successful, you will have access to all the Symfony packages in the vendor directory. 
* If you try to install AEM using the commands `composer install --nodev --optimize --autoloader` and you receive error messages, you can copy the `vendor` directory to the root of your remote directory. Then by repeating the same command lines, your installation will run smoothly.
* When installing the database, if you get error messages about an invalid `utf8mb4_unicode_ci`, you will have to do this installation manually by exporting the code of the database tables (from `user`) and to install the tables directly, for example from your phpMyAdmin interface, correcting the string *utf8mb4_unicode_ci* for the `user` table with *utf8_unicode_ci*. This procedure can be applied to all `user' tables, and following ones.

For any bug related to the installation, please contact the author.**

Translated with www.DeepL.com/Translator (free version)