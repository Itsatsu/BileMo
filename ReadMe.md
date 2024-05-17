# BileMo [![Codacy Badge](https://app.codacy.com/project/badge/Grade/23555bed4a8047d29c778be2cf7b65da)](https://app.codacy.com/gh/Itsatsu/BileMo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
Ceci est un projet pour le parcours de formation [Développeur d'application PHP/Symfony sur Openclassroom](https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony).
Le but de ce projet est de créer une API pour une entreprise fictive de vente de téléphone portable.

### Contexte du projet

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.
Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise.
Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface).
Il s’agit donc de vente exclusivement en B2B (business to business). Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.


## Fichier présent dans le projet
- schema de base de donnée
- Diagrammes de séquence
- Diagrammes de cas d'utilisation
- Docummantation de l'API
- Fichier nécessaires pour le bon fonctionnement du projet


## Prérequis / technologies utilisées ⚙️

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre système :
- Symfony 6.4
- [PHP](https://www.php.net/) 8.1 ou supérieur
- [Composer](https://getcomposer.org/) 2.6.2 ou supérieur  (pour l'installation des dépendances)
- [MySQL](https://www.mysql.com/) 8.0.30 ou supérieur (ou tout autre système de gestion de base de données compatible)
- [Symfony CLI](https://symfony.com/download) (pour lancer le projet)
- Fichier zip ou clone du projet
- [Postman](https://www.postman.com/) (pour tester l'API)

## Installation et lancement du projet 🚀

1. Clonez ou téléchargez le repository GitHub dans le dossier voulu
2. Installez les dépendances du projet avec la commande suivante :
   ```composer install```
3. Générez les clés jwt avec la commande suivante :
   ```openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096```
puis ```openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout```
4. Configurez vos variables d'environnement dans le fichier .env) à la racine du projet:
- Modifier la variable APP_ENV en dev ou prod selon votre environnement
- Ajouter une clé dans APP_SECRET
- Modifier la variable DATABASE_URL avec vos informations de connexion à la base de donnée
- Modifier la variable jwt_secret_key avec la clé de votre fichier .pem
- Modifier la variable jwt_public_key avec le chemin de votre fichier public.pem
- Ajouter la passphrase de votre fichier private.pem dans la variable jwt_passphrase si vous en avez mis une
5. Créez la base de données avec la commande suivante (assurez-vous que votre serveur MySQL local soit en cours d'exécution et de ne pas avoir de base de données nommé snowtricks)
   ```php bin/console doctrine:database:create```
6. Créez la structure de la base de données avec la commande suivante :
   ```php bin/console doctrine:schema:create```
7. Installez les fixtures avec la commande suivante :
   ```php bin/console doctrine:fixtures:load```

8. Lancez le serveur avec la commande suivante :
   ```symfony serve```

9. Vous pouvez accéder à la documentation de l'API via l'URL suivante :
   ```http://IpDuServeur:8000/api/doc```
10. Vous pouvez maintenant accéder à l'API via l'URL suivante avec Postman:
       ```http://IpDuServeur:8000/api```

## Connexion
- il y a 5 customer avec chacun 5 user associé
- Pour obtenir un token JWT, vous devez vous connecter via la route /api/login_check avec les identifiants suivants :
  - email: customer0@gmail.com
  - password: password
      
