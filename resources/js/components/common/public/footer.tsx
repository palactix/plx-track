import { Code } from "lucide-react";

export function Footer(){
  return (
     <footer className="border-t border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-900 py-6 sm:py-8 px-4 sm:px-6 lg:px-8 ">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-4 sm:gap-6">
              <span className="text-gray-500 dark:text-slate-400">© Palactix</span>
              <a href="https://github.com/palactix/plx-track" className="text-gray-500 dark:text-slate-400 hover:text-primary transition-colors flex items-center gap-1">
                <Code className="w-4 h-4" />
                GitHub
              </a>
            </div>
            <div className="text-gray-500 dark:text-slate-400 text-center sm:text-right text-sm">
              Open-source license notice (MIT)
            </div>
          </div>
        </div>
      </footer>
  )
}