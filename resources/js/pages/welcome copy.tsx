import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CheckCircle, Copy, BarChart3, Users, Shield, Zap, Code, TrendingUp, Eye, Share2, RefreshCw, ChevronUp, ChevronDown, Calendar, Lightbulb, Info, Sun, Moon, Menu, X } from 'lucide-react';
import { ImageWithFallback } from '@/components/figma/ImageWithFallback';
// import { Analytics } from '@/components/Analytics';

export default function App() {
  const [url, setUrl] = useState('');
  const [customAlias, setCustomAlias] = useState('');
  const [password, setPassword] = useState('');
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [expirationDate, setExpirationDate] = useState('');
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [currentPage, setCurrentPage] = useState<'home' | 'analytics'>('home');
  const [isDarkMode, setIsDarkMode] = useState(true);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const toggleTheme = () => {
    setIsDarkMode(!isDarkMode);
    if (isDarkMode) {
      document.documentElement.classList.remove('dark');
    } else {
      document.documentElement.classList.add('dark');
    }
  };

  const features = [
    { icon: Shield, title: "No Sign-up Required", description: "Start shortening links immediately" },
    { icon: Zap, title: "Customizable Short URLs", description: "Create branded short links" },
    { icon: Code, title: "100% Open Source (MIT)", description: "Transparent and trustworthy" },
    { icon: Users, title: "Part of Palactix Ecosystem", description: "Integrated platform benefits" },
    { icon: BarChart3, title: "Built-in Public Analytics", description: "Track link performance" },
    { icon: TrendingUp, title: "Developer Friendly", description: "API access and documentation" }
  ];

  const recentLinks = [
    {
      id: "MINvOJ",
      title: "Build Faster Laravel Apps: Deep Dive Into Jobs, Listeners & Artisan Commands",
      domain: "laracasts.com",
      clicks: 1,
      timeAgo: "28 minutes ago",
      image: "https://images.unsplash.com/photo-1692106979244-a2ac98253f6b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHx3ZWIlMjBkZXZlbG9wbWVudCUyMGNvZGluZ3xlbnwxfHx8fDE3NTU2MjQ4NDR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    },
    {
      id: "W7wJ64",
      title: "Build Faster Laravel Apps: Deep Dive Into Jobs, Listeners & Artisan Commands", 
      domain: "laracasts.com",
      clicks: 1,
      timeAgo: "21 minutes ago",
      image: "https://images.unsplash.com/photo-1546900703-cf06143d1239?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHx3ZWIlMjBkZXZlbG9wbWVudCUyMGNvZGluZ3xlbnwxfHx8fDE3NTU2MjQ4NDR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    }
  ];

  const themeClasses = isDarkMode 
    ? "min-h-screen bg-slate-900" 
    : "min-h-screen bg-gray-50";

  const headerClasses = isDarkMode 
    ? "border-b border-slate-700 bg-slate-900" 
    : "border-b border-gray-200 bg-white";

  const cardClasses = isDarkMode 
    ? "bg-slate-800 border-slate-700" 
    : "bg-white border-gray-200";

  const textPrimary = isDarkMode ? "text-white" : "text-gray-900";
  const textSecondary = isDarkMode ? "text-slate-300" : "text-gray-600";
  const textMuted = isDarkMode ? "text-slate-400" : "text-gray-500";

  return (
    <div className={themeClasses}>
      {/* Header */}
      <header className={headerClasses}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <span className="text-2xl font-bold" style={{ color: '#238636' }}>
                pix.bz
              </span>
            </div>

            {/* Desktop Navigation */}
            <nav className="hidden sm:flex items-center space-x-8">
              <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors`}>About</a>
              <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors`}>GitHub</a>
              <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors`}>Docs</a>
              
              {/* Theme Toggle */}
              <Button
                variant="ghost"
                size="sm"
                onClick={toggleTheme}
                className={`w-9 h-9 p-0 ${textSecondary} hover:${textPrimary.split(' ')[0]}-white hover:bg-${isDarkMode ? 'slate-700' : 'gray-100'}`}
                title={isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
              >
                {isDarkMode ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
              </Button>

              <Button variant="secondary" className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white hover:bg-slate-600' : 'bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200'}`}>
                Login
              </Button>
            </nav>

            {/* Mobile Navigation */}
            <div className="flex sm:hidden items-center space-x-2">
              {/* Mobile Theme Toggle */}
              <Button
                variant="ghost"
                size="sm"
                onClick={toggleTheme}
                className={`w-9 h-9 p-0 ${textSecondary} hover:${textPrimary.split(' ')[0]}-white hover:bg-${isDarkMode ? 'slate-700' : 'gray-100'}`}
                title={isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
              >
                {isDarkMode ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
              </Button>

              {/* Mobile Menu Button */}
              <Button
                variant="ghost"
                size="sm"
                onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                className={`w-9 h-9 p-0 ${textSecondary} hover:${textPrimary.split(' ')[0]}-white hover:bg-${isDarkMode ? 'slate-700' : 'gray-100'}`}
              >
                {isMobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
              </Button>
            </div>
          </div>

          {/* Mobile Menu Dropdown */}
          {isMobileMenuOpen && (
            <div className={`sm:hidden border-t ${isDarkMode ? 'border-slate-700 bg-slate-800' : 'border-gray-200 bg-white'} py-4`}>
              <div className="flex flex-col space-y-4">
                <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors px-4`}>About</a>
                <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors px-4`}>GitHub</a>
                <a href="#" className={`${textSecondary} hover:${textPrimary.split(' ')[0]}-white transition-colors px-4`}>Docs</a>
                <div className="px-4">
                  <Button variant="secondary" className={`w-full ${isDarkMode ? 'bg-slate-700 border-slate-600 text-white hover:bg-slate-600' : 'bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200'}`}>
                    Login
                  </Button>
                </div>
              </div>
            </div>
          )}
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative py-16 px-4 sm:px-6 lg:px-8">
        <div className="max-w-4xl mx-auto text-center">
          <div className="mb-8">
            <Badge variant="secondary" className="mb-6 px-4 py-2" style={{ backgroundColor: '#238636/20', borderColor: '#238636/30', color: '#4ade80' }}>
              <Share2 className="w-4 h-4 mr-2" />
              Shorten, Share, Track
            </Badge>
            <h1 className={`text-3xl sm:text-4xl md:text-5xl font-bold ${textPrimary} mb-6 leading-tight`}>
              Open Source Link Shortener
            </h1>
            <p className={`text-lg sm:text-xl ${textSecondary} mb-2`}>
              by <span className="font-semibold" style={{ color: '#238636' }}>Palactix</span>
            </p>
            <p className={`text-base sm:text-lg ${textMuted} max-w-2xl mx-auto`}>
              Create, Share, and Track Short links - Free & Open for All
            </p>
          </div>

          {/* URL Shortener Form */}
          <Card className={`max-w-4xl mx-auto ${cardClasses}`}>
            <CardContent className="p-4 sm:p-6">
              <div className="flex flex-col sm:flex-row gap-3 mb-4">
                <Input
                  type="url"
                  placeholder="Enter a long URL (e.g. https://example.com)"
                  value={url}
                  onChange={(e) => setUrl(e.target.value)}
                  className={`flex-1 ${isDarkMode ? 'bg-slate-700 border-slate-600 text-white placeholder:text-slate-400' : 'bg-gray-50 border-gray-300 text-gray-900 placeholder:text-gray-500'}`}
                  style={{ focusBorderColor: '#238636' }}
                />
                <Button className="text-white px-4 sm:px-6 md:px-8 w-full sm:w-auto" style={{ backgroundColor: '#238636' }} 
                       onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#1e7e34'}
                       onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#238636'}>
                  Generate Short Link
                </Button>
              </div>
              
              <button
                onClick={() => setShowAdvanced(!showAdvanced)}
                className="text-sm transition-colors flex items-center mx-auto mb-4"
                style={{ color: '#238636' }}
                onMouseEnter={(e) => e.currentTarget.style.color = '#4ade80'}
                onMouseLeave={(e) => e.currentTarget.style.color = '#238636'}
              >
                {showAdvanced ? <ChevronUp className="w-4 h-4 mr-1" /> : <ChevronDown className="w-4 h-4 mr-1" />}
                {showAdvanced ? 'Hide Advanced Options' : 'Show Advanced Options'}
              </button>

              {/* Advanced Options */}
              {showAdvanced && (
                <div className={`${isDarkMode ? 'bg-slate-700/30 border-slate-600' : 'bg-gray-50 border-gray-200'} rounded-lg border p-4 sm:p-6 mb-4`}>
                  <h3 className={`${textPrimary} font-medium mb-4 text-left`}>Advanced Options</h3>
                  
                  <div className="grid md:grid-cols-2 gap-4 mb-4">
                    {/* Custom Alias */}
                    <div>
                      <label className={`block ${textSecondary} text-sm mb-2 text-left`}>
                        Custom Alias (Optional)
                      </label>
                      <Input
                        placeholder="e.g., my-custom-link"
                        value={customAlias}
                        onChange={(e) => setCustomAlias(e.target.value)}
                        className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white placeholder:text-slate-400' : 'bg-white border-gray-300 text-gray-900 placeholder:text-gray-500'}`}
                      />
                    </div>

                    {/* Password Protection */}
                    <div>
                      <label className={`block ${textSecondary} text-sm mb-2 text-left`}>
                        Password Protection (Optional)
                      </label>
                      <Input
                        type="password"
                        placeholder="Enter password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white placeholder:text-slate-400' : 'bg-white border-gray-300 text-gray-900 placeholder:text-gray-500'}`}
                      />
                    </div>
                  </div>

                  <div className="grid md:grid-cols-2 gap-4 mb-4">
                    {/* Title */}
                    <div>
                      <label className={`block ${textSecondary} text-sm mb-2 text-left`}>
                        Title (Optional) • <span style={{ color: '#238636' }}>Auto-detected if empty</span>
                      </label>
                      <Input
                        placeholder="Give your link a title (or leave empty for auto-detection)"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white placeholder:text-slate-400' : 'bg-white border-gray-300 text-gray-900 placeholder:text-gray-500'}`}
                      />
                    </div>

                    {/* Expiration Date */}
                    <div>
                      <label className={`block ${textSecondary} text-sm mb-2 text-left`}>
                        Expiration Date (Optional)
                      </label>
                      <div className="relative">
                        <Input
                          type="datetime-local"
                          value={expirationDate}
                          onChange={(e) => setExpirationDate(e.target.value)}
                          className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`}
                        />
                        <Calendar className={`absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 ${textMuted} pointer-events-none`} />
                      </div>
                    </div>
                  </div>

                  {/* Description */}
                  <div className="mb-4">
                    <label className={`block ${textSecondary} text-sm mb-2 text-left`}>
                      Description (Optional) • <span style={{ color: '#238636' }}>Auto-detected if empty</span>
                    </label>
                    <Textarea
                      placeholder="Add a description for your link (or leave empty for auto-detection)"
                      value={description}
                      onChange={(e) => setDescription(e.target.value)}
                      className={`${isDarkMode ? 'bg-slate-700 border-slate-600 text-white placeholder:text-slate-400' : 'bg-white border-gray-300 text-gray-900 placeholder:text-gray-500'} resize-none`}
                      rows={3}
                    />
                  </div>

                  {/* Smart Detection Info */}
                  <div className="bg-purple-600/10 border border-purple-500/30 rounded-lg p-4 mb-4">
                    <div className="flex items-start gap-2">
                      <Lightbulb className="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" />
                      <div className="text-sm text-purple-300">
                        <strong>Smart Detection:</strong> If you leave title or description empty, we'll automatically fetch them from the webpage's meta tags for better link previews.
                      </div>
                    </div>
                  </div>
                </div>
              )}

              <div className={`p-4 ${isDarkMode ? 'bg-slate-700/50 border-slate-600' : 'bg-gray-50 border-gray-200'} rounded-lg border`}>
                <div className="flex items-start gap-2 text-sm">
                  <Info className="w-4 h-4 text-cyan-400 mt-0.5 flex-shrink-0" />
                  <p className={textSecondary}>
                    <strong>Tip:</strong> Create an account to manage your links, view detailed analytics, and access advanced features. 
                    <a href="#" className="hover:text-cyan-300 ml-1" style={{ color: '#238636' }}>Sign up free</a>
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </section>

      {/* Features Grid */}
      <section className="py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-6xl mx-auto">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((feature, index) => {
              const Icon = feature.icon;
              return (
                <Card key={index} className={`${cardClasses} hover:${isDarkMode ? 'bg-slate-700/50' : 'bg-gray-100'} transition-all duration-300`}>
                  <CardContent className="p-4">
                    <div className="flex items-center gap-3 mb-2">
                      <div className="w-8 h-8 rounded-full flex items-center justify-center" style={{ backgroundColor: '#238636/20' }}>
                        <CheckCircle className="w-4 h-4" style={{ color: '#4ade80' }} />
                      </div>
                      <Icon className={`w-4 h-4 ${textMuted}`} />
                    </div>
                    <h3 className={`font-semibold ${textPrimary} mb-1`}>{feature.title}</h3>
                    <p className={`${textMuted} text-sm`}>{feature.description}</p>
                  </CardContent>
                </Card>
              );
            })}
          </div>
        </div>
      </section>

      {/* Recent Public Links */}
      <section className="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 mb-8">
        <div className="max-w-4xl mx-auto">
          {/* Section Header */}
          <div className="mb-6 sm:mb-8">
            <div className="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
              <h2 className={`text-xl sm:text-2xl font-bold ${textPrimary} flex-shrink-0`}>Recent Public Links</h2>
              <Button 
                variant="secondary" 
                size="sm" 
                className={`self-start sm:self-auto ${isDarkMode ? 'bg-slate-700 border-slate-600 text-white hover:bg-slate-600' : 'bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200'}`}
              >
                <RefreshCw className="w-4 h-4 mr-2" />
                Refresh
              </Button>
            </div>
          </div>
          
          <div className="space-y-4">
            {recentLinks.map((link) => (
              <Card key={link.id} className={`${cardClasses} hover:${isDarkMode ? 'bg-slate-700/50' : 'bg-gray-50'} transition-all duration-300`}>
                <CardContent className="p-4 sm:p-6">
                  <div className="flex flex-col gap-4">
                    {/* Top Row: Image and Content */}
                    <div className="flex gap-4">
                      {/* Website Image/Favicon */}
                      <div className={`w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden flex-shrink-0 ${isDarkMode ? 'bg-slate-700' : 'bg-gray-200'} flex items-center justify-center`}>
                        <ImageWithFallback
                          src={link.image}
                          alt={`${link.domain} preview`}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      
                      {/* Content */}
                      <div className="flex-1 min-w-0">
                        <h3 className={`font-medium ${textPrimary} mb-1 line-clamp-2 sm:truncate`}>{link.title}</h3>
                        <div className={`flex flex-wrap items-center gap-2 text-sm ${textMuted} mb-2`}>
                          <span className="flex items-center gap-1">
                            <span className="w-2 h-2 rounded-full bg-green-400"></span>
                            {link.domain}
                          </span>
                          <span>•</span>
                          <span>{link.timeAgo}</span>
                        </div>
                        <div className={`text-sm ${textMuted} line-clamp-2`}>
                          Explore senior roles in Laravel trending: Jobs, Commands, and Queuesable Listeners Best practices and examples for efficient background processing.
                        </div>
                      </div>
                    </div>

                    {/* Bottom Row: Stats and Actions */}
                    <div className="flex items-center justify-between">
                      {/* Click Stats */}
                      <div className={`text-center px-3 py-1.5 ${isDarkMode ? 'bg-slate-700/50' : 'bg-gray-100'} rounded-lg`}>
                        <div className={`text-base sm:text-lg font-semibold ${textPrimary}`}>{link.clicks}</div>
                        <div className={`text-xs ${textMuted}`}>clicks</div>
                      </div>
                      
                      {/* Action Buttons */}
                      <div className="flex gap-2">
                        <Button 
                          size="sm" 
                          variant="ghost" 
                          className={`w-8 h-8 sm:w-9 sm:h-9 p-0 ${textMuted} hover:${textPrimary.split(' ')[0]}-white hover:${isDarkMode ? 'bg-slate-700' : 'bg-gray-100'} rounded-lg`}
                          title="Preview"
                        >
                          <Eye className="w-4 h-4" />
                        </Button>
                        <Button 
                          size="sm" 
                          variant="ghost" 
                          className={`w-8 h-8 sm:w-9 sm:h-9 p-0 ${textMuted} hover:${textPrimary.split(' ')[0]}-white hover:${isDarkMode ? 'bg-slate-700' : 'bg-gray-100'} rounded-lg`}
                          title="Copy Link"
                        >
                          <Copy className="w-4 h-4" />
                        </Button>
                        <Button 
                          size="sm" 
                          variant="ghost" 
                          className="w-8 h-8 sm:w-9 sm:h-9 p-0 text-slate-400 hover:text-white rounded-lg"
                          style={{ backgroundColor: 'rgba(35, 134, 54, 0.2)' }}
                          onClick={() => setCurrentPage('analytics')}
                          title="View Analytics"
                          onMouseEnter={(e) => e.currentTarget.style.backgroundColor = 'rgba(35, 134, 54, 0.3)'}
                          onMouseLeave={(e) => e.currentTarget.style.backgroundColor = 'rgba(35, 134, 54, 0.2)'}
                        >
                          <BarChart3 className="w-4 h-4" style={{ color: '#4ade80' }} />
                        </Button>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>

          <div className="mt-6 sm:mt-8 text-center">
            <div className={`flex flex-wrap items-center justify-center gap-2 text-sm ${textMuted}`}>
              <div className="w-2 h-2 rounded-full" style={{ backgroundColor: '#4ade80' }}></div>
              <span>Live</span>
              <span>•</span>
              <span>Auto-updates</span>
              <span>•</span>
              <span>Can view • 2 links shown</span>
            </div>
          </div>
        </div>
      </section>

      {/* Analytics CTA */}
      <section className="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 mb-8">
        <div className="max-w-4xl mx-auto">
          <Card className="border" style={{ backgroundColor: 'rgba(35, 134, 54, 0.1)', borderColor: 'rgba(35, 134, 54, 0.3)' }}>
            <CardContent className="p-6 sm:p-8 md:p-12 text-center">
              <div className="mb-6">
                <div className="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-4" style={{ backgroundColor: '#238636' }}>
                  <BarChart3 className="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                </div>
                <h2 className={`text-xl sm:text-2xl md:text-3xl font-bold ${textPrimary} mb-4`}>Want Full Analytics Access?</h2>
                <p className={`text-base sm:text-lg ${textSecondary} max-w-2xl mx-auto`}>
                  Get detailed insights, custom domains, and advanced features with a free account
                </p>
              </div>
              <Button size="lg" className="text-white px-6 sm:px-8 py-3" style={{ backgroundColor: '#238636' }}
                     onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#1e7e34'}
                     onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#238636'}>
                Sign Up Free
              </Button>
            </CardContent>
          </Card>
        </div>
      </section>

      {/* Footer */}
      <footer className={`border-t ${isDarkMode ? 'border-slate-700 bg-slate-900' : 'border-gray-200 bg-white'} py-6 sm:py-8 px-4 sm:px-6 lg:px-8 mt-8 sm:mt-16`}>
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-4 sm:gap-6">
              <span className={textMuted}>© Palactix</span>
              <a href="#" className={`${textMuted} hover:${textPrimary.split(' ')[0]}-white transition-colors flex items-center gap-1`}>
                <Code className="w-4 h-4" />
                GitHub
              </a>
            </div>
            <div className={`${textMuted} text-center sm:text-right text-sm`}>
              Open-source license notice (MIT)
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}