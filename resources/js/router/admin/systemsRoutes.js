import { authGuard } from './authGuard';

const systemsRoutes = [
    // ------------------ADMIN PORTION------------------
    { path: '/dashboard', name: 'admin.dashboard', component: () => import('@views/backend/Admin/Dashboard'), beforeEnter: authGuard },
    { path: '/admin', name: 'admin.index', component: () => import('@views/backend/Admin/Index'), beforeEnter: authGuard },
    { path: '/admin/create', name: 'admin.create', component: () => import('@views/backend/Admin/Create'), beforeEnter: authGuard },
    { path: '/admin-profile', name: 'admin.profile', component: () => import('@views/backend/Admin/View'), beforeEnter: authGuard },
    { path: '/admin/:id', name: 'admin.show', component: () => import('@views/backend/Admin/View'), beforeEnter: authGuard },
    { path: '/admin/:id/edit', name: 'admin.edit', component: () => import('@views/backend/Admin/Create'), beforeEnter: authGuard },
    // ------------------ROLE PORTION------------------
    { path: '/role', name: 'role.index', component: () => import('@views/backend/System/Role/Index'), beforeEnter: authGuard },
    { path: '/role/create', name: 'role.create', component: () => import('@views/backend/System/Role/Create'), beforeEnter: authGuard },
    { path: '/role/:id', name: 'role.show', component: () => import('@views/backend/System/Role/View'), beforeEnter: authGuard },
    { path: '/role/:id/edit', name: 'role.edit', component: () => import('@views/backend/System/Role/Create'), beforeEnter: authGuard },
    // ------------------MENU PORTION------------------
    { path: '/menu', name: 'menu.index', component: () => import('@views/backend/System/Menu/Index'), beforeEnter: authGuard },
    { path: '/menu/create', name: 'menu.create', component: () => import('@views/backend/System/Menu/Create'), beforeEnter: authGuard },
    { path: '/menu/:id', name: 'menu.show', component: () => import('@views/backend/System/Menu/View'), beforeEnter: authGuard },
    { path: '/menu/:id/edit', name: 'menu.edit', component: () => import('@views/backend/System/Menu/Create'), beforeEnter: authGuard },
    // ------------------SITE SETTING PORTION------------------
    { path: '/siteSetting', name: 'siteSetting.index', component: () => import('@views/backend/System/SiteSettings/Index'), beforeEnter: authGuard },
    { path: '/siteSetting/create', name: 'siteSetting.create', component: () => import('@views/backend/System/SiteSettings/Create'), beforeEnter: authGuard },
    // ------------------Mobile App Version PORTION------------------
    { path: '/mobileAppVersion', name: 'mobileAppVersion.index', meta: { title: 'MobileAppVersion', nav: true }, component: () => import('@views/backend/System/MobileAppVersion/Index'), beforeEnter: authGuard },
    { path: '/mobileAppVersion/create', name: 'mobileAppVersion.create', component: () => import('@views/backend/System/MobileAppVersion/Create'), beforeEnter: authGuard },
    { path: '/mobileAppVersion/:id', name: 'mobileAppVersion.show', component: () => import('@views/backend/System/MobileAppVersion/View'), beforeEnter: authGuard },
    { path: '/mobileAppVersion/:id/edit', name: 'mobileAppVersion.edit', component: () => import('@views/backend/System/MobileAppVersion/Create'), beforeEnter: authGuard },
    // ------------------MODULE PORTION------------------
    { path: '/module', name: 'module.index', component: () => import('@views/backend/System/Module/Index'), beforeEnter: authGuard },
    { path: '/module/create', name: 'module.create', component: () => import('@views/backend/System/Module/Create'), beforeEnter: authGuard },
    // ------------------ACTIVITY LOG PORTION------------------
    { path: '/activityLog', name: 'activityLog.index', component: () => import('@views/backend/System/ActivityLog/Index'), beforeEnter: authGuard },
    { path: '/activityLog/:id', name: 'activityLog.show', component: () => import('@views/backend/System/ActivityLog/View'), beforeEnter: authGuard },
    // ------------------NOTIFICATION PORTION------------------
    { path: '/notification', name: 'notification.index', component: () => import('@views/backend/Notification/Index'), beforeEnter: authGuard },
    { path: '/notification/:id', name: 'notification.show', component: () => import('@views/backend/Notification/View'), beforeEnter: authGuard },

    { path: '/teacher', name: 'teacher.index', meta: { title: 'Teacher', nav: true }, component: () => import('./../../views/backend/Teacher/Teacher/Index'), beforeEnter: authGuard },
    { path: '/teacher/create', name: 'teacher.create', component: () => import('./../../views/backend/Teacher/Teacher/Create'), beforeEnter: authGuard },
    { path: '/teacher/:id', name: 'teacher.show', component: () => import('./../../views/backend/Teacher/Teacher/View'), beforeEnter: authGuard },
    { path: '/teacher/:id/edit', name: 'teacher.edit', component: () => import('./../../views/backend/Teacher/Teacher/Create'), beforeEnter: authGuard },
    { path: '/teacher-import', name: 'teacher.import', component: () => import('./../../views/backend/Teacher/Teacher/Import'), beforeEnter: authGuard },

                // ------------------LEAVEAPPLICATION PORTION------------------
    { path: '/leaveApplication', name: 'leaveApplication.index', meta: { title: 'LeaveApplication', nav: true }, component: () => import('./../../views/backend/Teacher/LeaveApplication/Index'), beforeEnter: authGuard },
    { path: '/leaveApplication/create', name: 'leaveApplication.create', component: () => import('./../../views/backend/Teacher/LeaveApplication/Create'), beforeEnter: authGuard },
    { path: '/leaveApplication/:id', name: 'leaveApplication.show', component: () => import('./../../views/backend/Teacher/LeaveApplication/View'), beforeEnter: authGuard },
    { path: '/leaveApplication/:id/edit', name: 'leaveApplication.edit', component: () => import('./../../views/backend/Teacher/LeaveApplication/Create'), beforeEnter: authGuard },
    // ------------------TEACHERATTENDANCE PORTION------------------
    { path: '/teacherAttendance', name: 'teacherAttendance.index', meta: { title: 'TeacherAttendance', nav: true }, component: () => import('./../../views/backend/Teacher/TeacherAttendance/Index'), beforeEnter: authGuard },
    { path: '/teacherAttendance/create', name: 'teacherAttendance.create', component: () => import('./../../views/backend/Teacher/TeacherAttendance/Create'), beforeEnter: authGuard },
    { path: '/teacherAttendance/:id', name: 'teacherAttendance.show', component: () => import('./../../views/backend/Teacher/TeacherAttendance/View'), beforeEnter: authGuard },
    { path: '/teacherAttendance/:id/edit', name: 'teacherAttendance.edit', component: () => import('./../../views/backend/Teacher/TeacherAttendance/Create'), beforeEnter: authGuard },
]

export default systemsRoutes;