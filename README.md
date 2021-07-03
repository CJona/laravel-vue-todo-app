<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Jonathan Cameron's Examenproject Todo App

Onderstaande lijst vat de functionaliteiten in de applicatie samen:

- Multi User
- Todos: Bekijken, aanmaken, bewerken, verwijderen, markeren als voltooid, filteren
- Categorieen: Bekijken, aanmaken, bewerken, verwijderen
- Admin categorieen: Bekijken, aanmaken, bewerken, verwijderen

## Hoe de applicatie is gebouwd

De applicatie is gebouwd met Laravel als backend en Vue.js als frontend. De frontend draait volledig op de JavaScript framework Vue.js. 
De applicatie kent slechts 2 databasemodellen, namelijk: User en Category.

##### User Model  

De User databasemodel wordt gebruikt voor de authenticatie (multi user). In dit databasemodel worden de taken van de gebruikers bijgehouden onder het databasekolom `tasks`, en de admin rechten onder het databasekolom `is_admin`.

##### Category Model 

De Category databasemodel wordt gebruikt om de categorieen van de gebruikers en de admins bij te houden. De indicator voor een admin category in dit databasemodel is onder het databasekolom `is_admin`. De gebruikers worden altijd gekoppeld aan categorieen zodat normale gebruikers hun eigen categorieen krijgen en daarbij de admin categorieeen. De gebruikers kunnen aan de hand hiervan zien welke admin de globale categorie heeft aangemaakt.

##### Relaties

De User en Category databasemodel hebben een een-op-meer relaties. Dit betekent dat een gebruiker meerdere categorieen kan hebben. De onderstaande afbeelding weergeeft het Entiteit Relatie Diagram van deze applicatie.

<img src="https://i.imgur.com/k1GXzF8.png" alt="ERD">

## Ontwikkelen

De applicatie beheert zijn omgevingen met behulp van [Docker](https://docker.com). Dit betekent dat je niets op je eigen computer hoeft te installeren en dat je aan alle onderdelen van de applicatie kunt werken met de meegeleverde containers. Dit maakt het een fluitje van een cent om aan de slag te gaan met de ontwikkeling.

## Commando's

Om aan de slag te gaan, moet je Docker op je machine geïnstalleerd hebben. Met behulp van `docker-compose` commando kun je de containers beheren.

### Start de applicatie

**LET OP: U moet eerst een .env bestand hebben geconfigureerd voordat u de Docker containers kunt bouwen!!!**

Gebruik het commando `docker-compose up -d` om de applicatie te starten met al zijn containers. Dit commando bouwt de dockerfiles, ontkoppelt de uitvoering en draait het op de achtergrond.

### Stop de applicatie

Gebruik het commando `docker-compose down --remove-orphans` om alle onderliggende containers mee te verwijderen.

### Handige commando's:

`docker-compose exec app composer install` - Installeert de Composer pakketten

`docker-compose exec app npm install` - Installeert de NPM pakketten

`docker-compose exec app php artisan key:generate` - Genereert encryptie sleutel voor in de `.env`

`docker-compose exec app php artisan migrate:fresh --seed` - Genereert en vult de database

`docker-compose exec app npm run production` - Bouwt de Vue.js frontend applicatie

### Gebruikers in de applicatie

Om in te logggen als admin:
 - Email: `admin@admin.nl`
 - Wachtwoord: `1234567890`

Om in te logggen als normale gebruiker:
 - Email: `gebruiker@gebruiker.nl`
 - Wachtwoord: `1234567890`

## In een notendop

De bestanden waar het meeste werk in zitten zijn:

 - /app/Models/User.php
 - /app/Models/Category.php
 - /routes/web.php
 - /resources/js/Pages/Dashboard.vue
 - /resources/js/Pages/Admin.vue

En minder werk bij:
 - /database/migrations/create_users_table....
 - /database/migrations/create_categories_table....
 - /database/seeder/DatabaseSeeder.php
 - /docker/php.dockerfile
