<?php

namespace App\Http\Controllers;

use Image;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\Request;
use Validator;

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

                $resized_img = Image::make($avatar)->fit(300);
                
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
        $this->validate($request,[
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email',
            'tag_line' => 'max:50',
            'description' => 'max:1000'
        ]);

        try{
            $this->updateName($request);
            $this->changeEmail($request);
            $this->updateTag($request);
            $this->updateDescription($request);

            return response()->json(['message' => 'Your profile has been updated'], 200);
        }catch(Exception $e){
            return $e->getMessage();
            return response()->json(['error' => 'Something went wrong, please try again'], 400);
        }
    }


    /**
     * Update name of current user 
     * Input: first_name, last_name
     */
    public function updateName(Request $request){
        User::findOrFail(Auth::user()->id)->update(
            ['last_name'=>$request->last_name, "first_name"=>$request->first_name]
        );
    }

    /**
     * Change email for current user
     * Input: email
     */
    public function changeEmail(Request $request){
        User::findOrFail(Auth::user()->id)->update(
            ['email'=>$request->email]
        );
    }

    /**
     * Update tagline
     * input:tag_line
     */
    public function updateTag(Request $request){
        User::findOrFail(Auth::user()->id)->update(
            ['tag_line'=>$request->tag_line]
        );
    }


    /**
     * Update description
     * input: description
     */
    public function updateDescription(Request $request){
        User::findOrFail(Auth::user()->id)->update(
            ['description'=>$request->description]
        );
    }




}
