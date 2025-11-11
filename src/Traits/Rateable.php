<?php

namespace Alhawari\Rateify\Traits;

use Alhawari\Rateify\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Rateable
{
    /**
     * Get all ratings for the model.
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Get the average rating for the model.
     */
    public function averageRating(): float
    {
        return round($this->ratings()->avg('value'), 1) ?: 0;
    }

    /**
     * Get the total number of ratings for the model.
     */
    public function ratingsCount(): int
    {
        return $this->ratings()->count();
    }

    /**
     * Add or update a rating for the model.
     */
    public function rate(int $userId, int $value, ?string $comment = null): Rating
    {
        return $this->ratings()->updateOrCreate(
            ['user_id' => $userId],
            [
                'value' => $value,
                'comment' => $comment
            ]
        );
    }

    /**
     * Rate the model using the authenticated user.
     */
    public function rateByUser(int $value, ?string $comment = null): Rating
    {
        return $this->rate(Auth::id(), $value, $comment);
    }

    /**
     * Get the rating for a specific user.
     */
    public function getUserRating(int $userId): ?Rating
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }

    /**
     * Check if the model has been rated by a specific user.
     */
    public function isRatedBy(int $userId): bool
    {
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    /**
     * Remove a user's rating.
     */
    public function removeRating(int $userId): bool
    {
        return (bool) $this->ratings()->where('user_id', $userId)->delete();
    }
}
