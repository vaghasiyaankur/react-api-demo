<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ImageTest;
use Illuminate\Support\Facades\File;

class PageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Page Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling all functionality for
    | Pages.
    |
    */

    /**
     * Page List Information
     *
     * @return JSON $json
     * 
     */
    public function pageList()
    {
        $page_fields = Pages::latest()->get();

        return response()->json([
            'page_fields' => $page_fields
        ],200);
    }

    /**
     * Add Page Detail In DataBase
     *
     * @return Message (error or success)
     * 
     */
    public function addPage(Request $request)
    {
        // Validation Check
        $languages = Language::all();
        $rules = [];
        $rules['slug'] = 'unique:pages';
        foreach($languages as $language){
            $rules['title_'.$language->id] = 'required';
        }
        $firstLang = 'title_'.$languages[0]->id;
        $first_name = @$request->$firstLang;
        // dd($first_name);

        // Validation Check 
        $validator = Validator::make($request->all(),[
            'lang_id'       =>  'required',
            'page_order'    =>  'required|integer',
            'visibility'    =>  'required|integer',
            'title_active'  =>  'required|integer',
            'location'      =>  'required|string',
            // 'is_custom'     =>  'required|integer',
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'error' =>  $validator->messages()
            ], 401);
        }

        //Slug Create
        $slug = $request->slug;
        if(!$slug){
            $slug = Str::slug($first_name);
        }

        $pageavailable = Pages::where('slug', $slug)->count();
        // dd($productavailable);
        if ($pageavailable) {
            $lastPage = Pages::orderBy('id', 'desc')->first();
            $slug = $slug . '-' . ($lastPage->id + 1);
        }

        // Page Store
        $page = new Pages();
        $page->lang_id = $request->lang_id ? : 1;
        $page->title = $request->title;
        $page->slug = $slug;
        $page->description = $request->description;
        $page->keywords = $request->keywords;
        $page->page_content = $request->page_content;
        $page->page_order = $request->page_order;
        $page->visibility = $request->visibility;
        $page->title_active = $request->title_active;
        $page->location = $request->location;
        $page->is_custom = $request->is_custom ? : 1;
        $page->page_default_name = $request->page_default_name;
        $page->save();

        return response()->json([
            'success'   =>  $first_name.' Page Successfully Created'
        ],200);
    }

    /**
     * Delete Page Detail
     *
     * @return Message (error or success)
     * 
     */
    public function deletePage(Request $request)
    {   
        Pages::where('id',$request->id)->delete();

        return response()->json([
            'message'   =>  'Page Deleted Successfully'
        ],200);
    }

    /**
     * Update Page Detail
     *
     * @return Message (error or success)
     * 
     */
    public function updatePage(Request $request)
    {
        $updatePageId = Pages::where('id',$request->id)->first();

        // First Language Set
        $languages = Language::all();
         
        $firstLang = 'title_'.$languages[0]->id;
        $first_name = @$request->$firstLang;

        // Slug Create
        $slug = $request->slug;
        if(!$slug){
            $slug = Str::slug(@$first_name);
        }
        
        $pageavailable = Pages::where('slug', $slug)->where('id', '!=', $request->id)->count();
        if($pageavailable){
            $slug = $slug.'-'.($updatePageId->id);
        }

        // Page Update
        $pageUpdate = [
            'lang_id'       =>      $request->lang_id ? : 1,
            'title'         =>      $request->title,
            'slug'          =>      $slug,
            'description'   =>      $request->description,
            'keywords'      =>      $request->keywords,
            'page_content'  =>      $request->page_content,
            'page_order'    =>      $request->page_order,
            'visibility'    =>      $request->visibility,
            'title_active'  =>      $request->title_active,
            'location'      =>      $request->location ? : 'information',
            'is_custom'     =>      $request->is_custom ? : 1,
            'page_default_name' =>  $request->page_default_name,
        ];

        Pages::where('id', $request->id)->update($pageUpdate);
        
        return response()->json([
            'success'       =>     $first_name. ' Page Updated Successfully'
        ],200);
    }


    public function displayImage()
    {
        $images = ImageTest::all();
        return response()->json([
            'Image' =>  $images,
        ]);
    }

    public function uploadImage(Request $request)
    {   
        //  dd($request->image);
        // dd('Yes');
        // $validator = Validator::make($request->all(), [
        //     'image'     =>      'required|mimes:png,jpg,jpeg'
        // ]);
        
        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' =>  $validator->messages()
        //     ], 401);
        // }
        
        $default_image_name = '';
        $defaultImage = $request->image;
       
        // dd($defaultImage);
        $default_image_name = 'images/' . rand(1000000,9999999) . "." . $defaultImage->getClientOriginalExtension();
        $defaultImage->move('images/',$default_image_name);
        if($defaultImage){
            $image = new ImageTest();
            $image->image = $default_image_name;
            $image->save();
        }  
        
        return response()->json([
            'success' =>  $default_image_name . ' Image Upload Successfully'
        ], 200);
    }

    public function destroy($id)
    {
       $deleteProduct = ImageTest::find($id);
        if($deleteProduct){
            $destination = $deleteProduct->image;
            // dd($destination);
            if(File::exists($destination)){
                File::delete($destination);
            }
            $deleteProduct->delete();
            return response()->json([
                'success'   =>  'Image Delete Successfully'
            ],200);
        }else{
            return response()->json([
                'success'   =>  'Image Delete Error'
            ],200);
        }
    }

    public function updateImage(Request $request, $id)
    {
        
        $productImg = ImageTest::where('id',$id)->first();
        // dd($productImg);

        $path = $productImg->image;
        // dd($path);
        if(File::exists($path)){
            File::delete($path);
        }

        ImageTest::where('id',$id)->delete();

        $default_image_name = '';
        $defaultImage = $request->image;
        // dd($defaultImage);
        $default_image_name = 'images/' . rand(1000000,9999999) . "." . $defaultImage->getClientOriginalExtension();
        $defaultImage->move('images/',$default_image_name);
        if($defaultImage){
            $image = new ImageTest();
            $image->image = $default_image_name;
            $image->save();

            return response()->json([
                'success'   =>      $default_image_name . ' Image Successfully Updated'
            ],200);
        }  
    }
}
