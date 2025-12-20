# Google OAuth Setup Guide

## Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Go to "APIs & Services" > "Credentials"

## Step 2: Create OAuth 2.0 Credentials

1. Click "Create Credentials" > "OAuth client ID"
2. Select "Web application"
3. Add Authorized redirect URIs:
   - `http://127.0.0.1:8000/auth/google/callback` (for local development)
   - `https://yourdomain.com/auth/google/callback` (for production)
4. Copy the Client ID and Client Secret

## Step 3: Update .env File

```env
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

## Step 4: Run Migration

```bash
php artisan migrate
```

## Done!

Now users can login/register using their Google account.
