class Slider {
    constructor(element) {
        this.slider = element;
        this.slides = this.slider.querySelectorAll('.slide');
        this.indicators = this.slider.querySelectorAll('.indicator');
        this.prevBtn = this.slider.querySelector('.prev');
        this.nextBtn = this.slider.querySelector('.next');
        this.currentSlide = 0;
        this.slideCount = this.slides.length;
        this.interval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startAutoPlay();
        this.updateSlides();
    }

    setupEventListeners() {
        this.prevBtn?.addEventListener('click', () => this.prevSlide());
        this.nextBtn?.addEventListener('click', () => this.nextSlide());
        
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });

        this.slider.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.slider.addEventListener('mouseleave', () => this.startAutoPlay());
    }

    updateSlides() {
        // Mettre à jour les classes des slides
        this.slides.forEach((slide, index) => {
            slide.classList.remove('active');
            if (index === this.currentSlide) {
                slide.classList.add('active');
            }
        });

        // Mettre à jour les indicateurs
        this.indicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            if (index === this.currentSlide) {
                indicator.classList.add('active');
            }
        });
    }

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.slideCount;
        this.updateSlides();
    }

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.slideCount) % this.slideCount;
        this.updateSlides();
    }

    goToSlide(index) {
        this.currentSlide = index;
        this.updateSlides();
    }

    startAutoPlay() {
        this.interval = setInterval(() => this.nextSlide(), 5000);
    }

    stopAutoPlay() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    const sliderElement = document.querySelector('.hero-slider');
    if (sliderElement) {
        window.slider = new Slider(sliderElement);
    }
}); 