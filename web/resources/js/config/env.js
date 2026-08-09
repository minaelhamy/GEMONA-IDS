const ENV = {
    API_URL: window.GEMONA_CONFIG?.apiUrl || import.meta.env.VITE_HOST,
    DEMO: import.meta.env.VITE_DEMO,
    API_KEY: window.GEMONA_CONFIG?.apiKey || import.meta.env.VITE_API_KEY
};
export default ENV;
