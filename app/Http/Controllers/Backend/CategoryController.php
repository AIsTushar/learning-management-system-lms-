<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;



class CategoryController extends Controller
{

    // Fetch all categories from the database
    public function AllCategory()
    {

        $category = Category::latest()->get();
        return view('admin.backend.category.all_category', compact('category'));

    } // End Method

    // Add Category
    public function AddCategory()
    {
        return view('admin.backend.category.add_category');

    }// End Method

    public function StoreCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name',
            'image' => 'required|mimes:jpg,jpeg,png',
        ]);

        $image = Image::read($request->file('image'));
        $name_gen = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();

        $image->resize(370, 246)->save('upload/category/' . $name_gen);
        $save_url = 'upload/category/' . $name_gen;

        Category::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            'image' => $save_url,
        ]);


        $notification = array(
            'message' => "Category Inserted successfully!!",
            'alert-type' => 'success'
        );
        return redirect()->route('all.category')->with($notification);


    }// End Method

    public function EditCategory($id)
    {
        $category = Category::find($id);
        return view('admin.backend.category.edit_category', compact('category'));

    }// End Method

    public function UpdateCategory(Request $request)
    {
        $Cat_id = $request->id;

        if ($request->file('image')) {
            $request->validate([
                'category_name' => 'required|unique:categories,category_name',
                'image' => 'required|mimes:jpg,jpeg,png',
            ]);

            $image = Image::read($request->file('image'));
            $name_gen = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();

            $image->resize(370, 246)->save('upload/category/' . $name_gen);
            $save_url = 'upload/category/' . $name_gen;

            Category::find($Cat_id)->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
                'image' => $save_url,
            ]);


            $notification = array(
                'message' => "Category Updated with image successfully!!",
                'alert-type' => 'success'
            );
            return redirect()->route('all.category')->with($notification);
        } else {
            $request->validate([
                'category_name' => 'required|unique:categories,category_name',
            ]);
            Category::find($Cat_id)->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            ]);

            $notification = array(
                'message' => "Category Updated without image successfully!!",
                'alert-type' => 'success'
            );
            return redirect()->route('all.category')->with($notification);
        } // End Else


    }// End Method


    public function DeleteCategory($id)
    {
        $category = Category::find($id);
        $image = $category->image;
        $image_path = public_path($image);
        unlink($image_path);

        Category::find($id)->delete();

        $notification = array(
            'message' => "Category Deleted successfully!!",
            'alert-type' => 'success'
        );
        return redirect()->route('all.category')->with($notification);



    }// End Method


    /////////////// Sub Category Methods ///////////////
    public function AllSubCategory()
    {

        $subcategory = SubCategory::latest()->get();

        return view('admin.backend.subcategory.all_subcategory', compact('subcategory'));
    }//End Method

    public function AddSubCategory()
    {
        $category = Category::latest()->get();
        return view('admin.backend.subcategory.add_subcategory', compact('category'));

    }//End Method


    public function StoreSubCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'subcategory_name' => 'required|unique:sub_categories,subcategory_name',
        ]);


        SubCategory::insert([
            'category_id' => $request->category_id,
            'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),
            'subcategory_name' => $request->subcategory_name,
        ]);


        $notification = array(
            'message' => "Subcategory Inserted successfully!!",
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);
    }

    public function EditSubCategory($id)
    {
        $subcategory = SubCategory::find($id);
        $category = Category::latest()->get();
        return view('admin.backend.subcategory.edit_subcategory', compact('subcategory', 'category'));

    }//End Method

    public function UpdateSubCategory(Request $request)
    {


        $subcat_id = $request->id;

        SubCategory::find($subcat_id)->update([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),
        ]);


        $notification = array(
            'message' => 'SubCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);

    }

    public function DeleteSubCategory($id)
    {

        SubCategory::find($id)->delete();

        $notification = array(
            'message' => "Subcategory Deleted successfully!!",
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);

    }




}
