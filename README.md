Binôme : Antoine Berthel & Quentin Palmisano

Pour lancer le serveur :
   1) composer install
   2) php bin/console doctrine:database:create
   3) php bin/console make:migration
   4) php bin/console doctrine:migrations:migrate
   5) symfony server:start