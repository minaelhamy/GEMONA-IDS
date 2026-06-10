const LoginComponent = () => import("../../components/frontend/auth/LoginComponent");
const SignupComponent = () => import("../../components/frontend/auth/SignupComponent.vue");
const ForgotPasswordComponent = () => import("../../components/frontend/auth/ForgotPasswordComponent");
const ForgotPasswordVerifyComponent = () => import("../../components/frontend/auth/ForgotPasswordVerifyComponent");
const ResetPasswordComponent = () => import("../../components/frontend/auth/ResetPasswordComponent");
const SignupVerifyComponent = () => import("../../components/frontend/auth/SignupVerifyComponent");
const SocialLoginComponent = () => import("../../components/frontend/socialLogin/SocialLoginComponent.vue");

export default [
    {
        path: '/login',
        component: LoginComponent,
        name: 'auth.login',
        meta: {
            isFrontend: true,
            auth: false
        }
    },
    {
        path: "/login/google/callback",
        component: SocialLoginComponent,
        name: "frontend.socialLogin",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: '/signup',
        component: SignupComponent,
        name: 'auth.signup',
        meta: {
            isFrontend: true,
            auth: false
        }
    },
    {
        path: '/signup/verify',
        component: SignupVerifyComponent,
        name: 'auth.signupVerify',
        meta: {
            isFrontend: true,
            auth: false
        }
    },
    {
        path: '/forgot-password',
        component: ForgotPasswordComponent,
        name: 'auth.forgotPassword',
        meta: {
            isFrontend: true,
            auth: false
        }
    },
    {
        path: '/forgot-password/verify',
        name: 'auth.forgotPasswordVerify',
        component: ForgotPasswordVerifyComponent,
        meta: {
            isFrontend: true,
            auth: false
        }
    },
    {
        path: '/forgot-password/reset-password',
        name: 'auth.resetPassword',
        component: ResetPasswordComponent,
        meta: {
            isFrontend: true,
            auth: false
        }
    },

];
