import Home from './components/Home';
import NotFound from './components/NotFound';
import Dashboard from './components/Dashboard';
import DashboardProfile from './components/DashboardProfile';
import Login from './components/Login';
import Register from './components/Register';
import ForgotPassword from './components/ForgotPassword';
import ResetPasswordForm from './components/ResetPasswordForm';
import Payment from './components/Payment';
import Invoices from './components/Invoices';
import Transport from './components/Transport';
import Tovari from './components/Tovari';
import Chat from './components/Chat';
import AdminDashboard from './admin-components/AdminDashboard';
import VueRouter from 'vue-router';
import store from './store';

const routes = [
		{
			path: '/',
			component: Home,
			name: 'Home',
			meta: {
				hideForAuth: true
			}
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
			path: '/reset-password', 
			name: 'reset-password', 
			component: ForgotPassword, 
			meta: {
				requiresAuth:false
			}
		},
		{
		    path: '/reset-password/:token', 
		    name: 'reset-password-form', 
		    component: ResetPasswordForm, 
		    meta: {
		    	requiresAuth:false 
		    }
		},
		{
			path: '/dashboard',
			component: Dashboard,
			name: 'Dashboard',
			meta: {
				requiresAuth: true,
				adminAuth:false,
				userAuth:true
			},
			children: [
		        {
		          path: '',
		          name: 'Profile',
		          component: DashboardProfile,
		        },
		        {
		        	path: 'payment',
		        	name: 'Payment',
		        	component: Payment,
		        },
		        {
		       		path: 'invoices',
		        	name: 'Invoices',
		        	component: Invoices,
		        },
		        {
		       		path: 'tovari',
		        	name: 'Tovari',
		        	component: Tovari,
		        },
		        {
		       		path: 'transport',
		        	name: 'Transport',
		        	component: Transport,
		        },
		        {
		       		path: 'chat',
		        	name: 'Chat',
		        	component: Chat,
		        },
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
			//check which role it is beacuse user is logged in
			if(to.meta.adminAuth && !userAuth) {
				return store.getters.isAdmin ? next() : next({ name: 'Profile' })
			} else if(!to.meta.adminAuth && userAuth) {
				return store.getters.isDefaultUser ? next() : next({ name: 'Admin' })
			}
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

