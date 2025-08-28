import { Button } from "@/components/ui/button";
import { Link } from "@/queries/links/link-interface";
import { Check, Copy as CopyIcon } from "lucide-react";
import { useState } from "react";
import { toast } from "react-hot-toast";

export function CopyButton({ link }: { link: Link }) {
  const [copiedLinkId, setCopiedLinkId] = useState<null|string>(null);

  const copyToClipboard = async (link: Link) => {
    const fullUrl = `${window.location.origin}/${link.short_code}`;
    try {
      await navigator.clipboard.writeText(fullUrl);
      setCopiedLinkId(link.id);
      setTimeout(() => setCopiedLinkId(null), 2000);
      toast.success(`Copied: ${fullUrl}`);
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (error: unknown) {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = fullUrl;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      setCopiedLinkId(link.id);
      setTimeout(() => setCopiedLinkId(null), 2000);
      toast.success(`Copied: ${fullUrl}`);
    }
  };
  
  return (
    <Button 
      size="icon"
      variant="ghost"
      className="w-8 h-8 p-0 text-gray-500 dark:text-slate-400 hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg"
      title="Copy Link"
      onClick={() => copyToClipboard(link)}
    >
      {copiedLinkId === link.id ? <Check className="w-4 h-4" /> : <CopyIcon className="w-4 h-4" />}
    </Button>
  )
}