# Atelier 7 - GitLab CI/CD + SonarQube Cloud

Ce dossier contient le mini-site V1 et son pipeline GitLab Pages. La V2 vulnérable est séparée dans le dossier v2/ pour l'exercice de détection XSS.

## 1. Créer le projet GitLab

1. Crée un projet GitLab vierge nommé atelier-sonarqube.
2. Clone son URL HTTPS sur ton ordinateur.
3. Copie le contenu de ce dossier à la racine du dépôt cloné. Inclue le fichier caché .gitlab-ci.yml et n'inclus pas le dossier v2/.
4. Dans sonar-project.properties, remplace REPLACE_WITH_YOUR_SONAR_PROJECT_KEY et REPLACE_WITH_YOUR_SONAR_ORGANIZATION_KEY par les valeurs affichées dans ton propre projet SonarQube Cloud.

Les clés de l'exemple du PDF appartiennent à son auteur. Ne les recopie pas dans ton projet.

## 2. Préparer SonarQube Cloud

1. Connecte-toi à SonarQube Cloud avec GitLab et importe le projet GitLab.
2. Si Automatic Analysis est activé, désactive-le pour utiliser l'analyse par GitLab CI/CD.
3. Crée un token SonarQube Cloud, puis copie-le tout de suite : il ne sera plus affiché ensuite.
4. Dans GitLab, ouvre Settings > CI/CD > Variables et ajoute :
   - SONAR_HOST_URL = https://sonarcloud.io
   - SONAR_TOKEN = ton token, masqué. Ne l'ajoute jamais aux fichiers du dépôt.

## 3. Envoyer la V1 correcte

Pousse les fichiers V1 sur la branche par défaut (main). Le pipeline contient exactement deux jobs :

1. sonarqube-check analyse le code et attend le résultat du Quality Gate.
2. deploy-pages publie le mini-site dans GitLab Pages si l'analyse réussit.

Il n'y a pas de job séparé validate ou de test automatisé dans la V1. Si SonarQube indique qu'Automatic Analysis est activé, désactive-le dans les réglages du projet SonarQube Cloud puis relance le pipeline.

## 4. Passer à la V2 de démonstration

La V2 est volontairement vulnérable à une injection DOM XSS. Pour l'exercice uniquement :

1. Copie le contenu de v2/ à la racine de ton dépôt GitLab, en remplaçant les fichiers V1.
2. Dans sonar-project.properties et .gitlab-ci.yml, remplace REPLACE_WITH_YOUR_SONAR_PROJECT_KEY par la clé réelle de ton projet.
3. Pousse la V2 sur la branche par défaut et observe les jobs ainsi que le dashboard SonarQube Cloud.

Exemple de saisie de démonstration dans la page V2 :

    <img src=x onerror="document.body.insertAdjacentHTML('beforeend','<h2>Page modifiée par la saisie</h2>')">

La V2 est exclue du déploiement Vercel du portfolio. Ne l'héberge pas comme site public et n'utilise pas de données réelles. Le job security-check doit échouer s'il trouve des vulnérabilités ou des hotspots qui nécessitent une revue; les étapes suivantes du pipeline ne s'exécutent alors.
