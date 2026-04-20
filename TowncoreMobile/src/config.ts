// ── Environment Configuration ─────────────────────────────────
// Switch between local development and production API.
// In development (Expo Go / dev client), uses LOCAL_API_URL.
// In production builds, uses PROD_API_URL.

const LOCAL_API_URL = 'http://192.168.18.2:8000/api'; // ← Update IP to your machine's LAN IP
const PROD_API_URL = 'https://twncolors.com/api';

const isDev = __DEV__;

const API_BASE_URL = isDev ? LOCAL_API_URL : PROD_API_URL;

export const APP_NAME = 'Towncore';
export const IS_DEV = isDev;

// Media / upload base URL (without /api)
export const MEDIA_BASE_URL = isDev
  ? LOCAL_API_URL.replace('/api', '')
  : PROD_API_URL.replace('/api', '');

export default API_BASE_URL;
