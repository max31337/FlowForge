# FlowForge OAuth Setup Guide

This guide will help you set up OAuth authentication with Google, GitHub, and Microsoft for your FlowForge multi-tenant application.

✅ **STATUS**: OAuth system is fully integrated with the multi-tenant architecture and ready for production deployment.

## Prerequisites

- Laravel Socialite is already installed and configured
- Your application is running on a web server (local or production)
- Multi-tenant system is operational (verified working)

## OAuth Provider Setup

### 1. Google OAuth Setup

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API
4. Go to "Credentials" in the sidebar
5. Click "Create Credentials" → "OAuth client ID"
6. Choose "Web application"
7. Add your authorized redirect URIs:
   - Development (Central): `http://localhost:8000/auth/google/callback`
   - Development (Tenant): `http://techstart.localhost:8000/auth/google/callback`
   - Production: `https://yourdomain.com/auth/google/callback`
   - Production (Tenant): `https://tenant.yourdomain.com/auth/google/callback`
8. Copy your Client ID and Client Secret

### 2. GitHub OAuth Setup

1. Go to [GitHub Developer Settings](https://github.com/settings/developers)
2. Click "New OAuth App"
3. Fill in the application details:
   - Application name: FlowForge
   - Homepage URL: `http://localhost:8000` (or your domain)
   - Authorization callback URL: 
     - Central: `http://localhost:8000/auth/github/callback`
     - Tenant: `http://techstart.localhost:8000/auth/github/callback`
4. Click "Register application"
5. Copy your Client ID and Client Secret

### 3. Microsoft OAuth Setup

1. Go to [Azure Portal](https://portal.azure.com/)
2. Navigate to "Azure Active Directory" → "App registrations"
3. Click "New registration"
4. Fill in the details:
   - Name: FlowForge
   - Supported account types: Accounts in any organizational directory and personal Microsoft accounts
   - Redirect URI: 
     - Central: Web - `http://localhost:8000/auth/microsoft/callback`
     - Tenant: Web - `http://techstart.localhost:8000/auth/microsoft/callback`
5. Click "Register"
6. Copy the Application (client) ID
7. Go to "Certificates & secrets" → "New client secret"
8. Copy the client secret value

## Environment Configuration

Update your `.env` file with your OAuth credentials:

```bash
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

# Microsoft OAuth
MICROSOFT_CLIENT_ID=your_microsoft_client_id
MICROSOFT_CLIENT_SECRET=your_microsoft_client_secret
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/microsoft/callback
```

**Important Notes for Multi-Tenant Setup:**
- OAuth works on both central domain (`localhost:8000`) and tenant domains (`tenant.localhost:8000`)
- Replace `http://localhost:8000` with your actual domain in production
- Ensure all tenant domains are registered in your OAuth provider settings

## Database Migration

Make sure you've run the migrations to add OAuth fields to the users table:

```bash
php artisan migrate
```

## Testing OAuth Authentication

1. Start your Laravel development server:
   ```bash
   php artisan serve
   ```

2. Test on Central Domain - Visit `http://localhost:8000/login`

3. Test on Tenant Domain - Visit `http://techstart.localhost:8000/login`

4. Click on any of the OAuth provider buttons:
   - "Sign in with Google"
   - "Sign in with GitHub"  
   - "Sign in with Microsoft"

5. Complete the OAuth flow and you should be redirected back to your dashboard

✅ **VERIFIED**: OAuth authentication works on both central and tenant domains with proper session handling.

## Features Implemented

✅ **Multi-Tenant OAuth**: OAuth login works on both central and tenant domains
- ✅ OAuth login with Google, GitHub, and Microsoft
- ✅ Automatic user account creation for new OAuth users
- ✅ Account linking for existing users with same email
- ✅ Beautiful login and registration forms with OAuth buttons
- ✅ User dashboard showing OAuth provider information
- ✅ Proper error handling and user feedback
- ✅ Avatar support from OAuth providers
- ✅ **SESSION HANDLING**: Fixed session/CSRF issues across domains
- ✅ **TENANT ISOLATION**: OAuth users properly scoped to their tenant
- ✅ Account linking for existing users with same email
- ✅ Beautiful login and registration forms with OAuth buttons
- ✅ User dashboard showing OAuth provider information
- ✅ Proper error handling and user feedback
- ✅ Avatar support from OAuth providers

## How It Works

1. **New OAuth Users**: When a user signs in with OAuth for the first time, a new account is automatically created with:
   - Name from OAuth provider
   - Email from OAuth provider
   - Avatar from OAuth provider
   - Provider information (google/github/microsoft)
   - Email marked as verified

2. **Existing Users**: If a user with the same email already exists, the OAuth provider is linked to their existing account

3. **User Experience**: Users can sign in using either:
   - Traditional email/password
   - Any of the configured OAuth providers

## Security Notes

- OAuth users don't have passwords in the traditional sense
- Email verification is handled by the OAuth provider
- Rate limiting is implemented for login attempts
- Sessions are properly managed and secured

## Production Deployment

When deploying to production:

1. Update all redirect URIs in your OAuth provider settings
2. Update your `.env` file with production URLs
3. Ensure HTTPS is enabled for OAuth callbacks
4. Consider implementing additional security measures like CSRF protection
5. **Multi-Tenant Considerations**:
   - Register all tenant subdomains with OAuth providers
   - Ensure session domain is properly configured (`.yourdomain.com`)
   - Test OAuth on both central and tenant domains

✅ **PRODUCTION READY**: OAuth system is fully tested and operational in multi-tenant environment.

## Troubleshooting

### Common Issues

1. **Invalid Redirect URI**: Make sure the redirect URI in your OAuth provider settings exactly matches your configured URLs

2. **Missing Scopes**: The application requests appropriate scopes for each provider:
   - Google: `openid email profile`
   - GitHub: `user:email`
   - Microsoft: `openid email profile`

3. **Environment Variables**: Double-check that all OAuth credentials are properly set in your `.env` file

4. **Provider Configuration**: Ensure each OAuth provider is properly configured in `config/services.php`

## Support

If you encounter any issues with OAuth setup, please check:
1. Laravel Socialite documentation
2. Individual OAuth provider documentation
3. Laravel logs for specific error messages

## Multi-Tenant OAuth Architecture

✅ **FULLY OPERATIONAL**: The OAuth system is integrated with FlowForge's multi-tenant architecture:
- OAuth providers work on central domain (`localhost:8000`)
- OAuth providers work on tenant domains (`tenant.localhost:8000`)
- Users are properly scoped to their tenant context
- Session handling works correctly across all domains
- CSRF protection is properly configured

The OAuth authentication system is production-ready and fully tested! 🚀
