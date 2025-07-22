# FlowForge System Bootstrap & Seeding

This document explains the comprehensive seeding and bootstrap system for FlowForge.

## Overview

FlowForge uses a multi-stage seeding system that ensures the application is properly initialized with:

1. **System Bootstrap** - Essential system data (superadmin, system tenant, permissions)
2. **RBAC Setup** - Role-based access control permissions and roles  
3. **Dummy Data** - Comprehensive test data for development

## System Bootstrap (Automatic)

The `SystemBootstrapSeeder` creates essential system components that the application needs to function:

### What it creates:
- **Superadmin User**: Mark Anthony Navarro (admin@flowforge.com)
- **System Tenant**: FlowForge Platform (flowforge-platform)
- **Permissions**: Complete set of system and tenant permissions
- **Central Admin Role**: With all permissions assigned

### Key Features:
- ✅ **Idempotent**: Safe to run multiple times
- ✅ **Smart**: Only creates data that doesn't exist
- ✅ **Automatic**: Runs during deployment/startup
- ✅ **Safe**: No data loss, only additions

## Quick Start

### 1. Fresh Installation
```bash
# Fresh migration and complete setup
php artisan migrate:fresh
php artisan db:seed
```

### 2. Bootstrap Only (Production)
```bash
# Run just the system bootstrap
php artisan db:seed --class=SystemBootstrapSeeder
```

### 3. Complete Development Setup
```bash
# Full setup with dummy data
php artisan migrate:fresh
php artisan db:seed
# Answer "yes" when prompted for dummy data
```

## Superadmin Access

Once bootstrapped, you can access the system with:

**Central Admin Dashboard**: http://localhost:8000/admin

```
Email: admin@flowforge.com
Password: superadmin@flowforge123!
Name: Mark Anthony Navarro
```

## System Tenant

The system tenant is automatically created:

```
Name: FlowForge Platform
Slug: flowforge-platform
Domain: system.flowforge.local
Plan: enterprise
```

## Dummy Data (Development)

When you run the full seeder, you get realistic dummy tenants:

### Tenant Examples:
- **TechStart Solutions** - http://techstart.localhost:8000
- **Creative Minds Agency** - http://creative.localhost:8000  
- **Global Manufacturing Inc** - http://manufacturing.localhost:8000
- **HealthTech Solutions** - http://healthtech.localhost:8000
- **FinanceFlow Corp** - http://financeflow.localhost:8000

### Dummy User Access:
All dummy users have the password: `password`

```
owner@techstart.com / password     (Owner role)
admin@techstart.com / password     (Admin role)  
manager@techstart.com / password   (Manager role)
[user]@techstart.com / password    (User role)
```

## Features of Dummy Data

### Realistic Business Structure:
- **Industry-specific** tenants (Technology, Healthcare, Finance, etc.)
- **Role-appropriate** user distribution  
- **Business-relevant** projects and tasks
- **Proper categorization** by industry

### Comprehensive Data:
- **Users**: 5-30 per tenant based on company size
- **Projects**: 6-15 per tenant with realistic statuses
- **Tasks**: 8-20 per project with proper assignments
- **Categories**: Industry-specific task categories

### Tenant Types:
- **Startup**: 1-10 employees, free/pro plans
- **Agency**: 11-50 employees, pro/enterprise plans  
- **Enterprise**: 200+ employees, enterprise plans

## Permission System

### System Permissions (Central Admin):
- `manage_tenants` - Create/manage tenant organizations
- `impersonate_users` - Login as other users for support
- `view_system_analytics` - System-wide analytics
- `manage_system_settings` - Global configuration

### Tenant Permissions:
- **User Management**: `manage_users`, `create_users`, `read_users`, `update_users`, `delete_users`
- **Project Management**: `manage_projects`, `create_projects`, `read_projects`, `update_projects`, `delete_projects`  
- **Task Management**: `manage_tasks`, `create_tasks`, `read_tasks`, `update_tasks`, `delete_tasks`
- **Categories**: `manage_categories`, `read_categories`
- **Reports**: `view_reports`
- **Settings**: `manage_tenant_settings`

## Role Hierarchy

### Central Admin:
- **central_admin**: Super admin with all permissions

### Tenant Roles:
- **owner**: Full tenant permissions
- **admin**: Most permissions except user deletion  
- **manager**: Project/task management permissions
- **user**: Basic read permissions + own task editing

## Manual Commands

### System Bootstrap Command:
```bash
# Check if system is bootstrapped
php artisan system:bootstrap

# Force bootstrap even if data exists  
php artisan system:bootstrap --force
```

### Individual Seeders:
```bash
# Just system bootstrap
php artisan db:seed --class=SystemBootstrapSeeder

# Just RBAC setup
php artisan db:seed --class=RolePermissionSeeder

# Just dummy data
php artisan db:seed --class=DummyDataSeeder
```

## Production Deployment

For production deployment, always run:

```bash
php artisan migrate --force
php artisan db:seed --class=SystemBootstrapSeeder --force
```

This ensures:
- ✅ Database is up to date
- ✅ Superadmin account exists
- ✅ System tenant is created
- ✅ All permissions are loaded
- ✅ Central admin role is configured

## Security Notes

### Default Credentials:
⚠️ **IMPORTANT**: Change the superadmin password in production!

```bash
# Change password via tinker
php artisan tinker
>>> $admin = App\Models\User::where('email', 'admin@flowforge.com')->first();
>>> $admin->password = bcrypt('new-secure-password');
>>> $admin->save();
```

### Environment Variables:
Consider using environment variables for production:

```env
SUPERADMIN_NAME="Your Name"
SUPERADMIN_EMAIL="admin@yourcompany.com"  
SUPERADMIN_PASSWORD="secure-random-password"
```

## Troubleshooting

### Common Issues:

**"Class not found" errors**:
```bash
composer dump-autoload
```

**Database connection issues**:
```bash
php artisan config:clear
php artisan cache:clear
```

**Permission denied on routes**:
- Verify user has correct role
- Check middleware is applied
- Confirm permissions are seeded

### Verification Commands:
```bash
# Check superadmin exists
php artisan tinker --execute="User::where('email', 'admin@flowforge.com')->first()"

# Check system tenant exists  
php artisan tinker --execute="Tenant::where('slug', 'flowforge-platform')->first()"

# Count permissions
php artisan tinker --execute="Permission::count()"
```

## Development Tips

### Reset Everything:
```bash
php artisan migrate:fresh --seed
```

### Reset Just Data:
```bash
php artisan db:seed --force
```

### Test RBAC:
```bash
php artisan rbac:test techstart
```

### Create Additional Tenants:
```bash
php artisan tenant:create "New Company" new-company
```

This seeding system provides a robust foundation for FlowForge development and deployment! 🚀
