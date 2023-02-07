Exemples d’utilisation :

[wpversion type="latest"]

Affiche la plus récente version de WordPress. (ex. 6.1.1)

[wpversion type="validate" version="5.1"]

Valide le statut de la version WordPress en retournant l’une des valeurs suivantes: latest(green), outdated(orange), insecure(red). Les couleurs doivent être affichées selon le type de version.

[wpversion type="subversion" version="5.9"]

Affiche un tableau montrant la liste de toutes sous versions d’une version majeure.
[wpversion type="mine"]

Affiche la version de WordPress du site sur lequel est installée l’extension. Le code de couleur du shortcode de validation doit être utilisé lors de l’affichage de la version (green,orange,red).


- Une interface est disponible pour indiquer la date de dernière récupération des
données ainsi qu’un bouton pour mettre à jour les données sauvegardées.
- Les données récupérées doivent sont conservées pendant 24h avant de faire un nouvel appel à l’API