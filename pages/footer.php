<?php
// footer.php
?>
    </main> <!-- Fermeture du main ouvert dans le header -->

    <footer class="bg-base-200 border-t border-base-300 mt-12">
      <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          
          <!-- Colonne Logo et description -->
          <div class="space-y-4">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <span class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">SIG Market</span>
            </div>
            <p class="text-sm opacity-80">Système d'Information Géographique pour la gestion et la visualisation des marchés locaux.</p>
            
            <!-- Réseaux sociaux -->
            <div class="flex gap-4 pt-2">
              <a href="#" class="btn btn-ghost btn-circle btn-sm shadcn-btn">
                <i class="fab fa-facebook-f text-blue-600"></i>
              </a>
              <a href="#" class="btn btn-ghost btn-circle btn-sm shadcn-btn">
                <i class="fab fa-twitter text-sky-400"></i>
              </a>
              <a href="#" class="btn btn-ghost btn-circle btn-sm shadcn-btn">
                <i class="fab fa-instagram text-pink-600"></i>
              </a>
              <a href="#" class="btn btn-ghost btn-circle btn-sm shadcn-btn">
                <i class="fab fa-linkedin-in text-blue-700"></i>
              </a>
            </div>
          </div>

          <!-- Liens rapides -->
          <div>
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
              <i class="fas fa-link text-secondary"></i>
              Liens rapides
            </h3>
            <ul class="space-y-2">
              <li><a href="index.php" class="link link-hover flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-60"></i> Accueil</a></li>
              <li><a href="cartes.php" class="link link-hover flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-60"></i> Cartes interactives</a></li>
              <li><a href="stats.php" class="link link-hover flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-60"></i> Statistiques</a></li>
              <li><a href="marches.php" class="link link-hover flex items-center gap-2"><i class="fas fa-chevron-right text-xs opacity-60"></i> Liste des marchés</a></li>
            </ul>
          </div>

          <!-- Catégories -->
          <div>
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
              <i class="fas fa-tags text-primary"></i>
              Resources 
            </h3>
            <ul class="space-y-2">
              <li><a href="marches.php?categorie=alimentation" class="link link-hover flex items-center gap-2"><i class="fas fa-shopping-basket text-green-500 text-sm"></i> Blog</a></li>
              <li><a href="marches.php?categorie=textile" class="link link-hover flex items-center gap-2"><i class="fas fa-tshirt text-blue-500 text-sm"></i> Help Center </a></li>
              <li><a href="marches.php?categorie=artisanat" class="link link-hover flex items-center gap-2"><i class="fas fa-hammer text-amber-500 text-sm"></i> FAQ</a></li>
              <li><a href="marches.php?categorie=autres" class="link link-hover flex items-center gap-2"><i class="fas fa-ellipsis-h text-gray-500 text-sm"></i> Autres</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
              <i class="fas fa-envelope text-accent"></i>
              Contactez-nous
            </h3>
            <address class="not-italic space-y-2">
              <div class="flex items-start gap-2">
                <i class="fas fa-map-marker-alt mt-1 text-red-500"></i>
                <span>IT University, Andoharanofotsy</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-phone text-green-500"></i>
                <a href="tel:+1234567890" class="link link-hover">+261 38 02 267 89</a>
              </div>
              <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-blue-500"></i>
                <a href="mailto:contact@sigmarket.com" class="link link-hover">contact@sigmarket.com</a>
              </div>
            </address>

            <!-- Newsletter -->
            <div class="mt-4">
              <h4 class="font-semibold mb-2 flex items-center gap-2">
                <i class="fas fa-paper-plane text-info"></i>
                Newsletter
              </h4>
              <form class="join">
                <input type="email" placeholder="Votre email" class="input input-bordered join-item w-full max-w-xs" required />
                <button class="btn btn-primary join-item shadcn-btn">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Copyright et liens légaux -->
        <div class="border-t border-base-300 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
          <div class="text-sm opacity-80">
            © <?php echo date('Y'); ?> SIG Market. Tous droits réservés.
          </div>
          <div class="flex gap-4 mt-4 md:mt-0">
            <a href="confidentialite.php" class="link link-hover text-sm">Confidentialité</a>
            <a href="conditions.php" class="link link-hover text-sm">Conditions d'utilisation</a>
            <a href="mentions.php" class="link link-hover text-sm">Mentions légales</a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Bouton retour en haut -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            class="btn btn-circle btn-primary fixed bottom-6 right-6 shadow-lg shadcn-btn"
            aria-label="Retour en haut">
      <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts JS -->
    <script>
      // Animation pour le bouton retour en haut
      const scrollToTopBtn = document.querySelector('[aria-label="Retour en haut"]');
      
      window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
          scrollToTopBtn.classList.remove('opacity-0', 'invisible');
          scrollToTopBtn.classList.add('opacity-100', 'visible');
        } else {
          scrollToTopBtn.classList.remove('opacity-100', 'visible');
          scrollToTopBtn.classList.add('opacity-0', 'invisible');
        }
      });

      // Initialisation des tooltips (si vous utilisez des tooltips)
      document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggers = document.querySelectorAll('[data-tip]');
        tooltipTriggers.forEach(trigger => {
          trigger.addEventListener('mouseenter', function() {
            const tooltip = this.nextElementSibling;
            if (tooltip && tooltip.classList.contains('tooltip')) {
              tooltip.classList.remove('invisible', 'opacity-0');
              tooltip.classList.add('visible', 'opacity-100');
            }
          });
          trigger.addEventListener('mouseleave', function() {
            const tooltip = this.nextElementSibling;
            if (tooltip && tooltip.classList.contains('tooltip')) {
              tooltip.classList.remove('visible', 'opacity-100');
              tooltip.classList.add('invisible', 'opacity-0');
            }
          });
        });
      });
    </script>
  </body>
</html>