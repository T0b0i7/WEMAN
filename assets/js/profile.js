document.addEventListener('DOMContentLoaded', function() {
    // Animation au défilement
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.card, .table-responsive');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementTop < windowHeight - 100) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    window.addEventListener('scroll', animateOnScroll);

    // Gestion des onglets
    const menuLinks = document.querySelectorAll('.profile-menu .nav-link');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            menuLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Simuler le chargement
            const targetSection = document.querySelector(this.getAttribute('href'));
            targetSection.style.opacity = '0.5';
            
            setTimeout(() => {
                targetSection.style.opacity = '1';
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        });
    });

    // Gestion du formulaire de profil
    const profileForm = document.getElementById('profileForm');
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Animation de chargement
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sauvegarde...';
        submitBtn.disabled = true;
        
        // Simuler la sauvegarde
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Sauvegardé!';
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-success');
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
                submitBtn.disabled = false;
            }, 2000);
        }, 1500);
    });

    // Upload de photo de profil
    const editPhotoBtn = document.querySelector('.edit-photo');
    editPhotoBtn.addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const profileImg = document.querySelector('.profile-image img');
                    profileImg.style.opacity = '0';
                    setTimeout(() => {
                        profileImg.src = e.target.result;
                        profileImg.style.opacity = '1';
                    }, 300);
                };
                reader.readAsDataURL(file);
            }
        };
        
        input.click();
    });
}); 