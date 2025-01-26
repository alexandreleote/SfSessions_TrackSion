export function initStats() {
    // Fonction pour mettre à jour les statistiques des sessions
    function updateSessionStats() {
      const currentSessions = document.querySelectorAll('[data-session-type="current"]').length;
      const nextSessions = document.querySelectorAll('[data-session-type="next"]').length;
      const pastSessions = document.querySelectorAll('[data-session-type="past"]').length;
      
      // Mise à jour des compteurs si présents
      const stats = {
        current: document.querySelector('.stats-current'),
        next: document.querySelector('.stats-next'),
        past: document.querySelector('.stats-past')
      };
      
      if (stats.current) stats.current.textContent = currentSessions;
      if (stats.next) stats.next.textContent = nextSessions;
      if (stats.past) stats.past.textContent = pastSessions;
    }
    
    // Initialisation des stats
    updateSessionStats();
}