# Gacha Tracker Backend server

This the backend server for the <a href="https://github.com/sesvete/gacha-tracker-apache" title="App link">Gacha Tracker App Apache</a>

## Server Setup
- ### Prerequesites
    - Apache2
    - php
    - MariaDb (or another SQL database)
    - Composer

- ### Setup
    - import the sql schematic found in the sql folder
    - Create dbh.inc.php file in the folder includes and fill it with your data (use dbh_template.inc.php as template)
    - Create credentials.inc.php file in the folder includes and fill it with your data (use credentials_template.inc.php as template)
    - while positioned in the project root directory, run the terminal comand: <b>composer install<b>