
import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

type StatCardProps = {
  icon: React.ReactNode;
  title: string;
  value: string;
  description?: string;
  className?: string;
  trend?: {
    value: number;
    isPositive: boolean;
  };
};

const StatCard = ({ 
  icon, 
  title, 
  value, 
  description, 
  className,
  trend
}: StatCardProps) => {
  return (
    <Card className={cn(className)}>
      <CardHeader className="flex flex-row items-center justify-between pb-2">
        <CardTitle className="text-sm font-medium text-muted-foreground">{title}</CardTitle>
        <div className="h-8 w-8 rounded-full bg-muted flex items-center justify-center text-muted-foreground">
          {icon}
        </div>
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        {trend && (
          <p className={cn(
            "text-xs mt-1",
            trend.isPositive ? "text-secondary" : "text-destructive"
          )}>
            {trend.isPositive ? "↑" : "↓"} {Math.abs(trend.value)}%
            {description && <span className="text-muted-foreground"> {description}</span>}
          </p>
        )}
        {!trend && description && (
          <p className="text-xs text-muted-foreground mt-1">{description}</p>
        )}
      </CardContent>
    </Card>
  );
};

export default StatCard;
