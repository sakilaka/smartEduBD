import { authGuard } from './authGuard';

const primaryResultRoutes = [
    // ------------------PRIMARY GRADE MANAGEMENT PORTION------------------
    { path: '/primaryGradeManagement', name: 'primaryGradeManagement.index', component: () => import('@views/backend/Result/Primary/GradeManagement/Index'), beforeEnter: authGuard },
    { path: '/primaryGradeManagement/create', name: 'primaryGradeManagement.create', component: () => import('@views/backend/Result/Primary/GradeManagement/Create'), beforeEnter: authGuard },
    { path: '/primaryGradeManagement/:id', name: 'primaryGradeManagement.show', component: () => import('@views/backend/Result/Primary/GradeManagement/View'), beforeEnter: authGuard },
    { path: '/primaryGradeManagement/:id/edit', name: 'primaryGradeManagement.edit', component: () => import('@views/backend/Result/Primary/GradeManagement/Create'), beforeEnter: authGuard },

    // ------------------SUBJECT ASSIGN PORTION------------------
    { path: '/primarySubjectAssign', name: 'primarySubjectAssign.index', component: () => import('@views/backend/Result/Primary/SubjectAssign/Index'), beforeEnter: authGuard },
    { path: '/primarySubjectAssign/create', name: 'primarySubjectAssign.create', component: () => import('@views/backend/Result/Primary/SubjectAssign/Create'), beforeEnter: authGuard },
    { path: '/primarySubjectAssign/:id', name: 'primarySubjectAssign.show', component: () => import('@views/backend/Result/Primary/SubjectAssign/View'), beforeEnter: authGuard },
    { path: '/primarySubjectAssign/:id/edit', name: 'primarySubjectAssign.edit', component: () => import('@views/backend/Result/Primary/SubjectAssign/Create'), beforeEnter: authGuard },

    // ------------------CLASS TEST RESULT PORTION------------------
    { path: '/primaryClassTestResult', name: 'primaryClassTestResult.index', component: () => import('@views/backend/Result/Primary/ClassTestResult/Index'), beforeEnter: authGuard },
    { path: '/primaryClassTestResult/create', name: 'primaryClassTestResult.create', component: () => import('@views/backend/Result/Primary/ClassTestResult/Create'), beforeEnter: authGuard },
    { path: '/primaryClassTestResult/:id', name: 'primaryClassTestResult.show', component: () => import('@views/backend/Result/Primary/ClassTestResult/View'), beforeEnter: authGuard },
    { path: '/primaryClassTestResult/:id/edit', name: 'primaryClassTestResult.edit', component: () => import('@views/backend/Result/Primary/ClassTestResult/Edit'), beforeEnter: authGuard },
    { path: '/primaryClassTestResult-marksheet/:id', name: 'primaryClassTestResult.marksheet', component: () => import('@views/backend/Result/Primary/ClassTestResult/Marksheet'), beforeEnter: authGuard },

    // ------------------RESULT PORTION------------------
    { path: '/primaryResult', name: 'primaryResult.index', component: () => import('@views/backend/Result/Primary/Result/Index'), beforeEnter: authGuard },
    { path: '/primaryResult/create', name: 'primaryResult.create', component: () => import('@views/backend/Result/Primary/Result/Create'), beforeEnter: authGuard },
    { path: '/primaryResult/:id/edit', name: 'primaryResult.edit', component: () => import('@views/backend/Result/Primary/Result/Edit'), beforeEnter: authGuard },
    { path: '/primaryResult/:id', name: 'primaryResult.show', component: () => import('@views/backend/Result/Primary/Result/View'), beforeEnter: authGuard },
    { path: '/primaryResult-report', name: 'primaryResult.result', component: () => import('@views/backend/Result/Primary/Report/Result'), beforeEnter: authGuard },
    { path: '/primaryResult-classwise', name: 'primaryResult.classwiseResult', component: () => import('@views/backend/Result/Primary/Report/ClasswiseResult'), beforeEnter: authGuard },
    { path: '/primaryResult-subjectwise', name: 'primaryResult.subjectwiseResult', component: () => import('@views/backend/Result/Primary/Report/SubjectwiseResult'), beforeEnter: authGuard },
    { path: '/primaryResult-marksheet/:id', name: 'primaryResult.marksheet', component: () => import('@views/backend/Result/Primary/Report/Marksheet'), beforeEnter: authGuard },
    { path: '/primaryResult-tabulation-sheet', name: 'primaryResult.tabulationSheet', component: () => import('@views/backend/Result/Primary/Report/TabulationSheet'), beforeEnter: authGuard },
    { path: '/primaryResult-grade-summary', name: 'primaryResult.gradeSummary', component: () => import('@views/backend/Result/Primary/Report/GradeSummary'), beforeEnter: authGuard },
    { path: '/primaryResult-number-sheet', name: 'primaryResult.numberSheet', component: () => import('@views/backend/Result/Primary/Report/NumberSheet'), beforeEnter: authGuard },


]

export default primaryResultRoutes;