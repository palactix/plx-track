// import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';
// import AuthSplitLayoutTemplate from '@/layouts/auth/auth-split-layout';
import AuthCardLayoutTemplate from '@/layouts/auth/auth-card-layout';

export default function AuthLayout({ children, title, description, ...props }: { children: React.ReactNode; title: string; description: string }) {
    return (
        <AuthCardLayoutTemplate title={title} description={description} {...props}>
            {children}
        </AuthCardLayoutTemplate>
    );
}
