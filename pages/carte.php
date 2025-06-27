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
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> -->
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
          clearMarkers();
        
          if (data.status === 'success') {
              data.data.forEach(marche => {
                  const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
                  
                  const marcheData = {
                      ...marche,
                      latitude: parseFloat(lat),
                      longitude: parseFloat(lng)
                  };

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
          clearMarkers();
          
          if (data.status === 'success') {
              data.resultat.forEach(marche => {
                  const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
                  
                  const marcheData = {
                      ...marche,
                      latitude: parseFloat(lat),
                      longitude: parseFloat(lng)
                  };

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
      
      loadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Chargement...';
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

    function clearMarkers() {
      markersMarche.forEach(m => m.setMap(null));
      markersMarche = [];
      infoWindows = [];
    }

    google.maps.event.addDomListener(window, 'load', initialize);
  </script>
</head>
<body class="bg-gray-50">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Bienvenue <?= htmlspecialchars($_SESSION['pseudo']) ?> !</h1>
   <!-- Version améliorée avec meilleures pratiques -->
<a href="../api/logout.php" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
  Se déconnecter
</a>
    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Left column for forms -->
      <div class="w-full lg:w-1/3 space-y-6">
        <!-- Toolbar -->
        <div class="bg-white p-4 rounded-lg shadow">
          <div class="left-buttons">
            <button onclick="loadAllMarches()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-200">
              <i class="fas fa-map-marker-alt mr-2"></i> Afficher tous les marchés
            </button>
          </div>
        </div>

        <!-- Recherche standard -->
        <form onsubmit="event.preventDefault(); getMarcheBy();" class="bg-white p-4 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4 text-gray-700">Recherche standard</h2>
          
          <div class="mb-4">
            <label for="critere" class="block text-sm font-medium text-gray-700 mb-1">Critère :</label>
            <select id="critere" name="critere" onchange="toggleSurface();" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
              <option value="nom">Nom</option>
              <option value="type_couverture">Type couverture</option>
              <option value="jour_ouverture">Jour d'ouverture</option>
              <option value="produit_vendu">Produit vendu</option>
            </select>
          </div>

          <div id="valeurInput" class="mb-4">
            <label for="valeur" class="block text-sm font-medium text-gray-700 mb-1">Valeur :</label>
            <input type="text" id="valeur" name="valeur" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div id="produits" class="mb-4 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-1">Produits :</label>
            <div class="space-y-2">
                  <label class="flex items-center">
                     <input type="checkbox" name="produit[]" value="Legumes" class="mr-2">
                    <span>Légumes</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Fruits" class="mr-2">
                    <span>Fruits</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Viande" class="mr-2">
                    <span>Viande</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Poisson" class="mr-2">
                    <span>Poisson</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Volaille" class="mr-2">
                    <span>Volaille</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Artisanat" class="mr-2">
                    <span>Artisanat</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Fleur" class="mr-2">
                    <span>Fleur</span>
                  </label>
                   <label class="flex items-center">
                    <input type="checkbox" name="produit[]" value="Boeuf" class="mr-2">
                    <span>Boeuf</span>
                  </label>



       
            </div>
          </div>

          <div id="surface" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Surface entre :</label>
            <div class="flex gap-2">
              <input type="number" id="surfaceMin" placeholder="Min" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
              <span class="self-center">-</span>
              <input type="number" id="surfaceMax" placeholder="Max" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-200">
            Rechercher
          </button>
        </form>

        <!-- Recherche spatiale -->
        <form onsubmit="event.preventDefault(); findMarcheBy();" class="bg-blue-50 p-4 rounded-lg shadow border border-blue-100">
          <h2 class="text-lg font-semibold mb-4 text-gray-700">Recherche spatiale</h2>
          
          <div class="mb-4">
            <label for="critereSpatial" class="block text-sm font-medium text-gray-700 mb-1">Type de recherche :</label>
            <select id="critereSpatial" name="critereSpatial" onchange="toggleSpatial();" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
              <option value="district">District</option>
              <option value="region">Région</option>
              <option value="commune">Commune</option>
              <option value="province">Province</option>
              <option value="rayon">Rayon autour du point cliqué</option>
              <option value="near">Plus proche du point cliqué</option>
            </select>
          </div>

          <div id="zoneInput" class="mb-4">
            <label for="nomZone" class="block text-sm font-medium text-gray-700 mb-1">Nom de la zone :</label>
           <select id="nomZone" name="nomZone" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></select> 
          </div>

          <div id="rayonInput" class="mb-4 hidden">
            <label for="rayon" class="block text-sm font-medium text-gray-700 mb-1">Rayon en kilomètres :</label>
            <input type="number" id="rayon" name="rayon" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
          </div>

          <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-200">
            Rechercher (spatial)
          </button>
        </form>
      </div>

      <!-- Right column for map -->
      <div class="w-full lg:w-2/3">
        <div id="carteId" class="w-full h-[600px] rounded-lg shadow-lg border border-gray-200"></div>
        <input type="hidden" id="lat">
        <input type="hidden" id="lng">
      </div>
    </div>
  </div>

  <script>
    function toggleSurface() {
      const critere = document.getElementById("critere").value;
      document.getElementById("produits").classList.toggle("hidden", critere !== "produit_vendu");
      document.getElementById("valeurInput").classList.toggle("hidden", critere === "produit_vendu");
    }

    function toggleSpatial() {
      const critere = document.getElementById("critereSpatial").value;
      document.getElementById("zoneInput").classList.toggle("hidden", !['district','region','commune','province'].includes(critere));
      document.getElementById("rayonInput").classList.toggle("hidden", critere !== 'rayon');
       if (['district','region','commune','province'].includes(critere)) {
        fetch(`../api/getZones.php?type=${critere}`)
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById("nomZone");
            select.innerHTML = ""; // Vider les anciennes options
            data.forEach(zone => {
              const option = document.createElement("option");
              option.value = zone;
              option.textContent = zone;
              select.appendChild(option);
            });
          });
      }
    }

    function createInfoWindow(marche) {
      return new google.maps.InfoWindow({
        content: `
          <div class="min-w-[200px] p-2 font-sans">
            <h3 class="m-0 mb-2 text-blue-600 text-base border-b border-gray-200 pb-1">
              ${marche.nom}
            </h3>
            <div class="mb-2">
              <strong>Surface:</strong> ${marche.surface || 'N/A'} m²
            </div>
            <div class="mb-2">
              <strong>Type:</strong> ${marche.type_couverture || 'N/A'}
            </div>
            <div class="mb-2">
              <strong>Ouverture:</strong> ${marche.jours_ouverts || 'N/A'}
            </div>
            ${marche.description ? `
              <div class="mb-2 italic">
                "${marche.description}"
              </div>
            ` : ''}
            ${marche.photo_url ? `
              <div class="mt-2">
                <img src="../${marche.photo_url}" 
                     alt="Photo du marché"
                     class="max-w-full max-h-[150px] rounded border border-gray-200">
              </div>
            ` : ''}
          </div>
        `
      });
    }

    function addMarcheMarker(marche) {
      const lat = parseFloat(marche.latitude);
      const lng = parseFloat(marche.longitude);
      if (isNaN(lat) || isNaN(lng)) return null;

      const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: carte,
        title: marche.nom,
        icon: {
          url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
          scaledSize: new google.maps.Size(32, 32)
        }
      });

      const infoWindow = createInfoWindow(marche);

      marker.addListener('click', () => {
        markersMarche.forEach(m => {
          if (m.infoWindow) m.infoWindow.close();
        });
        infoWindow.open(carte, marker);
      });

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