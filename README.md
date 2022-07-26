### Requirement
1. Docker is installed on system

### Install
1. Start docker
2. docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
3. ./vendor/bin/sail up -d
4. ./vendor/bin/sail artisan starmoozie:install
5. ./vendor/bin/sail artisan migrate --seed
6. Login app dengan email password
7. Buat permission baru dengan nama "import"
8. Edit Menu dengan nama menu, kemudian checklist permission "import"
9. Edit Group dengan nama developer, kemudian checklist "Import" pada Menu Pengguna
10. Sudah bisa import excel pada menu "Pengguna"

### Modul / package
- Saya menggunakan composer package dari project saya starmoozie/laravel-crud, starmoozie/laravel-crud-generators, starmoozie/laravel-menu-permission
- Folder starmoozie ada divendor
- Core app ada di starmoozie/laravel-crud
- Permission ada di starmoozie/laravel-menu-permission
- Beberapa controller, model, dan request ada disitu, kecuali Untuk User dan Role, sudah saya rubah ke folder default laravel
- Semua controller extends dari Package saya
- Package ini masih dalam tahap pengembangan, mungkin ada beberapa bug

### Username password
- starmoozie@gmail.com
- password