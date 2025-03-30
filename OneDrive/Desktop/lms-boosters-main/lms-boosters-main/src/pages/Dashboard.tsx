
import React from 'react';
import { BookOpen, Users, BookCheck, TrendingUp } from 'lucide-react';
import MainLayout from '@/components/layout/MainLayout';
import StatCard from '@/components/dashboard/StatCard';
import CourseProgress from '@/components/dashboard/CourseProgress';
import RecentActivities from '@/components/dashboard/RecentActivities';

const mockCourses = [
  {
    id: '1',
    title: 'WordPress Development Fundamentals',
    studentsCount: 125,
    completionPercentage: 68,
    status: 'published' as const,
  },
  {
    id: '2',
    title: 'Advanced Theme Customization',
    studentsCount: 89,
    completionPercentage: 42,
    status: 'published' as const,
  },
  {
    id: '3',
    title: 'Plugin Development Masterclass',
    studentsCount: 56,
    completionPercentage: 20,
    status: 'draft' as const,
  },
  {
    id: '4',
    title: 'WooCommerce Integration',
    studentsCount: 12,
    completionPercentage: 5,
    status: 'draft' as const,
  }
];

const mockActivities = [
  {
    id: '1',
    user: {
      name: 'Sarah Johnson',
      initials: 'SJ',
    },
    action: 'completed',
    target: 'WordPress Development Fundamentals',
    time: '5 minutes ago',
  },
  {
    id: '2',
    user: {
      name: 'Michael Smith',
      initials: 'MS',
    },
    action: 'started',
    target: 'Plugin Development Masterclass',
    time: '15 minutes ago',
  },
  {
    id: '3',
    user: {
      name: 'Emma Davis',
      initials: 'ED',
    },
    action: 'submitted',
    target: 'Theme Customization Quiz',
    time: '1 hour ago',
  },
  {
    id: '4',
    user: {
      name: 'Alex Wilson',
      initials: 'AW',
    },
    action: 'enrolled in',
    target: 'WooCommerce Integration',
    time: '2 hours ago',
  },
  {
    id: '5',
    user: {
      name: 'Jessica Miller',
      initials: 'JM',
    },
    action: 'commented on',
    target: 'Advanced Theme Customization',
    time: '3 hours ago',
  }
];

const Dashboard = () => {
  return (
    <MainLayout>
      <div className="space-y-6">
        <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
        
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <StatCard
            icon={<BookOpen size={18} />}
            title="Total Courses"
            value="12"
            description="4 active courses"
          />
          <StatCard
            icon={<Users size={18} />}
            title="Total Students"
            value="842"
            trend={{ value: 12.5, isPositive: true }}
            description="since last month"
          />
          <StatCard
            icon={<BookCheck size={18} />}
            title="Course Completions"
            value="286"
            trend={{ value: 8.2, isPositive: true }}
            description="since last month"
          />
          <StatCard
            icon={<TrendingUp size={18} />}
            title="Avg. Completion Rate"
            value="52%"
            trend={{ value: 3.8, isPositive: false }}
            description="since last month"
          />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <CourseProgress courses={mockCourses} />
          <RecentActivities activities={mockActivities} />
        </div>
      </div>
    </MainLayout>
  );
};

export default Dashboard;
