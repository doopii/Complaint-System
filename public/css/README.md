# Modular CSS Architecture Guide

## 📁 Folder Structure

```
css/
├── shared/           # Files used across ALL pages
│   ├── variables.css    # CSS custom properties (colors, fonts, spacing)
│   ├── layout.css      # Global layout (navbar, body, header)
│   └── components.css  # Reusable components (forms, buttons, tables, alerts)
├── auth/            # Authentication module (Team Member 1)
│   ├── auth-shared.css  # Common auth styling
│   ├── register.css     # Registration page specific
│   └── login.css        # Login page specific
└── student/         # Student complaint module (Team Member 2)
    ├── student-shared.css    # Common student styling
    ├── complaint-form.css    # Complaint submission form
    ├── complaint-detail.css  # Complaint detail view
    └── dashboard.css         # Student dashboard
```

## 🎯 Team Responsibilities

### Team Member 1 (Authentication)
- **Modules**: Registration, Login
- **Files to edit**: 
  - `css/auth/auth-shared.css`
  - `css/auth/register.css` 
  - `css/auth/login.css`
- **Pages**: register.blade.php, login.blade.php

### Team Member 2 (Student Complaints)
- **Modules**: Student complaint handling
- **Files to edit**:
  - `css/student/student-shared.css`
  - `css/student/complaint-form.css`
  - `css/student/complaint-detail.css`
  - `css/student/dashboard.css`
- **Pages**: student_complaint_*.blade.php

### Team Members 3 & 4 (Admin & Reports)
- **Modules**: Admin complaint handling, Reports
- **Next step**: Create `css/admin/` folder structure
- **Suggested files**:
  - `css/admin/admin-shared.css`
  - `css/admin/dashboard.css`
  - `css/admin/complaint-detail.css`
  - `css/admin/assign.css`
  - `css/admin/update.css`
  - `css/reports/` (if needed)

## 📋 CSS Loading Order

All pages should load CSS in this exact order:

```html
<link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
<!-- Then add module-specific CSS -->
<link rel="stylesheet" href="{{ asset('css/[module]/[module]-shared.css') }}">
<link rel="stylesheet" href="{{ asset('css/[module]/[specific-page].css') }}">
```

## ✅ Implementation Status

### ✅ Completed
- [x] Shared CSS files created
- [x] Auth module CSS files created
- [x] Student module CSS files created
- [x] All view files updated to use modular CSS
- [x] Authentication pages (register, login)
- [x] Student pages (complaint form, detail, dashboard)
- [x] General pages (home)

### 🚧 Pending (For Team Members 3 & 4)
- [ ] Create admin module CSS files
- [ ] Update admin view files with admin-specific CSS
- [ ] Create reports module CSS files (if needed)

## 🎨 CSS Custom Properties

All colors, fonts, and spacing are defined in `css/shared/variables.css`. Use these variables throughout your module-specific files:

```css
/* Examples */
background: var(--form-bg);
color: var(--color-text-light);
border: 1px solid var(--form-input-border);
```

## 🔧 Best Practices

1. **Always use shared variables** - Don't hardcode colors or sizes
2. **Follow the loading order** - Shared files first, then module-specific
3. **Keep module files focused** - Only include styles relevant to your module
4. **Use descriptive class names** - Follow existing patterns
5. **Test across all your module's pages** - Ensure consistency

## 🚀 Getting Started

1. Your module's view files are already set up with the correct CSS links
2. Edit your module-specific CSS files to customize styling
3. Use browser dev tools to inspect existing classes and variables
4. Test your changes across all pages in your module

## 💡 Tips

- Use `/* Module-specific comment */` to clearly mark your additions
- Check `css/shared/components.css` for existing styles before creating new ones
- Ask Team Member 1 or 2 if you need help with the shared files
- Keep the clean aesthetic with the existing color scheme and fonts
