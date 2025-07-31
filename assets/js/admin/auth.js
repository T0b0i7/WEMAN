// Configuration
const AUTH_CONFIG = {
    API_URL: 'https://api.wemantche.com/v1',
    TOKEN_KEY: 'adminToken',
    REFRESH_TOKEN_KEY: 'adminRefreshToken',
    SESSION_DURATION: 3600000, // 1 heure
    REFRESH_THRESHOLD: 300000, // 5 minutes avant expiration
};

class AuthManager {
    constructor() {
        this.token = localStorage.getItem(AUTH_CONFIG.TOKEN_KEY);
        this.refreshToken = localStorage.getItem(AUTH_CONFIG.REFRESH_TOKEN_KEY);
        this.setupInterceptors();
    }

    // Initialisation des intercepteurs pour les requêtes API
    setupInterceptors() {
        axios.interceptors.request.use(
            config => {
                if (this.token) {
                    config.headers.Authorization = `Bearer ${this.token}`;
                }
                return config;
            },
            error => Promise.reject(error)
        );

        axios.interceptors.response.use(
            response => response,
            async error => {
                if (error.response?.status === 401 && this.refreshToken) {
                    try {
                        await this.refreshSession();
                        return axios(error.config);
                    } catch (refreshError) {
                        this.logout();
                        throw refreshError;
                    }
                }
                throw error;
            }
        );
    }

    // Connexion admin
    async login(email, password) {
        try {
            const response = await axios.post(`${AUTH_CONFIG.API_URL}/auth/login`, {
                email,
                password
            });

            const { token, refreshToken, user } = response.data;
            this.setSession(token, refreshToken, user);
            return user;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Erreur de connexion');
        }
    }

    // Rafraîchissement de la session
    async refreshSession() {
        try {
            const response = await axios.post(`${AUTH_CONFIG.API_URL}/auth/refresh`, {
                refreshToken: this.refreshToken
            });

            const { token, refreshToken } = response.data;
            this.setSession(token, refreshToken);
            return token;
        } catch (error) {
            this.logout();
            throw new Error('Session expirée');
        }
    }

    // Configuration de la session
    setSession(token, refreshToken, user = null) {
        this.token = token;
        this.refreshToken = refreshToken;
        localStorage.setItem(AUTH_CONFIG.TOKEN_KEY, token);
        localStorage.setItem(AUTH_CONFIG.REFRESH_TOKEN_KEY, refreshToken);
        if (user) {
            localStorage.setItem('adminUser', JSON.stringify(user));
        }
    }

    // Déconnexion
    logout() {
        localStorage.removeItem(AUTH_CONFIG.TOKEN_KEY);
        localStorage.removeItem(AUTH_CONFIG.REFRESH_TOKEN_KEY);
        localStorage.removeItem('adminUser');
        window.location.href = '/admin/login.php';
    }

    // Vérification de l'authentification
    isAuthenticated() {
        return !!this.token;
    }

    // Récupération des informations utilisateur
    getUser() {
        try {
            return JSON.parse(localStorage.getItem('adminUser'));
        } catch {
            return null;
        }
    }
}

// Export de l'instance unique
export const authManager = new AuthManager();