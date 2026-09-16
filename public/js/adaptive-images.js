/**
 * Velora Eyes - Adaptive Images Engine
 * Auto-detects image aspect ratios and applies optimal layout classes.
 */

window.VeloraAdaptive = {
    updateImage: function(img) {
        if (!img || !img.naturalWidth || !img.naturalHeight) return;

        const ratio = img.naturalWidth / img.naturalHeight;
        const container = img.closest('.adaptive-image-container');

        if (!container) return;

        // Ranges:
        // Portrait (e.g. 9:16) < 0.8
        // Square (e.g. 1:1) >= 0.8 and <= 1.25
        // Landscape (e.g. 16:9) > 1.25

        if (ratio < 0.8) {
            container.setAttribute('data-image-ratio', 'portrait');
            container.style.setProperty('--img-aspect', '9/16');
        } else if (ratio > 1.25) {
            container.setAttribute('data-image-ratio', 'landscape');
            container.style.setProperty('--img-aspect', `${img.naturalWidth}/${img.naturalHeight}`);
        } else {
            container.setAttribute('data-image-ratio', 'square');
            container.style.setProperty('--img-aspect', '1/1');
        }
    },

    init: function() {
        const images = document.querySelectorAll('img[data-adaptive-image]');

        images.forEach(img => {
            if (img.complete && img.naturalWidth > 0) {
                this.updateImage(img);
            } else {
                img.addEventListener('load', () => this.updateImage(img), { once: true });
                // Fallback for cached images that might fail to report complete immediately
                setTimeout(() => {
                    if (img.complete && img.naturalWidth > 0) {
                        this.updateImage(img);
                    }
                }, 100);
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.VeloraAdaptive.init();
});
