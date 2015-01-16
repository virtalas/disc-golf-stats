# Missä kansiossa komento suoritetaan
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

source $DIR/config/environment.sh

echo "Siirretään tiedostoja users-palvelimelle..."

# Tämä komento siirtää tiedostot palvelimelta
rsync -r $DIR/ $USERNAME@users.cs.helsinki.fi:htdocs/$PROJECT_FOLDER

echo "Valmis!"

echo "Generoidaan Composerilla classmap_autoload.php-tiedosto..."

# Päivitetään autoload_classmap.php-tiedosto
ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER
php composer.phar dump-autoload
exit"

echo "Valmis! Sovelluksesi on nyt valmiina osoitteessa $USERNAME.users.cs.helsinki.fi/$PROJECT_FOLDER"
