<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Want;
use App\Category;
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

                whereIn('category_id', $request->categories)->orderBy($sort[0], $sort[1])->with(array('comments' => function($query) { $query->with('replies')->latest()->limit(2);}))->paginate(10);

            }elseif(in_array($request->sort_by, $filter['sort_by']) && $request->categories[0] == ""){

                return Want::where(['status'=> 1])->with(['user'])->orderBy($sort[0], $sort[1])->with(array('comments' => function($query) { $query->with('replies')->latest()->limit(2);}))->paginate(10);

            }elseif(empty($request->sort_by) && $request->categories[0] == ""){
                return Want::where(['status'=> 1])->with(['user'])->with(array('bookmark' => function($query) { $query->where('user_id', Auth::user()->id); }))->orderBy('created_at', 'desc')->with(array('comments' => function($query) { $query->with('replies')->latest()->limit(2);}))->paginate(10);
            }else{
                return Want::where(['status'=> 1])->with(['user'])->
                whereIn('category_id', $request->categories)->orderBy('created_at', 'desc')->with(array('comments' => function($query) { $query->with('replies')->latest()->limit(2);}))->simplePaginate(10);
            }

         }catch(Exception $e){
            return $e->getMessage(); 
         }
     }

}
