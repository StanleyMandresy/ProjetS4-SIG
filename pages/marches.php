<?php
// pages/marches.php
include 'header.php';
?>

<!DOCTYPE html>
<html data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Marchés - SIG Market</title>
    <style>
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen bg-base-100">
    <div class="container mx-auto px-4 py-6">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-primary">
                <i class="fas fa-store mr-2"></i>
                Gestion des Marchés
            </h1>
            <button onclick="openModal('add')" class="btn btn-primary shadcn-btn">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Marché
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-base-200 p-4 rounded-box shadow-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Rechercher</span>
                    </label>
                    <input type="text" id="searchInput" placeholder="Nom du marché..." 
                           class="input input-bordered" onkeyup="filterMarches()">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Type de couverture</span>
                    </label>
                    <select id="filterCouverture" class="select select-bordered" onchange="filterMarches()">
                        <option value="">Tous</option>
                        <option value="couvert">Couvert</option>
                        <option value="plein air">Plein air</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Surface</span>
                    </label>
                    <select id="filterSurface" class="select select-bordered" onchange="filterMarches()">
                        <option value="">Toutes</option>
                        <option value="0-500">0 - 500 m²</option>
                        <option value="500-1000">500 - 1000 m²</option>
                        <option value="1000-2000">1000 - 2000 m²</option>
                        <option value="2000+">Plus de 2000 m²</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        <div id="alertContainer"></div>

        <!-- Tableau des marchés -->
        <div class="bg-base-100 rounded-box shadow-lg overflow-hidden">
            <div class="table-container">
                <table class="table table-zebra w-full">
                    <thead class="sticky top-0 bg-base-200">
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Surface (m²)</th>
                            <th>Type</th>
                            <th>Jours d'ouverture</th>
                            <th>Coordonnées</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="marchesTableBody">
                        <!-- Les données seront chargées via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal d'ajout/modification -->
        <div id="marcheModal" class="modal">
            <div class="modal-box w-11/12 max-w-5xl">
                <h3 id="modalTitle" class="font-bold text-lg mb-4">
                    <i class="fas fa-store mr-2"></i>
                    Nouveau Marché
                </h3>
                
                <form id="marcheForm" class="space-y-4">
                    <input type="hidden" id="marcheId">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informations de base -->
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Nom du marché *</span>
                                </label>
                                <input type="text" id="nom" name="nom" required 
                                       class="input input-bordered" placeholder="Ex: Marché Analakely">
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Surface (m²)</span>
                                </label>
                                <input type="number" id="surface" name="surface" 
                                       class="input input-bordered" placeholder="Ex: 1500">
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Type de couverture</span>
                                </label>
                                <select id="type_couverture" name="type_couverture" class="select select-bordered">
                                    <option value="">Choisir...</option>
                                    <option value="couvert">Couvert</option>
                                    <option value="plein air">Plein air</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Jours d'ouverture</span>
                                </label>
                                <input type="text" id="jours_ouverts" name="jours_ouverts" 
                                       class="input input-bordered" 
                                       placeholder="Ex: lundi-mardi-mercredi">
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Photo</span>
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" 
                                       class="file-input file-input-bordered w-full">
                                <div id="currentPhoto" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Carte et coordonnées -->
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Localisation sur la carte</span>
                                </label>
                                <div id="modalMap" style="height: 300px; width: 100%;" class="rounded-box border"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Latitude</span>
                                    </label>
                                    <input type="number" step="any" id="latitude" name="latitude" 
                                           class="input input-bordered" placeholder="-18.9106">
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Longitude</span>
                                    </label>
                                    <input type="number" step="any" id="longitude" name="longitude" 
                                           class="input input-bordered" placeholder="47.5162">
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Description</span>
                                </label>
                                <textarea id="description" name="description" 
                                          class="textarea textarea-bordered h-24" 
                                          placeholder="Description du marché..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button type="button" onclick="closeModal()" class="btn btn-ghost">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary shadcn-btn">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div id="deleteModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-error">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirmer la suppression
                </h3>
                <p class="py-4">Êtes-vous sûr de vouloir supprimer ce marché ? Cette action est irréversible.</p>
                <div class="modal-action">
                    <button onclick="closeDeleteModal()" class="btn btn-ghost">Annuler</button>
                    <button onclick="confirmDelete()" class="btn btn-error">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOVYRIgupAurZup5y1PRh8Ismb1A3lLao"></script>
    <script>
        let map, marker;
        let deleteId = null;
        let marchesData = [];

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadMarches();
        });

        // Chargement des marchés
        async function loadMarches() {
            try {
                const response = await fetch('../api/getAllMarches.php');
                const data = await response.json();
                
                if (data.status === 'success') {
                    marchesData = data.data;
                    displayMarches(marchesData);
                } else {
                    showAlert('Erreur lors du chargement des marchés', 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion lors du chargement', 'error');
                console.error('Error:', error);
            }
        }

        // Affichage des marchés
        function displayMarches(marches) {
            const tbody = document.getElementById('marchesTableBody');
            tbody.innerHTML = '';

            marches.forEach(marche => {
                const row = document.createElement('tr');
                row.innerHTML = `
                   <td>
                        ${marche.photo_url 
                            ? `<img src="../${marche.photo_url}" alt="${marche.nom}" class="w-16 h-16 object-cover rounded-lg">`
                            : `<div class="w-16 h-16 bg-base-300 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-base-content opacity-50"></i>
                            </div>`
                        }
                    </td>
                    <td>
                        <div class="font-semibold">${marche.nom}</div>
                        ${marche.description ? `<div class="text-sm opacity-70">${marche.description}</div>` : ''}
                    </td>
                    <td>${marche.surface ? marche.surface + ' m²' : 'N/A'}</td>
                    <td>
                        <span class="badge ${marche.type_couverture === 'couvert' ? 'badge-primary' : 'badge-secondary'}">
                            ${marche.type_couverture || 'N/A'}
                        </span>
                    </td>
                    <td>${marche.jours_ouverts || 'N/A'}</td>
                    <td>
                        ${marche.latitude && marche.longitude ? 
                            `<div class="text-xs">
                                <div>Lat: ${parseFloat(marche.latitude).toFixed(4)}</div>
                                <div>Lng: ${parseFloat(marche.longitude).toFixed(4)}</div>
                            </div>` : 'N/A'
                        }
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <button onclick="editMarche(${marche.id})" class="btn btn-sm btn-info shadcn-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteMarche(${marche.id})" class="btn btn-sm btn-error shadcn-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Filtrage des marchés
        function filterMarches() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const couvertureFilter = document.getElementById('filterCouverture').value;
            const surfaceFilter = document.getElementById('filterSurface').value;

            const filtered = marchesData.filter(marche => {
                const matchesSearch = marche.nom.toLowerCase().includes(searchTerm);
                const matchesCouverture = !couvertureFilter || marche.type_couverture === couvertureFilter;
                
                let matchesSurface = true;
                if (surfaceFilter) {
                    const surface = parseFloat(marche.surface) || 0;
                    switch (surfaceFilter) {
                        case '0-500':
                            matchesSurface = surface <= 500;
                            break;
                        case '500-1000':
                            matchesSurface = surface > 500 && surface <= 1000;
                            break;
                        case '1000-2000':
                            matchesSurface = surface > 1000 && surface <= 2000;
                            break;
                        case '2000+':
                            matchesSurface = surface > 2000;
                            break;
                    }
                }

                return matchesSearch && matchesCouverture && matchesSurface;
            });

            displayMarches(filtered);
        }

        // Ouverture du modal
        function openModal(mode, id = null) {
            const modal = document.getElementById('marcheModal');
            const title = document.getElementById('modalTitle');
            
            if (mode === 'add') {
                title.innerHTML = '<i class="fas fa-plus mr-2"></i> Nouveau Marché';
                document.getElementById('marcheForm').reset();
                document.getElementById('marcheId').value = '';
                document.getElementById('currentPhoto').innerHTML = '';
            } else if (mode === 'edit' && id) {
                title.innerHTML = '<i class="fas fa-edit mr-2"></i> Modifier le Marché';
                loadMarcheData(id);
            }

            modal.classList.add('modal-open');
            
            // Initialiser la carte après ouverture du modal
            setTimeout(() => {
                initModalMap();
            }, 100);
        }

        // Fermeture du modal
        function closeModal() {
            document.getElementById('marcheModal').classList.remove('modal-open');
        }

        // Initialisation de la carte dans le modal
        function initModalMap() {
            const mapElement = document.getElementById('modalMap');
            if (!mapElement) return;

            map = new google.maps.Map(mapElement, {
                center: { lat: -18.8792, lng: 47.5079 }, // Centre de Madagascar
                zoom: 6,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            map.addListener('click', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                if (marker) marker.setMap(null);

                marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            });
        }

        // Chargement des données d'un marché pour modification
        async function loadMarcheData(id) {
            try {
                const response = await fetch(`../api/getMarcheById.php?id=${id}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const marche = data.data;
                    
                    document.getElementById('marcheId').value = marche.id;
                    document.getElementById('nom').value = marche.nom || '';
                    document.getElementById('surface').value = marche.surface || '';
                    document.getElementById('type_couverture').value = marche.type_couverture || '';
                    document.getElementById('jours_ouverts').value = marche.jours_ouverts || '';
                    document.getElementById('description').value = marche.description || '';
                    document.getElementById('latitude').value = marche.latitude || '';
                    document.getElementById('longitude').value = marche.longitude || '';
                    
                    // Afficher la photo actuelle
                    if (marche.photo_url) {
                        document.getElementById('currentPhoto').innerHTML = 
                            `<img src="${marche.photo_url}" alt="Photo actuelle" class="w-32 h-32 object-cover rounded-lg">`;
                    }
                    
                    // Placer le marker sur la carte
                    if (marche.latitude && marche.longitude) {
                        setTimeout(() => {
                            const lat = parseFloat(marche.latitude);
                            const lng = parseFloat(marche.longitude);
                            
                            map.setCenter({ lat: lat, lng: lng });
                            map.setZoom(10);
                            
                            if (marker) marker.setMap(null);
                            marker = new google.maps.Marker({
                                position: { lat: lat, lng: lng },
                                map: map,
                                icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                            });
                        }, 200);
                    }
                } else {
                    showAlert('Erreur lors du chargement des données du marché', 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion lors du chargement des données', 'error');
            }
        }

        // Modification d'un marché
        function editMarche(id) {
            openModal('edit', id);
        }

        // Suppression d'un marché
        function deleteMarche(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.add('modal-open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('modal-open');
            deleteId = null;
        }

        async function confirmDelete() {
            if (!deleteId) return;

            try {
                const response = await fetch('../api/deleteMarche.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: deleteId })
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert('Marché supprimé avec succès', 'success');
                    loadMarches();
                } else {
                    showAlert(data.message || 'Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion lors de la suppression', 'error');
            }

            closeDeleteModal();
        }

        // Soumission du formulaire
        document.getElementById('marcheForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = document.getElementById('marcheId').value;
            
            // Ajouter les coordonnées
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (lat) formData.append('latitude', lat);
            if (lng) formData.append('longitude', lng);

            try {
                const url = id ? '../api/updateMarche.php' : '../api/createMarche.php';
                if (id) formData.append('id', id);

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert(id ? 'Marché modifié avec succès' : 'Marché créé avec succès', 'success');
                    closeModal();
                    loadMarches();
                } else {
                    // Afficher l'erreur avec une durée plus longue
                    showAlert(data.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            } catch (error) {
                // Afficher l'erreur avec une durée plus longue
                showAlert('Erreur de connexion lors de l\'enregistrement', 'error');
                console.error('Error:', error);
            }
        });

        // Fonction d'affichage des alertes
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 
                             type === 'error' ? 'alert-error' : 'alert-info';
            
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} mb-4`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="btn btn-sm btn-ghost">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            alertContainer.appendChild(alert);
            
            // Durée d'affichage selon le type
            const autoRemoveTime = type === 'error' ? 60000 : 5000; // Erreurs: 15s, autres: 5s
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, autoRemoveTime);
        }
    </script>
</body>
</html>

<?php include 'footer.php'; ?>