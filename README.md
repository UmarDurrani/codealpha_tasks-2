# Social Media Platform

A mini social media platform built with PHP and MySQL.

## Features
- User registration and login
- Create posts
- Like/unlike posts
- Comment on posts
- Follow/unfollow users
- User profiles
- Search users

## Installation

1. Create MySQL database using `database.sql`
2. Update database credentials in `config.php`
3. Upload files to your web server
4. Make sure `images` directory is writable for avatar uploads

## Default Credentials
Register new users through the registration form.

## File Structure
- `config.php` - Database configuration
- `index.php` - Main feed
- `login.php` - User login
- `register.php` - User registration
- `profile.php` - User profile
- `post.php` - Create posts
- `like.php` - Like/unlike posts
- `comment.php` - Add comments
- `follow.php` - Follow/unfollow users
- `search.php` - Search users

## Security Notes
- Uses prepared statements to prevent SQL injection
- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
