import { authGuard } from './authGuard';

const frontendRoutes = [
    // ------------------MENU PORTION------------------
    { path: '/frontMenu', name: 'frontMenu.index', component: () => import('@views/backend/Website/Menu/Index'), beforeEnter: authGuard },
    { path: '/frontMenu/create', name: 'frontMenu.create', component: () => import('@views/backend/Website/Menu/Create'), beforeEnter: authGuard },
    { path: '/frontMenu/:id', name: 'frontMenu.show', component: () => import('@views/backend/Website/Menu/View'), beforeEnter: authGuard },
    { path: '/frontMenu/:id/edit', name: 'frontMenu.edit', component: () => import('@views/backend/Website/Menu/Create'), beforeEnter: authGuard },
    // ------------------CONTENT PORTION------------------
    { path: '/content/:slug', name: 'content.show', component: () => import('@views/backend/Website/Content/View'), beforeEnter: authGuard },
    { path: '/content/:slug/create', name: 'content.create', component: () => import('@views/backend/Website/Content/Create'), beforeEnter: authGuard },
    { path: '/content-file/:slug', name: 'content.file', component: () => import('@views/backend/Website/Content/CreateFile'), beforeEnter: authGuard },
    // ------------------SLIDER PORTION------------------
    { path: '/slider', name: 'slider.index', component: () => import('@views/backend/Website/Gallery/Slider/Index'), beforeEnter: authGuard },
    { path: '/slider/create', name: 'slider.create', component: () => import('@views/backend/Website/Gallery/Slider/Create'), beforeEnter: authGuard },
    { path: '/slider/:id', name: 'slider.show', component: () => import('@views/backend/Website/Gallery/Slider/View'), beforeEnter: authGuard },
    { path: '/slider/:id/edit', name: 'slider.edit', component: () => import('@views/backend/Website/Gallery/Slider/Create'), beforeEnter: authGuard },
    // ------------------ALBUM PORTION------------------
    { path: '/album', name: 'album.index', component: () => import('@views/backend/Website/Gallery/Album/Index'), beforeEnter: authGuard },
    { path: '/album/create', name: 'album.create', component: () => import('@views/backend/Website/Gallery/Album/Create'), beforeEnter: authGuard },
    { path: '/album/:id', name: 'album.show', component: () => import('@views/backend/Website/Gallery/Album/View'), beforeEnter: authGuard },
    { path: '/album/:id/edit', name: 'album.edit', component: () => import('@views/backend/Website/Gallery/Album/Create'), beforeEnter: authGuard },
    // ------------------PHOTO PORTION------------------
    { path: '/photo', name: 'photo.index', component: () => import('@views/backend/Website/Gallery/Photo/Index'), beforeEnter: authGuard },
    { path: '/photo/create', name: 'photo.create', component: () => import('@views/backend/Website/Gallery/Photo/Create'), beforeEnter: authGuard },
    { path: '/photo/:id', name: 'photo.show', component: () => import('@views/backend/Website/Gallery/Photo/View'), beforeEnter: authGuard },
    { path: '/photo/:id/edit', name: 'photo.edit', component: () => import('@views/backend/Website/Gallery/Photo/Edit'), beforeEnter: authGuard },
    // ------------------VIDEO PORTION------------------
    { path: '/video', name: 'video.index', component: () => import('@views/backend/Website/Gallery/Video/Index'), beforeEnter: authGuard },
    { path: '/video/create', name: 'video.create', component: () => import('@views/backend/Website/Gallery/Video/Create'), beforeEnter: authGuard },
    { path: '/video/:id', name: 'video.show', component: () => import('@views/backend/Website/Gallery/Video/View'), beforeEnter: authGuard },
    { path: '/video/:id/edit', name: 'video.edit', component: () => import('@views/backend/Website/Gallery/Video/Create'), beforeEnter: authGuard },
    // ------------------News portion------------------
    { path: '/news', name: 'news.index', component: () => import('@views/backend/Website/News/Index'), beforeEnter: authGuard },
    { path: '/news/create', name: 'news.create', component: () => import('@views/backend/Website/News/Create'), beforeEnter: authGuard },
    { path: '/news/:id', name: 'news.show', component: () => import('@views/backend/Website/News/View'), beforeEnter: authGuard },
    { path: '/news/:id/edit', name: 'news.edit', component: () => import('@views/backend/Website/News/Create'), beforeEnter: authGuard },
    // ------------------NOTICE PORTION------------------
    { path: '/notice', name: 'notice.index', meta: { title: 'Notice', nav: true }, component: () => import('@views/backend/Website/Notice/Index'), beforeEnter: authGuard },
    { path: '/notice/create', name: 'notice.create', component: () => import('@views/backend/Website/Notice/Create'), beforeEnter: authGuard },
    { path: '/notice/:id', name: 'notice.show', component: () => import('@views/backend/Website/Notice/View'), beforeEnter: authGuard },
    { path: '/notice/:id/edit', name: 'notice.edit', component: () => import('@views/backend/Website/Notice/Create'), beforeEnter: authGuard },
    { path: '/notice-officeOrder', name: 'notice.officeOrder', component: () => import('@views/backend/Website/Notice/OfficeOrder'), beforeEnter: authGuard },
]

export default frontendRoutes;