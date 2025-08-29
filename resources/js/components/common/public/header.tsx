import { Button } from '@/components/ui/button';
import { Sun, Moon, Menu, X } from 'lucide-react';
import { useState } from 'react';
import { Link } from '@inertiajs/react';
import { useAppearance } from '@/hooks/use-appearance';

export const Header = () => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const { toggleAppearance } = useAppearance();
  

  return (
    <header className="border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex  h-10 w-25 items-center justify-center">
               <Link href='/'><img src='/logo/plx-track-logo.svg' alt='App Logo' /></Link>
            </div>

            {/* Desktop Navigation */}
            <nav className="hidden sm:flex items-center space-x-8">
              <Link href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors">About</Link>
              <Link href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors">GitHub</Link>
              <Link href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors">Docs</Link>
              {/* Theme Toggle (uses prefers-color-scheme) */}
              <Button
                variant="ghost"
                size="sm"
                onClick={() => toggleAppearance()}
                className="w-9 h-9 p-0 text-gray-600 dark:text-slate-300 hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-700"
                title="Toggle theme"
              >
                <Sun className="w-4 h-4 block dark:hidden" />
                <Moon className="w-4 h-4 hidden dark:block" />
              </Button>
              <Link href={'/login'}>
                <Button variant="secondary" className="bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                  Login
                </Button>
              </Link>
            </nav>

            {/* Mobile Navigation */}
            <div className="flex sm:hidden items-center space-x-2">
              {/* Mobile Theme Toggle */}
              <Button
                variant="ghost"
                size="sm"
                onClick={() => toggleAppearance()}
                className="w-9 h-9 p-0 text-gray-600 dark:text-slate-300 hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-700"
                title="Toggle theme"
              >
                <Sun className="w-4 h-4 block dark:hidden" />
                <Moon className="w-4 h-4 hidden dark:block" />
              </Button>
              {/* Mobile Menu Button */}
              <Button
                variant="ghost"
                size="sm"
                onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                className="w-9 h-9 p-0 text-gray-600 dark:text-slate-300 hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-700"
              >
                {isMobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
              </Button>
            </div>
          </div>
          {/* Mobile Menu Dropdown */}
          {isMobileMenuOpen && (
            <div className="sm:hidden border-t border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800 py-4">
              <div className="flex flex-col space-y-4">
                <a href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors px-4">About</a>
                <a href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors px-4">GitHub</a>
                <a href="#" className="text-gray-600 dark:text-slate-300 hover:text-primary transition-colors px-4">Docs</a>
                <div className="px-4">
                  <Button variant="secondary" className="w-full bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                    Login
                  </Button>
                </div>
              </div>
            </div>
          )}
        </div>
      </header>
  )
}