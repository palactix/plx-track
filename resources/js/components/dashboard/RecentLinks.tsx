import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useRecentLinks } from '@/queries/dashboard/use-dashboard';
import { Link2, Plus, Copy } from 'lucide-react';

export function RecentLinks() {
    const { data: links = [], isLoading } = useRecentLinks();

    if (isLoading) {
        return (
            <Card>
                <CardHeader>
                    <div className="flex items-center justify-between">
                        <CardTitle className="flex items-center gap-2 text-gray-900 dark:text-white">
                            <Link2 className="w-5 h-5" />
                            Recent Links
                        </CardTitle>
                        <Skeleton className="w-24 h-8" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div className="space-y-4">
                        {Array.from({ length: 3 }).map((_, index) => (
                            <div key={index} className="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                <div className="flex-1">
                                    <Skeleton className="w-3/4 h-5 mb-2" />
                                    <div className="flex items-center gap-4">
                                        <Skeleton className="w-20 h-4" />
                                        <Skeleton className="w-16 h-4" />
                                        <Skeleton className="w-20 h-4" />
                                    </div>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Skeleton className="w-16 h-6" />
                                    <Skeleton className="w-8 h-8" />
                                </div>
                            </div>
                        ))}
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between">
                    <CardTitle className="flex items-center gap-2 text-gray-900 dark:text-white">
                        <Link2 className="w-5 h-5" />
                        Recent Links
                    </CardTitle>
                    <Button
                        size="sm"
                        className="text-white bg-green-600 hover:bg-green-700"
                    >
                        <Plus className="w-4 h-4 mr-2" />
                        New Link
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {links.map((link) => (
                        <div key={link.id} className="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <div className="flex-1">
                                <h3 className="font-medium text-gray-900 dark:text-white mb-1 truncate">
                                    {link.title}
                                </h3>
                                <div className="flex items-center gap-4 text-sm">
                                    <span className="text-gray-500 dark:text-gray-400">{link.shortUrl}</span>
                                    <span className="text-gray-500 dark:text-gray-400">{link.clicks} clicks</span>
                                    <span className="text-gray-500 dark:text-gray-400">{link.created}</span>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <Badge className="text-green-400 border-green-500/30 bg-green-50 dark:bg-green-900/20">
                                    {link.status}
                                </Badge>
                                <Button size="sm" variant="ghost" className="w-8 h-8 p-0">
                                    <Copy className="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
