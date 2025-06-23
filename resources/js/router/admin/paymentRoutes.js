import { authGuard } from './authGuard';

const paymentRoutes = [
    // ------------------PAYMENT GATEWAY PORTION------------------
    { path: '/paymentGateway', name: 'paymentGateway.index', meta: { title: 'PaymentGateway', nav: true }, component: () => import('@views/backend/PaymentGateway/Index'), beforeEnter: authGuard },
    { path: '/paymentGateway/create', name: 'paymentGateway.create', component: () => import('@views/backend/PaymentGateway/Create'), beforeEnter: authGuard },
    { path: '/paymentGateway/:id', name: 'paymentGateway.show', component: () => import('@views/backend/PaymentGateway/View'), beforeEnter: authGuard },
    { path: '/paymentGateway/:id/edit', name: 'paymentGateway.edit', component: () => import('@views/backend/PaymentGateway/Create'), beforeEnter: authGuard },
    // ------------------FEE SETUP PORTION------------------
    { path: '/feeSetup', name: 'feeSetup.index', meta: { title: 'FeeSetup', nav: true }, component: () => import('@views/backend/payment/fee-setup/Index'), beforeEnter: authGuard },
    { path: '/feeSetup/create', name: 'feeSetup.create', component: () => import('@views/backend/payment/fee-setup/Create'), beforeEnter: authGuard },
    { path: '/feeSetup/:id', name: 'feeSetup.show', component: () => import('@views/backend/payment/fee-setup/View'), beforeEnter: authGuard },
    { path: '/feeSetup/:id/edit', name: 'feeSetup.edit', component: () => import('@views/backend/payment/fee-setup/Create'), beforeEnter: authGuard },
    // ------------------TUITION FEE GENERATE PORTION------------------
    { path: '/tuitionFeeGenerate', name: 'tuitionFeeGenerate.index', component: () => import('@views/backend/payment/tuition-fee-generate/Index'), beforeEnter: authGuard },
    { path: '/tuitionFeeGenerate/create', name: 'tuitionFeeGenerate.create', component: () => import('@views/backend/payment/tuition-fee-generate/Create'), beforeEnter: authGuard },
    { path: '/tuitionFeeGenerate/:id', name: 'tuitionFeeGenerate.show', component: () => import('@views/backend/payment/tuition-fee-generate/View'), beforeEnter: authGuard },
    { path: '/tuitionFeeGenerate/:id/edit', name: 'tuitionFeeGenerate.edit', component: () => import('@views/backend/payment/tuition-fee-generate/Create'), beforeEnter: authGuard },

    // ------------------INVOICE PORTION------------------
    { path: '/invoice', name: 'invoice.index', meta: { title: 'Student Payment Report', nav: true }, component: () => import('@views/backend/Invoice/Index'), beforeEnter: authGuard },
    { path: '/invoice/create', name: 'invoice.create', component: () => import('@views/backend/Invoice/Create'), beforeEnter: authGuard },
    { path: '/invoice/:id', name: 'invoice.show', component: () => import('@views/backend/Invoice/View'), beforeEnter: authGuard },
    { path: '/invoice/:id/edit', name: 'invoice.edit', component: () => import('@views/backend/Invoice/Create'), beforeEnter: authGuard },
    { path: '/account-wise-payment', name: 'invoice.accountWise', component: () => import('@views/backend/Invoice/Index'), beforeEnter: authGuard },
    { path: '/account-head-wise-payment', name: 'invoice.accountHeadWise', component: () => import('@views/backend/Invoice/Index'), beforeEnter: authGuard },
    { path: '/refund-amount', name: 'invoice.refundAmount', component: () => import('@views/backend/Invoice/RefundAmount'), beforeEnter: authGuard },
    { path: '/department-wise-payment', name: 'invoice.departmentWise', component: () => import('@views/backend/Invoice/Departmentwise'), beforeEnter: authGuard },
    { path: '/tuition-fees-dues', name: 'invoice.tuitionFeesDues', component: () => import('@views/backend/Invoice/TuitionFeesDues'), beforeEnter: authGuard },
    { path: '/account-summary', name: 'invoice.accountSummary', component: () => import('@views/backend/Invoice/AccountSummary'), beforeEnter: authGuard },
    { path: '/invoice-print', name: 'invoice.print', component: () => import('@views/backend/Invoice/Print'), beforeEnter: authGuard },
    { path: '/invoice-service-charge', name: 'invoice.serviceCharge', component: () => import('@views/backend/Invoice/ServiceChargeReport'), beforeEnter: authGuard },

];

export default paymentRoutes;