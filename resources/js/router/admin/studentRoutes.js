import { authGuard } from './authGuard';

const studentRoutes = [
    // ------------------STUDENT PORTION------------------
    { path: '/student', name: 'student.index', meta: { title: 'Student', nav: true }, component: () => import('@views/backend/Student/Student/Index'), beforeEnter: authGuard },
    { path: '/student/create', name: 'student.create', component: () => import('@views/backend/Student/Student/Create'), beforeEnter: authGuard },
    { path: '/student/:id', name: 'student.show', component: () => import('@views/backend/Student/Student/View'), beforeEnter: authGuard },
    { path: '/student/:id/edit', name: 'student.edit', component: () => import('@views/backend/Student/Student/Create'), beforeEnter: authGuard },
    { path: '/student-discount', name: 'student.discount', component: () => import('@views/backend/Student/Student/Discount'), beforeEnter: authGuard },
    { path: '/student-import', name: 'student.import', component: () => import('@views/backend/Student/Student/Import'), beforeEnter: authGuard },
    { path: '/studentPayment', name: 'studentPayment.student', component: () => import('@views/backend/Student/Student/Payment'), beforeEnter: authGuard },
    { path: '/student-idcard', name: 'idcard.index', component: () => import('@views/backend/Student/IDCard/Index'), beforeEnter: authGuard },

    // ------------------STUDENT PROMOTION PORTION------------------
    { path: '/studentPromotion', name: 'studentPromotion.index', meta: { title: 'StudentPromotion', nav: true }, component: () => import('@views/backend/Student/Promotion/Index'), beforeEnter: authGuard },
    { path: '/studentPromotion/create', name: 'studentPromotion.create', component: () => import('@views/backend/Student/Promotion/Create'), beforeEnter: authGuard },
    { path: '/studentPromotion/:id', name: 'studentPromotion.show', component: () => import('@views/backend/Student/Promotion/View'), beforeEnter: authGuard },
    { path: '/studentPromotion/:id/edit', name: 'studentPromotion.edit', component: () => import('@views/backend/Student/Promotion/Create'), beforeEnter: authGuard },

    // ------------------ATTENDANCE PORTION------------------
    { path: '/attendance', name: 'attendance.index', meta: { title: 'Attendance', nav: true }, component: () => import('@views/backend/Attendance/Attendance/Index'), beforeEnter: authGuard },
    { path: '/attendance/create', name: 'attendance.create', component: () => import('@views/backend/Attendance/Attendance/Create'), beforeEnter: authGuard },
    { path: '/attendance/:id', name: 'attendance.show', component: () => import('@views/backend/Attendance/Attendance/View'), beforeEnter: authGuard },
    { path: '/attendance/:id/edit', name: 'attendance.edit', component: () => import('@views/backend/Attendance/Attendance/Create'), beforeEnter: authGuard },
    { path: '/attendance-report', name: 'attendance.attendanceReport', component: () => import('@views/backend/Attendance/Attendance/Report'), beforeEnter: authGuard },
    { path: '/attendance-sheet', name: 'attendance.attendanceSheet', component: () => import('@views/backend/Attendance/Attendance/Sheet'), beforeEnter: authGuard },

    // ------------------GUARDIAN PORTION------------------
    { path: '/guardian', name: 'guardian.index', component: () => import('@views/backend/Guardian/Index'), beforeEnter: authGuard },
    { path: '/guardian/create', name: 'guardian.create', component: () => import('@views/backend/Guardian/Create'), beforeEnter: authGuard },
    { path: '/guardian/:id', name: 'guardian.show', component: () => import('@views/backend/Guardian/View'), beforeEnter: authGuard },
    { path: '/guardian/:id/edit', name: 'guardian.edit', component: () => import('@views/backend/Guardian/Create'), beforeEnter: authGuard },

    { path: '/admin-admit-card', name: 'adminAdmit.index', meta: { title: 'AdminAdmitCard', nav: true }, component: () => import('@views/backend/Student/Card/AdminAdmit'), beforeEnter: authGuard },
    { path: '/student-admit-card', name: 'studentAdmit.index', meta: { title: 'StudentAdmitCard', nav: true }, component: () => import('@views/backend/Student/Card/StudentAdmit'), beforeEnter: authGuard },
    { path: '/seat-card', name: 'seat.index', meta: { title: 'SeatCard', nav: true }, component: () => import('@views/backend/Student/Card/Seat'), beforeEnter: authGuard },

]

export default studentRoutes;
