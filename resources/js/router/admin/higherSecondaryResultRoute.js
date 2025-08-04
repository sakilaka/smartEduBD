import { authGuard } from './authGuard';

const higherSecondaryResultRoutes = [
    // ------------------PRIMARY GRADE MANAGEMENT PORTION------------------
    { path: '/higherSecondaryGradeManagement', name: 'higherSecondaryGradeManagement.index', component: () => import('@views/backend/Result/higherSecondary/GradeManagement/Index'), beforeEnter: authGuard },
    { path: '/higherSecondaryGradeManagement/create', name: 'higherSecondaryGradeManagement.create', component: () => import('@views/backend/Result/higherSecondary/GradeManagement/Create'), beforeEnter: authGuard },
    { path: '/higherSecondaryGradeManagement/:id', name: 'higherSecondaryGradeManagement.show', component: () => import('@views/backend/Result/higherSecondary/GradeManagement/View'), beforeEnter: authGuard },
    { path: '/higherSecondaryGradeManagement/:id/edit', name: 'higherSecondaryGradeManagement.edit', component: () => import('@views/backend/Result/higherSecondary/GradeManagement/Create'), beforeEnter: authGuard },

    // ------------------SUBJECT ASSIGN PORTION------------------
    { path: '/higherSecondarySubjectAssign', name: 'higherSecondarySubjectAssign.index', component: () => import('@views/backend/Result/higherSecondary/SubjectAssign/Index'), beforeEnter: authGuard },
    { path: '/higherSecondarySubjectAssign/create', name: 'higherSecondarySubjectAssign.create', component: () => import('@views/backend/Result/higherSecondary/SubjectAssign/Create'), beforeEnter: authGuard },
    { path: '/higherSecondarySubjectAssign/:id', name: 'higherSecondarySubjectAssign.show', component: () => import('@views/backend/Result/higherSecondary/SubjectAssign/View'), beforeEnter: authGuard },
    { path: '/higherSecondarySubjectAssign/:id/edit', name: 'higherSecondarySubjectAssign.edit', component: () => import('@views/backend/Result/higherSecondary/SubjectAssign/Create'), beforeEnter: authGuard },

    // ------------------CLASS TEST RESULT PORTION------------------
    { path: '/higherSecondaryClassTestResult', name: 'higherSecondaryClassTestResult.index', component: () => import('@views/backend/Result/higherSecondary/ClassTestResult/Index'), beforeEnter: authGuard },
    { path: '/higherSecondaryClassTestResult/create', name: 'higherSecondaryClassTestResult.create', component: () => import('@views/backend/Result/higherSecondary/ClassTestResult/Create'), beforeEnter: authGuard },
    { path: '/higherSecondaryClassTestResult/:id', name: 'higherSecondaryClassTestResult.show', component: () => import('@views/backend/Result/higherSecondary/ClassTestResult/View'), beforeEnter: authGuard },
    { path: '/higherSecondaryClassTestResult/:id/edit', name: 'higherSecondaryClassTestResult.edit', component: () => import('@views/backend/Result/higherSecondary/ClassTestResult/Edit'), beforeEnter: authGuard },
    { path: '/higherSecondaryClassTestResult-marksheet/:id', name: 'higherSecondaryClassTestResult.marksheet', component: () => import('@views/backend/Result/higherSecondary/ClassTestResult/Marksheet'), beforeEnter: authGuard },

    // ------------------RESULT PORTION------------------
    { path: '/higherSecondaryResult', name: 'higherSecondaryResult.index', component: () => import('@views/backend/Result/higherSecondary/Result/Index'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult/create', name: 'higherSecondaryResult.create', component: () => import('@views/backend/Result/higherSecondary/Result/Create'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult/:id/edit', name: 'higherSecondaryResult.edit', component: () => import('@views/backend/Result/higherSecondary/Result/Edit'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult/:id', name: 'higherSecondaryResult.show', component: () => import('@views/backend/Result/higherSecondary/Result/View'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult-report', name: 'higherSecondaryResult.result', component: () => import('@views/backend/Result/higherSecondary/Report/Result'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult-subjectwise', name: 'higherSecondaryResult.subjectwiseResult', component: () => import('@views/backend/Result/higherSecondary/Report/SubjectwiseResult'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult-marksheet/:id', name: 'higherSecondaryResult.marksheet', component: () => import('@views/backend/Result/higherSecondary/Report/Marksheet'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult-tabulation-sheet', name: 'higherSecondaryResult.tabulationSheet', component: () => import('@views/backend/Result/higherSecondary/Report/TabulationSheet'), beforeEnter: authGuard },
    { path: '/higherSecondaryResult-grade-summary', name: 'higherSecondaryResult.gradeSummary', component: () => import('@views/backend/Result/higherSecondary/Report/GradeSummary'), beforeEnter: authGuard },

]

export default higherSecondaryResultRoutes;
