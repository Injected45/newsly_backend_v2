export const ENDPOINTS = {
  // Auth
  REGISTER: '/auth/register',
  LOGIN: '/auth/login',
  LOGOUT: '/auth/logout',
  ME: '/auth/me',
  
  // Setup
  CHECK_SETUP: '/setup/check',
  COMPLETE_SETUP: '/setup/complete',
  SKIP_SETUP: '/setup/skip',
  
  // Articles
  ARTICLES: '/articles',
  ARTICLES_LATEST: '/articles/latest',
  ARTICLES_BREAKING: '/articles/breaking',
  ARTICLE_DETAIL: (id) => `/articles/${id}`,
  
  // Countries & Sources
  COUNTRIES: '/countries',
  SOURCES: '/sources',
  CATEGORIES: '/categories',
  
  // Bookmarks
  BOOKMARKS: '/bookmarks',
  
  // Subscriptions
  SUBSCRIPTIONS: '/subscriptions',
};



