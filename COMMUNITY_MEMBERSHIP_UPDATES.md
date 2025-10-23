# Community Membership Updates

## Overview
This document describes the changes made to implement toggle functionality for community membership and email notifications.

## Changes Made

### 1. Created Email Notification (`/app/Notifications/CommunityJoined.php`)
- **New File**: Created a notification class that sends a welcome email when a user joins a community
- **Features**:
  - Sends email with community name and description
  - Includes a link to visit the community
  - Uses the configured SMTP settings from `.env`

### 2. Updated CommunityController (`/app/Http/Controllers/CommunityController.php`)
- **Modified `join()` method** to toggle membership:
  - If user is NOT a member → Joins the community
    - Increments the `members` count
    - Adds user to `community_user` pivot table
    - Sends welcome email notification
  - If user IS a member → Leaves the community
    - Decrements the `members` count
    - Removes user from `community_user` pivot table
    - Prevents last admin from leaving
  - Returns JSON response with `is_member` and `member_count` for frontend updates

### 3. Updated API Controller (`/app/Http/Controllers/Api/CommunityApiController.php`)
- **Modified `join()` method**: Same toggle functionality as web controller
- **Modified `leave()` method**: Updated to decrement member count
- Maintains consistency between web and API endpoints

### 4. Updated Frontend JavaScript (`/resources/views/home.blade.php`)
- **Modified `joinCommunity()` function**:
  - Now handles both join and leave actions
  - Updates button state dynamically based on membership status
  - Updates member count in real-time
  - Shows appropriate success messages
  - Displays "Join Discussion" link after joining
  - Shows "Leave Community" button for members

### 5. Updated Frontend Display (`/resources/views/home.blade.php`)
- **Modified community card display**:
  - Shows both "Join Discussion" and "Leave Community" buttons for members
  - Shows only "Join Community" button for non-members
  - Uses appropriate colors: green for discussion, red for leave, blue for join

## How It Works

### Joining a Community
1. User clicks "Join Community" button
2. Frontend sends POST request to `/communities/{slug}/join`
3. Backend:
   - Increments `members` field
   - Adds user to pivot table
   - Sends email notification
4. Frontend:
   - Updates member count display
   - Shows "Join Discussion" link
   - Displays success notification

### Leaving a Community
1. User clicks "Leave Community" button (shown when member)
2. Frontend sends POST request to `/communities/{slug}/join` (same endpoint)
3. Backend:
   - Checks if user is last admin (prevents leaving if so)
   - Decrements `members` field
   - Removes user from pivot table
4. Frontend:
   - Updates member count display
   - Changes button back to "Join Community"
   - Displays success notification

## Email Configuration
The system uses the following SMTP settings from `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hamza.mbarki2002@gmail.com
MAIL_PASSWORD=oyfxuvuekszbydfu
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hamza.mbarki2002@gmail.com
MAIL_FROM_NAME="Bookshare"
```

## Testing
To test the functionality:
1. Login to the application
2. Navigate to a community on the home page
3. Click "Join Community" - you should:
   - See member count increase
   - Receive an email notification
   - See "Join Discussion" and "Leave Community" buttons
4. Click "Leave Community" - you should:
   - See member count decrease
   - Button changes back to "Join Community"

## API Endpoints
- `POST /communities/{community}/join` - Toggle membership (join if not member, leave if member)
- `POST /communities/{community}/leave` - Explicitly leave a community (legacy endpoint, still functional)

## Security Features
- Authorization checks via `CommunityPolicy`
- Prevents last admin from leaving a community
- CSRF token validation on all requests
- Only authenticated users can join/leave communities
