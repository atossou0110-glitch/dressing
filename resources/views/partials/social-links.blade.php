<!-- Social Links Navigation -->
<nav class="social-links-nav" aria-label="Réseaux sociaux">
    <a href="https://facebook.com/kingrangement" 
       target="_blank" 
       rel="noopener noreferrer"
       class="social-link"
       aria-label="Facebook">
        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>
    <a href="mailto:contact@kingrangement.com" 
       class="social-link"
       aria-label="Gmail">
        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
        </svg>
    </a>
    <a href="https://wa.me/22967844280" 
       target="_blank" 
       rel="noopener noreferrer"
       class="social-link whatsapp-link"
       aria-label="WhatsApp">
        <svg class="social-icon whatsapp-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181 0 6.167 1.239 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.455L.057 24zm11.892-22.27c-6.065 0-11 4.935-11 11.000 0 1.892.482 3.773 1.388 5.396l.9 1.405 1.287-4.101c-.738-1.215-1.195-2.609-1.195-4.038 0-4.495 3.642-8.15 8.12-8.15 2.173 0 4.212.853 5.744 2.357 1.532 1.505 2.438 3.509 2.433 5.654-.006 4.496-3.644 8.151-8.12 8.151-1.805 0-3.534-.479-5.028-1.378l-.799-.47-3.055.987.772-2.465.512-.316c1.011-.681 2.164-1.059 3.498-1.059 4.495 0 8.15 3.645 8.15 8.12 0 4.495-3.645 8.15-8.15 8.15-.572 0-1.14-.063-1.695-.186l-.704-.105-2.872.972.771-2.468-.513-.319z" fill="white"/>
        </svg>
    </a>
</nav>

<style>
.social-links-nav {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin: 1.5rem 0;
    padding: 1rem 0;
    border-top: 1px solid rgba(212, 175, 55, 0.15);
    border-bottom: 1px solid rgba(212, 175, 55, 0.15);
}

/* Social Link Styles */
.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.2rem;
    height: 2.2rem;
    border-radius: 50%;
    background: transparent;
    color: #d4af37;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1.5px solid rgba(212, 175, 55, 0.4);
    animation: slideUp 0.6s ease-out backwards;
}

/* Staggered animation for each link */
.social-link:nth-child(1) {
    animation-delay: 0.1s;
}

.social-link:nth-child(2) {
    animation-delay: 0.2s;
}

.social-link:nth-child(3) {
    animation-delay: 0.3s;
}

/* Slide-up animation */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effects */
.social-link:hover {
    background: rgba(212, 175, 55, 0.15);
    border-color: #d4af37;
    transform: translateY(-4px);
    box-shadow: 0 0 20px rgba(212, 175, 55, 0.6),
                inset 0 0 10px rgba(212, 175, 55, 0.2);
}

/* WhatsApp specific styles */
.social-link.whatsapp-link {
    background: #25D366 !important;
    border-color: #25D366 !important;
    color: white;
}

.social-link.whatsapp-link:hover {
    background: #1fa853 !important;
    border-color: #1fa853 !important;
    box-shadow: 0 0 20px rgba(37, 211, 102, 0.6),
                inset 0 0 10px rgba(37, 211, 102, 0.2);
}

/* Icon styles */
.social-icon {
    width: 1rem;
    height: 1rem;
    transition: all 0.3s ease;
}

.social-link:hover .social-icon {
    transform: scale(1.2);
    filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.8));
}

/* Facebook specific color */
.social-link:not(.whatsapp-link) {
    color: #d4af37;
}

/* WhatsApp icon color */
.social-link.whatsapp-link .whatsapp-icon {
    color: white;
}

/* Responsive */
@media (max-width: 640px) {
    .social-links-nav {
        gap: 0.8rem;
        margin: 1rem 0;
        padding: 0.8rem 0;
    }

    .social-link {
        width: 2rem;
        height: 2rem;
    }

    .social-icon {
        width: 0.9rem;
        height: 0.9rem;
    }
}
</style>
