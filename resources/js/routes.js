import Home from './components/Home';
import NotFound from './components/NotFound';
import Dashboard from './components/dashboard/Dashboard';
import DashboardProfile from './components/dashboard/DashboardProfile';
import Login from './components/Login';
import Register from './components/Register';
import ForgotPassword from './components/ForgotPassword';
import ResetPasswordForm from './components/ResetPasswordForm';
import Payment from './components/Payment';
import Invoices from './components/Invoices';
import Transport from './components/transport/Transport';
import Tovari from './components/tovari/Tovari';
import Chat from './components/chat/Chat';
import Firms from './components/Firms';
import SingleFirm from './components/SingleFirm';
import UsersList from './components/users/UsersList';
import SingleUser from './components/users/SingleUser';
import MyUsers from './components/users/MyUsers';
import MyTovari from './components/tovari/MyTovari';
import MyTransport from './components/transport/MyTransport';
import PrivacyPolicy from './components/PrivacyPolicy';
import ContactForm from './components/ContactForm';
import News from './components/news/News';
import AdminDashboard from './admin-components/dashboard/AdminDashboard';
import AdminTovari from './admin-components/tovari/AdminTovari';
import AdminTransport from './admin-components/transport/AdminTransport';
import AdminUsers from './admin-components/users/AdminUsers';
import AdminFirms from './admin-components/firms/AdminFirms';
import VueRouter from 'vue-router';
import store from './store';

const routes = [
		{
			path: '/',
			component: Home,
			name: 'Home',
			meta: {
				hideForAuth: true
			},
			children: [
				{
					path: '/reset-password',
					component: ForgotPassword,
					name: 'ForgotPassword',
					meta: {
						hideForAuth: true,
						requiresAuth:false
					}
				},	
				{
					path: '/reset-password/:token', 
					name: 'reset-password-form', 
					component: ResetPasswordForm, 
					meta: {
						hideForAuth: true,
						requiresAuth:false,
					}
				},
				{
					path: '/privacy',
					name: 'PrivacyPolicyDefault',
					component: PrivacyPolicy,
					meta: {
						hideForAuth: true,
						requiresAuth: false,
					},
				},
				{
					path: '/contact',
					name: 'ContactFormDefault',
					component: ContactForm,
					meta: {
						hideForAuth: true,
						requiresAuth: false,
					},
				},
			]
		},
		{
			path: '*',
			component: NotFound
		},
		{
			path: '/login',
			component: Login,
			name: 'Login',
			meta: {
				hideForAuth: true
			}
		},
		{
			path: '/register',
			component: Register,
			meta: {
				hideForAuth: true
			}
		},
		{
			path: '/dashboard',
			component: Dashboard,
			name: 'Dashboard',
			meta: {
				requiresAuth: true,
				adminAuth:false,
				userAuth:true,
			},
			children: [
		        {
		          path: '',
		          name: 'Profile',
		          component: DashboardProfile,
		          meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
					},
		        },
		        {
		          path: '/dashboard/privacy',
		          name: 'PrivacyPolicy',
		          component: PrivacyPolicy,
		          meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
					},
		        }, 
		        {
		          path: '/dashboard/contact',
		          name: 'ContactForm',
		          component: ContactForm,
		          meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
					},
		        },
		        {
		        	path: 'payment',
		        	name: 'Payment',
		        	component: Payment,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						isMainUser: true
					},
		        },
		        {
		        	path: 'news',
		        	name: 'News',
		        	component: News,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		       		path: 'invoices',
		        	name: 'Invoices',
		        	component: Invoices,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						isMainUser: true,
					},
		        },
		        {
		       		path: 'tovari',
		        	name: 'Tovari',
		        	component: Tovari,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		       		path: 'transport',
		        	name: 'Transport',
		        	component: Transport,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		       		path: 'chat',
		        	name: 'Chat',
		        	component: Chat,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'firms',
		        	name: 'Firms',
		        	component: Firms,
		        },
		        {
		        	path: 'firms/:id',
		        	name: 'SingleFirm',
		        	component: SingleFirm,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'users',
		        	name: 'Users',
		        	component: UsersList,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'users/:id',
		        	name: 'SingleUser',
		        	component: SingleUser,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'my-users',
		        	name: 'MyUsers',
		        	component: MyUsers,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						isMainUser: true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'my-tovari',
		        	name: 'MyTovari',
		        	component: MyTovari,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        },
		        {
		        	path: 'my-transport',
		        	name: 'MyTransport',
		        	component: MyTransport,
		        	meta: {
						requiresAuth: true,
						adminAuth:false,
						userAuth:true,
						hasPayed:true
					},
		        }
		    ],
		},
		{
			path: '/admin',
			name: 'Admin',
			component: AdminDashboard,
			meta: {
				requiresAuth: true,
				adminAuth:true,
				userAuth:false
			},
			children: [
				{
					path: 'users',
					name: 'AdminUsers',
					component: AdminUsers,
					meta: {
						requiresAuth: true,
						adminAuth:true,
						userAuth:false
					},
				},
				{
					path: 'tovari',
					name: 'AdminTovari',
					component: AdminTovari,
					meta: {
						requiresAuth: true,
						adminAuth:true,
						userAuth:false
					},
				},
				{
					path: 'transport',
					name: 'AdminTransport',
					component: AdminTransport,
					meta: {
						requiresAuth: true,
						adminAuth:true,
						userAuth:false
					},
				},
				{
					path: 'firms',
					name: 'AdminFirms',
					component: AdminFirms,
					meta: {
						requiresAuth: true,
						adminAuth:true,
						userAuth:false
					},
				},

			]
		}
]


const router = new VueRouter({
	mode: 'history',
	linkActiveClass: 'current_link',
	routes: routes
})


router.beforeEach((to,form,next) => {
	if(to.meta.requiresAuth) {
		if(store.getters.isLoggedInAndHasToken) {
			//check if we need to be main use
			if(to.meta.isMainUser) {
				return store.getters.isMainUser ? next() : next({ name: 'Profile' })
			}

			if(to.meta.hasPayed) {
				// return store.getters.isPayed ? next() : next({ name: 'Payment' })
			}
			
			//check which role it is beacuse user is logged in
			if(to.meta.adminAuth && !to.meta.userAuth) {
				return store.getters.isAdmin ? next() : next({ name: 'Profile' })
			} else if(!to.meta.adminAuth && to.meta.userAuth) {
				return store.getters.isDefaultUser ? next() : next({ name: 'Admin' })
			}
			// return next({ name: nextName })
			// return nextFlag === false ? next({ name: 'Payment' }) : next()
			return next();
		} else {
			return next({ name: 'Login' })
		}
	} else if(to.meta.hideForAuth) {
		if(store.getters.isLoggedInAndHasToken) {
			return store.getters.isAdmin ? next({ name: 'Admin' }) : next({ name: 'Profile' })
		} else {
			return next()
		}
	} else {
		return next()
	}

})

export default router

