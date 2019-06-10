<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Want;
use App\Category;
use Laracombee;
use Illuminate\Support\Facades\Auth;

use Exception;

class NewsFeedController extends Controller
{
    /**
     * News feed. It returns collection of posts basid on filters and sorts
     * Input: categories, sort_by
     */

     public function newsFeed(Request $request){
         try{
            //Filters
            $filter = array(
                'sort_by' => ['cost#asc', 'cost#desc', 'created_at#asc', 'created_at#desc'],
            );

            //sort method
            $sort = explode('#', $request->sort_by);

            if(in_array($request->sort_by, $filter['sort_by']) && $request->categories[0] != "" && sizeOf($request->categories)) {
                 
                //Makes sure such categories exist
                Category::whereIn('id', $request->categories)->firstOrFail();
                return Want::where(['status'=> 1])->with(['user'])->

                whereIn('category_id', $request->categories)->orderBy($sort[0], $sort[1])->with(array('comments' => function($query) { $query->with('replies.user')->limit(2)->with('user');}))->paginate(10);

            }elseif(in_array($request->sort_by, $filter['sort_by']) && $request->categories[0] == ""){

                return Want::where(['status'=> 1])->with(['user'])->orderBy($sort[0], $sort[1])->with(array('comments' => function($query) { $query->with('replies.user')->limit(2)->with('user');}))->paginate(10);

            }elseif(empty($request->sort_by) && $request->categories[0] == ""){
                $user = User::find(1);
                // Prepare the request for recombee server, we need 10 recommended items for a given user.
                $recommendations = Laracombee::recommendTo(Auth::user(), 10, [ //optional parameters:
                    'filter' => "'user_id' != ".Auth::user()->id."",
                    'diversity'=> '.35',
                    'rotationRate'=> '1'
                  ])->wait();
                $recc_id = $recommendations['recommId'];
                $reccs = $recommendations['recomms'];
                $recommended_ids = [];
                foreach($reccs as $x){
                    array_push($recommended_ids, $x['id']);
                }

                return Want::whereIn('id', $recommended_ids)->with(['user'])->with(array('bookmark' => function($query) { $query->where('user_id', Auth::user()->id); }))->orderBy('created_at', 'desc')->with(array('comments' => function($query) { $query->with('replies.user')->limit(2)->with('user');}))->paginate(10);

                // return Want::where(['status'=> 1])->with(['user'])->with(array('bookmark' => function($query) { $query->where('user_id', Auth::user()->id); }))->orderBy('created_at', 'desc')->with(array('comments' => function($query) { $query->with('replies.user')->limit(2)->with('user');}))->paginate(10);
            }else{
                return Want::where(['status'=> 1])->with(['user'])->
                whereIn('category_id', $request->categories)->orderBy('created_at', 'desc')->with(array('comments' => function($query) { $query->with('replies.user')->limit(2)->with('user');}))->simplePaginate(10);
            }

         }catch(Exception $e){
            return $e->getMessage(); 
         }
     }

}
