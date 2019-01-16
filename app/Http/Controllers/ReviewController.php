<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Review;
use App\User;
use App\Want;
use App\Rating;
use App\Http\Requests\Review as ReviewValid;

use Exception;

class ReviewController extends Controller
{
    /**
     * Given rating, feedback, and fulfiller id, this
     * method adds a review for the fulfiller from the current user.
     */
    public function addReview(ReviewValid $request){
        try{
            //check if want has been completed
            Want::where('user_id', '=', Auth::user()->id)->where('status', '=', 3)->where('id', '=', $request->want_id)->firstOrFail();
           
            //add review
            $review = new Review();
            $review->user_id = Auth::user()->id;
            $review->fulfiller_id = $request->fulfiller_id;
            $review->want_id = $request->want_id;
            $review->rating = $request->rating;
            $review->feedback = $request->feedback;
            $review->save();

            // update Want state 
            $want = Want::findOrFail($request->want_id);
            $want->status = 4;
            $want->save();

            //update the rating of the user who got rated
            if($request->rating != null){
                $this->updateUserRating($request->fulfiller_id, $request->rating);
            }

            return response()->json(['message'=> 'Thank you for your review'], 200);  

        }catch(Exception $e){
            
            return response()->json(['message'=> 'Remember, you can only add a review once'], 400);

        }
        
    }

    /**
     * Update rating for user if the status of the Want is complete 
     */
    public function updateUserRating($user, $rating){
        try{
            User::findOrFail($user);

            $total_review = Review::where('fulfiller_id', '=', $user)->count();

            $sum_reviews = Review::where('fulfiller_id', '=', $user)->sum('rating');

            $newRate = number_format($sum_reviews /  $total_review, 2);
        
            $rating = Rating::where('user_id', '=', $user)->update(
                ['total_ratings' => $total_review, 'current_rating'=> $newRate]
            );

        }catch(Exception $e){
            throw new Exception('Something went wrong');
        }
    }
    
    /**
     * Get all reviews for this user with pagination (8 per page)
     */
    public function getAllReviews($user){
        try{
            $reviews = User::findOrFail($user)->reviews()->with('fulfiller')->paginate(2);
            return $reviews;
        }catch(Exception $e){
            return response()->json(['message'=> 'Something went wrong'], 400);
        }

    }
}
