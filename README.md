JestemGraczem.pl
================

## Komendy
### Baza danych
`php bin/console doctrine:schema:update --force` - Aktualizacja struktury bazy
`php bin/console doctrine:generate:entity` - Tworzenie nowej tabeli
### Bundle
`php bin/console generate:bundle` - Generator Bundle
### Cache
`php bin/console cache:clear --env=prod` - Czyszczenie cache produkcja
`php bin/console cache:clear` - Czyszczenie cache dev
### Assets
`php bin/console assets:install --symlink` - Instalacja assetów
### FOSUserBundle
`php bin/console fos:user:promote testuser ROLE_ADMIN` - Promowanie użytkownika na admina
`php bin/console fos:user:promote testuser ROLE_SUPER_ADMIN` - Promowanie użytkownika na super saiyajin