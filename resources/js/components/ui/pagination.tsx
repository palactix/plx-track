import * as React from "react"
import { ChevronLeft, ChevronRight, MoreHorizontal } from "lucide-react"

import { cn } from "@/lib/utils"
import { buttonVariants } from "@/components/ui/button"

const Pagination = ({ className, ...props }: React.ComponentProps<"nav">) => (
  <nav
    role="navigation"
    aria-label="pagination"
    className={cn("mx-auto flex w-full justify-center", className)}
    {...props}
  />
)
Pagination.displayName = "Pagination"

const PaginationContent = React.forwardRef<
  HTMLUListElement,
  React.ComponentProps<"ul">
>(({ className, ...props }, ref) => (
  <ul
    ref={ref}
    className={cn("flex flex-row items-center gap-1", className)}
    {...props}
  />
))
PaginationContent.displayName = "PaginationContent"

const PaginationItem = React.forwardRef<
  HTMLLIElement,
  React.ComponentProps<"li">
>(({ className, ...props }, ref) => (
  <li ref={ref} className={cn("", className)} {...props} />
))
PaginationItem.displayName = "PaginationItem"

type ButtonSize = "default" | "sm" | "lg" | "icon"

type PaginationLinkProps = {
  isActive?: boolean
  size?: ButtonSize
} & React.ComponentProps<"a">

const PaginationLink = ({
  className,
  isActive,
  size = "icon",
  ...props
}: PaginationLinkProps) => (
  <a
    aria-current={isActive ? "page" : undefined}
    className={cn(
      buttonVariants({
        variant: isActive ? "outline" : "ghost",
        size,
      }),
      className
    )}
    {...props}
  />
)
PaginationLink.displayName = "PaginationLink"

const PaginationPrevious = ({
  className,
  ...props
}: React.ComponentProps<typeof PaginationLink>) => (
  <PaginationLink
    aria-label="Go to previous page"
    size="default"
    className={cn("gap-1 pl-2.5", className)}
    {...props}
  >
    <ChevronLeft className="h-4 w-4" />
    <span>Previous</span>
  </PaginationLink>
)
PaginationPrevious.displayName = "PaginationPrevious"

const PaginationNext = ({
  className,
  ...props
}: React.ComponentProps<typeof PaginationLink>) => (
  <PaginationLink
    aria-label="Go to next page"
    size="default"
    className={cn("gap-1 pr-2.5", className)}
    {...props}
  >
    <span>Next</span>
    <ChevronRight className="h-4 w-4" />
  </PaginationLink>
)
PaginationNext.displayName = "PaginationNext"

const PaginationEllipsis = ({
  className,
  ...props
}: React.ComponentProps<"span">) => (
  <span
    aria-hidden
    className={cn("flex h-9 w-9 items-center justify-center", className)}
    {...props}
  >
    <MoreHorizontal className="h-4 w-4" />
    <span className="sr-only">More pages</span>
  </span>
)
PaginationEllipsis.displayName = "PaginationEllipsis"

export {
  Pagination,
  PaginationContent,
  PaginationLink,
  PaginationItem,
  PaginationPrevious,
  PaginationNext,
  PaginationEllipsis,
}

// Simple numeric pagination renderer
type PaginationNumbersProps = {
  current: number
  total: number
  onChange: (page: number) => void
  maxDisplayed?: number
}

function PaginationNumbers({ current, total, onChange, maxDisplayed = 7 }: PaginationNumbersProps) {
  if (total <= 1) return null

  const pages: (number | "...")[] = []
  const half = Math.floor(maxDisplayed / 2)
  let start = Math.max(1, current - half)
  let end = Math.min(total, current + half)

  if (end - start + 1 < maxDisplayed) {
    if (start === 1) end = Math.min(total, start + maxDisplayed - 1)
    else if (end === total) start = Math.max(1, end - maxDisplayed + 1)
  }

  if (start > 1) {
    pages.push(1)
    if (start > 2) pages.push("...")
  }

  for (let p = start; p <= end; p++) pages.push(p)

  if (end < total) {
    if (end < total - 1) pages.push("...")
    pages.push(total)
  }

  return (
    <Pagination>
      <PaginationContent>
        <PaginationItem>
          <PaginationLink onClick={() => onChange(Math.max(1, current - 1))}>Previous</PaginationLink>
        </PaginationItem>
        {pages.map((p, i) => (
          typeof p === "number" ? (
            <PaginationItem key={p}>
              <PaginationLink isActive={p === current} onClick={() => onChange(p)}>{p}</PaginationLink>
            </PaginationItem>
          ) : (
            <PaginationItem key={`e-${i}`}>
              <PaginationEllipsis />
            </PaginationItem>
          )
        ))}
        <PaginationItem>
          <PaginationLink onClick={() => onChange(Math.min(total, current + 1))}>Next</PaginationLink>
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  )
}

export { PaginationNumbers }
