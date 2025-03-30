
import React from 'react';
import { NavLink } from 'react-router-dom';
import { cn } from '@/lib/utils';
import { 
  LayoutDashboard, 
  BookOpen, 
  GraduationCap, 
  Users, 
  FileText,
  Settings,
  HelpCircle
} from 'lucide-react';

type NavItemProps = {
  to: string;
  icon: React.ReactNode;
  label: string;
};

const NavItem = ({ to, icon, label }: NavItemProps) => {
  return (
    <NavLink 
      to={to}
      className={({ isActive }) => cn(
        "flex items-center gap-3 px-3 py-2 rounded-md transition-colors",
        isActive 
          ? "bg-sidebar-accent text-sidebar-accent-foreground" 
          : "text-sidebar-foreground/80 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground"
      )}
    >
      <span className="w-5 h-5">{icon}</span>
      <span>{label}</span>
    </NavLink>
  );
};

const Sidebar = () => {
  return (
    <div className="h-screen bg-sidebar fixed w-64 flex flex-col py-6 border-r border-sidebar-border">
      <div className="px-6 mb-8">
        <h1 className="text-xl font-bold text-sidebar-foreground flex items-center gap-2">
          <GraduationCap className="h-6 w-6 text-secondary" />
          <span>LMS Boosters</span>
        </h1>
      </div>
      
      <div className="px-3 space-y-1 flex-1">
        <NavItem to="/" icon={<LayoutDashboard size={18} />} label="Dashboard" />
        <NavItem to="/courses" icon={<BookOpen size={18} />} label="Courses" />
        <NavItem to="/lessons" icon={<FileText size={18} />} label="Lessons" />
        <NavItem to="/students" icon={<Users size={18} />} label="Students" />
        <NavItem to="/quizzes" icon={<GraduationCap size={18} />} label="Quizzes" />
      </div>
      
      <div className="px-3 pt-6 border-t border-sidebar-border space-y-1">
        <NavItem to="/settings" icon={<Settings size={18} />} label="Settings" />
        <NavItem to="/help" icon={<HelpCircle size={18} />} label="Help" />
      </div>
    </div>
  );
};

export default Sidebar;
