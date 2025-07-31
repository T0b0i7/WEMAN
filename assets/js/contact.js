class ContactManager {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.form?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e);
        });
    }

    async handleSubmit(event) {
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';

            // Simulation d'envoi à une API
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            this.showNotification('Message envoyé avec succès!', 'success');
            form.reset();

        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de l\'envoi du message', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Envoyer le message';
        }
    }

    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.appendChild(toast);
        document.body.appendChild(container);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            container.remove();
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.contactManager = new ContactManager();
});

// Animation des icônes de contact au survol
const iconBoxes = document.querySelectorAll('.icon-box-sm');
iconBoxes.forEach(box => {
    box.addEventListener('mouseenter', function() {
        this.querySelector('i').classList.add('fa-bounce');
    });
    
    box.addEventListener('mouseleave', function() {
        this.querySelector('i').classList.remove('fa-bounce');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const messageResult = document.getElementById('messageResult');

    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const messageResult = document.getElementById('messageResult');

            try {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

                const response = await fetch('process_contact.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                messageResult.style.display = 'block';
                
                if (data.success) {
                    messageResult.className = 'alert alert-success';
                    messageResult.textContent = 'Votre message a été envoyé avec succès !';
                    form.reset();
                } else {
                    messageResult.className = 'alert alert-danger';
                    messageResult.textContent = data.error || 'Une erreur est survenue';
                }
            } catch (error) {
                messageResult.className = 'alert alert-danger';
                messageResult.textContent = 'Une erreur est survenue';
                messageResult.style.display = 'block';
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Envoyer le message';
            }
        });
    }
});