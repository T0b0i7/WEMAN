<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>404 - Page Non Trouvée</title>
    <style>
        /* Reset et Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            background: #000;
        }
        
        /* Canvas */
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            touch-action: none;
        }
        
        /* Message d'erreur */
        .error-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
            width: 90%;
            max-width: 800px;
            padding: 20px;
            pointer-events: none;
        }
        
        .error-code {
            font-size: clamp(80px, 25vw, 150px);
            font-weight: 900;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }
        
        .error-title {
            font-size: clamp(24px, 6vw, 36px);
            margin-bottom: 20px;
        }
        
        .error-message {
            font-size: clamp(16px, 4vw, 20px);
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .home-button {
            display: inline-block;
            padding: 12px 30px;
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: clamp(14px, 4vw, 18px);
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            pointer-events: auto;
            backdrop-filter: blur(5px);
        }
        
        .home-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        /* Adaptations mobiles */
        @media (max-width: 768px) {
            .error-container {
                padding: 15px;
            }
            
            .error-code {
                margin-bottom: 5px;
            }
            
            .error-message {
                margin-bottom: 20px;
            }
            
            .home-button {
                padding: 10px 25px;
            }
        }
    </style>
</head>
<body>
    <canvas id="particle-canvas"></canvas>
    
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-title">Page Non Trouvée</div>
        <div class="error-message">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </div>
        <a href="login.php" class="home-button">Retour à l'accueil</a>
    </div>

    <script>
        // Configuration initiale
        (function() {
            'use strict';
            
            // Détection des appareils
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isTablet = /iPad|Android|Tablet/i.test(navigator.userAgent);
            
            // Optimisation des performances
            const performanceMode = isMobile ? 'light' : 'full';
            
            // Éléments du DOM
            const canvas = document.getElementById('particle-canvas');
            const ctx = canvas.getContext('2d', { alpha: false });
            
            // Variables de dimension
            let width = window.innerWidth;
            let height = window.innerHeight;
            let dpr = Math.min(2, window.devicePixelRatio);
            
            // Paramètres des particules
            const particleConfig = {
                count: performanceMode === 'full' ? 2000 : 800,
                size: {
                    min: 1,
                    max: performanceMode === 'full' ? 3 : 2
                },
                speed: {
                    min: 0.2,
                    max: 1.5
                },
                lineDistance: 100,
                lineOpacity: 0.2
            };
            
            // Couleurs
            const colors = [
                'rgba(255, 255, 255, 0.8)',
                'rgba(100, 200, 255, 0.7)',
                'rgba(255, 100, 200, 0.7)'
            ];
            
            // Collection de particules
            let particles = [];
            
            // Initialisation
            function init() {
                setupCanvas();
                createParticles();
                setupEventListeners();
                startAnimation();
                
                // Protection basique
                protectCode();
            }
            
            // Configuration du canvas
            function setupCanvas() {
                canvas.width = width * dpr;
                canvas.height = height * dpr;
                canvas.style.width = width + 'px';
                canvas.style.height = height + 'px';
                ctx.scale(dpr, dpr);
            }
            
            // Création des particules
            function createParticles() {
                particles = [];
                
                for (let i = 0; i < particleConfig.count; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        size: Math.random() * (particleConfig.size.max - particleConfig.size.min) + particleConfig.size.min,
                        speedX: (Math.random() * 2 - 1) * (Math.random() * (particleConfig.speed.max - particleConfig.speed.min) + particleConfig.speed.min),
                        speedY: (Math.random() * 2 - 1) * (Math.random() * (particleConfig.speed.max - particleConfig.speed.min) + particleConfig.speed.min),
                        color: colors[Math.floor(Math.random() * colors.length)]
                    });
                }
            }
            
            // Animation
            function animate() {
                ctx.clearRect(0, 0, width, height);
                
                // Dessiner les connexions entre particules
                drawConnections();
                
                // Mettre à jour et dessiner les particules
                updateParticles();
                
                requestAnimationFrame(animate);
            }
            
            // Dessiner les connexions
            function drawConnections() {
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < particleConfig.lineDistance) {
                            ctx.strokeStyle = `rgba(255, 255, 255, ${particleConfig.lineOpacity * (1 - distance / particleConfig.lineDistance)})`;
                            ctx.lineWidth = 0.5;
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.stroke();
                        }
                    }
                }
            }
            
            // Mettre à jour les particules
            function updateParticles() {
                particles.forEach(p => {
                    // Mise à jour de la position
                    p.x += p.speedX;
                    p.y += p.speedY;
                    
                    // Rebond sur les bords
                    if (p.x < 0 || p.x > width) p.speedX *= -1;
                    if (p.y < 0 || p.y > height) p.speedY *= -1;
                    
                    // Dessiner la particule
                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();
                });
            }
            
            // Gestion des événements
            function setupEventListeners() {
                window.addEventListener('resize', handleResize);
                
                // Interaction souris/tactile
                if (!isMobile) {
                    window.addEventListener('mousemove', handleMouseMove);
                } else {
                    window.addEventListener('touchmove', handleTouchMove, { passive: true });
                }
            }
            
            // Redimensionnement
            function handleResize() {
                width = window.innerWidth;
                height = window.innerHeight;
                setupCanvas();
            }
            
            // Interaction souris
            function handleMouseMove(e) {
                const mouseX = e.clientX;
                const mouseY = e.clientY;
                
                particles.forEach(p => {
                    const dx = mouseX - p.x;
                    const dy = mouseY - p.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 150) {
                        const force = 150 / distance;
                        p.x -= dx * force * 0.01;
                        p.y -= dy * force * 0.01;
                    }
                });
            }
            
            // Interaction tactile
            function handleTouchMove(e) {
                if (e.touches.length > 0) {
                    const touchX = e.touches[0].clientX;
                    const touchY = e.touches[0].clientY;
                    
                    particles.forEach(p => {
                        const dx = touchX - p.x;
                        const dy = touchY - p.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < 150) {
                            const force = 150 / distance;
                            p.x -= dx * force * 0.01;
                            p.y -= dy * force * 0.01;
                        }
                    });
                }
            }
            
         
            
            // Démarrer l'animation
            function startAnimation() {
                // Attendre que la page soit prête
                if (document.readyState === 'complete') {
                    animate();
                } else {
                    window.addEventListener('load', animate);
                }
            }
            
            // Initialiser l'application
            init();
        })();
    </script>
</body>
</html>