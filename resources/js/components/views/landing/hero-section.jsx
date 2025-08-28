import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CheckCircle, Copy, BarChart3, Users, Shield, Zap, Code, TrendingUp, Eye, Share2, RefreshCw, ChevronUp, ChevronDown, Calendar, Lightbulb, Info, Sun, Moon, Menu, X } from 'lucide-react';
import { LinkGenerator } from '@/components/common/links/link-generator';

export function HeroSection (){
	return (
		<section className="relative px-4 py-16 sm:px-6 lg:px-8">
			<div className="mx-auto max-w-4xl text-center">
				<div className="mb-8">
					<Badge variant="secondary" className="mb-6 border-primary/30 bg-primary/20 px-4 py-2 text-secondary">
						<Share2 className="mr-2 h-4 w-4" />
					Shorten, Share, Track
				</Badge>
				<h1 className="mb-6 text-3xl leading-tight font-bold text-gray-900 sm:text-4xl md:text-5xl dark:text-white">
					Open Source Link Shortener
				</h1>
				<p className="mb-2 text-lg text-gray-600 sm:text-xl dark:text-slate-300">
					by <a href='https://palactix.com' target='_blank'><span className="font-semibold text-primary">Palactix</span></a>
				</p>
				<p className="mx-auto max-w-2xl text-base text-gray-500 sm:text-lg dark:text-slate-400">
					Create, Share, and Track Short links - Free & Open for All
				</p>
			</div>
			{/* URL Shortener Form */}
			<Card className="mx-auto max-w-4xl border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
				<CardContent className="p-4 sm:p-6">
					<LinkGenerator />
				</CardContent>
			</Card>
		</div>
	</section>
	)
};
