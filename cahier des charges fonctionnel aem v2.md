# Cahier des charges fonctionnel
> 21 05 2021

**Remarque** : à cette étape, l’aspect visuel et l’ergonomie du site ne sont pas traités.
## Catégories / Besoins fonctionnels
### Catégories des Thèmes et ateliers
#### Thèmes
1. Permet de faciliter l’attribution des droits à un ADMIN_RESTREINT ou un MODERATEUR pour des catégories de **Thèmes**~~
2. Permet de faciliter l’inscription de cohortes d’utilisateurs à des catégories de **Thèmes**~~ (Desinscirption en groupe a faire)
3. Permet de grouper **Thèmes** et utilisateurs sur des sujets précis : Par exemple, trois associations peuvent pratiquer leurs activités démocratiques indépendamment les uns des autres, sans que les uns voient ce qui se passe chez les autres. À noter, un utilisateur peut avoir accès à plusieurs catégories lui permettant d’avoir accès à plusieurs **Thèmes** (!important). Sauvegarde dans la base de données probablement en `ARRAY`.

#### Ateliers
Ils reçoivent en héritage la catégorie à laquelle leur Thème est associé.

À cela s’ajoute peut-être une autre forme de catégorisation par **Mots-clefs** permettant pour un utilisateur de passer d’un atelier à l’autre par mot-clefs plutôt que par navigation « verticale ». 
#### Conclusion
1. Un **Thème** peut être associé à une **Catégorie**
2. Un **Atelier** ou un **Thème** peuvent être associé à un ou plusieurs **Mots-clefs** (#)

#### Public / privé
1. Ajouter à la base de données pour les **Themes** un champ *Public* (O/N) qui permet de savoir si l’atelier ou le thème va être visible dans l’espace public sans login. Par contre, il faut permettre que même dans la partie publique, en étant logué, on n’ait accès qu’à ce qui nous intéresse, d’où l’intérêt des **Catégories**.
2. Ajouter deux champs aux **Thèmes** et **Ateliers** : permettre ou non la **Délégation** et établir la profondeur de la délégation « en cascade » : 
    * 0 pour pas de limitation, 
    * 1 pour « une personne peut recevoir une délégation »
    * etc ...

## Délégations / besoins fonctionnels
* L’ADMIN ou ADMIN_RESTREINTt doit pouvoir 
    * permettre que les participants à un **Thème** ou un **Atelier** puissent avoir accès au principe de **Délégation** ou pas
    * si la délégation est fonctionnelle, permettre le niveau de délégation en cascade. 

## Pages de Gestion du site
Classés éventuellement par onglets

* L’ADMIN du site et les ADMIN_RESTREINT doivent avoir accès à une page de gestion complète. 
* Les modérateurs doivent avoir une page récapitulative des activités dont ils ont la charge : 
    * nouveaux posts de forum
    * nouvelles propositions
    * signalements éventuels
* tous les utilisateurs loggés ont accès à :
   - toutes les réponses à leurs threads de forums
   - tous les votes auxquels ils doivent participer
   - toutes leurs délégations (item d’avant, sauf s’ils ont délégué leur vote)
   - tous leurs signalements (et les réponses éventuelles des modérateurs qui y ont répondu) 
   
En outre, ils ont accès à leur page de gestion de leur profil; chargement en AJAX.
### Gestion de la Page d’accueil
#### L’administrateur peut :
1. Gérer la plupart des balises meta, notamment pour ce qui est du référencement naturel (title, description, url) et du référencement réseaux sociaux (balises OpenGraph et Twitter). 
**Remarque** : pour l’instant, c’est géré dans la doctrine.yaml. On peut ajouter des variables supplémentaires utilisables. En soi, c’est plutôt un travail pour le webmestre, donc ce n’est pas *prioritaire*.
2. Gérer l’aspect de la page d’accueil : logo général du site, Titre général, image d’arrière-plan (voir par exemple dans l’aspect le site de [démocratie participative de Villeurbanne](https://participez.villeurbanne.fr/?locale=fr), et Descriptif / Présentation du site. Ne pas limiter le nombre de caractères et proposer une interface WYSIWYG pour le descriptif, mais donner la possibilité de limiter le nombre de caractères dans le twig. 
Les champs titre et descriptif ne sont pas forcément les mêmes que les meta title, description ou les balises OpenGraph ou Twitter.
   ~~3. Gérer les **Thèmes** visibles sur la page d’accueil. Un **Thème** n’apparaitra pas s’il est défini en tant que « privé ».
4. Gestion du pied de page.
### Gestion des Thèmes
#### L’ADMIN peut :
1. Créer des **Thèmes**
2. Ôter des **Thèmes** de la consultation publique (plus accessible sauf pour un ADMIN ou un ADMIN_RESTREINT si ça colle à ses attributions)
3. Les fonctionnalités actuelles relatives aux **Thèmes** n’ont plus à être changés.
4. Définir quels sont les **Thèmes** visibles depuis la page d’accueil (redite avec point 3. précédent, nécessaire ?)
5. Définir quels sont les **Thèmes** publics (même s’ils ne sont pas tous accessibles depuis la page d’accueil)
6. Attribuer une Catégorie aux **Thèmes** (voir plus haut)
7. Permettre ou non la possibilité de **Délégation** ; si elle est permise, la délégation en cascade peut être :
    * totale (0)
    * partielle (1, 2, 3, ...).
#### L’ADMIN_RESTREINT peut :
1. Permettre ou non la possibilité de **Délégation** ; si elle est permise, la délégation en cascade peut être :
    * totale (0)
    * partielle (1, 2, 3, ...)
### Gestions des Ateliers
#### L’ADMIN peut :
1. Créer des **Ateliers**. Les fonctionnalités actuelles sont OK, y compris pour la déclaration des premières propositions. Ajouts :
    * Pouvoir définir des périodes de temps différents pour le débat et la concertation (**Forum**, **Propositions**) et pour le **Vote**.
    * Proposer un ou plusieurs documents pdf (MAX 5) associé(s) à l’**Atelier**.
2. Retirer des **Ateliers**
3. Permettre ou non la possibilité de **Délégation** ; si elle est permise, la délégation en cascade peut être :
    * totale (0)
    * partielle (1, 2, 3, ...)
4. Permettre ou non le signalement des threads de **Forums** à caractère problématique
5. Attribuer un MODERATEUR à un atelier (redite nécessaire ?).
6. Attribuer des **Mots-clefs** à un atelier (avec possibilité de créer le **Mot-clef** à la volée
#### L’ADMIN_RESTREINT peut :
Faire la même chose qu’un ADMIN, mais uniquement pour les **Thèmes** dont il a la charge.
#### Un MODERATEUR peut :
1. Débrayer la fonction **Délégation** sur l’**Atelier**
2. Débrayer la fonction **Signalement** sur l’**Atelier**
3. Ajouter des Documents à l’**Atelier**
### Gestion des utilisateurs
#### L’ADMIN peut :
1. Créer un nouvel utilisateur
2. Attribuer un rôle à l’inscrit. Par défaut : USER
    * Il peut attribuer le rôle d’administrateur restreint. Dans ce cas, il attribue le rôle à un ou plusieurs **Thèmes**, ou à une catégorie.
    * Il peut retirer le rôle d’administrateur restreint.
    * Il peut attribuer ou retirer le rôle de MODERATEUR pour un **Thème** ou un **Atelier**.
3. Associer un utilisateur à une ou plusieurs catégories
4. Éliminer un utilisateur : le bannir  (l’éliminer de toutes les catégories) de telle sorte que son adresse email puisse être reconnue (et son I.P. ? Est-ce que ça sert à quelque chose ?)
5. Prévenir un utilisateur que son comportement ne correspond pas aux règles définies sur la plateforme. 
6. Ban temporaire. Il doit définir la durée du ban.
7. Une page lui indique les utilisateurs nouvellement inscrits qui ont fait la demande de participer à un **Thème**
#### L’ADMIN_RESTREINT peut :
1. Créer un nouvel utilisateur associé à la catégorie qu’il gère. S’il en a plusieurs, il doit pouvoir choisir une ou plusieurs catégories pour le même utilisateur.
2. Attribuer un rôle à l’inscrit. Par défaut : **USER**. Il peut également attribuer ou retirer le rôle MODERATEUR pour un **Thème** ou un **Atelier**
3. Éliminer un utilisateur correspondant à une catégorie dont il a la charge : le bannir (l’éliminer de toutes les catégories) de telle sorte que son adresse email puisse être reconnue (même commentaire)
4. Prévenir un utilisateur que son comportement ne correspond pas aux règles définies sur la plateforme si un signalement effectué par un ou plusieurs **USER**s s’avèrent vrais. 
5. Ban temporaire. Il doit définir la durée du ban.
#### Le MODERATEUR peut :
1. Prévenir un utilisateur que son comportement ne correspond pas aux règles définies sur la plateforme (*idem*).
2. Ban temporaire. Il doit définir la durée du ban.
#### L'USER peut :
1. S’inscrire sur la plateforme. Nom Prénom, email. La procédure est celle-ci :
    * Il reçoit un email de confirmation
    * Le lien l’amène à une page dans laquelle il définit un mot de passe.
    * Il est alors logué.
2. Peut demander à s’inscrire à un **Thème** (validée par un ADMIN ou un ADMIN_RESTREIN si le **Thème** correspond à un de ceux dont il a la charge)
3. A accès à une page lui permettant de définir son avatar : 
    * Nom
    * Prénom 
    * Login
    * Choix de l'identité visible depuis l’espace public
    * bio
    * changement d’e-mail pas possible
    * changement de mot de passe possible
    * choix d’un avatar (photo ou avatar choisi dans une liste).
La même page (navigation par onglet ajax ?) le renseigne sur :
    * Les threads de forums les plus récents qu’il a écrit avec une réponse apportée à son thread, ou d’autres réactions dans le même forum.
    * Les nouvelles propositions faites dans les **Ateliers** auxquels il participe
    * L’ouverture de nouveaux **Ateliers** dans les **Thèmes** auxquels il participe.

**Remarque** concernant la politique d'identification des utilisateurs. 
4. Peut contacter :
    * Le ou les **ADMIN**s
    * Le ou les **ADMIN_RESTREINT**s qui ont la charge des **Catégories** auxquels il participe (exprimé en terme de **Catégories** ou de **Thèmes** (à voir ...)

**Remarque** qui n’a rien à voir là ! Il faut absolument définir une politique d'identification forte (la liberté d'expression ne se fait qu'à visage découvert). Bien sûr, l'anonymat sera toujours possible, mais il faut e rendre le moins probable. Par exemple, pour ce qui est de l'expresion démocratique, il faut qu'on puisse imposer à l'**USER** de s'indentifier via un document complémentaire. Au choix de la structure qui utilise la plateforme.

Il sera donc nécessaire d'ajouter une page / formulaire pour justifier de son identité.

#### Gestion des forums
**Rappel 1** : les dates de début et fin du forum sont différentes de celles du vote (choisies par l'admin).

**Rappel 2** : Un système de signalement des threads de forums peuvent être — ou non — permises par l’**ADMIN**, l’**ADMIN_RESTREINT** ou le **MODERATEUR**

L'**ADMIN**, l'**ADMIN_RESTREINT** ou le **MODERATEUR** peuvent : 
* lire facilement l’ensemble des nouveaux threads de forum. 
* voir facilement les threads de forums qui ont été signalés (voir remarque plus haut).
* éliminer les threads de forums s’ils s’avèrent problématiques. **Remarque** : en lieu et place du thread dans le fil de lecture, un message indique qu’il a été éliminé. Pas d’indication de l’auteur incriminé. Lorsqu’un thread est éliminé, l’auteur est prévenu par email.

L'utilisateur peut :
* créer une réponse à un thread de **Forum**
* éditer ses réponses précédentes
* si l'**ADMIN** ou l'**ADMIN_RESTREINT** le permettent, signaler un thread de **FORUM**.f

## Statistiques 
### 1. Statistiques de connexion
### 2. Statistiques de participation
Pour chacun des **Ateliers** en activité, soit en terme de débats, de votes, ou des deux, on doit voir :
* Le nombre de votes effectué (s’il y a lieu)
* Le nombre de **Propositions**
* Le nombre de **threads** dans les forums
### 3. Quorums et atteintes de quorums
Pour chaque vote en cours, doit afficher :
* Le nombre total de votes exprimés par **Propositions** classées selon les **Thèmes** et **Ateliers**
* Les **Propositions** pour lesquelles le quorum est atteint (visuel couleur)
* Les **Propositions** pour lesquelles le quorum n’est pas atteint (visuel couleur)

Pour chaque proposition dont le Quorum n’est pas atteint et dont la date de fin de vote est proche (laps de temps défini par l’**ADMIN** ou l’**ADMIN_RESTREINT** dans son interface), un bouton « envoi collectif de rappel » à tous ceux qui n’ont pas voté et qui n’ont pas délégué leur vote (ils peuvent être par ailleurs être délégués finaux dans la cascade).
## Partie publique
### Votes
* Il faut revoir la présentation qui ne colle pas. Il faut savoir si on a voté à une proposition dès qu’on visionne la liste des propositions.
### Ergonomie
* Un **ADMIN** ou un **ADMIN_RESTREINT** peuvent tout autant devenir **MODERATEUR** selon les circonstances. Sur leur page de gestion, les fonctionnalités s’ajoutent selon les rôles qu’ils peuvent avoir sous la forme de signets.
* 
### Ateliers
* Ajouter l’affichage du ou des **Mots-clefs** 
### Propositions
Bug : une fois qu’on a voté, c’est la première proposition qui apparaît. Il faut que ce soit la proposition pour laquelle on a voté qui apparaisse. 

### Forums
La date de création d'un thread de forum doit être affichée.

## Installation
### Première installation
### Mise à jour d’une installation existante

## FAIRE UN SYSTEME DE NOTIFICATIONS
MODIFIER README