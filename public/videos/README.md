# Hero Video Setup

## Instructions pour ajouter la vidéo du hero

### Emplacement de la vidéo
- **Chemin**: `/public/videos/hero-dressing-showcase.mp4`
- **Nom du fichier**: `hero-dressing-showcase.mp4`
- **Format**: MP4 (H.264 codec)

### Spécifications recommandées

**Résolution et dimensions:**
- Résolution: 1920x1920px (carré pour le design responsive)
- Durée: 10-15 secondes pour un bon effet de boucle
- Framerate: 30 fps ou 60 fps
- Bitrate: 5-8 Mbps

**Optimisations:**
- Utiliser la compression H.264 pour une meilleure compatibilité
- Ajouter un poster/thumbnail pour le chargement
- Taille du fichier finale: 4-8 MB maximum

### Contenu recommandé

La vidéo doit mettre en avant :
- Le **Dressing Ivoire Modulable** (produit E)
- Transitions fluides entre les différentes vues du produit
- Zoom-in progressif pour créer de l'impact
- Mouvements subtils et professionnels (pas de cuts rapides)
- Couleurs cohérentes avec la palette brand (ivoire, sable, profond)

### Exemple avec FFmpeg

Si vous avez une vidéo source et besoin de la convertir:

```bash
# Redimensionner et compresser
ffmpeg -i source-video.mp4 -vf scale=1920:1920:force_original_aspect_ratio=decrease,pad=1920:1920:(1920-iw)/2:(1920-ih)/2 -c:v libx264 -preset medium -crf 28 -c:a aac -b:a 128k hero-dressing-showcase.mp4

# Version optimisée pour web
ffmpeg -i source-video.mp4 -vf scale=1920:1920 -c:v libx264 -crf 28 -movflags +faststart -c:a aac hero-dressing-showcase.mp4
```

### Alternative: Utiliser des images animées

Si vous n'avez pas de vidéo, vous pouvez:
1. Créer un carrousel d'images du produit
2. Les animer avec CSS (lent zoom-in)
3. Le fallback image du composant affichera déjà une image du produit

### Intégration au front-end

Le composant est déjà configuré pour:
- ✅ Charger la vidéo depuis `/public/videos/hero-dressing-showcase.mp4`
- ✅ Afficher un poster (thumbnail) avant lecture
- ✅ Boucler automatiquement (autoplay + loop)
- ✅ Pas de son (muted)
- ✅ Responsive sur mobile (playsinline)
- ✅ Fallback sur image si vidéo non disponible

### Fichier du composant

Voir: `resources/views/partials/hero-video-section.blade.php`

La vidéo apparaîtra **entre les sections Rangement et Dressing** de la page d'accueil.
