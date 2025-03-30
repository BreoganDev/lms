
import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';

type CourseProgressProps = {
  courses: {
    id: string;
    title: string;
    studentsCount: number;
    completionPercentage: number;
    status: 'published' | 'draft' | 'archived';
  }[];
};

const CourseProgress = ({ courses }: CourseProgressProps) => {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Course Progress</CardTitle>
        <CardDescription>Student enrollment and completion rates</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-4">
          {courses.map((course) => (
            <div key={course.id} className="space-y-2">
              <div className="flex items-center justify-between">
                <div className="font-medium">{course.title}</div>
                <div className="text-sm text-muted-foreground">{course.studentsCount} students</div>
              </div>
              <div className="flex items-center gap-2">
                <Progress value={course.completionPercentage} className="h-2" />
                <span className="text-xs font-medium">{course.completionPercentage}%</span>
              </div>
              <div className="flex justify-between items-center">
                <span className={`text-xs px-2 py-0.5 rounded-full ${
                  course.status === 'published' ? 'bg-secondary/20 text-secondary' : 
                  course.status === 'draft' ? 'bg-orange-100 text-orange-700' : 
                  'bg-gray-100 text-gray-700'
                }`}>
                  {course.status.charAt(0).toUpperCase() + course.status.slice(1)}
                </span>
              </div>
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  );
};

export default CourseProgress;
