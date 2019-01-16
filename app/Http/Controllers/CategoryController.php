<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Exception;

class CategoryController extends Controller
{
    /**
     * Get all the category
     */
    public function getAll(){
        try{
            return Category::all();
        }catch(Exception $e){
            return "something went wrong";
        }
    }


    /**
     * Get single category 
     */
    public function Getsingle($id){
        try{
            return Category::findOrFail($id);
        }catch(Exception $e){
            return "something went wrong";
        }
    }
}
