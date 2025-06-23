import { authGuard } from './authGuard';

const secondaryResultRoutes = [
    // ------------------PRIMARY GRADE MANAGEMENT PORTION------------------
    { path: '/secondaryGradeManagement', name: 'secondaryGradeManagement.index', component: () => import('@views/backend/Result/Secondary/GradeManagement/Index'), beforeEnter: authGuard },
    { path: '/secondaryGradeManagement/create', name: 'secondaryGradeManagement.create', component: () => import('@views/backend/Result/Secondary/GradeManagement/Create'), beforeEnter: authGuard },
    { path: '/secondaryGradeManagement/:id', name: 'secondaryGradeManagement.show', component: () => import('@views/backend/Result/Secondary/GradeManagement/View'), beforeEnter: authGuard },
    { path: '/secondaryGradeManagement/:id/edit', name: 'secondaryGradeManagement.edit', component: () => import('@views/backend/Result/Secondary/GradeManagement/Create'), beforeEnter: authGuard },

    // ------------------SUBJECT ASSIGN PORTION------------------
    { path: '/secondarySubjectAssign', name: 'secondarySubjectAssign.index', component: () => import('@views/backend/Result/Secondary/SubjectAssign/Index'), beforeEnter: authGuard },
    { path: '/secondarySubjectAssign/create', name: 'secondarySubjectAssign.create', component: () => import('@views/backend/Result/Secondary/SubjectAssign/Create'), beforeEnter: authGuard },
    { path: '/secondarySubjectAssign/:id', name: 'secondarySubjectAssign.show', component: () => import('@views/backend/Result/Secondary/SubjectAssign/View'), beforeEnter: authGuard },
    { path: '/secondarySubjectAssign/:id/edit', name: 'secondarySubjectAssign.edit', component: () => import('@views/backend/Result/Secondary/SubjectAssign/Create'), beforeEnter: authGuard },

    // ------------------CLASS TEST RESULT PORTION------------------
    { path: '/secondaryClassTestResult', name: 'secondaryClassTestResult.index', component: () => import('@views/backend/Result/Secondary/ClassTestResult/Index'), beforeEnter: authGuard },
    { path: '/secondaryClassTestResult/create', name: 'secondaryClassTestResult.create', component: () => import('@views/backend/Result/Secondary/ClassTestResult/Create'), beforeEnter: authGuard },
    { path: '/secondaryClassTestResult/:id', name: 'secondaryClassTestResult.show', component: () => import('@views/backend/Result/Secondary/ClassTestResult/View'), beforeEnter: authGuard },
    { path: '/secondaryClassTestResult/:id/edit', name: 'secondaryClassTestResult.edit', component: () => import('@views/backend/Result/Secondary/ClassTestResult/Edit'), beforeEnter: authGuard },
    { path: '/secondaryClassTestResult-marksheet/:id', name: 'secondaryClassTestResult.marksheet', component: () => import('@views/backend/Result/Secondary/ClassTestResult/Marksheet'), beforeEnter: authGuard },

    // ------------------RESULT PORTION------------------
    { path: '/secondaryResult', name: 'secondaryResult.index', component: () => import('@views/backend/Result/Secondary/Result/Index'), beforeEnter: authGuard },
    { path: '/secondaryResult/create', name: 'secondaryResult.create', component: () => import('@views/backend/Result/Secondary/Result/Create'), beforeEnter: authGuard },
    { path: '/secondaryResult/:id/edit', name: 'secondaryResult.edit', component: () => import('@views/backend/Result/Secondary/Result/Edit'), beforeEnter: authGuard },
    { path: '/secondaryResult/:id', name: 'secondaryResult.show', component: () => import('@views/backend/Result/Secondary/Result/View'), beforeEnter: authGuard },
    { path: '/secondaryResult-report', name: 'secondaryResult.result', component: () => import('@views/backend/Result/Secondary/Report/Result'), beforeEnter: authGuard },
    { path: '/secondaryResult-classwise', name: 'secondaryResult.classwiseResult', component: () => import('@views/backend/Result/Secondary/Report/ClasswiseResult'), beforeEnter: authGuard },
    { path: '/secondaryResult-subjectwise', name: 'secondaryResult.subjectwiseResult', component: () => import('@views/backend/Result/Secondary/Report/SubjectwiseResult'), beforeEnter: authGuard },
    { path: '/secondaryResult-marksheet/:id', name: 'secondaryResult.marksheet', component: () => import('@views/backend/Result/Secondary/Report/Marksheet'), beforeEnter: authGuard },
    { path: '/secondaryResult-tabulation-sheet', name: 'secondaryResult.tabulationSheet', component: () => import('@views/backend/Result/Secondary/Report/TabulationSheet'), beforeEnter: authGuard },
    { path: '/secondaryResult-grade-summary', name: 'secondaryResult.gradeSummary', component: () => import('@views/backend/Result/Secondary/Report/GradeSummary'), beforeEnter: authGuard },
    { path: '/secondaryResult-number-sheet', name: 'secondaryResult.numberSheet', component: () => import('@views/backend/Result/Secondary/Report/NumberSheet'), beforeEnter: authGuard },

]

export default secondaryResultRoutes;