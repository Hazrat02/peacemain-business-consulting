import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import ServicesView from '../views/ServicesView.vue'
import ContactView from '../views/ContactView.vue'
import AboutView from '../views/AboutView.vue'
import riskView from '../views/RiskView.vue'
import consultantsView from '../views/consultants.vue'
import estateView from '../views/estate.vue'
import constructionView from '../views/construction.vue'
import finnaceView from '../views/finnace.vue'
import dealershipView from '../views/dealership.vue'
import termsView from '../views/terms.vue'
import cookieView from '../views/cookie.vue'
import storyView from '../views/story.vue'
import faqView from '../views/faq.vue'
import careersView from '../views/careers.vue'
import applyview from '../views/Apply.vue'
// import LeadView from '../views/LeadView.vue'




const router = createRouter({
  
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [



    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta:{
   
        // requiresAuth:'auth'
      },
      
    },
    {
      path: '/about',
      name: 'about',
     
      component: AboutView,
      meta:{

      },
    },
    // Auth is handled in Laravel backend (Inertia), not in old frontend routes.
    {
      path: '/servics',
      name: 'servics',

      component:ServicesView,
     
    },
    {
      path: '/contact',
      name: 'contactUs',
  
      component:ContactView,
 
    },
    {
      path: '/risk/disclosure',
      name: 'risk',
  
      component:riskView,
 
    },
    
    {
      path: '/consultants',
      name: 'consultants',
  
      component:consultantsView,
 
    },
    {
      path: '/estate',
      name: 'estate',
  
      component:estateView,
 
    },
    {
      path: '/construction',
      name: 'construction',
  
      component:constructionView,
 
    },
    {
      path: '/finnace',
      name: 'finnace',
  
      component:finnaceView,
 
    },
    {
      path: '/dealership',
      name: 'dealership',
  
      component:dealershipView,
 
    },
    {
      path: '/terms',
      name: 'terms',
  
      component:termsView,
 
    },
    {
      path: '/cookie',
      name: 'cookie',
  
      component:cookieView,
 
    },
    {
      path: '/story',
      name: 'story',
  
      component:storyView,
 
    },
    {
      path: '/faq',
      name: 'faq',
  
      component:faqView,
 
    },
    {
      path: '/careers',
      name: 'careers',
  
      component:careersView,
 
    },
    {
      path: '/apply',
      name: 'job',
  
      component:applyview,
 
    },




















    // user Deshboard--------------------------------------------------
    // {
    //   path: '/dashboard',
    //   name: 'dashboard',

    //   component:DeshboardView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/kyc',
    //   name: 'kyc',

    //   component:KycView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    
    // {
    //   path: '/deposit',
    //   name: 'deposit',

    //   component:DepositView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/deposit/log',
    //   name: 'deposit-log',

    //   component:depositLog,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    
    // {
    //   path: '/withdraw',
    //   name: 'withdraw',

    //   component:WithdrawView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/withdraw/log',
    //   name: 'withdraw-log',

    //   component:withdrawLog,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/transaction',
    //   name: 'transaction',

    //   component:TransactionView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },

    // {
    //   path: '/account',
    //   name: 'account',

    //   component:AccountView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/change',
    //   name: 'change',

    //   component:ChangeView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },




    // admin----------------------------
    // {
    //   path: '/admin/dashboard',
    //   name: 'admindashboard',

    //   component:AdminDeshboardview,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/user',
    //   name: 'alluser',

    //   component:UserManageview,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/user.details/:id?',
    //   name: 'userdetails',

    //   component:UserDetailsview,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/deposit/:id?',
    //   name: 'AdminDeposit',

    //   component:AdminDepositView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/withdraw/:id?',
    //   name: 'AdminWithdraw',

    //   component:AdminWithdrawView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/transfer/:id?',
    //   name: 'transferView',

    //   component:transferView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/leads',
    //   name: 'leads',

    //   component:LeadsManageview,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/leadcollect',
    //   name: 'lead',

    //   component:Leadsview,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    
    // {
    //   path: '/admin/transaction',
    //   name: 'Admintransaction',

    //   component:AdminTransactionView,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    // {
    //   path: '/admin/change',
    //   name: 'adminchange',

    //   component:AdminChangeView ,
    //   meta:{
    //     requiresAuth:true,
    //   },
    // },
    
  ],
  scrollBehavior(to, from, savedPosition) {
    // always scroll to top
    return { top: 0 }
  },

})










export default router
