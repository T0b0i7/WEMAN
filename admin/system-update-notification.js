document.addEventListener('DOMContentLoaded', () => {
    // Vérifier la date
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth(); // 0-11 (0 = janvier)
    const currentDay = currentDate.getDate();
    
    // Vérifier si c'est Noël (25 décembre)
    if (currentMonth === 11 && currentDay === 25) {
        showChristmasNotification();
    } 
    // Vérifier si c'est en octobre
    else if (currentMonth === 9 && !localStorage.getItem('dailyNotificationShown')) {
        showSystemUpdateNotification();
        // Marquer la notification comme montrée pour aujourd'hui
        const today = new Date().toDateString();
        localStorage.setItem('dailyNotificationShown', today);
    }
});

// Fonction pour la notification de Noël
function showChristmasNotification() {
    Swal.fire({
        title: '<strong class="christmas-title">🎄 Joyeux Noël! 🎄</strong>',
        html: `
            <div class="christmas-animation">
                <div class="snow"></div>
                <div class="message-box">
                    <h3>L'équipe WEMANTCHE vous souhaite</h3>
                    <h2 class="christmas-message">Un Merveilleux Noël!</h2>
                    <p>Que cette période soit remplie de joie et de bonheur</p>
                    <div class="christmas-icons">
                        <span>🎁</span>
                        <span>⭐</span>
                        <span>🕯️</span>
                        <span>🎅</span>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Merci! 🎄',
        confirmButtonColor: '#e42837',
        customClass: {
            popup: 'christmas-popup animate__animated animate__jackInTheBox'
        }
    });
}

// Fonction pour la notification système
function showSystemUpdateNotification() {
    // ... votre code existant pour la notification système ...
}

// Styles pour les notifications
const styles = document.createElement('style');
styles.textContent = `
    /* ... vos styles existants ... */

    /* Styles de Noël */
    .christmas-popup {
        background: linear-gradient(135deg, #1a472a, #2d5a40);
        border: 3px solid gold;
    }

    .christmas-title {
        color: #e42837 !important;
        font-family: 'Mountains of Christmas', cursive;
        font-size: 2.5em;
    }

    .christmas-message {
        color: gold;
        font-size: 2em;
        margin: 20px 0;
        font-family: 'Mountains of Christmas', cursive;
        animation: glow 2s ease-in-out infinite alternate;
    }

    .christmas-icons {
        font-size: 2em;
        margin-top: 20px;
    }

    .christmas-icons span {
        margin: 0 10px;
        animation: bounce 1s ease infinite;
    }

    .snow {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        background-image: 
            radial-gradient(3px 3px at 50px 50px, #fff, transparent),
            radial-gradient(2px 2px at 100px 100px, #fff, transparent),
            radial-gradient(2px 2px at 150px 150px, #fff, transparent);
        animation: snow 5s linear infinite;
    }

    @keyframes snow {
        0% { background-position: 0 0, 0 0, 0 0; }
        100% { background-position: 500px 500px, 200px 200px, -100px 300px; }
    }

    @keyframes glow {
        from { text-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px gold; }
        to { text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px gold; }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
`;

// Ajouter la police Google Fonts pour Noël
const fontLink = document.createElement('link');
fontLink.href = 'https://fonts.googleapis.com/css2?family=Mountains+of+Christmas:wght@700&display=swap';
fontLink.rel = 'stylesheet';
document.head.appendChild(fontLink);

// Ajouter les styles
document.head.appendChild(styles);

// Ajouter le lien vers Animate.css pour les animations
const animateCssLink = document.createElement('link');
animateCssLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
animateCssLink.rel = 'stylesheet';
document.head.appendChild(animateCssLink);