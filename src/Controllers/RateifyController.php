<?php

namespace Alhawari\Rateify\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Alhawari\Rateify\Models\Rating;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RateifyController
{
    /**
     * Store a new rating or update existing one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model' => ['required', 'string'],
                'id' => ['required', 'integer', 'min:1'],
                'value' => [
                    'required',
                    'integer',
                    'min:' . config('rateify.min_rating', 1),
                    'max:' . config('rateify.max_rating', 5)
                ],
                'comment' => ['nullable', 'string', 'max:1000'],
            ], [
                'value.min' => 'The rating must be at least :min star.',
                'value.max' => 'The rating may not be greater than :max stars.',
                'value.integer' => 'The rating must be a whole number.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $modelClass = $validated['model'];

            // Validate the model class exists and uses the Rateable trait
            if (!class_exists($modelClass) || !in_array(
                'Alhawari\\Rateify\\Traits\\Rateable',
                array_keys((new \ReflectionClass($modelClass))->getTraits())
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'The specified model is not rateable.'
                ], 422);
            }

            $item = $modelClass::findOrFail($validated['id']);
            
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to rate.'
                ], 401);
            }

            // Check if the model allows rating (if it has a canBeRated method)
            if (method_exists($item, 'canBeRated') && !$item->canBeRated(Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not allowed to rate this item.'
                ], 403);
            }

            // Create or update the rating
            $rating = $item->rate(
                Auth::id(),
                $validated['value'],
                $validated['comment'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Rating saved successfully.',
                'data' => [
                    'rating' => $rating->value,
                    'comment' => $rating->comment,
                    'average' => $item->averageRating(),
                    'count' => $item->ratingsCount(),
                    'user_rating' => $item->getUserRating(Auth::id())?->value
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'The item you are trying to rate does not exist.'
            ], 404);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving your rating.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the user's rating.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model' => ['required', 'string'],
                'id' => ['required', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $modelClass = $validated['model'];
            $item = $modelClass::findOrFail($validated['id']);

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to remove your rating.'
                ], 401);
            }

            $removed = $item->removeRating(Auth::id());

            return response()->json([
                'success' => $removed,
                'message' => $removed ? 'Rating removed successfully.' : 'No rating found to remove.',
                'data' => [
                    'average' => $item->averageRating(),
                    'count' => $item->ratingsCount(),
                    'user_rating' => null
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing your rating.'
            ], 500);
        }
    }
}
