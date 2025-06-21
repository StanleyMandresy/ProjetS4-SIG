<?php
// pages/edit_marche.php
include 'header.php';
require_once '../inc/fonction.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  header('Location: marches.php');
  exit;
}

$marche = getMarcheById($id);
if (!$marche) {
  header('Location: marches.php');
  exit;
}
?>

<!DOCTYPE html>
<html data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Marché - SIG Market</title>
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
    <h1 class="text-3xl font-bold text-center mb-6">Modifier Marché</h1>

    <form id="editMarcheForm" enctype="multipart/form-data" class="space-y-4 bg-base-200 p-6 rounded-box shadow-lg">
      <input type="hidden" name="id" value="<?php echo $marche['id']; ?>">
      <div class="form-control">
        <label class="label" for="nom"><span class="label-text font-semibold">Nom</span></label>
        <input type="text" id="nom" name="nom" class="input input-bordered w-full" value="<?php echo htmlspecialchars($marche['nom']); ?>" required>
      </div>
      <div class="form-control">
        <label class="label" for="surface"><span class="label-text font-semibold">Surface (m²)</span></label>
        <input type="number" id="surface" name="surface" class="input input-bordered w-full" value="<?php echo htmlspecialchars($marche['surface']); ?>">
      </div>
      <div class="form-control">
        <label class="label" for="type_couverture"><span class="label-text font-semibold">Type de couverture</span></label>
        <input type="text" id="type_couverture" name="type_couverture" class="input input-bordered w-full" value="<?php echo htmlspecialchars($marche['type_couverture']); ?>">
      </div>
      <div class="form-control">
        <label class="label" for="jours_ouverts"><span class="label-text font-semibold">Jours ouverts</span></label>
        <input type="text" id="jours_ouverts" name="jours_ouverts" class="input input-bordered w-full" value="<?php echo htmlspecialchars($marche['jours_ouverts']); ?>">
      </div>
      <div class="form-control">
        <label class="label" for="description"><span class="label-text font-semibold">Description</span></label>
        <textarea id="description" name="description" class="textarea textarea-bordered w-full"><?php echo htmlspecialchars($marche['description']); ?></textarea>
      </div>
      <div class="form-control">
        <label class="label" for="photo"><span class="label-text font-semibold">Photo</span></label>
        <?php if ($marche['photo_url']) { ?>
          <img src="<?php echo htmlspecialchars($marche['photo_url']); ?>" alt="Photo du marché" class="w-32 h-32 object-cover mb-2">
        <?php } ?>
        <input type="file" id="photo" name="photo" class="file-input file-input-bordered w-full" accept="image/*">
      </div>
      <div class="form-control">
        <label class="label"><span class="label-text font-semibold">Emplacement</span></label>
        <div id="carteId" class="rounded-box"></div>
        <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($marche['latitude']); ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($marche['longitude']); ?>">
      </div>
      <button type="submit" class="btn btn-primary w-full">
        <i class="fas fa-save"></i> Mettre à jour
      </button>
    </form>
  </div>

  <script>
    let carte;
    let marker = null;

    function initializeMap() {
      const mapOptions = {
        center: new google.maps.LatLng(<?php echo $marche['latitude'] ?: -18.8792; ?>, <?php echo $marche['longitude'] ?: 47.5079; ?>),
        zoom: <?php echo $marche['latitude'] ? 12 : 7; ?>,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };
      carte = new google.maps.Map(document.getElementById("carteId"), mapOptions);

      <?php if ($marche['latitude'] && $marche['longitude']) { ?>
        marker = new google.maps.Marker({
          position: { lat: <?php echo $marche['latitude']; ?>, lng: <?php echo $marche['longitude']; ?> },
          map: carte,
          icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
        });
      <?php } ?>

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

    document.getElementById("editMarcheForm").addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const btn = this.querySelector("button[type='submit']");
      btn.innerHTML = '<span class="loading loading-spinner"></span> Mise à jour...';
      btn.disabled = true;

      fetch('../api/updateMarche.php', {
        method: 'POST',
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            alert('Marché mis à jour avec succès !');
            window.location.href = 'marches.php';
          } else {
            alert('Erreur : ' + data.message);
          }
        })
        .finally(() => {
          btn.innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
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