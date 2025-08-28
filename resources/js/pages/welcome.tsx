import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { CheckCircle, BarChart3, Users, Shield, Zap, Code, Globe } from 'lucide-react';
import { Header } from '@/components/common/public/header';
import { HeroSection } from '@/components/views/landing/hero-section';
import { RecentLinks } from '@/components/views/landing/recent-links';
import { Footer } from '@/components/common/public/footer';
// import { Analytics } from '@/components/Analytics';

export default function App() {  

  const features = [
    { icon: Shield, title: "No Sign-up Required", description: "Start shortening links instantly — no barriers." },
    { icon: Zap, title: "Customizable Short URLs", description: "Create branded and memorable short links." },
    { icon: BarChart3, title: "Built-in Public Analytics", description: "Track clicks and performance in real time." },
    { icon: Code, title: "100% Open Source (MIT)", description: "Transparent, trustworthy, and community-driven." },
    { icon: Users, title: "Developer Friendly", description: "Simple API access with clear documentation." },
    { icon: Globe, title: "Part of Palactix Ecosystem", description: "Scale seamlessly with integrated platform benefits." },
  ];

  

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-slate-900">
      {/* Header */}
      <Header />
      {/* Hero Section */}
      <HeroSection />

      {/* Features Grid */}
      <section className="py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-6xl mx-auto">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((feature, index) => {
              const Icon = feature.icon;
              return (
                <Card key={index} className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-all duration-300">
                  <CardContent className="">
                    <div className="flex items-center gap-3 mb-2">
                      <div className="w-8 h-8 rounded-full flex items-center justify-center bg-primary/20">
                        <CheckCircle className="w-4 h-4 text-secondary" />
                      </div>
                      <Icon className="w-4 h-4 text-gray-500 dark:text-slate-400" />
                    </div>
                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1">{feature.title}</h3>
                    <p className="text-gray-500 dark:text-slate-400 text-sm">{feature.description}</p>
                  </CardContent>
                </Card>
              );
            })}
          </div>
        </div>
      </section>

      {/* Recent Public Links */}
      <RecentLinks /> 

      {/* Analytics CTA */}
      <section className="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 mb-8">
        <div className="max-w-4xl mx-auto">
          <Card className="border bg-primary/10 border-primary/30">
            <CardContent className="p-6 sm:p-8 md:p-12 text-center">
              <div className="mb-6">
                <div className="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-primary">
                  <BarChart3 className="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                </div>
                <h2 className="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">Want Full Analytics Access?</h2>
                <p className="text-base sm:text-lg text-gray-600 dark:text-slate-300 max-w-2xl mx-auto">
                  Get detailed insights, custom domains, and advanced features with a free account
                </p>
              </div>
              <Button size="lg" className="text-white px-6 sm:px-8 py-3 bg-primary hover:bg-secondary">
                Sign Up Free
              </Button>
            </CardContent>
          </Card>
        </div>
      </section>

      {/* Footer */}
     <Footer />
    </div>
  );
}