import { Footer } from "@/components/common/public/footer";
import { Header } from "@/components/common/public/header";

import { ReactNode } from "react";

export function PublicLayout({children}: {children: ReactNode}) {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-slate-900">
       <Header />
       {children}
       <Footer />
    </div>
  )
}