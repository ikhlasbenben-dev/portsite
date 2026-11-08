# Atelier 6

Application d'inscription et d'affichage des stagiaires avec PHP, MySQL sur Aiven et Cloudinary.

## Base de données

Dans Aiven, ouvrez **Query editor** (ou connectez-vous avec un client MySQL), sélectionnez `defaultdb` et exécutez le contenu de `schema.sql`.

## Variables d'environnement

Copiez `.env.example` en `.env` pour un environnement local, puis renseignez les valeurs. Dans Vercel, ajoutez les mêmes clés dans **Settings → Environment Variables**. Les valeurs sensibles ne doivent pas être commitées.

- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
- `DB_SSL_CA` : chemin vers le certificat CA Aiven. Téléchargez le certificat CA depuis la page de connexion Aiven et placez-le sous `certs/ca.pem` pour le déploiement.
- `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`

Le dépôt est prévu pour servir la page portfolio et les fichiers Atelier 6. Le runtime Vercel doit fournir PHP 8.1+, PDO MySQL, cURL et Fileinfo.

## Pages

- `index.html` : formulaire d'inscription.
- `liste.html` : annuaire des profils.
- `../api/inscrire.php` : validation, upload Cloudinary et insertion MySQL.
- `../api/liste.php` : lecture des profils depuis MySQL.
