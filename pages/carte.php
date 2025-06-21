<?php
session_start();

include 'header.php';


if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <style type="text/css">
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: sans-serif;
    }
    #carteId {
      height: 70%;
    }
    form {
      padding: 10px;
      background-color: #f5f5f5;
    }
    label {
      display: block;
      margin-top: 5px;
    }
 
  </style>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOVYRIgupAurZup5y1PRh8Ismb1A3lLao"></script>

  <script>
    let carte;
    let marker = null;
    let markersMarche = [];

       function initialize() {
      const mapOptions = {
        center: new google.maps.LatLng(-18.8792, 47.5079),
        zoom: 7,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
          {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#444444"}]
          },
          {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [{"color": "#f2f2f2"}]
          },
          {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [{"visibility": "off"}]
          },
          {
            "featureType": "road",
            "elementType": "all",
            "stylers": [{"saturation": -100}, {"lightness": 45}]
          },
          {
            "featureType": "road.highway",
            "elementType": "all",
            "stylers": [{"visibility": "simplified"}]
          },
          {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [{"visibility": "off"}]
          },
          {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [{"visibility": "off"}]
          },
          {
            "featureType": "water",
            "elementType": "all",
            "stylers": [{"color": "#d4e6f4"}, {"visibility": "on"}]
          }
        ]
      };

      carte = new google.maps.Map(document.getElementById("carteId"), mapOptions);

      carte.addListener("click", function (event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();

        if (marker) marker.setMap(null);

        marker = new google.maps.Marker({
          position: { lat: lat, lng: lng },
          map: carte,
          icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
        });

        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;
      });
    }

    function getMarcheBy() {
      const critere = document.getElementById("critere").value;
      let valeur = document.getElementById("valeur").value;
      const surfaceMin = document.getElementById("surfaceMin").value;
      const surfaceMax = document.getElementById("surfaceMax").value;

      if (critere === "produit_vendu") {
        // Récupérer les produits cochés
        const produits = [];
        document.querySelectorAll("input[name='produit[]']:checked").forEach(cb => {
          produits.push(cb.value);
        });
        valeur = JSON.stringify(produits);
      }

      const url = `../api/getMarcheBy.php?critere=${critere}&valeur=${encodeURIComponent(valeur)}&surfaceMin=${surfaceMin}&surfaceMax=${surfaceMax}`;

    fetch(url)
    .then(res => res.json())
    .then(data => {
        // Effacer les marqueurs existants
        clearMarkers();
      
        if (data.status === 'success') {
            data.data.forEach(marche => {
                // Extraire les coordonnées depuis geom
                const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
                
                // Préparer l'objet marché avec les bonnes propriétés
                const marcheData = {
                    ...marche,
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lng)
                };

                // Utiliser la fonction existante addMarcheMarker
                addMarcheMarker(marcheData);
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des données');
    });
  }
function findMarcheBy() {
  const critere = document.getElementById("critereSpatial").value;
  const nomZone = document.getElementById("nomZone").value;
  const rayon = document.getElementById("rayon").value;
  const lat = document.getElementById("lat").value;
  
  const lng = document.getElementById("lng").value;
 alert(`lat et lng: ${lat} - ${lng}`);

  let url = `../api/findMarcheBy.php?critereSpatial=${critere}`;

  if (critere === 'rayon') {
    url += `&valeur=${rayon}&lat=${lat}&lng=${lng}`;
  } else if (critere === 'near') {
    url += `&lat=${lat}&lng=${lng}`;
  } else {
    url += `&valeur=${encodeURIComponent(nomZone)}`;
  }

 fetch(url)
    .then(res => res.json())
    .then(data => {
        // Effacer les marqueurs existants
        clearMarkers();
        
      

        if (data.status === 'success') {
            data.resultat.forEach(marche => {
                // Extraire les coordonnées depuis geom
                const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
                
                // Préparer l'objet marché avec les bonnes propriétés
                const marcheData = {
                    ...marche,
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lng)
                };

                // Utiliser la fonction existante addMarcheMarker
                addMarcheMarker(marcheData);
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des données');
    });
}
function loadAllMarches() {
    const loadButton = document.querySelector('.left-buttons button');
    const originalHTML = loadButton.innerHTML;
    
    loadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
    loadButton.disabled = true;

    fetch('../api/getAllMarches.php')
        .then(res => res.json())
        .then(data => {
            clearMarkers();
            
            if (data.status === 'success') {
                data.data.forEach(marche => {
                    addMarcheMarker(marche);
                });
                
                if (markersMarche.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markersMarche.forEach(m => bounds.extend(m.getPosition()));
                    carte.fitBounds(bounds, {padding: 20});
                }
            }
        })
        .finally(() => {
            loadButton.innerHTML = originalHTML;
            loadButton.disabled = false;
        });
}

// Fonction pour effacer tous les marqueurs
function clearMarkers() {
    markersMarche.forEach(m => m.setMap(null));
    markersMarche = [];
    infoWindows = [];
}

 google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
  <h1>Bienvenue <?= htmlspecialchars($_SESSION['pseudo']) ?> !</h1>
<p><a href="../api/logout.php">Se déconnecter</a></p>
  <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background-color: #f5f5f5; border-radius: 5px; margin-bottom: 10px;">
  <div class="left-buttons">
    <button onclick="loadAllMarches()" class="btn btn-primary">
      <i class="fas fa-map-marker-alt"></i> Afficher tous les marchés
    </button>
  </div>
  


  <form onsubmit="event.preventDefault(); getMarcheBy();">
    <label for="critere">Critère :</label>
    <select id="critere" name="critere" onchange="toggleSurface();">
      <option value="nom">Nom</option>
      <option value="type_couverture">Type couverture</option>
      <option value="jour_ouverture">Jour d'ouverture</option>
      <option value="produit_vendu">Produit vendu</option>
    </select>

    <label for="valeur">Valeur :</label>
    <input type="text" id="valeur" name="valeur">

    <div id="produits" style="display:none;">
      <label>Produits :</label>
      <label><input type="checkbox" name="produit[]" value="Légumes"> Légumes</label>
      <label><input type="checkbox" name="produit[]" value="Viande"> Viande</label>
      <label><input type="checkbox" name="produit[]" value="Poisson"> Poisson</label>
      <!-- Ajoute les autres produits ici -->
    </div>

    <div id="surface">
      <label>Surface entre :</label>
      <input type="number" id="surfaceMin" placeholder="Min"> -
      <input type="number" id="surfaceMax" placeholder="Max">
    </div>

    <button type="submit">Rechercher</button>
  </form>

<form onsubmit="event.preventDefault(); findMarcheBy();" style="margin-top:20px; background-color: #e8f4ff;">
  <label for="critereSpatial">Recherche spatiale :</label>
  <select id="critereSpatial" name="critereSpatial" onchange="toggleSpatial();">
    <option value="district">District</option>
    <option value="region">Région</option>
    <option value="commune">Commune</option>
    <option value="province">Province</option>
    <option value="rayon">Rayon autour du point cliqué</option>
    <option value="near">Plus proche du point cliqué</option>
  </select>

  <div id="zoneInput">
    <label for="nomZone">Nom de la zone :</label>
    <input type="text" id="nomZone" name="nomZone">
  </div>

  <div id="rayonInput" style="display:none;">
    <label for="rayon">Rayon en kilomètres :</label>
    <input type="number" id="rayon" name="rayon">
  </div>

  <button type="submit">Rechercher (spatial)</button>
</form>
 
  <!-- Votre carte et champs cachés existants... -->
  <div id="carteId" style="height: 500px; width: 100%; margin-top: 20px;"></div>
  <input type="hidden" id="lat">
  <input type="hidden" id="lng">

  <!-- Styles pour les modals -->
  
  </style>


  <script>
    function toggleSurface() {
      const critere = document.getElementById("critere").value;
      document.getElementById("produits").style.display = (critere === "produit_vendu") ? "block" : "none";
      document.getElementById("valeur").style.display = (critere === "produit_vendu") ? "none" : "block";
    }
    function toggleSpatial() {
  const critere = document.getElementById("critereSpatial").value;
  document.getElementById("zoneInput").style.display = ['district','region','commune','province'].includes(critere) ? 'block' : 'none';
  document.getElementById("rayonInput").style.display = (critere === 'rayon') ? 'block' : 'none';
}
function createInfoWindow(marche) {
    return new google.maps.InfoWindow({
        content: `
            <div style="min-width: 200px; padding: 10px; font-family: Arial, sans-serif;">
                <h3 style="margin: 0 0 10px 0; color: #1a73e8; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    ${marche.nom}
                </h3>
                <div style="margin-bottom: 8px;">
                    <strong>Surface:</strong> ${marche.surface || 'N/A'} m²
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Type:</strong> ${marche.type_couverture || 'N/A'}
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Ouverture:</strong> ${marche.jours_ouverts || 'N/A'}
                </div>
                ${marche.description ? `
                    <div style="margin-bottom: 8px; font-style: italic;">
                        "${marche.description}"
                    </div>
                ` : ''}
                ${marche.photo_url ? `
                    <div style="margin-top: 10px;">
                        <img src="../${marche.photo_url}" 
                             alt="Photo du marché"
                             style="max-width: 100%; max-height: 150px; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                ` : ''}
            </div>
        `
    });
}
function addMarcheMarker(marche) {
    // Vérification des coordonnées
    const lat = parseFloat(marche.latitude);
    const lng = parseFloat(marche.longitude);
    if (isNaN(lat) || isNaN(lng)) return null;

    // Création du marqueur
    const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: carte,
        title: marche.nom,
        icon: {
            url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
            scaledSize: new google.maps.Size(32, 32)
        }
    });

    // Création de l'infobulle
    const infoWindow = createInfoWindow(marche);

    // Gestion du clic
    marker.addListener('click', () => {
        // Fermer toutes les autres infobulles
        markersMarche.forEach(m => {
            if (m.infoWindow) m.infoWindow.close();
        });
        
        // Ouvrir l'infobulle actuelle
        infoWindow.open(carte, marker);
    });

    // Stocker l'infobulle avec le marqueur
    marker.infoWindow = infoWindow;
    markersMarche.push(marker);
    
    return marker;
}
   </script>

</body>
</html>

<?php
include 'footer.php';
?>