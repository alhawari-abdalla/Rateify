# ⭐ Rateify — Laravel Rating System

[![Latest Version](https://img.shields.io/packagist/v/alhawari/rateify.svg?style=flat-square)](https://packagist.org/packages/alhawari/rateify)
[![Total Downloads](https://img.shields.io/packagist/dt/alhawari/rateify.svg?style=flat-square)](https://packagist.org/packages/alhawari/rateify)
[![License](https://img.shields.io/github/license/alhawari-code/rateify)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/alhawari/rateify)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-8.x%20to%2010.x-orange)](https://laravel.com/)

A robust, flexible, and easy-to-use Laravel package for adding star ratings (1-5) to any Eloquent model. Perfect for product reviews, content ratings, and user feedback systems.

## ✨ Features

- Rate any Eloquent model
- Support for rating comments
- Prevent duplicate ratings
- Calculate average ratings
- Get rating counts
- Check if a user has rated an item
- Remove ratings
- Fully customizable (min/max rating values)
- RESTful API endpoints
- Secure with built-in authentication
- Comprehensive validation
- Well-documented code

## 🚀 Installation

1. Install the package via Composer:

```bash
composer require alhawari/rateify
```

2. Publish the configuration file (optional):

```bash
php artisan vendor:publish --provider="Alhawari\\Rateify\\RateifyServiceProvider" --tag=config
```

3. Publish and run the migrations:

```bash
php artisan vendor:publish --provider="Alhawari\\Rateify\\RateifyServiceProvider" --tag=migrations
php artisan migrate
```

## ⚙️ Configuration

The configuration file allows you to customize the rating system:

```php
return [
    'max_rating' => 5,  // Maximum rating value
    'min_rating' => 1,  // Minimum rating value
];
```

## 🛠 Usage

### 1. Add the Rateable Trait to Your Model

```php
use Alhawari\Rateify\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Rateable;
    
    // Optional: Add custom logic to control who can rate
    public function canBeRated($userId): bool
    {
        return $this->user_id !== $userId; // Prevent users from rating their own posts
    }
}
```

### 2. Basic Usage

```php
// Get the average rating
$averageRating = $post->averageRating();

// Get the number of ratings
$ratingCount = $post->ratingsCount();

// Check if a user has rated the post
$hasRated = $post->isRatedBy($userId);

// Get a user's rating
$userRating = $post->getUserRating($userId);

// Rate a post
$rating = $post->rate($userId, 5, 'Great post!');

// Rate using the authenticated user
$rating = $post->rateByUser(4, 'Nice!');

// Remove a rating
$removed = $post->removeRating($userId);
```

## 🌐 API Endpoints

### Rate or Update a Rating

```http
POST /rateify/rate
```

**Parameters:**

| Parameter | Type   | Required | Description                     |
|-----------|--------|----------|---------------------------------|
| model     | string | Yes      | Fully qualified model class     |
| id        | int    | Yes      | ID of the model to rate         |
| value     | int    | Yes      | Rating value (1-5)              |
| comment   | string | No       | Optional comment with the rating |

**Example Response:**

```json
{
    "success": true,
    "message": "Rating saved successfully.",
    "data": {
        "rating": 5,
        "comment": "Great post!",
        "average": 4.5,
        "count": 10,
        "user_rating": 5
    }
}
```

### Remove a Rating

```http
DELETE /rateify/rate
```

**Parameters:**

| Parameter | Type   | Required | Description                 |
|-----------|--------|----------|-----------------------------|
| model     | string | Yes      | Fully qualified model class |
| id        | int    | Yes      | ID of the model             |

## 🔒 Permissions

By default, all API endpoints are protected by the `auth` middleware, so only authenticated users can rate items.

## 🧪 Testing

```bash
composer test
```

## 🤝 Contributing

Please see contact at alhawari.officail@gmail.com   .

## 🔐 Security

If you discover any security related issues, please email alhawari.officail@gmail.com  or use issue tracker.

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 📚 Credits

- [Your Name](https://github.com/alhawari-abdalla)
- [All Contributors](../../contributors)

---

⭐ If you find this package useful, please consider giving it a star on [GitHub](https://github.com/alhawari-abdalla/rateify).
