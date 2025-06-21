<?php
// pages/add_marche.php
include 'header.php';
?>

<!DOCTYPE html>
<html data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter un Marché - SIG Market</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOVYRIgupAurZup5y1PRh8Ismb1A3lLao"></script>
  <style>
    #carteId { height: 50vh; width: 100%; }
  </style>
</head>
<body class="min-h-screen bg-base-100 p-4">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-center mb-6">Ajouter un Marché</h1>

    <form id="addMarcheForm" enctype="multipart/form-data" class="space-y-4 bg-base-200 p-6 rounded-box shadow-lg">
      <div class="form-control">
        <label class="label" for="nom"><span class="label-text font-semibold">Nom</span></label>
        <input type="text" id="nom" name="nom" class="input input-bordered w-full" required>
      </div>
      <div class="form-control">
        <label class="label" for="surface"><span class="label-text font-semibold">Surface (m²)</span></label>
        <input type="number" id="surface" name="surface" class="input input-bordered w-full">
      </div>
      <div class="form-control">
        <label class="label" for="type_couverture"><span class="label-text font-semibold">Type de couverture</span></label>
        <input type="text" id="type_couverture" name="type_couverture" class="input input-bordered w-full">
      </div>
      <div class="form-control">
        <label class="label" for="jours_ouverts"><span class="label-text font-semibold">Jours ouverts</span></label>
        <input type="text" id="jours_ouverts" name="jours_ouverts" class="input input-bordered w-full">
      </div>
      <div class="form-control">
        <label class="label" for="description"><span class="label-text font-semibold">Description</span></label>
        <textarea id="description" name="description" class="textarea textarea-bordered w-full"></textarea>
      </div>
      <div class="form-control">
        <label class="label" for="photo"><span class="label-text font-semibold">Photo</span></label>
        <input type="file" id="photo" name="photo" class="file-input file-input-bordered w-full" accept="image/*">
      </div>
      <div class="form-control">
        <label class="label"><span class="label-text font-semibold">Emplacement</span></label>
        <div id="carteId" class="rounded-box"></div>
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
      </div>
      <button type="submit" class="btn btn-primary w-full">
        <i class="fas fa-save"></i> Ajouter
      </button>
    </form>
  </div>

  <script>
    let carte;
    let marker = null;

    function initializeMap() {
      const mapOptions = {
        center: new google.maps.LatLng(-18.8792, 47.5079), // Centré sur Madagascar
        zoom: 7,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };
      carte = new google.maps.Map(document.getElementById("carteId"), mapOptions);

      carte.addListener("click", function (event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();
        if (marker) marker.setMap(null);
        marker = new google.maps.Marker({
          position: { lat, lng },
          map: carte,
          icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
        });
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
      });
    }

    document.getElementById("addMarcheForm").addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const btn = this.querySelector("button[type='submit']");
      btn.innerHTML = '<span class="loading loading-spinner"></span> Enregistrement...';
      btn.disabled = true;

      fetch('../api/createMarche.php', {
        method: 'POST',
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            alert('Marché ajouté avec succès !');
            window.location.href = 'marches.php';
          } else {
            alert('Erreur : ' + data.message);
          }
        })
        .finally(() => {
          btn.innerHTML = '<i class="fas fa-save"></i> Ajouter';
          btn.disabled = false;
        });
    });

    google.maps.event.addDomListener(window, 'load', initializeMap);
  </script>
</body>
</html>

<?php
include 'footer.php';
?>