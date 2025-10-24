# Community Chat Feature - Setup Guide

## Overview
A real-time chat interface has been implemented for community discussions. When users click the "Join Discussion" button, they can now chat with other community members in real-time.

## Features Implemented

### 1. Database Structure
- Created `community_messages` table to store chat messages
- Each message is linked to a community and a user
- Messages are automatically ordered by creation time

### 2. Chat Interface
- Modern, responsive chat UI with message bubbles
- User avatars and names displayed with each message
- Different styling for own messages vs. others' messages
- Auto-scrolling to latest messages
- Message timestamps (e.g., "5 minutes ago")
- Real-time updates via polling (every 5 seconds)

### 3. Access Control
- Only community members can send messages
- Non-members see a message prompting them to join
- Guests are prompted to log in

### 4. User Experience
- Collapsible chat panel to save space
- Message count badge in header
- Loading states for sending messages
- Form validation (max 1000 characters)
- Smooth animations and transitions

## Files Created/Modified

### New Files:
1. `/database/migrations/2025_10_23_120000_create_community_messages_table.php` - Database schema
2. `/app/Models/CommunityMessage.php` - Message model
3. `/app/Http/Livewire/CommunityChat.php` - Chat component logic
4. `/resources/views/livewire/community-chat.blade.php` - Chat UI
5. `/app/Events/MessageSent.php` - Broadcasting event (for future WebSocket support)

### Modified Files:
1. `/app/Models/Community.php` - Added messages relationship
2. `/app/Http/Livewire/Communities/ShowCommunity.php` - Updated to load chat
3. `/resources/views/livewire/communities/show-community.blade.php` - Added chat component

## Setup Instructions

### 1. Run Database Migration
```bash
php artisan migrate
```

This will create the `community_messages` table.

### 2. Test the Feature
1. Navigate to any community page (click "Join Discussion" button from home)
2. If you're a member, you should see the chat interface
3. Send a message and watch it appear in the chat
4. Messages update automatically every 5 seconds

### 3. Optional: Enable Real-Time Updates with WebSockets

For instant message delivery without polling, you can set up Laravel Broadcasting:

#### Option A: Using Pusher (Easiest)
1. Sign up for a free Pusher account at https://pusher.com
2. Create a new app and get your credentials
3. Update your `.env` file:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

4. Install Pusher PHP SDK:
```bash
composer require pusher/pusher-php-server
```

5. Install Laravel Echo and Pusher JS:
```bash
npm install --save-dev laravel-echo pusher-js
```

6. Update `/resources/js/bootstrap.js` to enable Echo:
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

7. Rebuild your assets:
```bash
npm run build
```

#### Option B: Using Laravel WebSockets (Self-hosted)
1. Install Laravel WebSockets:
```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
```

2. Update `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

3. Start the WebSocket server:
```bash
php artisan websockets:serve
```

## Current Implementation Details

### Polling Strategy
- Currently uses `wire:poll.5s` to refresh messages every 5 seconds
- This works without additional setup
- Good for low-to-medium traffic communities
- Can be upgraded to WebSockets for instant delivery

### Message Loading
- Loads the last 100 messages
- Messages are ordered chronologically
- Automatically scrolls to the latest message

### Security
- Messages are validated (required, max 1000 characters)
- User authentication is enforced
- Community membership is checked before allowing messages

## Future Enhancements (Optional)

1. **Message Reactions** - Add emoji reactions to messages
2. **File Attachments** - Allow users to share images/files
3. **Message Editing/Deletion** - Let users modify their messages
4. **Typing Indicators** - Show when someone is typing
5. **Online Status** - Display who's currently online
6. **Message Search** - Search through chat history
7. **Mentions** - @mention other users
8. **Message Notifications** - Notify users of new messages

## Troubleshooting

### Messages not appearing?
1. Check if the migration ran successfully
2. Verify you're a member of the community
3. Check browser console for JavaScript errors
4. Clear your browser cache

### Chat not showing up?
1. Ensure you're on a community show page
2. Verify the Livewire component is properly loaded
3. Check that Font Awesome icons are loaded (for UI elements)

### Performance issues?
1. Consider implementing pagination for message history
2. Enable WebSocket broadcasting instead of polling
3. Add caching for frequently accessed data

## Support
If you encounter any issues, check:
- Laravel logs: `storage/logs/laravel.log`
- Browser console for JavaScript errors
- Network tab for failed requests
