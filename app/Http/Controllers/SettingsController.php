<?php

namespace App\Http\Controllers;

use Image;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Update the this users profile image 
     * Throw error if something goes wrong, or show success message 
     */
    public function updateAvatar(Request $request){
        try{
            if($request->hasFile('avatar')){
                $avatar = $request->file('avatar');

                $resized_img = Image::make($avatar)->resize(300, 300);
                
                if($resized_img ->filesize() > 5000000 ){
                    return response()->json([
                        'status' => 0,
                        'message' => "Your file size was too large, please try again with a smaller file size",
                    ]);

                }
                
                //check if last avatar needs to be deleted
                $current_avatar = Auth::user()->avatar;
                if($current_avatar != "avatars/default.png"){
                    Storage::disk('s3')->delete($current_avatar);
                }
                
                //save resized img to s3
                $filename = time() . '.'. 'jpg';
                Storage::disk('s3')->put("avatars/{$filename}",  $resized_img->stream('jpg')->__toString(), 'public');
                
                //update user avatar 
                $user = Auth::user();
                $user->avatar = "avatars/".$filename;
                $user->save();

                return response()->json([
                    'message' => "Profile image updated successfully",
                    200
                ]);
                
            }
            

        }catch(Exception $e){
            return response()->json([
                'errro' => "Something went wrong while uploading, please try again",
                400
            ]);
        }
    }

    /**
     * Update name, email, tag_line, description
     */
    public function updateNameEmailTagDes(Request $request){
        try{

            // return $this->updateName($request);
            // return $this->changeEmail($request);
            // $this->updateTag($request);
            // $this->updateDescription($request);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }


    /**
     * Update name of current user 
     * Input: first_name, last_name
     */
    public function updateName(Request $request){
        try{

            // $request->validate([
            //     'first_name' => 'between:1,50|string',
            //     'last_name' => 'between:1,50|string'
            // ]);
            if(empty($request->first_name) && !empty($request->last_name)){
                User::findOrFail(Auth::user()->id)->update(
                    ['last_name'=>$request->last_name]
                );

                return response()->json(['message'=> 'last name updated'], 200);  

            }elseif(empty($request->last_name) && !empty($request->first_name)){
                User::findOrFail(Auth::user()->id)->update(
                    ['first_name'=>$request->first_name]
                );

                return response()->json(['message'=> 'first name updated'], 200);  

            }elseif(!empty($request->first_name) && !empty($request->last_name)){
                User::findOrFail(Auth::user()->id)->update(
                    ['first_name'=>$request->first_name, 'last_name'=>$request->last_name]
                );

                return response()->json(['message'=> 'First and last name updated'], 200);  

            }else{
                return;
            }

        }catch(Exception $e){
            throw new Exception("Something went wrong, name has not been updated");
        }
    }

    /**
     * Change email for current user
     * Input: new_email
     */
    public function changeEmail(Request $request){
        try{
        
        if(empty($request->new_email)) return;

        $request->validate([
            'new_email' => 'email',
        ]);

        User::findOrFail(Auth::user()->id)->update(
                ['email'=>$request->new_email]
        );

        }catch(Exception $e){
            throw new Exception("Name is not changed");
        }
    }

    /**
     * Update tagline
     * input:tag_line
     */
    public function updateTag(Request $request){
        try{
            if(empty($request->tag_line)) return;

            $request->validate([
                'tag_line' => 'max:50',
            ]);

            User::findOrFail(Auth::user()->id)->update(
                    ['tag_line'=>$request->new_email]
            );
        }catch(Exception $e){
            throw new Exception("Name is not changed");
        }
    }


    /**
     * Update description
     * input: description
     */
    public function updateDescription(Request $request){
        try{
            if(empty($request->description)) return;

            $request->validate([
                'description' => 'max:1000',
            ]);

            User::findOrFail(Auth::user()->id)->update(
                    ['description'=>$request->description]
            );
        }catch(Exception $e){
            throw new Exception("description not updated");
        }
    }




}
