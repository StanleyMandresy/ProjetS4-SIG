<?php
// header.php
?>
<!DOCTYPE html>
<html data-theme="light" lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIG - Système d'Information Géographique</title>
  <!-- DaisyUI CSS -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Animation pour le dropdown */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .dropdown-animation {
      animation: fadeIn 0.2s ease-out forwards;
    }
    
    /* Style personnalisé inspiré de ShadCN */
    .shadcn-btn {
      @apply transition-all duration-200 ease-in-out;
    }
    .shadcn-btn:hover {
      @apply transform scale-[1.03];
    }
    .shadcn-card {
      @apply border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200;
    }
  </style>
</head>
<body>
  <header class="bg-base-100 border-b border-base-200 sticky top-0 z-50">
    <div class="container mx-auto px-4">
      <div class="navbar">
        <!-- Logo -->
        <div class="flex-1">
          <a href="index.php" class="btn btn-ghost normal-case text-xl shadcn-btn">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <span class="font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">SIG Market</span>
            </div>
          </a>
        </div>

        <!-- Navigation principale -->
        <div class="flex-none hidden lg:flex">
          <ul class="menu menu-horizontal px-1 gap-1">
            <!-- Accueil -->
            <li>
              <a href="carte.php" class="shadcn-btn flex items-center gap-2">
                <i class="fas fa-home"></i>
                Accueil
              </a>
            </li>
            
            <!-- Dropdown Catégories -->
            
            
            <!-- Cartes -->
            <li>
              <a href="carte.php" class="shadcn-btn flex items-center gap-2">
                <i class="fas fa-map-marked-alt"></i>
                Cartes
              </a>
            </li>
            

           <!-- Marche -->
            <li>
              <a href="marches.php" class="shadcn-btn flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i>
                Marches
              </a>
            </li>
            
            <!-- Info (dropdown) -->
            <li>
              <details>
                <summary class="shadcn-btn">
                  <i class="fas fa-user"></i>    
                  <?= htmlspecialchars($_SESSION['pseudo']) ?>
                </summary>
                <ul class="p-2 bg-base-100 rounded-box shadow-lg dropdown-animation z-50 w-64">
                
                  <li>
                    <a href="../api/logout.php" class="flex items-center gap-2">
                      <i class="fas fa-sign-out-alt text-primary"></i>
                      Se deconnecter
                    </a>
                  </li>
                </ul>
              </details>
            </li>
          </ul>
        </div>

        <!-- Mobile menu button -->
        <div class="flex-none lg:hidden">
          <label for="mobile-menu" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div class="lg:hidden">
      <input type="checkbox" id="mobile-menu" class="drawer-toggle" />
      <div class="drawer-side z-50">
        <label for="mobile-menu" class="drawer-overlay"></label>
        <ul class="menu p-4 w-80 h-full bg-base-100 text-base-content">
          <!-- Logo mobile -->
          <li class="mb-4">
            <a href="index.php" class="flex items-center gap-2 text-xl font-bold">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">SIG Market</span>
            </a>
          </li>
          
          <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
          
          <!-- Catégories mobile -->
          <li class="menu-title">
            <span><i class="fas fa-layer-group"></i> Catégories</span>
          </li>
          <li><a href="marches.php?categorie=alimentation"><i class="fas fa-shopping-basket text-green-500"></i> Alimentation</a></li>
          <li><a href="marches.php?categorie=textile"><i class="fas fa-tshirt text-blue-500"></i> Textile</a></li>
          <li><a href="marches.php?categorie=artisanat"><i class="fas fa-hammer text-amber-500"></i> Artisanat</a></li>
          <li><a href="marches.php?categorie=autres"><i class="fas fa-ellipsis-h text-gray-500"></i> Autres</a></li>
          
          <li><a href="cartes.php"><i class="fas fa-map-marked-alt"></i> Cartes</a></li>
          <li><a href="stats.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
          
          <!-- Info mobile -->
          <li class="menu-title">
            <span><i class="fas fa-info-circle"></i> Informations</span>
          </li>
          <li><a href="apropos.php"><i class="fas fa-question-circle text-info"></i> À propos</a></li>
          <li><a href="contact.php"><i class="fas fa-envelope text-secondary"></i> Contact</a></li>
          <li><a href="aide.php"><i class="fas fa-life-ring text-primary"></i> Aide & FAQ</a></li>
        </ul>
      </div>
    </div>
  </header>

  <main class="container mx-auto px-4 py-6">