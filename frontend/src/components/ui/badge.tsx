import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const badgeVariants = cva(
  "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
  {
    variants: {
      variant: {
        default: "bg-indigo-500/10 text-indigo-400 border border-indigo-500/20",
        secondary: "bg-zinc-800 text-zinc-300 border border-zinc-700",
        destructive: "bg-rose-500/10 text-rose-400 border border-rose-500/20",
        outline: "text-zinc-400 border border-zinc-800",
        success: "bg-emerald-500/10 text-emerald-400 border border-emerald-500/20",
        cyan: "bg-cyan-500/10 text-cyan-400 border border-cyan-500/20",
        amber: "bg-amber-500/10 text-amber-400 border border-amber-500/20",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
);

export interface BadgeProps
  extends React.HTMLAttributes<HTMLDivElement>,
    VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, ...props }: BadgeProps) {
  return (
    <div className={cn(badgeVariants({ variant }), className)} {...props} />
  );
}

export { Badge, badgeVariants };
