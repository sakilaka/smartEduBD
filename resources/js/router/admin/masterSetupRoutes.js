import { authGuard } from './authGuard';

const masterSetupRoutes = [
    // ------------------ACCOUNT HEAD PORTION------------------
    { path: '/accountHead', name: 'accountHead.index', component: () => import('@views/backend/MasterSetup/AccountHead/Index'), beforeEnter: authGuard },
    { path: '/accountHead/create', name: 'accountHead.create', component: () => import('@views/backend/MasterSetup/AccountHead/Create'), beforeEnter: authGuard },
    { path: '/accountHead/:id', name: 'accountHead.show', component: () => import('@views/backend/MasterSetup/AccountHead/View'), beforeEnter: authGuard },
    { path: '/accountHead/:id/edit', name: 'accountHead.edit', component: () => import('@views/backend/MasterSetup/AccountHead/Create'), beforeEnter: authGuard },
    // ------------------PACKAGE PORTION------------------
    { path: '/package', name: 'package.index', component: () => import('@views/backend/MasterSetup/Package/Index'), beforeEnter: authGuard },
    { path: '/package/create', name: 'package.create', component: () => import('@views/backend/MasterSetup/Package/Create'), beforeEnter: authGuard },
    { path: '/package/:id', name: 'package.show', component: () => import('@views/backend/MasterSetup/Package/View'), beforeEnter: authGuard },
    { path: '/package/:id/edit', name: 'package.edit', component: () => import('@views/backend/MasterSetup/Package/Create'), beforeEnter: authGuard },
    // ------------------INSTITUTION PORTION------------------
    { path: '/institution', name: 'institution.index', component: () => import('@views/backend/MasterSetup/Institution/Index'), beforeEnter: authGuard },
    { path: '/institution/create', name: 'institution.create', component: () => import('@views/backend/MasterSetup/Institution/Create'), beforeEnter: authGuard },
    { path: '/institution/:id', name: 'institution.show', component: () => import('@views/backend/MasterSetup/Institution/View'), beforeEnter: authGuard },
    { path: '/institution/:id/edit', name: 'institution.edit', component: () => import('@views/backend/MasterSetup/Institution/Create'), beforeEnter: authGuard },
    // ------------------CAMPUS PORTION------------------
    { path: '/campus', name: 'campus.index', component: () => import('@views/backend/MasterSetup/Campus/Index'), beforeEnter: authGuard },
    { path: '/campus/create', name: 'campus.create', component: () => import('@views/backend/MasterSetup/Campus/Create'), beforeEnter: authGuard },
    { path: '/campus/:id', name: 'campus.show', component: () => import('@views/backend/MasterSetup/Campus/View'), beforeEnter: authGuard },
    { path: '/campus/:id/edit', name: 'campus.edit', component: () => import('@views/backend/MasterSetup/Campus/Create'), beforeEnter: authGuard },
    // ------------------MEDIUM PORTION------------------
    { path: '/medium', name: 'medium.index', component: () => import('@views/backend/MasterSetup/Medium/Index'), beforeEnter: authGuard },
    { path: '/medium/create', name: 'medium.create', component: () => import('@views/backend/MasterSetup/Medium/Create'), beforeEnter: authGuard },
    { path: '/medium/:id', name: 'medium.show', component: () => import('@views/backend/MasterSetup/Medium/View'), beforeEnter: authGuard },
    { path: '/medium/:id/edit', name: 'medium.edit', component: () => import('@views/backend/MasterSetup/Medium/Create'), beforeEnter: authGuard },
    // ------------------GROUP PORTION------------------
    { path: '/group', name: 'group.index', component: () => import('@views/backend/MasterSetup/Group/Index'), beforeEnter: authGuard },
    { path: '/group/create', name: 'group.create', component: () => import('@views/backend/MasterSetup/Group/Create'), beforeEnter: authGuard },
    { path: '/group/:id', name: 'group.show', component: () => import('@views/backend/MasterSetup/Group/View'), beforeEnter: authGuard },
    { path: '/group/:id/edit', name: 'group.edit', component: () => import('@views/backend/MasterSetup/Group/Create'), beforeEnter: authGuard },
    // ------------------SECTION PORTION------------------
    { path: '/section', name: 'section.index', component: () => import('@views/backend/MasterSetup/Section/Index'), beforeEnter: authGuard },
    { path: '/section/create', name: 'section.create', component: () => import('@views/backend/MasterSetup/Section/Create'), beforeEnter: authGuard },
    { path: '/section/:id', name: 'section.show', component: () => import('@views/backend/MasterSetup/Section/View'), beforeEnter: authGuard },
    { path: '/section/:id/edit', name: 'section.edit', component: () => import('@views/backend/MasterSetup/Section/Create'), beforeEnter: authGuard },
    // ------------------ACADEMICSESSION PORTION------------------
    { path: '/academicSession', name: 'academicSession.index', component: () => import('@views/backend/MasterSetup/AcademicSession/Index'), beforeEnter: authGuard },
    { path: '/academicSession/create', name: 'academicSession.create', component: () => import('@views/backend/MasterSetup/AcademicSession/Create'), beforeEnter: authGuard },
    { path: '/academicSession/:id', name: 'academicSession.show', component: () => import('@views/backend/MasterSetup/AcademicSession/View'), beforeEnter: authGuard },
    { path: '/academicSession/:id/edit', name: 'academicSession.edit', component: () => import('@views/backend/MasterSetup/AcademicSession/Create'), beforeEnter: authGuard },
    // ------------------ACADEMIC CLASS PORTION------------------
    { path: '/academicClass', name: 'academicClass.index', component: () => import('@views/backend/MasterSetup/AcademicClass/Index'), beforeEnter: authGuard },
    { path: '/academicClass/create', name: 'academicClass.create', component: () => import('@views/backend/MasterSetup/AcademicClass/Create'), beforeEnter: authGuard },
    { path: '/academicClass/:id', name: 'academicClass.show', component: () => import('@views/backend/MasterSetup/AcademicClass/View'), beforeEnter: authGuard },
    { path: '/academicClass/:id/edit', name: 'academicClass.edit', component: () => import('@views/backend/MasterSetup/AcademicClass/Create'), beforeEnter: authGuard },
    // ------------------SHIFT PORTION------------------
    { path: '/shift', name: 'shift.index', component: () => import('@views/backend/MasterSetup/Shift/Index'), beforeEnter: authGuard },
    { path: '/shift/create', name: 'shift.create', component: () => import('@views/backend/MasterSetup/Shift/Create'), beforeEnter: authGuard },
    { path: '/shift/:id', name: 'shift.show', component: () => import('@views/backend/MasterSetup/Shift/View'), beforeEnter: authGuard },
    { path: '/shift/:id/edit', name: 'shift.edit', component: () => import('@views/backend/MasterSetup/Shift/Create'), beforeEnter: authGuard },
    // ------------------EXAM PORTION------------------
    { path: '/exam', name: 'exam.index', meta: { title: 'Exam', nav: true }, component: () => import('@views/backend/MasterSetup/Exam/Index'), beforeEnter: authGuard },
    { path: '/exam/create', name: 'exam.create', component: () => import('@views/backend/MasterSetup/Exam/Create'), beforeEnter: authGuard },
    { path: '/exam/:id', name: 'exam.show', component: () => import('@views/backend/MasterSetup/Exam/View'), beforeEnter: authGuard },
    { path: '/exam/:id/edit', name: 'exam.edit', component: () => import('@views/backend/MasterSetup/Exam/Create'), beforeEnter: authGuard },

    // ------------------ACADEMIC CLASS MAPPING PORTION------------------
    { path: '/academicClassMapping/:id', name: 'academicClassMapping.show', component: () => import('@views/backend/MasterSetup/AcademicClassMapping/View'), beforeEnter: authGuard },
    { path: '/academicClassMapping/:id/edit', name: 'academicClassMapping.edit', component: () => import('@views/backend/MasterSetup/AcademicClassMapping/Create'), beforeEnter: authGuard },

    // ------------------SUBJECT PORTION------------------
    { path: '/subject', name: 'subject.index', meta: { title: 'Subject', nav: true }, component: () => import('@views/backend/MasterSetup/Subject/Index'), beforeEnter: authGuard },
    { path: '/subject/create', name: 'subject.create', component: () => import('@views/backend/MasterSetup/Subject/Create'), beforeEnter: authGuard },
    { path: '/subject/:id', name: 'subject.show', component: () => import('@views/backend/MasterSetup/Subject/View'), beforeEnter: authGuard },
    { path: '/subject/:id/edit', name: 'subject.edit', component: () => import('@views/backend/MasterSetup/Subject/Create'), beforeEnter: authGuard },


    // ------------------SMS TRANSACTION PORTION------------------
    { path: '/smsTransaction', name: 'smsTransaction.index', meta: { title: 'SmsTransaction', nav: true }, component: () => import('@views/backend/SMS/SmsTransaction/Index'), beforeEnter: authGuard },
    { path: '/smsTransaction/create', name: 'smsTransaction.create', component: () => import('@views/backend/SMS/SmsTransaction/Create'), beforeEnter: authGuard },
    { path: '/smsTransaction/:id', name: 'smsTransaction.show', component: () => import('@views/backend/SMS/SmsTransaction/View'), beforeEnter: authGuard },
    { path: '/smsTransaction/:id/edit', name: 'smsTransaction.edit', component: () => import('@views/backend/SMS/SmsTransaction/Create'), beforeEnter: authGuard },
    // ------------------SMS TEMPLATE PORTION------------------
    { path: '/smsTemplate', name: 'smsTemplate.index', meta: { title: 'SmsTemplate', nav: true }, component: () => import('@views/backend/SMS/SmsTemplate/Index'), beforeEnter: authGuard },
    { path: '/smsTemplate/create', name: 'smsTemplate.create', component: () => import('@views/backend/SMS/SmsTemplate/Create'), beforeEnter: authGuard },
    { path: '/smsTemplate/:id', name: 'smsTemplate.show', component: () => import('@views/backend/SMS/SmsTemplate/View'), beforeEnter: authGuard },
    { path: '/smsTemplate/:id/edit', name: 'smsTemplate.edit', component: () => import('@views/backend/SMS/SmsTemplate/Create'), beforeEnter: authGuard },
    // ------------------SMS HISTORY PORTION------------------
    { path: '/smsHistory', name: 'smsHistory.index', meta: { title: 'SmsHistory', nav: true }, component: () => import('@views/backend/SMS/SmsHistory/Index'), beforeEnter: authGuard },
    { path: '/smsHistory/create', name: 'smsHistory.create', component: () => import('@views/backend/SMS/SmsHistory/Create'), beforeEnter: authGuard },
    { path: '/smsHistory/:id', name: 'smsHistory.show', component: () => import('@views/backend/SMS/SmsHistory/View'), beforeEnter: authGuard },
    { path: '/smsHistory/:id/edit', name: 'smsHistory.edit', component: () => import('@views/backend/SMS/SmsHistory/Create'), beforeEnter: authGuard },

]

export default masterSetupRoutes;