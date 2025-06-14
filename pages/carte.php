<?php
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
        mapTypeId: google.maps.MapTypeId.ROADMAP
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
          markersMarche.forEach(m => m.setMap(null));
          markersMarche = [];

          if (data.status === 'success') {
            data.data.forEach(marche => {
              const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
              const marker = new google.maps.Marker({
                position: { lat: parseFloat(lat), lng: parseFloat(lng) },
                map: carte,
                title: marche.nom
              });
              markersMarche.push(marker);
            });
          }
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
      markersMarche.forEach(m => m.setMap(null));
      markersMarche = [];
console.log(data);
      if (data.status === 'success') {
        data.resultat.forEach(marche => {
          const [lng, lat] = marche.geom.replace("POINT(", "").replace(")", "").split(" ");
          const marker = new google.maps.Marker({
            position: { lat: parseFloat(lat), lng: parseFloat(lng) },
            map: carte,
            title: marche.nom,
         
          });
          markersMarche.push(marker);
        });
      }
    });
}

    google.maps.event.addDomListener(window, 'load', initialize);
  </script>
</head>
<body>

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



  <div id="carteId"></div>

  <input type="hidden" id="lat">
  <input type="hidden" id="lng">

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
  </script>

</body>
</html>
